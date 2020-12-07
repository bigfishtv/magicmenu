<?php
namespace MagicMenu;

use Cake\Core\InstanceConfigTrait;
use Cake\View\StringTemplateTrait;

use MagicMenu\Contracts\PathStrategy;
use MagicMenu\Contracts\UrlBuilder;
use MagicMenu\Utility\ArrayUtils;

class Menu
{
    use InstanceConfigTrait;
    use StringTemplateTrait;

    protected $_defaultConfig = [
        'activeClass' => 'active',
        'hereClass' => 'here',
        'depth' => null,
        'emptyUrl' => 'javascript:void(0);',
        'templates' => [
            'wrapper' => '<ul{{attrs}}>{{items}}</ul>',
            'item' => '<li><a href="{{url}}"{{attrs}}><span>{{title}}</span></a>{{children}}</li>',
            'separator' => '',
        ],
    ];

    protected $_items = [];

    protected $_activePath = null;

    protected $_pathStrategy;
    protected $_urlBuilder;

    public function __construct(array $items = [], array $options = [])
    {
        $this->setConfig($options);
        $this->setItems($items);
    }

    public function setPathStrategy(PathStrategy $pathStrategy = null)
    {
        $this->_pathStrategy = $pathStrategy;
        $this->_activePath = null;

        return $this;
    }

    public function getPathStrategy()
    {
        return $this->_pathStrategy;
    }

    public function setUrlBuilder(UrlBuilder $urlBuilder)
    {
        $this->_urlBuilder = $urlBuilder;

        return $this;
    }

    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    public function setItems(array $items)
    {
        $this->_items = $items;
        $this->_activePath = null;

        return $this;
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function setActivePath(array $path)
    {
        $this->_activePath = $path;

        return $this;
    }

    public function getActivePath()
    {
        if (is_null($this->_activePath) && $this->_pathStrategy) {
            $this->_activePath = $this->_pathStrategy->getActivePath($this->getFlattenedItems());
        }

        return (array)$this->_activePath;
    }

    public function getItemAt(array $path)
    {
        $items = $this->_items;
        while (($i = array_shift($path)) !== null) {
            if (!$path && isset($items[$i])) {
                return $items[$i];
            } elseif (isset($items[$i]['children'])) {
                $items = $items[$i]['children'];
            } else {
                break;
            }
        }

        return false;
    }

    public function setDepth($depth)
    {
        return $this->setConfig('depth', $depth, false);
    }

    public function getDepth()
    {
        $depth = $this->getConfig('depth');
        if (is_array($depth) && count($depth) == 2) {
            return $depth;
        }

        return [0, INF];
    }

    public function getFlattenedItems()
    {
        return $this->_flatten($this->_items);
    }

    protected function _flatten(array $items, array $path = [])
    {
        $index = 0;

        return array_reduce($items, function ($list, $item) use ($path, &$index) {
            $path[] = $index++;
            $children = ArrayUtils::consume('children', $item);
            $list[] = compact('path', 'item');
            if (is_array($children)) {
                $list = array_merge($list, $this->_flatten($children, $path));
            }

            return $list;
        }, []);
    }

    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * Render menu to a string
     *
     * @return string
     */
    public function render()
    {
        $items = $this->_items;
        $path = [];
        list($minDepth, $maxDepth) = $this->getDepth();
        if ($minDepth > 0) {
            $path = array_slice($this->getActivePath(), 0, $minDepth);
            $item = $this->getItemAt($path);
            if (!empty($item['children'])) {
                $items = $item['children'];
            } else {
                $items = [];
            }
        }
        if (!$items) {
            return '';
        }

        return $this->_renderWrapper($items, $path);
    }

    public function getItemUrl($item)
    {
        if (!isset($item['url']) && !empty($item['children'][0])) {
            return $this->getItemUrl($item['children'][0]);
        } elseif (!empty($item['url'])) {
            return $item['url'];
        }

        return false;
    }

    protected function _getTemplateName($name, $level)
    {
        if ($this->getTemplates($level . '.' . $name)) {
            return $level . '.' . $name;
        }

        return $name;
    }

    protected function _renderWrapper(array $items, array $path = [])
    {
        $index = 0;
        $items = array_filter(array_map(function ($item) use ($path, &$index) {
            $path[] = $index++;
            if (isset($item['visible']) && ($item['visible'] === false || $item['visible'] === 0)) {
                return false;
            }

            return $this->_renderItem($item, $path);
        }, $items));
        $options = [
            //'class' => 'nav'
        ];
        
        $separator = $this->formatTemplate($this->_getTemplateName('separator', count($path)), []);

        return $this->formatTemplate($this->_getTemplateName('wrapper', count($path)), [
            'attrs' => $this->templater()->formatAttributes($options),
            'items' => implode($separator, $items),
        ]);
    }

    protected function _renderItem($item, array $path = [])
    {
        $here = $this->getActivePath() == $path;
        $active = array_slice($this->getActivePath(), 0, count($path)) == $path;
        $class = [];
        if ($active) {
            $class[] = $this->getConfig('activeClass');
        }
        if ($here) {
            $class[] = $this->getConfig('hereClass');
        }
        $options = [
            'class' => $class ? implode(' ', $class) : null
        ];
        list($minDepth, $maxDepth) = $this->getDepth();
        if (!empty($item['children']) && $maxDepth >= count($path)) {
            $children = $this->_renderWrapper($item['children'], $path);
        } else {
            $children = '';
        }

        return $this->formatTemplate($this->_getTemplateName('item', count($path) - 1), [
            'title' => isset($item['title']) ? h($item['title']) : '',
            'unescapedTitle' => isset($item['title']) ? $item['title'] : '',
            'url' => $this->getItemUrl($item) ?: $this->getConfig('emptyUrl'),
            'attrs' => $this->templater()->formatAttributes($options),
            'children' => $children,
        ] + $item);
    }
}

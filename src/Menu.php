<?php
namespace MagicMenu;

use Cake\Core\InstanceConfigTrait;
use Cake\View\StringTemplateTrait;

use MagicMenu\Utility\ArrayUtils;

class Menu
{
	use StringTemplateTrait;
	use InstanceConfigTrait;
	
	protected $_defaultConfig = [
		'depth' => null,
        'templates' => [
            'wrapper' => '<ul{{attrs}}>{{items}}</ul>',
			'item' => '<li><a href="{{url}}"{{attrs}}><span>{{title}}</span></a>{{children}}</li>',
        ],
    ];

	protected $_items = [];

	protected $_activePath = [];

	public function __construct(array $items = [], array $options = [])
	{
		$this->setItems($items);
		$this->config($options);
	}

	public function setItems(array $items)
	{
		$this->_items = $items;
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
		return $this->_activePath;
	}

	public function getItemAt(array $path)
	{
		$items = $this->_items;
		while (($i = array_shift($path)) !== null) {
			if (!$path && isset($items[$i])) {
				return $items[$i];
			} else if (isset($items[$i]['children'])) {
				$items = $items[$i]['children'];
			} else {
				break;
			}
		}
		return false;
	}

	public function setDepth($depth)
	{
		return $this->config('depth', $depth);
	}

	public function getDepth()
	{
		$depth = $this->config('depth');
		if (!$depth) {
			return [0, INF];
		}
		return $depth;
	}

	public function getFlattenedItems()
	{
		return $this->_flatten($this->_items);
	}

	protected function _flatten(array $items, array $path = [])
	{
		$index = 0;
		return array_reduce($items, function($list, $item) use ($path, &$index) {
			$path[] = $index++;
			$children = ArrayUtils::consume('children', $item);
			$list[] = compact('path', 'item');
			if (is_array($children)) {
				$list = array_merge($list, $this->_flatten($children, $path));
			}
			return $list;
		}, []);
	}

	public function render()
	{
		return $this->_renderWrapper($this->_items);
	}

	protected function _renderWrapper(array $items, array $path = [])
	{
		$index = 0;
		$items = array_map(function($item) use ($path, &$index) {
			$path[] = $index++;
			return $this->_renderItem($item, $path);
		}, $items);
		$options = [
			//'class' => 'nav'
		];
		return $this->formatTemplate('wrapper', [
            'attrs' => $this->templater()->formatAttributes($options),
            'items' => implode('', $items),
        ]);
	}

	protected function _renderItem($item, array $path = [])
	{
		$here = $this->getActivePath() == $path;
		$active = array_slice($this->getActivePath(), 0, count($path)) == $path;
		$class = [];
		if ($active) {
			$class[] = 'active';
		}
		if ($here) {
			$class[] = 'here';
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
		return $this->formatTemplate('item', [
			'title' => h($item['title']),
			'url' => $item['url'],
			'attrs' => $this->templater()->formatAttributes($options),
			'children' => $children,
		]);
	}

}
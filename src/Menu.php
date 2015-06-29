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
        'errorClass' => 'error',
        'templates' => [
            'wrapper' => '<ul{{attrs}}>{{items}}</ul>',
			'item' => '<li><a href="{{url}}"{{attrs}}><span>{{title}}</span></a>{{children}}</li>',
        ],
    ];

	protected $_items = [];

	protected $_activePath = [];

	public function __construct($items = [], $options = [])
	{
		$this->setItems($items);
		$this->config($options);
	}

	public function setItems($items)
	{
		$this->_items = $items;
		return $this;
	}

	public function getItems()
	{
		return $this->_items;
	}

	public function setActivePath($path)
	{
		$this->_activePath = $path;
		return $this;
	}

	public function getActivePath()
	{
		return $this->_activePath;
	}

	public function getItemAt($path)
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

	public function render()
	{
		return $this->renderWrapper($this->_items);
	}

	public function getFlattenedItems()
	{
		return $this->_flatten($this->_items);
	}

	protected function _flatten($items, $path = [])
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

	public function renderWrapper($items)
	{
		$items = array_map(function($item) {
			return $this->renderItem($item);
		}, $items);
		$options = [
			//'class' => 'nav'
		];
		return $this->formatTemplate('wrapper', [
            'attrs' => $this->templater()->formatAttributes($options),
            'items' => implode('', $items),
        ]);
	}

	public function renderItem($item)
	{
		$options = [
			//'class' => 'active'
		];
		if (!empty($item['children'])) {
			$children = $this->renderWrapper($item['children']);
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
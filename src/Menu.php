<?php
namespace MagicMenu;

use Cake\Core\InstanceConfigTrait;
use Cake\View\StringTemplateTrait;

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

	public function render()
	{
		return $this->renderWrapper($this->_items);
	}

	public function renderWrapper($items) {
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

	public function renderItem($item) {
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
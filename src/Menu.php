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
		$items = array_map(function($item) {
			$options = [
				//'class' => 'active'
			];
			return $this->formatTemplate('item', [
				'title' => $item['title'],
				'url' => $item['url'],
				'attrs' => $this->templater()->formatAttributes($options),
				'children' => '',
			]);
		}, $this->_items);

		$options = [
			'class' => 'nav'
		];

		$wrapper = $this->formatTemplate('wrapper', [
            'attrs' => $this->templater()->formatAttributes($options),
            'items' => implode('', $items),
        ]);

        return $wrapper;
	}

}
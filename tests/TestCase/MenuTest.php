<?php
namespace MagicMenu\Test;

use MagicMenu\Menu;

use Cake\TestSuite\TestCase;

class MenuTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
		$items = [
			['title' => 'About', 'url' => '/about'],
			['title' => 'Work', 'url' => '/work', 'children' => [
				['title' => 'Thiess', 'url' => '/work/thiess'],
				['title' => 'MAX Employment', 'url' => '/work/max-employment'],
				['title' => 'Spike & Dadda', 'url' => '/work/spike-and-dadda'],
			]],
			['title' => 'Contact', 'url' => '/contact']
		];
		$this->Menu = new Menu($items);
	}

	public function tearDown()
	{
		unset($this->Menu);
	}

	public function testSetItems()
	{
		$items = [
			['title' => 'One'],
			['title' => 'Two'],
		];

		$menu = $this->Menu->setItems($items);
		$this->assertEquals($menu, $this->Menu, 'Menu::setItems() should return Menu instance');

		$result = $this->Menu->getItems();
		$this->assertEquals($result, $items, 'Menu::getItems() should match the items in Menu::setItems()');
	}

	public function testConstructorParams()
	{
		$items = [
			['title' => 'One'],
			['title' => 'Two'],
		];

		$options = [
			'randomConfig' => 'randomValue'
		];

		$menu = new Menu($items, $options);

		$result = $menu->getItems();
		$this->assertEquals($result, $items, 'Menu::__construct should call setItems()');

		$result = $menu->config('randomConfig');
		$this->assertEquals($result, $options['randomConfig'], 'Menu::__construct should call config()');
	}

	public function testBasicRender()
	{
		$result = $this->Menu->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/about"><span>About</span></a></li>',
				'<li><a href="/work"><span>Work</span></a>',
					'<ul>',
						'<li><a href="/work/thiess"><span>Thiess</span></a></li>',
						'<li><a href="/work/max-employment"><span>MAX Employment</span></a></li>',
						'<li><a href="/work/spike-and-dadda"><span>Spike &amp; Dadda</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="/contact"><span>Contact</span></a></li>',
			'</ul>'
		]);
		$this->assertEquals($expected, $result);
	}

}
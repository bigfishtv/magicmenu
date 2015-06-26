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

	public function testRender()
	{
		$result = $this->Menu->render();
		$expected = '<ul class="nav"><li><a href="/about"><span>About</span></a></li><li><a href="/work"><span>Work</span></a></li><li><a href="/contact"><span>Contact</span></a></li></ul>';
		$this->assertEquals($result, $expected);
	}

}
<?php
namespace MagicMenu\Test;

use MagicMenu\View\Helper\MagicMenuHelper;

use Cake\TestSuite\TestCase;

class MagicMenuHelperTest extends TestCase {
	
	public function setUp()
	{
		parent::setUp();
		$this->View = $this->getMock('Cake\View\View');
		$this->MagicMenu = new MagicMenuHelper($this->View);
	}

	public function tearDown()
	{
		unset($this->MagicMenu, $this->View);
	}

	public function testCreate()
	{
		$items = [
			['title' => 'One'],
			['title' => 'Two']
		];
		$menu = $this->MagicMenu->create($items, ['id' => 'random', 'randomConfig' => 'randomValue']);

		$class = get_class($menu);
		$expected = 'MagicMenu\Menu';
		$this->assertEquals($expected, $class, 'MagicMenuHelper::create should return a Menu instance');

		$menu2 = $this->MagicMenu->get('random');
		$this->assertSame($menu, $menu2, 'MagicMenuHelper::get should return a Menu instance');

		$value = $menu->config('randomConfig');
		$expected = 'randomValue';
		$this->assertEquals($expected, $value, '"randomConfig" key should be set on Menu instance');

		$value = $menu->config('id');
		$this->assertNull($value, '"id" key should NOT be set on Menu instance');

		$expected = $this->MagicMenu->create($items, ['id' => 0]);
		$result = $this->MagicMenu->get(0);
		$this->assertSame($expected, $result, 'id can also be an integer');

		$menu2 = $this->MagicMenu->get('random');
		$this->assertSame($menu, $menu2, 'MagicMenuHelper should remember more than a single instance');
	}

	public function testCustomClassWithConfig()
	{
		$menuClass = 'MagicMenu\Test\MockMenu';
		$menu = $this->MagicMenu->config('menuClass', $menuClass)->create();
		$this->assertEquals($menuClass, get_class($menu));
	}

	public function testCustomClassWithOptions()
	{
		$menuClass = 'MagicMenu\Test\MockMenu';
		$menu = $this->MagicMenu->create([], ['menuClass' => $menuClass]);
		$this->assertEquals($menuClass, get_class($menu));

		$value = $this->MagicMenu->config('menuClass');
		$expected = 'MagicMenu\Menu';
		$this->assertEquals($expected, $value, 'Constructor options should NOT set class config');
	}

	public function testGetUndefined()
	{
		$result = $this->MagicMenu->get('undefinedInstance');
		$expected = false;
		$this->assertSame($expected, $result, 'MagicMenuHelper::get with undefined instance should return false');
	}

}

class MockMenu extends \MagicMenu\Menu {

}
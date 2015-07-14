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
		$menu = $this->MagicMenu->create($items, ['randomConfig' => 'randomValue'], 'random');

		$class = get_class($menu);
		$expected = 'MagicMenu\Menu';
		$this->assertEquals($expected, $class, 'MagicMenuHelper::create should return a Menu instance');

		$menu2 = $this->MagicMenu->getMenu('random');
		$this->assertSame($menu, $menu2, 'MagicMenuHelper::getMenu should return a Menu instance');

		$value = $menu->config('randomConfig');
		$expected = 'randomValue';
		$this->assertEquals($expected, $value, '"randomConfig" key should be set on Menu instance');

		$expected = $this->MagicMenu->create($items, [], 0);
		$result = $this->MagicMenu->getMenu(0);
		$this->assertSame($expected, $result, 'id can also be an integer');

		$menu2 = $this->MagicMenu->getMenu('random');
		$this->assertSame($menu, $menu2, 'MagicMenuHelper should remember more than a single instance');
	}

	public function testCustomClassWithConfig()
	{
		$menuClass = 'MagicMenu\Test\MockMenu';
		$menu = $this->MagicMenu->config('Menu', $menuClass)->create();
		$this->assertEquals($menuClass, get_class($menu));
	}

	public function testGetUndefined()
	{
		$result = $this->MagicMenu->getMenu('undefinedInstance');
		$expected = false;
		$this->assertSame($expected, $result, 'MagicMenuHelper::getMenu with undefined instance should return false');
	}

	public function testSetMenu()
	{
		$menu = new MockMenu();
		$result = $this->MagicMenu->setMenu('something', $menu);

		$this->assertSame($this->MagicMenu, $result, 'MagicMenuHelper::setMenu should return MagicMenuHelper instance');

		$result = $this->MagicMenu->getMenu('something');
		$this->assertSame($menu, $result, 'MagicMenuHelper::getMenu should return menu instance from MagicMenuHelper::setMenu');
	}

}

class MockMenu extends \MagicMenu\Menu {

}
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

	public function testRenderWithNestedActiveItem()
	{
		$result = $this->Menu->setActivePath([1, 1])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/about"><span>About</span></a></li>',
				'<li><a href="/work" class="active"><span>Work</span></a>',
					'<ul>',
						'<li><a href="/work/thiess"><span>Thiess</span></a></li>',
						'<li><a href="/work/max-employment" class="active"><span>MAX Employment</span></a></li>',
						'<li><a href="/work/spike-and-dadda"><span>Spike &amp; Dadda</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="/contact"><span>Contact</span></a></li>',
			'</ul>'
		]);
		$this->assertEquals($expected, $result);

		$result = $this->Menu->setActivePath([1])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/about"><span>About</span></a></li>',
				'<li><a href="/work" class="active"><span>Work</span></a>',
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

	public function testFlattenedItems()
	{
		$expected = [
			[
				'path' => [0],
				'item' => ['title' => 'About', 'url' => '/about'],
			],
			[
				'path' => [1],
				'item' => ['title' => 'Work', 'url' => '/work'],
			],
			[
				'path' => [1, 0],
				'item' => ['title' => 'Thiess', 'url' => '/work/thiess'],
			],
			[
				'path' => [1, 1],
				'item' => ['title' => 'MAX Employment', 'url' => '/work/max-employment'],
			],
			[
				'path' => [1, 2],
				'item' => ['title' => 'Spike & Dadda', 'url' => '/work/spike-and-dadda'],
			],
			[
				'path' => [2],
				'item' => ['title' => 'Contact', 'url' => '/contact'],
			]
		];

		$result = $this->Menu->getFlattenedItems();
		$this->assertEquals($expected, $result);
	}

	public function testSetActivePath()
	{
		$path = [1, 2];
		$menu = $this->Menu->setActivePath($path);
		$this->assertSame($menu, $this->Menu, 'Menu::setActivePath() should return Menu instance');

		$result = $this->Menu->getActivePath();
		$this->assertEquals($result, $path, 'Menu::getActivePath() should match the path in Menu::setActivePath()');
	}

	public function testGetActivePath()
	{
		$result = $this->Menu->getActivePath();
		$expected = [];
		$this->assertSame($expected, $result, 'Menu::getActivePath() should return array');
	}

	public function testGetItemBasic()
	{
		$result = $this->Menu->getItemAt([1, 1]);
		$expected = ['title' => 'MAX Employment', 'url' => '/work/max-employment'];
		$this->assertEquals($expected, $result);
	}

	public function testGetItemWithChildren()
	{
		$result = $this->Menu->getItemAt([1]);
		$expected = ['title' => 'Work', 'url' => '/work', 'children' => [
			['title' => 'Thiess', 'url' => '/work/thiess'],
			['title' => 'MAX Employment', 'url' => '/work/max-employment'],
			['title' => 'Spike & Dadda', 'url' => '/work/spike-and-dadda'],
		]];
		$this->assertEquals($expected, $result);
	}

	public function testGetItemInvalidPath()
	{
		$result = $this->Menu->getItemAt([3]);
		$this->assertFalse($result);
	}

	public function testGetItemEmptyPath()
	{
		$result = $this->Menu->getItemAt([]);
		$this->assertFalse($result);
	}

}
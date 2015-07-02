<?php
namespace MagicMenu\Test;

use MagicMenu\Menu;

use Cake\TestSuite\TestCase;

class MenuTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
		$items = $this->getDefaultMenuItems();
		$this->Menu = new Menu($items);
	}

	public function getDefaultMenuItems()
	{
		return [
			['title' => 'About', 'url' => '/about'],
			['title' => 'Work', 'url' => '/work', 'children' => [
				['title' => 'Thiess', 'url' => '/work/thiess'],
				['title' => 'MAX Employment', 'url' => '/work/max-employment'],
				['title' => 'Spike & Dadda', 'url' => '/work/spike-and-dadda'],
			]],
			['title' => 'Contact', 'url' => '/contact']
		];
	}

	public function getDeepMenuItems()
	{	
		$items = $this->getDefaultMenuItems();
		// add children to /About
		$items[0]['children'] = [
			['title' => 'Team', 'url' => '/about/team'],
		];
		// add children to /Work/Thiess
		$items[1]['children'][0]['children'] = [
			['title' => 'Translate', 'url' => '/work/thiess/translate'],
			['title' => '80 Years', 'url' => '/work/thiess/80-years'],
			['title' => 'Website', 'url' => '/work/thiess/website'],
		];
		return $items;
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

	public function testMenuToString()
	{
		$result = (string) $this->Menu;
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

	public function testMenuToStringInvalidDepth()
	{
		$result = (string) $this->Menu->setDepth([2, 2]);
		$expected = '';
		$this->assertSame($expected, $result);
	}

	public function testRenderWithActiveItem()
	{
		$result = $this->Menu->setActivePath([1])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/about"><span>About</span></a></li>',
				'<li><a href="/work" class="active here"><span>Work</span></a>',
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
						'<li><a href="/work/max-employment" class="active here"><span>MAX Employment</span></a></li>',
						'<li><a href="/work/spike-and-dadda"><span>Spike &amp; Dadda</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="/contact"><span>Contact</span></a></li>',
			'</ul>'
		]);
		$this->assertEquals($expected, $result);
	}

	public function testDefaultDepth()
	{
		$result = $this->Menu->config('depth');
		$this->assertNull($result);

		$result = $this->Menu->getDepth();
		$expected = [0, INF];
		$this->assertEquals($expected, $result);
	}

	public function testSetDepth()
	{
		$depth = [0, 0];
		$menu = $this->Menu->setDepth($depth);
		$this->assertSame($this->Menu, $menu, 'Menu::setDepth() should return menu instance');

		$result = $this->Menu->getDepth();
		$this->assertEquals($depth, $result, 'Menu::getDepth() value should equal value set in Menu::setDepth()');

		$result = $this->Menu->config('depth');
		$this->assertEquals($depth, $result, 'Menu->config(\'depth\') should equal value set in Menu::setDepth()');

		$this->Menu->config('depth', [0, 2], false);
		$this->Menu->config('depth', [4, 3], false);
		$result = $this->Menu->config('depth');
		$expected = [4, 3];
		$this->assertEquals($expected, $result);

		$this->Menu->setDepth([0, 2]);
		$this->Menu->setDepth([4, 3]);
		$result = $this->Menu->getDepth();
		$expected = [4, 3];
		$this->assertEquals($expected, $result);
	}

	public function testSetInvalidDepth()
	{
		$expected = [0, INF];

		$result = $this->Menu->setDepth([0, 1, 2])->getDepth();
		$this->assertEquals($expected, $result);

		$result = $this->Menu->setDepth(3)->getDepth();
		$this->assertEquals($expected, $result);

		$result = $this->Menu->setDepth(null)->getDepth();
		$this->assertEquals($expected, $result);

		$result = $this->Menu->setDepth('random string')->getDepth();
		$this->assertEquals($expected, $result);

		$result = $this->Menu->setDepth(true)->getDepth();
		$this->assertEquals($expected, $result);
	}

	public function testRenderMaxDepthSimple()
	{
		$result = $this->Menu->config('depth', [0, 0])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/about"><span>About</span></a></li>',
				'<li><a href="/work"><span>Work</span></a></li>',
				'<li><a href="/contact"><span>Contact</span></a></li>',
			'</ul>'
		]);
		$this->assertEquals($expected, $result);
	}

	public function testRenderMinDepth()
	{
		$result = $this->Menu->setActivePath([1, 1])->setDepth([1, 1])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/work/thiess"><span>Thiess</span></a></li>',
				'<li><a href="/work/max-employment" class="active here"><span>MAX Employment</span></a></li>',
				'<li><a href="/work/spike-and-dadda"><span>Spike &amp; Dadda</span></a></li>',
			'</ul>',
		]);
		$this->assertEquals($expected, $result);
	}

	public function testRenderMinDepthWithoutActiveChildren()
	{
		$result = $this->Menu->setActivePath([1])->setDepth([1, 1])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/work/thiess"><span>Thiess</span></a></li>',
				'<li><a href="/work/max-employment"><span>MAX Employment</span></a></li>',
				'<li><a href="/work/spike-and-dadda"><span>Spike &amp; Dadda</span></a></li>',
			'</ul>',
		]);
		$this->assertEquals($expected, $result);
	}

	public function testRenderMinDepthNoActivePath()
	{
		$result = $this->Menu->setDepth([1, 1])->render();
		$this->assertFalse($result);
	}

	public function testRenderMinDepthNoActiveParents()
	{
		$result = $this->Menu->setActivePath([0])->setDepth([1, 1])->render();
		$this->assertFalse($result);
	}

	public function testRenderDeepMenu()
	{
		$this->Menu->setItems($this->getDeepMenuItems());

		$result = $this->Menu->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/about"><span>About</span></a>',
					'<ul>',
						'<li><a href="/about/team"><span>Team</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="/work"><span>Work</span></a>',
					'<ul>',
						'<li><a href="/work/thiess"><span>Thiess</span></a>',
							'<ul>',
								'<li><a href="/work/thiess/translate"><span>Translate</span></a></li>',
								'<li><a href="/work/thiess/80-years"><span>80 Years</span></a></li>',
								'<li><a href="/work/thiess/website"><span>Website</span></a></li>',
							'</ul>',
						'</li>',
						'<li><a href="/work/max-employment"><span>MAX Employment</span></a></li>',
						'<li><a href="/work/spike-and-dadda"><span>Spike &amp; Dadda</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="/contact"><span>Contact</span></a></li>',
			'</ul>',
		]);
		$this->assertEquals($expected, $result);
	}

	public function testRenderDeepMenuMinMaxDepth()
	{
		$this->Menu->setItems($this->getDeepMenuItems());

		$result = $this->Menu->setActivePath([1, 0, 2])->setDepth([1, 2])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/work/thiess" class="active"><span>Thiess</span></a>',
					'<ul>',
						'<li><a href="/work/thiess/translate"><span>Translate</span></a></li>',
						'<li><a href="/work/thiess/80-years"><span>80 Years</span></a></li>',
						'<li><a href="/work/thiess/website" class="active here"><span>Website</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="/work/max-employment"><span>MAX Employment</span></a></li>',
				'<li><a href="/work/spike-and-dadda"><span>Spike &amp; Dadda</span></a></li>',
			'</ul>',
		]);
		$this->assertEquals($expected, $result);
	}

	public function testRenderDeepMenuMinMaxDepthWithoutActiveChildren()
	{
		$this->Menu->setItems($this->getDeepMenuItems());

		$result = $this->Menu->setActivePath([1, 0])->setDepth([2, INF])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/work/thiess/translate"><span>Translate</span></a></li>',
				'<li><a href="/work/thiess/80-years"><span>80 Years</span></a></li>',
				'<li><a href="/work/thiess/website"><span>Website</span></a></li>',
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

	public function testRenderWithSeparator()
	{
		$templates = [
			'separator' => '<hr>',
			'wrapper' => '<div class="content">{{items}}</div>',
			'item' => '<h1>{{title}}</h1>',
		];
		$result = $this->Menu->templates($templates)->render();
		$expected = implode('', [
			'<div class="content">',
				'<h1>About</h1>',
				'<hr>',
				'<h1>Work</h1>',
				'<hr>',
				'<h1>Contact</h1>',
			'</div>',
		]);
		$this->assertEquals($expected, $result);
	}

	public function testRenderCustomItemAttributeAndNoTitleOrUrl()
	{
		$templates = [
			'item' => '<li>{{crazy_title}}</li>',
		];
		$items = [
			['crazy_title' => 'Random unescaped & title']
		];
		$expected = implode('', [
			'<ul>',
				'<li>Random unescaped & title</li>',
			'</ul>',
		]);
		$result = $this->Menu->setItems($items)->templates($templates)->render();
		$this->assertEquals($expected, $result);
	}

	public function testRenderUndefinedCustomItemAttribute()
	{
		$templates = [
			'item' => '<li>{{crazy_title}}</li>',
		];
		$items = [
			['title' => 'Random unescaped & title']
		];
		$expected = implode('', [
			'<ul>',
				'<li></li>',
			'</ul>',
		]);
		$result = $this->Menu->setItems($items)->templates($templates)->render();
		$this->assertEquals($expected, $result);
	}

	public function testRenderUnescapedTitle()
	{
		$templates = [
			'item' => '<li>{{unescapedTitle}}</li>',
		];
		$items = [
			['title' => 'Unescaped &']
		];
		$expected = implode('', [
			'<ul>',
				'<li>Unescaped &</li>',
			'</ul>',
		]);
		$result = $this->Menu->setItems($items)->templates($templates)->render();
		$this->assertEquals($expected, $result);
	}

	public function testRenderCustomActiveClasses()
	{
		$this->Menu->config([
			'hereClass' => 'there',
			'activeClass' => 'hold & on'
		]);
		$result = $this->Menu->setDepth([0, 0])->setActivePath([1])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/about"><span>About</span></a></li>',
				'<li><a href="/work" class="hold &amp; on there"><span>Work</span></a></li>',
				'<li><a href="/contact"><span>Contact</span></a></li>',
			'</ul>'
		]);
		$this->assertEquals($expected, $result);
	}

}
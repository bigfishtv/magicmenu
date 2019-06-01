<?php
namespace MagicMenu\Test;

use MagicMenu\Menu;

use Cake\TestSuite\TestCase;

class MenuTest extends TestCase
{
	public function setUp():void
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

		$result = $menu->getConfig('randomConfig');
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

	public function testRenderVisibleFalse()
	{
		$result = $this->Menu->setItems([
			['title' => 'About', 'url' => '/about'],
			['title' => 'Work', 'url' => '/work', 'visible' => false, 'children' => [
				['title' => 'Thiess', 'url' => '/work/thiess'],
				['title' => 'MAX Employment', 'url' => '/work/max-employment'],
				['title' => 'Spike & Dadda', 'url' => '/work/spike-and-dadda'],
			]],
			['title' => 'Contact', 'url' => '/contact']
		])->setActivePath([1, 2])->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/about"><span>About</span></a></li>',
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
		$result = $this->Menu->getConfig('depth');
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

		$result = $this->Menu->getConfig('depth');
		$this->assertEquals($depth, $result, 'Menu->config(\'depth\') should equal value set in Menu::setDepth()');

		$this->Menu->setConfig('depth', [0, 2], false);
		$this->Menu->setConfig('depth', [4, 3], false);
		$result = $this->Menu->getConfig('depth');
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
		$result = $this->Menu->setConfig('depth', [0, 0])->render();
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

	public function testSetPathStrategy()
	{
		$pathStrategy = new MockPathStrategy();

		$menu = $this->Menu->setPathStrategy($pathStrategy);
		$this->assertEquals($menu, $this->Menu, 'Menu::setPathStrategy() should return Menu instance');

		$result = $this->Menu->getPathStrategy();
		$this->assertEquals($result, $pathStrategy, 'Menu::getPathStrategy() should match the items in Menu::setPathStrategy()');
	}

	public function testSetUrlBuilder()
	{
		$urlBuilder = new MockUrlBuilder();

		$menu = $this->Menu->setUrlBuilder($urlBuilder);
		$this->assertEquals($menu, $this->Menu, 'Menu::setUrlBuilder() should return Menu instance');

		$result = $this->Menu->getUrlBuilder();
		$this->assertEquals($result, $urlBuilder, 'Menu::getUrlBuilder() should match the items in Menu::setUrlBuilder()');
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
		$result = $this->Menu->setTemplates($templates)->render();
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
		$result = $this->Menu->setItems($items)->setTemplates($templates)->render();
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
		$result = $this->Menu->setItems($items)->setTemplates($templates)->render();
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
		$result = $this->Menu->setItems($items)->setTemplates($templates)->render();
		$this->assertEquals($expected, $result);
	}

	public function testRenderCustomActiveClasses()
	{
		$this->Menu->setConfig([
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

	public function testGetItemUrlString()
	{
		$item = ['url' => '/my/url'];
		$result = $this->Menu->getItemUrl($item);
		$expected = '/my/url';
		$this->assertEquals($expected, $result);
	}

	public function testGetItemUrlNull()
	{
		$item = ['url' => null];
		$result = $this->Menu->getItemUrl($item);
		$this->assertFalse($result);
	}

	public function testGetItemUrlEmptyString()
	{
		$item = ['url' => ''];
		$result = $this->Menu->getItemUrl($item);
		$this->assertFalse($result);
	}

	public function testGetItemUrlArray()
	{
		$url = ['controller' => 'fake', 'action' => 'fake'];
		$item = ['url' => $url];
		$result = $this->Menu->getItemUrl($item);
		$this->assertEquals($result, $url);
	}

	public function testGetItemUrlUndefined()
	{
		$result = $this->Menu->getItemUrl([]);
		$this->assertFalse($result);
	}

	public function testGetItemUrlNested()
	{
		$item = [
			'children' => [
				['children' => [
					['children' => [
						['url' => '/nested']
					]]
				]]
			]
		];
		$result = $this->Menu->getItemUrl($item);
		$expected = '/nested';
		$this->assertEquals($expected, $result);
	}

	public function testGetItemUrlNestedWithEmptyString()
	{
		$item = [
			'children' => [[
				'url' => '',
				'children' => [
					['children' => [
						['url' => '/nested']
					]]
				]]
			]
		];
		$result = $this->Menu->getItemUrl($item);
		$this->assertFalse($result, 'Empty string should return false and not inherit from child');
	}

	public function testRenderUrlAttributeInheritance()
	{
		$items = [
			// should inherit from child
			['title' => 'One', 'children' => [
				['title' => 'OneChild', 'url' => '/one/child'],
			]],
			// should not inherit from child
			['title' => 'Two', 'url' => false, 'children' => [
				['title' => 'TwoChild', 'url' => '/two/child'],
			]],
			// should inherit from child
			['title' => 'Three', 'url' => null, 'children' => [
				['title' => 'ThreeChild', 'url' => '/three/child'],
			]],
			// will be empty because can't inherit from first child
			['title' => 'Four', 'children' => [
				['title' => 'FourChild'],
				['title' => 'FourChild2', 'url' => '/four/child2'],
			]],
			// should inherit from grandchild
			['title' => 'Five', 'children' => [
				['title' => 'FiveChild', 'children' => [
					['title' => 'FiveGrandchild', 'url' => '/five/grandchild']
				]],
				['title' => 'FiveChild2', 'url' => '/five/child2'],
			]],
			// should not inherit from child
			['title' => 'Six', 'url' => '', 'children' => [
				['title' => 'SixChild', 'url' => '/six/child'],
			]],
		];
		$result = $this->Menu->setItems($items)->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="/one/child"><span>One</span></a>',
					'<ul>',
						'<li><a href="/one/child"><span>OneChild</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="javascript:void(0);"><span>Two</span></a>',
					'<ul>',
						'<li><a href="/two/child"><span>TwoChild</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="/three/child"><span>Three</span></a>',
					'<ul>',
						'<li><a href="/three/child"><span>ThreeChild</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="javascript:void(0);"><span>Four</span></a>',
					'<ul>',
						'<li><a href="javascript:void(0);"><span>FourChild</span></a></li>',
						'<li><a href="/four/child2"><span>FourChild2</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="/five/grandchild"><span>Five</span></a>',
					'<ul>',
						'<li><a href="/five/grandchild"><span>FiveChild</span></a>',
							'<ul>',
								'<li><a href="/five/grandchild"><span>FiveGrandchild</span></a></li>',
							'</ul>',
						'</li>',
						'<li><a href="/five/child2"><span>FiveChild2</span></a></li>',
					'</ul>',
				'</li>',
				'<li><a href="javascript:void(0);"><span>Six</span></a>',
					'<ul>',
						'<li><a href="/six/child"><span>SixChild</span></a></li>',
					'</ul>',
				'</li>',
			'</ul>'
		]);
		$this->assertEquals($expected, $result);
	}

	public function testRenderWithEmptyUrlConfig()
	{
		$items = [
			['title' => 'Undefined url']
		];
		$result = $this->Menu->setItems($items)->setConfig('emptyUrl', '#')->render();
		$expected = implode('', [
			'<ul>',
				'<li><a href="#"><span>Undefined url</span></a></li>',
			'</ul>'
		]);
		$this->assertEquals($expected, $result);
	}

	// TODO: implement this functionality
	// public function testRenderWithArrayUrl()
	// {	
	// 	$items = [
	// 		['title' => 'Page', 'url' => ['controller' => 'something', 'action' => 'view']],
	// 	];
	// 	$result = $this->Menu->setItems($items)->render();
	// 	$expected = implode('', [
	// 		'<ul>',
	// 			'<li><a href="/something/view"><span>Page</span></a></li>',
	// 		'</ul>'
	// 	]);
	// 	$this->assertEquals($expected, $result);
	// }

}

class MockPathStrategy implements \MagicMenu\Contracts\PathStrategy
{

	public function getActivePath(array $items)
	{
		return [1, 2];
	}

}

class MockUrlBuilder implements \MagicMenu\Contracts\UrlBuilder
{

	public function getItemUrl($item)
	{
		return $item['url'];
	}

}
<?php
namespace MagicMenu\Test;

use MagicMenu\CakePathStrategy;

use Cake\TestSuite\TestCase;

use Cake\Routing\Router;

class CakePathStrategyTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();

		Router::reload();
		Router::$initialized = true;
		Router::scope('/', function ($routes) {
		    $routes->connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);
		    $routes->connect('/some/path', ['controller' => 'random', 'action' => 'stuff']);
			$routes->connect('/*', ['controller' => 'Pages', 'action' => 'display']);
		});

		$this->Strategy = new CakePathStrategy();
	}

	public function tearDown()
	{
		parent::tearDown();
		unset($this->Strategy);
	}

	protected function getDefaultMenuItems()
	{
		return [
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
				'path' => [1, 3],
				'item' => ['title' => 'Spike & Dadda', 'url' => '/some/path'],
			],
			[
				'path' => [2],
				'item' => ['title' => 'Contact', 'url' => '/contact'],
			]
		];
	}

	protected function getBasicQuerystringMenuItems()
	{
		return [
			[
				'path' => [0],
				'item' => ['url' => '/'],
			],
			[
				'path' => [1],
				'item' => ['url' => '/?page=1'],
			],
			[
				'path' => [2],
				'item' => ['url' => '/?page=2'],
			],
			[
				'path' => [2, 0],
				'item' => ['url' => '/?page=2&pageSize=50'],
			],
		];
	}

	public function testConstructor()
	{
		$url = '/totally/custom/url';
		$strategy = new CakePathStrategy($url);
		$result = $strategy->getUrl();
		$this->assertEquals($url, $result);

		$strategy = new CakePathStrategy();
		$result = $strategy->getUrl();
		$this->assertNull($result);
	}

	public function testSetUrlString()
	{
		$url = '/unique/mate';
		$result = $this->Strategy->setUrl($url);
		$this->assertSame($this->Strategy, $result, 'CakePathStrategy::setUrl() should be chainable');

		$result = $this->Strategy->getUrl();
		$this->assertEquals($url, $result);
	}

	public function testSetUrlArray()
	{
		$url = array('controller' => 'custom', 'action' => 'url');
		$result = $this->Strategy->setUrl($url)->getUrl();
		$this->assertEquals($url, $result);
	}

	public function testExactString()
	{
		$url = '/work/thiess';
		$result = $this->Strategy->setUrl($url)->getActivePath($this->getDefaultMenuItems());
		$expected = [1, 0];
		$this->assertEquals($expected, $result);
	}

	public function testNoMatch()
	{
		$url = '/';
		$result = $this->Strategy->setUrl($url)->getActivePath($this->getDefaultMenuItems());
		$expected = [];
		$this->assertEquals($expected, $result);
	}

	public function testExactArray()
	{
		$url = array('controller' => 'Pages', 'action' => 'display', 'work');
		$result = $this->Strategy->setUrl($url)->getActivePath($this->getDefaultMenuItems());
		$expected = [1];
		$this->assertEquals($expected, $result);
	}

	public function testOptionalQuerystring()
	{
		$url = '/work/thiess?ignore=me&me=too';
		$result = $this->Strategy->setUrl($url)->getActivePath($this->getDefaultMenuItems());
		$expected = [1, 0];
		$this->assertEquals($expected, $result);
	}

	public function testChildPassParams()
	{
		$url = '/contact/sub/page/here';
		$result = $this->Strategy->setUrl($url)->getActivePath($this->getDefaultMenuItems());
		$expected = [2];
		$this->assertEquals($expected, $result);
	}

	public function testParentPassParams()
	{
		$url = '/some/';
		$result = $this->Strategy->setUrl($url)->getActivePath($this->getDefaultMenuItems());
		$expected = [];
		$this->assertEquals($expected, $result);
	}

	// TODO: implement this functionality (and verify test is correct)
	// public function testQuerystringValues()
	// {
	// 	$url = '/?lang=es&page=1';
	// 	$result = $this->Strategy->setUrl($url)->getActivePath($this->getBasicQuerystringMenuItems());
	// 	$expected = [1];
	// 	$this->assertEquals($expected, $result);

	// 	$url = '/?lang=es';
	// 	$result = $this->Strategy->setUrl($url)->getActivePath($this->getBasicQuerystringMenuItems());
	// 	$expected = [0];
	// 	$this->assertEquals($expected, $result);

	// 	$url = '/?page=2&pageSize=10';
	// 	$result = $this->Strategy->setUrl($url)->getActivePath($this->getBasicQuerystringMenuItems());
	// 	$expected = [2];
	// 	$this->assertEquals($expected, $result);

	// 	$url = '/?pageSize=50';
	// 	$result = $this->Strategy->setUrl($url)->getActivePath($this->getBasicQuerystringMenuItems());
	// 	$expected = [0];
	// 	$this->assertEquals($expected, $result);
	// }

};
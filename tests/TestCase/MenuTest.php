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

	public function testRender()
	{
		$result = $this->Menu->render();
		$expected = '<ul class="nav"><li><a href="/about"><span>About</span></a></li><li><a href="/work"><span>Work</span></a></li><li><a href="/contact"><span>Contact</span></a></li></ul>';
		$this->assertEquals($result, $expected);
	}

}
<?php
namespace MagicMenu\Test;

use Cake\Routing\Router;
use Cake\TestSuite\TestCase;

use MagicMenu\CakeUrlBuilder;

class CakeUrlBuilderTest extends TestCase
{
    public function setUp():void
    {
        parent::setUp();

        Router::reload();
        Router::connect('/some/url', ['controller' => 'random', 'action' => 'stuff']);

        $this->CakeUrlBuilder = new CakeUrlBuilder();
    }

    public function testUrlBuilderString()
    {
        $item = [
            'title' => 'Something',
            'url' => '/some/url'
        ];
        $result = $this->CakeUrlBuilder->getItemUrl($item);
        $expected = '/some/url';
        $this->assertEquals($expected, $result);
    }

    public function testUrlBuilderArray()
    {
        $item = [
            'title' => 'Something',
            'url' => ['controller' => 'random', 'action' => 'stuff']
        ];
        $result = $this->CakeUrlBuilder->getItemUrl($item);
        $expected = '/some/url';
        $this->assertEquals($expected, $result);
    }
}

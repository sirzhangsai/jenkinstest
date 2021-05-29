<?php

use FastD\Routing\RouteCollection;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testNamespace()
    {
        $collection = new RouteCollection('\\Controller\\');
        $collection->get('/', 'IndexController@welcome');
        $route = $collection->getRoute('/');
        $this->assertEquals('\\Controller\\IndexController@welcome', $route->getCallback());
    }

    public function testMiddleware()
    {
        $collection = new RouteCollection();
        $collection->middleware('cors', function (RouteCollection $router) {
            $router->get('/', 'IndexController@welcome');
        });
        $this->assertEquals(['cors'], $collection->getRoute('/')->getMiddleware());

        $collection->get('/welcome', 'IndexController@welcome');
        $this->assertEmpty($collection->getRoute('/welcome')->getMiddleware());
    }

    public function testGroup()
    {
        $collection = new RouteCollection();
        $collection->group([
            'middleware' => 'test1',
        ], function ($router) {
            $router->get('/', 'Demo@Demo')->withAddMiddleware('test');
        });
    }

    public function testRouteName()
    {
        $collection = new RouteCollection();
        $collection->get([
            'name' => 'demo',
            'path' => '/',
        ], 'IndexController@welcome');

        $route = $collection->getRoute('demo');
        $this->assertEquals('/', $route->getPath());
    }
}

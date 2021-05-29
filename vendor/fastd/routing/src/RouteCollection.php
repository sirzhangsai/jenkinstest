<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Routing;


use FastD\Routing\Exceptions\RouteNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RouteCollection
 *
 * @package FastD\Routing
 */
class RouteCollection
{
    const ROUTES_CHUNK = 10;

    /**
     * @var array
     */
    protected $with = [];

    /**
     * @var array
     */
    protected $middleware = [];

    /**
     * @var Route
     */
    protected $activeRoute;

    /**
     * @var Route[]
     */
    public $staticRoutes = [];

    /**
     * @var Route[]
     */
    public $dynamicRoutes = [];

    /**
     * @var array
     */
    public $aliasMap = [];

    /**
     * @var int
     */
    protected $num = 1;

    /**
     * 路由分组计数器
     *
     * @var int
     */
    protected $index = 0;

    /**
     * @var array
     */
    protected $regexes = [];

    /**
     * @var string
     */
    protected $namespace;

    /**
     * RouteCollection constructor.
     * @param null $namespace
     */
    public function __construct($namespace = null)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param          $path
     * @param callable $callback
     * @return RouteCollection
     */
    public function group($path, callable $callback)
    {
        $middleware = $this->middleware;
        if (is_array($path)) {
            $middlewareOptions = isset($path['middleware']) ? $path['middleware'] : [];
            if (is_array($middlewareOptions)) {
                $this->middleware = array_merge($this->middleware, $middlewareOptions);
            } else {
                $this->middleware[] = $middlewareOptions;
            }
            $path = isset($path['prefix']) ? $path['prefix'] : '';
        }

        array_push($this->with, $path);

        $callback($this);

        array_pop($this->with);
        $this->middleware = $middleware;

        return $this;
    }

    /**
     * @param $middleware
     * @param callable $callback
     * @return RouteCollection
     */
    public function middleware($middleware, callable $callback)
    {
        array_push($this->middleware, $middleware);

        $callback($this);

        array_pop($this->middleware);

        return $this;
    }

    /**
     * @param $callback
     * @return string
     */
    protected function concat($callback)
    {
        return !is_string($callback) ? $callback : $this->namespace . $callback;
    }

    /**
     * @param $path
     * @param $callback
     * @param array $defaults
     * @param $hosts
     * @return Route
     */
    public function get($path, $callback, $hosts = [], array $defaults = [])
    {
        return $this->addRoute('GET', $path, $this->concat($callback), $hosts);
    }

    /**
     * @param $path
     * @param $callback
     * @param array $defaults
     * @param $hosts
     * @return Route
     */
    public function post($path, $callback, $hosts = [], array $defaults = [])
    {
        return $this->addRoute('POST', $path, $this->concat($callback), $hosts);
    }

    /**
     * @param $path
     * @param $callback
     * @param array $defaults
     * @param $hosts
     * @return Route
     */
    public function put($path, $callback, $hosts = [], array $defaults = [])
    {
        return $this->addRoute('PUT', $path, $this->concat($callback), $hosts);
    }

    /**
     * @param $path
     * @param $callback
     * @param array $defaults
     * @param $hosts
     * @return Route
     */
    public function delete($path, $callback, $hosts = [], array $defaults = [])
    {
        return $this->addRoute('DELETE', $path, $this->concat($callback), $hosts);
    }

    /**
     * @param $path
     * @param $callback
     * @param array $defaults
     * @param $hosts
     * @return Route
     */
    public function head($path, $callback, $hosts = [], array $defaults = [])
    {
        return $this->addRoute('HEAD', $path, $this->concat($callback), $hosts);
    }

    /**
     * @param $path
     * @param $callback
     * @param array $defaults
     * @param $hosts
     * @return Route
     */
    public function options($path, $callback, $hosts = [], array $defaults = [])
    {
        return $this->addRoute('OPTIONS', $path, $this->concat($callback), $hosts);
    }

    /**
     * @param $path
     * @param $callback
     * @param array $defaults
     * @param $hosts
     * @return Route
     */
    public function patch($path, $callback, $hosts = [], array $defaults = [])
    {
        return $this->addRoute('PATCH', $path, $this->concat($callback), $hosts);
    }

    /**
     * @param $name
     * @return bool|Route
     */
    public function getRoute($name)
    {
        foreach ($this->aliasMap as $method => $routes) {
            if (isset($routes[$name])) {
                return $routes[$name];
            }
        }

        return false;
    }

    /**
     * @return Route
     */
    public function getActiveRoute()
    {
        return $this->activeRoute;
    }

    /**
     * @param $method
     * @param $path
     * @param $callback
     * @param $hosts
     * @return Route
     */
    public function createRoute($method, $path, $callback, $hosts)
    {
        return new Route($method, $path, $callback, $hosts);
    }

    /**
     * @param $method
     * @param $path
     * @param $callback
     * @param $hosts
     * @return Route
     */
    public function addRoute($method, $path, $callback, $hosts)
    {
        if (is_array($path)) {
            $name = $path['name'];
            $path = implode('/', $this->with) . $path['path'];
        } else {
            $name = $path = implode('/', $this->with) . $path;
        }

        $hostsList = (array)$hosts;

        if (isset($this->aliasMap[$method][$name])) {
            foreach ($this->aliasMap[$method][$name] as $route) {
                if (in_array($route->getHosts(), $hostsList)) {
                    return $route;
                }
            }
        }

        $route = $this->createRoute($method, $path, $callback, $hostsList);
        $route->withAddMiddleware($this->middleware);

        if ($route->isStatic()) {
            $this->staticRoutes[$method][$path][] = $route;
        } else {
            $this->dynamicRoutes[$method][] = $route;
        }

        $this->aliasMap[$method][$name][] = $route;

        return $route;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return Route
     * @throws RouteNotFoundException
     */
    public function match(ServerRequestInterface $serverRequest)
    {
        $method = $serverRequest->getMethod();
        $path = $serverRequest->getUri()->getPath();
        $host = $serverRequest->getUri()->getHost();

        if (isset($this->staticRoutes[$method][$path])) {
            foreach ($this->staticRoutes[$method][$path] as $route) {
                if (in_array($host, $route->getHosts())) {
                    return $this->activeRoute = $route;
                }
            }
            foreach ($this->staticRoutes[$method][$path] as $route) {
                if (empty($route->getHosts())) {
                    return $this->activeRoute = $route;
                }
            }

        } else {
            $possiblePath = $path;
            if ('/' === substr($possiblePath, -1)) {
                $possiblePath = rtrim($possiblePath, '/');
            } else {
                $possiblePath .= '/';
            }
            if (isset($this->staticRoutes[$method][$possiblePath])) {
                foreach ($this->staticRoutes[$method][$possiblePath] as $route) {
                    if (
                        empty($route->getHosts())
                        || in_array($host, $route->getHosts())
                    ) {
                        return $this->activeRoute = $route;
                    }
                }
            }
            unset($possiblePath);
        }

        if (
            !isset($this->dynamicRoutes[$method])
            || false === $route = $this->matchDynamicRoute($serverRequest, $method, $path, $host)
        ) {
            throw new RouteNotFoundException($path);
        }

        return $this->activeRoute = $route;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param string $method
     * @param string $path
     * @param string $host
     * @return bool|Route
     */
    protected function matchDynamicRoute(ServerRequestInterface $serverRequest, $method, $path, $host)
    {
        foreach ($this->dynamicRoutes[$method] as $route) {
            if (!preg_match('~^' . $route->getRegex() . '$~', $path, $matches)) {
                continue;
            }

            $match = array_slice($matches, 1, count($route->getVariables()));
            $attributes = array_combine($route->getVariables(), $match);
            $attributes = array_filter($attributes);
            $route->mergeParameters($attributes);
            foreach ($route->getParameters() as $key => $attribute) {
                $serverRequest->withAttribute($key, $attribute);
            }

            return $route;
        }

        return false;
    }

    /**
     * @param $name
     * @param array $parameters
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public function generateUrl($name, array $parameters = [], $format = '')
    {
        if (false === ($route = $this->getRoute($name))) {
            throw new RouteNotFoundException($name);
        }

        if (!empty($format)) {
            $format = '.' . $format;
        } else {
            $format = '';
        }

        if ($route->isStaticRoute()) {
            return $route->getPath() . $format;
        }

        $parameters = array_merge($route->getParameters(), $parameters);
        $queryString = [];

        foreach ($parameters as $key => $parameter) {
            if (!in_array($key, $route->getVariables())) {
                $queryString[$key] = $parameter;
                unset($parameters[$key]);
            }
        }

        $search = array_map(function ($v) {
            return '{' . $v . '}';
        }, array_keys($parameters));

        $replace = $parameters;

        $path = str_replace($search, $replace, $route->getPath());

        if (false !== strpos($path, '[')) {
            $path = str_replace(['[', ']'], '', $path);
            $path = rtrim(preg_replace('~(({.*?}))~', '', $path), '/');
        }

        return $path . $format . ([] === $queryString ? '' : '?' . http_build_query($queryString));
    }
}
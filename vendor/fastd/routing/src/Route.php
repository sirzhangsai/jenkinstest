<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Routing;


/**
 * Class Route
 *
 * @package FastD\Routing
 */
class Route extends RouteRegex
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var mixed
     */
    protected $callback;

    /**
     * @var array
     */
    protected $hosts = [];

    /**
     * @var array
     */
    protected $middleware = [];

    /**
     * Route constructor.
     *
     * @param string $method
     * @param $path
     * @param $callback
     * @param array $hosts
     */
    public function __construct($method, $path, $callback, $hosts = [])
    {
        parent::__construct($path);

        $this->withMethod($method);

        $this->withCallback($callback);

        if(!empty($hosts)){
            $this->withHosts($hosts);
        }
    }

    /**
     * @param array|string $method
     * @return $this
     */
    public function withMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param array|string $hosts
     * @return $this
     */
    public function withHosts($hosts)
    {
        $this->hosts = $hosts;

        return $this;
    }

    /**
     * @return string
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    /**
     * @param string $callback
     * @return $this
     */
    public function withCallback($callback = null)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return \Closure
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function withParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function mergeParameters(array $parameters)
    {
        $this->parameters = array_merge($this->parameters, array_filter($parameters));

        return $this;
    }

    /**
     * @param $middleware
     * @return $this
     */
    public function withMiddleware($middleware)
    {
        $this->middleware = [$middleware];

        return $this;
    }

    /**
     * @param $middleware
     * @return $this
     */
    public function withAddMiddleware($middleware)
    {
        if (is_array($middleware)) {
            $this->middleware = array_merge($this->middleware, $middleware);
        } else {
            $this->middleware[] = $middleware;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
}

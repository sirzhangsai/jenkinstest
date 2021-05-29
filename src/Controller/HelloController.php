<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace Controller;


use FastD\Http\ServerRequest;

class HelloController
{
    /**
     * @param ServerRequest $request
     * @return \FastD\Http\Response
     */
    public function hello(ServerRequest $request)
    {
        return json([
            'msg' => 'hello '.$request->getAttribute('name')
        ]);
    }
}
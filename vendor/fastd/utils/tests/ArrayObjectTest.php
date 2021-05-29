<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */


use FastD\Utils\ArrayObject;


class ArrayObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayObject
     */
    protected $array;

    public function setUp()
    {
        $this->array = ArrayObject::make();
    }

    public function testCreate()
    {
        $this->assertTrue($this->array->isEmpty());
    }

    public function testMergeSample()
    {
        $this->array['consoles'] = [
            'A'
        ];

        $this->array->merge([
            'consoles' => [
                'B'
            ]
        ]);

        $this->assertEquals([
            'consoles' => [
                'A', 'B'
            ]
        ], $this->array->getArrayCopy());
    }

    public function testMerge ()
    {
        $this->array->merge([
            'foo' => 'bar'
        ]);

        $this->assertEquals([
            'foo' => 'bar',
        ], $this->array->getArrayCopy());

        $this->array->merge([
            'foobar' => 'zyc'
        ]);

        $this->assertEquals([
            'foo' => 'bar',
            'foobar' => 'zyc'
        ], $this->array->getArrayCopy());

        $this->array->merge([
            'database' => [
                'host' => '127.0.0.1',
                'options' => [
                    'foo' => 'bar'
                ]
            ]
        ]);

        $this->assertEquals([
            'foo' => 'bar',
            'foobar' => 'zyc',
            'database' => [
                'host' => '127.0.0.1',
                'options' => [
                    'foo' => 'bar'
                ]
            ]
        ], $this->array->getArrayCopy());

        $this->array->merge([
            'foo' => 'foobar',
            'database' => [
                'host' => '127.0.0.1',
                'options' => [
                    'foo' => 'bar',
                    'foobar' => 'zyc'
                ]
            ]
        ]);

        $this->assertEquals([
            'foo' => 'foobar',
            'foobar' => 'zyc',
            'database' => [
                'host' => '127.0.0.1',
                'options' => [
                    'foo' => 'bar',
                    'foobar' => 'zyc'
                ]
            ]
        ], $this->array->getArrayCopy());


        $this->array->merge([
            'foo' => null,
        ]);

        $this->array->merge([
            'foo' => [
                'bar' => 'var',
            ],
        ]);

        $this->assertSame(
            [
                'foo' => [
                    0 => NULL,
                    'bar' => 'var',
                ],
                'foobar' => 'zyc',
                'database' => [
                    'host' => '127.0.0.1',
                    'options' => [
                        'foo' => 'bar',
                        'foobar' => 'zyc',
                    ],
                ],
            ],
            $this->array->getArrayCopy()
        );
    }

    public function testArrayKey()
    {
        $this->array->merge([
            'foo' => [
                'bar' => 'foobar'
            ]
        ]);

        $this->assertEquals('foobar', $this->array->find('foo.bar'));
    }
}

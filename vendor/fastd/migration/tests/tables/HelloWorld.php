<?php

use \FastD\Migration\MigrationAbstract;
use \FastD\Migration\Column;
use \FastD\Migration\Table;


class HelloWorld extends MigrationAbstract
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $table = new Table('hello_world');

        $table
            ->addColumn('id', 'INT', 11, false, '0', '')
            ->addColumn('content', 'VARCHAR', 200, false, '', '')
            ->addColumn('user', 'VARCHAR', 200, false, '', '')
            ->addColumn('created', 'DATETIME', null, false, 'CURRENT_TIMESTAMP', '')
        ;

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function dataSet()
    {
        
    }
}
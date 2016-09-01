<?php

use Phinx\Migration\AbstractMigration;

class CreateTableUserAccess extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('user_access', ['id' => false, 'primary_key' => 'id']);

        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => true, 'limit' => 10])
            ->addColumn('userId', 'integer', ['signed' => false, 'limit' => 10])
            ->addColumn('groupId', 'integer', ['signed' => false, 'limit' => 10])
            ->addColumn('route', 'string')
            ->addColumn('method', 'enum', ['values' => ['*', 'AJAX', 'GET', 'POST', 'OPTIONS', 'HEAD', 'PUT', 'DELETE', 'TRACE', 'CONNECT']])
            ->addColumn('access', 'enum', ['values' => ['true', 'false']])
            ->addIndex(['userId'])
            ->addIndex(['groupId'])
            ->addIndex(['route'])
            ->create()
        ;
    }
}

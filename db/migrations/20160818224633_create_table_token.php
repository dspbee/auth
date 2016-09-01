<?php

use Phinx\Migration\AbstractMigration;

class CreateTableToken extends AbstractMigration
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
        $table = $this->table('token', ['id' => false, 'primary_key' => 'id']);

        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => true, 'limit' => 10])
            ->addColumn('token', 'binary', ['length' => 64])
            ->addColumn('use', 'integer', ['signed' => false, 'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY])
            ->addColumn('userId', 'integer', ['signed' => false, 'limit' => 10])
            ->addColumn('groupId', 'integer', ['signed' => false, 'limit' => 10])
            ->addColumn('data', 'string')
            ->addColumn('date', 'date')
            ->addIndex(['token'], ['unique' => true])
            ->create()
        ;
    }
}

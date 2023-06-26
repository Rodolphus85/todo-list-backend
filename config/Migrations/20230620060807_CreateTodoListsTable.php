<?php
use Migrations\AbstractMigration;

class CreateTodoListsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('todo_lists');
        $table
            ->addColumn('title', 'string', ['limit' => 250])
            ->addColumn('description', 'string', ['limit' => 500])
            ->addColumn('url_video', 'string', [
                'default' => null,
                'null' => true
            ])
            ->addColumn('path_file', 'string', [
                'default' => null,
                'null' => true
            ])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->create()
        ;

        $refTable = $this->table('todo_lists');
        $refTable
            ->addColumn('todo_list_id', 'integer', [
                'default' => null,
                'null' => true,
                'signed' => 'disable'
            ])
            ->addForeignKey('todo_list_id', 'todo_lists', 'id', ['delete' => 'CASCADE'])
            ->update()
        ;
    }
}

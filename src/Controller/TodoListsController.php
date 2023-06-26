<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Routing\Router;

/**
 * TodoLists Controller
 *
 */
class TodoListsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function index()
    {
        $query = $this->TodoLists->find('threaded', [
            'keyField' => $this->TodoLists->primaryKey(),
            'parentField' => 'todo_list_id'
        ]);
        $todoLists = $query->toArray();

        $this->set([
            'todoLists' => $todoLists,
            '_serialize' => ['todoLists']
        ]);
    }

    public function view($id)
    {
        $todoList = $this->TodoLists->get($id);

        if (null !== $todoList->path_file) {
            $todoList->path_file = Router::fullbaseUrl(). '/todo-list-backend/webroot/' . 'upload/' . $todoList->path_file;
        }

        $this->set([
            'todoList' => $todoList,
            '_serialize' => ['todoList']
        ]);
    }

    public function add()
    {
        $this->request->allowMethod(['post', 'put']);

        if (null === $this->request->getData('file')) {
            $todoList = $this->TodoLists->newEntity($this->request->getData());
        } else {
            $filename = $this->decodeFile($message);

            $todoList = $this->TodoLists->newEntity();
            $todoList->title = $this->request->getData('title');
            $todoList->description = $this->request->getData('description');
            $todoList->todo_list_id = $this->request->getData('todo_list_id');
            $todoList->url_video = $this->request->getData('url_video');
            $todoList->path_file = $filename;
        }

        if ($this->TodoLists->save($todoList)) {
            $message = 'Saved';
        } else {
            $message = 'not save list';
        }

        $this->set([
            'message' => $message,
            'todoList' => $todoList,
            '_serialize' => ['message', 'todoList']
        ]);
    }

    private function decodeFile(string &$message = null): string
    {
        $encodedfile = $this->request->getData('file');
        $allowed_extensions = array('jpg','jpeg','png');
        $extension = explode('/', mime_content_type($encodedfile))[1];

        $file = explode(',', $encodedfile);

        $data = base64_decode($file[1]);

        $filename = uniqid() . '.' . $extension;
        $pathFile = WWW_ROOT . 'upload/'.$filename;

        if (in_array(strtolower($extension),$allowed_extensions)) {     
            if(file_put_contents($pathFile, $data)) {
                $message = 'Saved';
            }else{
                $message = 'not save file';
            }
        } else {
            $message = 'file type not allowed';
        }

        return $filename;
    }

    public function edit($id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $todoList = $this->TodoLists->get($id);

        if (null !== $this->request->getData('file')) {
            $filename = $this->decodeFile($message);

            $todoList->title = $this->request->getData('title');
            $todoList->description = $this->request->getData('description');
            $todoList->todo_list_id = $this->request->getData('todo_list_id');
            $todoList->url_video = $this->request->getData('url_video');
            $todoList->path_file = $filename;
        }

        $todoList = $this->TodoLists->patchEntity($todoList, $this->request->getData());

        if ($this->TodoLists->save($todoList)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set([
            'message' => $message,
            '_serialize' => ['message']
        ]);
    }

    public function delete($id)
    {
        $this->request->allowMethod(['delete']);
        $todoList = $this->TodoLists->get($id);
        $message = 'Deleted';
        if (!$this->TodoLists->delete($todoList)) {
            $message = 'Error';
        }
        $this->set([
            'message' => $message,
            '_serialize' => ['message']
        ]);
    }

    public function beforeRender(Event $event) {
        $this->setCorsHeaders();
    }
    
    public function beforeFilter(Event $event) {
        if ($this->request->is('options')) {
            $this->setCorsHeaders();
            return $this->response;
        }
    }
    
    private function setCorsHeaders() {
        $this->response = $this->response->cors($this->request)
            ->allowOrigin(['*'])
            ->allowMethods(['*'])
            ->allowHeaders(['x-xsrf-token', 'Origin', 'Content-Type', 'X-Auth-Token'])
            ->allowCredentials(['true'])
            ->exposeHeaders(['Link'])
            ->maxAge(300)
            ->build();
    }
}

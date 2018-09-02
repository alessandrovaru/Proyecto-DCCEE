<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Role_User;
use App\Models\Requirement;
use Sirius\Validation\Validator;

class AdminController extends BaseController{

    //INDEX DE ADMIN
    public function getIndex(){

    echo $_SESSION['role'];

        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
            $user = User::find($userId);
            return $this->render('admin/index.twig', []);
            echo  $_SESSION['userId'];
            } else{
            header('Location: ' . BASE_URL . 'auth/login');
        }
    }


    //INDEX DE LISTAS DE ADMIN
    public function getList_user(){
        $users = User::all();
        return $this->render('admin/list-users.twig', ['users' => $users]);
    }

    //INDEX DE LISTAS requerimientos DE ADMIN
    public function getList_requirements(){
        $users = User::all();

        $requires = Requirement::all();
        // foreach ($requires as $t) {
        //   echo $t->user->name;;
        // }

        return $this->render('admin/list-requirements.twig', ['users' => $users , 'requirements' => $requires]);
    }

    //CREAR USUARIOS CON ADMIN
    public function getCreate_user(){
        return $this->render('admin/insert-user.twig', []);
    }

    public function postCreate_user(){
        $errors = [];
        $result = false;
        $validator = new Validator();
        $validator->add('email', 'email');
        $validator->add('password', 'required');

        if($validator->validate($_POST)){
            $users = new User([
                'email'=> $_POST['email'],
                'name'=> $_POST['name'],
                'last_name'=> $_POST['last_name'],
                'birth_date'=> $_POST['birth_date'],
                'id_number'=> $_POST['id_number'],

                'password'=> password_hash($_POST['password'], PASSWORD_DEFAULT)
        ]);
        $users->save();
        $result=true;
        } else {
            $errors = $validator->getMessages();

        }
        return $this->render('admin/insert-user.twig', [
            'result' => $result,
            'errors' => $errors
        ]);
    }
}

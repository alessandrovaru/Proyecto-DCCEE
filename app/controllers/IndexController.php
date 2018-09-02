<?php

namespace App\Controllers;
use App\Models\User;
use App\Models\Role;
use App\Models\Roleuser;


class IndexController extends BaseController {

    public function getIndex(){
        //Si esta logueado agarra la variable de sesion y lo pasa a variable ademas de hacer render
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
            $user = User::find($userId);
            if($user){

              
                return $this->render('index.twig', ['user' => $user]);
            }
        }
        header('Location: ' . BASE_URL . 'auth/login');

    }




}

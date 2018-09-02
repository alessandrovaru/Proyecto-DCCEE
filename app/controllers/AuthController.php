<?php

namespace App\Controllers;

use Sirius\Validation\Validator;
use App\Models\User;
use App\Models\Roleuser;
//use App\Log;

class AuthController extends BaseController {

    public function getLogin(){
        if (isset($_SESSION['userId'])) {
            echo ' Ya estas logueado';
        }else{
            return $this->render('login.twig');
        }
    }

    public function postLogin(){
        $errors = [];
        $validator = new Validator();
        $validator->add('email', 'required');
        $validator->add('email', 'email');
        $validator->add('password', 'required');

        if ($validator->validate($_POST)) {
            $user = User::where('email', $_POST['email'])->first();
            if($user){
                if (password_verify($_POST['password'], $user->password)) {
                    $_SESSION['userId'] = $user->id;
                    //PASANDO EL ROL A LA VARIABLE DE SESION
                    foreach ($user->roles as $role) {
                        $_SESSION['role'] = $role->role;
                    }

                    $tipo = $user->role_user;

                    if ($_SESSION['role'] == 'Normal') {
                        header('Location:' . BASE_URL . 'users/home');
                        return null;
                    }
//
                    if ($_SESSION['role'] == 'Admin') {
                        header('Location:' . BASE_URL . 'admin');
                        return null;
                    }

                    header('Location:' . BASE_URL . 'admin');
                }
            }
            $validator->addMessage('email','Username and/or password does not match');
        }

        $errors = $validator->getMessages();

        return $this->render('login.twig', [
            'errors' => $errors
        ]);
    }

    public function getLogout(){
        unset($_SESSION['userId']);
        header('Location: ' . BASE_URL . 'auth/login');
    }


    //REGISTRAR USUARIOS NORMALES

    public function getSignup(){
        if (isset($_SESSION['userId'])) {
            echo ' Ya estas logueado';
        }else{
            return $this->render('users/sign_up.twig', []);
        }

    }

    public function postSignup(){
        $errors = [];
        $result = false;

        $validator = new Validator();
            $validator->add('email', 'email');
            $validator->add('email', 'required');
            $validator->add('name', 'required');
            $validator->add('password', 'required');
            $validator->add('last_name', 'required');
            $validator->add('birth_date', 'required');



        if($validator->validate($_POST)){
            $users = new User([
                'email'=> $_POST['email'],
                'name'=> $_POST['name'],
                'last_name'=> $_POST['last_name'],
                'birth_date'=> $_POST['birth_date'],
                'id_number'=> $_POST['id_number'],
                'password'=> password_hash($_POST['password'], PASSWORD_DEFAULT)
                ]);

                $set_user = User::where('email', $_POST['email'])->first();
//VALIDCACION DE EMAIL
                if (isset ($set_user)) {
                  echo 'ya hay una cuenta con tu email';
                } else {
                  $users->save();
  //rol a usuario
                  $role_user = new Roleuser([
                    'user_id' => $users->id,
                    'role_id' => '2'
                  ]);
                  $role_user->save();
//INSERTAR IMAGEN A CARPETA
                  $id_insert = $users->id;

                  if ($_FILES['archivo']['error']>0) {
                      echo 'error al cargar archivo';
                  } else{

                      $permitido = array('image/gif', 'image/png', 'image/jpeg', 'application/pdf');
                      $limite_kb = 2000;

                      if (in_array($_FILES['archivo']['type'], $permitido) && $_FILES['archivo']['type']<= $limite_kb * 1024  ) {

                          $ruta = 'files/' . $id_insert . '/';
                          $archivo = $ruta . $_FILES['archivo']['name'];

                              if (!file_exists($ruta)) {
                                  mkdir($ruta);
                              }

                              if (!file_exists($archivo)) {
                                //TIPO DE ARCHIVO PARA RENOMBRAR
                                if ($_FILES['archivo']['type'] == 'image/jpeg' || $_FILES['archivo']['type'] == 'image/png') {
                                  $formato = '.jpg';

                                  $nombre_antiguo = $_FILES['archivo']['name'];
                                  $nombre_antiguo = "foto_de_perfil $id_insert" . $formato;
                                }


                                  $resultado = @move_uploaded_file($_FILES['archivo']['tmp_name'], "$ruta/$nombre_antiguo");
                                      if ($resultado) {
                                          echo 'se guardo';
                                      }else{
                                          echo 'no se guardo';
                                      }
                              }else{
                                  echo 'el archivo ya existe';
                              }
                      }else{
                          echo 'archivo no permitido';
                      }
                  }
                }

        $result=true;

        } else {
            $errors = $validator->getMessages();
        }

        return $this->render('users/sign_up.twig', [
            'result' => $result,
            'errors' => $errors
        ]);

    }
}

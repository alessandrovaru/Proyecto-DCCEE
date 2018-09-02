<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Requirement;
use Sirius\Validation\Validator;

class UserController extends BaseController{

    // INDEX USUARIOS NORMALES
    public function getHome(){
        //Si esta logueado agarra la variable de sesion y lo pasa a variable ademas de hacer render
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
            $user = User::find($userId);
            if($user){
              //devuelve foto de perfil
              $ruta = 'files/' . $user->id . '/';
              $archivo = $ruta . "foto_de_perfil $user->id";
                $path = 'files/'. $user->id;
                if (file_exists($path)) {
                    $directorio = opendir($path);

                    while ($archivo = readdir($directorio)) {
                        if (!is_dir($archivo)) {
                            echo "<img src='../files/$user->id/$archivo' width = '300px' />";
                        }
                    }
                }

                $requisitos_usuario = null;

                $requirements_user = $user->requirements;

                foreach ($requirements_user as $req) {
                    $requisitos_usuario = $req->semester_requirements;
                }

                return $this->render('users/index.twig', ['user' => $user, 'requisitos_usuario' => $requisitos_usuario ]);
            }
        }
        header('Location: ' . BASE_URL . 'auth/login');
    }

    public function getNewRequirements(){
      echo $_SESSION['userId'];
      return $this->render('users/new-requirements.twig');
    }

    public function postNewRequirements(){
      $errors = [];
      $result = false;

      $validator = new Validator();
      $validator->add('semester', 'required');

      if($validator->validate($_POST)){
          $requierement = new Requirement([
              'semester_requirements'=> $_POST['semester'],
              'asunto' => $_POST['asunto'],
              'carta_explicativa' => $_POST['carta_explicativa'],
              'user_id' => $_SESSION['userId'],
              ]);

              $requierement->save();

      $result=true;

      $id_insert = $_SESSION['userId'];

      if ($_FILES['foto_tipo_carnet']['error']>0) {
          echo 'error al cargar archivo';
      } else{

          $permitido = array('image/gif', 'image/png', 'image/jpeg', 'application/pdf', 'application/msword');
          $limite_kb = 2000;

          if (in_array($_FILES['foto_tipo_carnet']['type'], $permitido) && $_FILES['foto_tipo_carnet']['type']<= $limite_kb * 1024  ) {

              $ruta_semestre = 'files/' . $requierement->semester_requirements . '/' ;
              $ruta =   $ruta_semestre . $id_insert . '/';

                  if (!file_exists($ruta_semestre)) {
                      mkdir($ruta_semestre);
                  }

                  if (!file_exists($ruta)) {
                      mkdir($ruta);
                  }

          //FOTO TIPO CARNET
          $foto_tipo_carnet = $ruta . $_FILES['foto_tipo_carnet']['name'];
                            if (!file_exists($foto_tipo_carnet)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['foto_tipo_carnet']['type'] == 'image/jpeg' || $_FILES['foto_tipo_carnet']['type'] == 'image/png') {
                                $formato = '.jpg';

                                $nombre_antiguo = $_FILES['foto_tipo_carnet']['name'];
                                $nombre_antiguo = "foto_tipo_carnet $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['foto_tipo_carnet']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //Fotocopia de la cedula
          $cedula = $ruta . $_FILES['cedula']['name'];
                           if (!file_exists($cedula)) {
                             //TIPO DE ARCHIVO PARA RENOMBRAR
                             if ($_FILES['cedula']['type'] == 'image/jpeg' || $_FILES['cedula']['type'] == 'image/png') {
                               $formato = '.jpg';

                               $nombre_antiguo = $_FILES['cedula']['name'];
                               $nombre_antiguo = "cedula $id_insert" . $formato;
                             }


                               $resultado = @move_uploaded_file($_FILES['cedula']['tmp_name'], "$ruta/$nombre_antiguo");
                                   if ($resultado) {
                                       echo 'se guardo';
                                   }else{
                                       echo 'no se guardo';
                                   }
                           }else{
                               echo 'el archivo ya existe';
                           }
          //Constancia de trabajo
          $constancia_trabajo = $ruta . $_FILES['constanciaTrabajo']['name'];
                            if (!file_exists($constancia_trabajo)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['constanciaTrabajo']['type'] == 'application/pdf' || $_FILES['constanciaTrabajo']['type'] == 'application/msword') {
                                $formato = '.pdf';
                                $nombre_antiguo = $_FILES['constanciaTrabajo']['name'];
                                $nombre_antiguo = "Constancia_de_trabajo $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['constanciaTrabajo']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
        //Constancia de trabajo
        $constancia_estudio = $ruta . $_FILES['constanciaEstudios']['name'];
                          if (!file_exists($constancia_estudio)) {
                            //TIPO DE ARCHIVO PARA RENOMBRAR
                            if ($_FILES['constanciaEstudios']['type'] == 'application/pdf' || $_FILES['constanciaEstudios']['type'] == 'application/msword') {
                              $formato = '.pdf';
                              $nombre_antiguo = $_FILES['constanciaEstudios']['name'];
                              $nombre_antiguo = "Constancia_de_estudio $id_insert" . $formato;
                            }


                              $resultado = @move_uploaded_file($_FILES['constanciaEstudios']['tmp_name'], "$ruta/$nombre_antiguo");
                                  if ($resultado) {
                                      echo 'se guardo';
                                  }else{
                                      echo 'no se guardo';
                                  }
                          }else{
                              echo 'el archivo ya existe';
                          }

          //TRABAJADOR POR CUENTA PROPIA MAYOR A SUELDO MINIMO
          $certificacion_ingresos_may = $ruta . $_FILES['certificacionIngresosMay']['name'];
                            if (!file_exists($certificacion_ingresos_may)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['certificacionIngresosMay']['type'] == 'application/pdf' || $_FILES['certificacionIngresosMay']['type'] == 'application/msword' || $_FILES['certificacionIngresosMay']['type'] == 'image/jpeg' || $_FILES['certificacionIngresosMay']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['certificacionIngresosMay']['name'];
                                $nombre_antiguo = "Certificacion_ingresos_mayores $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['certificacionIngresosMay']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //TRABAJADOR POR CUENTA PROPIA MENOR A SUELDO MINIMO
          $certificacion_ingresos_men = $ruta . $_FILES['certificacionIngresosMen']['name'];
                            if (!file_exists($certificacion_ingresos_men)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['certificacionIngresosMen']['type'] == 'application/pdf' || $_FILES['certificacionIngresosMen']['type'] == 'application/msword' || $_FILES['certificacionIngresosMen']['type'] == 'image/jpeg' || $_FILES['certificacionIngresosMen']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['certificacionIngresosMen']['name'];
                                $nombre_antiguo = "Certificacion_ingresos_menores $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['certificacionIngresosMen']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //PENSIONADOS
          $pensionados = $ruta . $_FILES['pensionados']['name'];
                            if (!file_exists($pensionados)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['pensionados']['type'] == 'application/pdf' || $_FILES['pensionados']['type'] == 'image/jpeg' || $_FILES['pensionados']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['pensionados']['name'];
                                $nombre_antiguo = "pensionados $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['pensionados']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //CESANTES
          $cesantes = $ruta . $_FILES['cesantes']['name'];
                            if (!file_exists($cesantes)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['cesantes']['type'] == 'application/pdf' || $_FILES['cesantes']['type'] == 'image/jpeg' || $_FILES['cesantes']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['cesantes']['name'];
                                $nombre_antiguo = "cesantes $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['cesantes']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //CREDITOS, ALQUILERES, ETC
          $creditos = $ruta . $_FILES['creditos']['name'];
                            if (!file_exists($creditos)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['creditos']['type'] == 'application/pdf' || $_FILES['creditos']['type'] == 'image/jpeg' || $_FILES['creditos']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['creditos']['name'];
                                $nombre_antiguo = "credito $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['creditos']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //SERVICIOS
          $servicios = $ruta . $_FILES['servicios']['name'];
                            if (!file_exists($servicios)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['servicios']['type'] == 'application/pdf' || $_FILES['servicios']['type'] == 'image/jpeg' || $_FILES['servicios']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['servicios']['name'];
                                $nombre_antiguo = "servicios $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['servicios']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //POLIZA
          $poliza = $ruta . $_FILES['poliza']['name'];
                            if (!file_exists($poliza)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['poliza']['type'] == 'application/pdf' || $_FILES['poliza']['type'] == 'image/jpeg' || $_FILES['poliza']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['poliza']['name'];
                                $nombre_antiguo = "poliza $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['poliza']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //DIVORCIADOS
          $divorcio = $ruta . $_FILES['divorcio']['name'];
                            if (!file_exists($divorcio)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['divorcio']['type'] == 'application/pdf' || $_FILES['divorcio']['type'] == 'image/jpeg' || $_FILES['divorcio']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['divorcio']['name'];
                                $nombre_antiguo = "divorcio $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['divorcio']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //OTHERS
          $others = $ruta . $_FILES['others']['name'];
                            if (!file_exists($others)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['others']['type'] == 'application/pdf' || $_FILES['others']['type'] == 'image/jpeg' || $_FILES['others']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['others']['name'];
                                $nombre_antiguo = "others $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['others']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //DISCAPACIDADES
          $discapacidades = $ruta . $_FILES['discapacidades']['name'];
                            if (!file_exists($discapacidades)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['discapacidades']['type'] == 'application/pdf' || $_FILES['discapacidades']['type'] == 'image/jpeg' || $_FILES['discapacidades']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['discapacidades']['name'];
                                $nombre_antiguo = "discapacidades $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['discapacidades']['tmp_name'], "$ruta/$nombre_antiguo");
                                    if ($resultado) {
                                        echo 'se guardo';
                                    }else{
                                        echo 'no se guardo';
                                    }
                            }else{
                                echo 'el archivo ya existe';
                            }
          //BUSES, TAXIS
          $certificadoLinea = $ruta . $_FILES['certificadoLinea']['name'];
                            if (!file_exists($certificadoLinea)) {
                              //TIPO DE ARCHIVO PARA RENOMBRAR
                              if ($_FILES['certificadoLinea']['type'] == 'application/pdf' || $_FILES['certificadoLinea']['type'] == 'image/jpeg' || $_FILES['certificadoLinea']['type'] == 'image/png') {
                                $formato = '.jpg';
                                $nombre_antiguo = $_FILES['certificadoLinea']['name'];
                                $nombre_antiguo = "certificadoLinea $id_insert" . $formato;
                              }


                                $resultado = @move_uploaded_file($_FILES['certificadoLinea']['tmp_name'], "$ruta/$nombre_antiguo");
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

                } else {
                    $errors = $validator->getMessages();
                }

                return $this->render('users/new-requirements.twig', [
                    'result' => $result,
                    'errors' => $errors
                ]);
      }


      public function getShow($user_id){
        $userP = User::where('id', '=', $user_id)->get();
        $requirementsP = Requirement::where('user_id', '=', $user_id)->get();
        return $this->render('users/user-show.twig', ['userP' => $userP, 'requirements' => $requirementsP]);
      }
//REQUERIMIENTOS
      public function getRequirements($requirement_id){

        $requirementsP = Requirement::where('id', '=', $requirement_id)->get();

          //$this->validar($requirementsP );


          foreach ($requirementsP as $reqP) {

            $ruta = 'files/' . $reqP->semester_requirements . '/' . $reqP->user->id . '/' ;
            //RUTAS ARCHIVOS
            $cedula = $ruta . "cedula " . $reqP->user->id . ".jpg";
            $foto_tipo_carnet = $ruta . "foto_tipo_carnet " . $reqP->user->id . ".jpg";
            $constancia_trabajo = $ruta . "Constancia_de_trabajo " . $reqP->user->id . ".pdf";
            $constancia_estudio = $ruta . "Constancia_de_estudio " . $reqP->user->id . ".pdf";
            $certificacion_ingresos_may = $ruta . "Certificacion_ingresos_mayores " . $reqP->user->id . ".jpg";
            $certificacion_ingresos_men = $ruta . "Certificacion_ingresos_menores " . $reqP->user->id . ".jpg";
            $pensionados = $ruta . "pensionados " . $reqP->user->id . ".jpg";
            $cesantes = $ruta . "cesantes " . $reqP->user->id . ".jpg";
            $creditos = $ruta . "credito " . $reqP->user->id . ".jpg";
            $servicios = $ruta . "servicios " . $reqP->user->id . ".jpg";
            $poliza = $ruta . "poliza " . $reqP->user->id . ".jpg";
            $divorcio = $ruta . "divorcio " . $reqP->user->id . ".jpg";
            $others = $ruta . "others " . $reqP->user->id . ".jpg";
            $discapacidades = $ruta . "discapacidades " . $reqP->user->id . ".jpg";
            $certificadoLinea = $ruta . "certificadoLinea " . $reqP->user->id . ".jpg";



              if (file_exists($cedula)) {
                $CEDULAExist = true;

                }else {
                  $CEDULAExist = false;
              }

              if (file_exists($foto_tipo_carnet)) {
                $FTPExist = true;
                }else {
                  $FTPExist = false;
              }

              if (file_exists($constancia_trabajo)) {
                $CTExist = true;
                }else {
                  $CTExist = false;
              }

              if (file_exists($constancia_estudio)) {
                $CTEsExist = true;
                }else {
                  $CTEsExist = false;
              }



              if (file_exists($certificacion_ingresos_may)) {
                $CIMAYExist = true;
                }else {
                  $CIMAYExist = false;
              }
              if (file_exists($certificacion_ingresos_men)) {
                $CIMENExist = true;
                }else {
                  $CIMENExist = false;
              }
              if (file_exists($pensionados)) {
                $PENSIONADOSExist = true;
                }else {
                  $PENSIONADOSExist = false;
              }
              if (file_exists($cesantes)) {
                $CESANTESExist = true;
                }else {
                  $CESANTESExist = false;
              }
              if (file_exists($creditos)) {

                $CREDITOSExist = true;
                }else {
                  $CREDITOSExist = false;
              }

              if (file_exists($servicios)) {
                $SERVICIOSExist = true;
                }else {
                  $SERVICIOSExist = false;
              }

              if (file_exists($poliza)) {
                $POLIZAExist = true;
                }else {
                  $POLIZAExist = false;
              }

              if (file_exists($divorcio)) {
                $DIVORCIOExist = true;
                }else {
                  $DIVORCIOExist = false;
              }
              if (file_exists($others)) {
                $OTHERSExist = true;
                }else {
                  $OTHERSExist = false;
              }
              if (file_exists($discapacidades)) {
                $DISCAPACIDADESExist = true;
                }else {
                  $DISCAPACIDADESExist = false;
              }
              if (file_exists($certificadoLinea)) {
                $CLExist = true;
                }else {
                  $CLExist = false;
              }
            }

        return $this->render('users/requirement-show.twig', ['requirements' => $requirementsP ,
                                                            'CEDULAExist' => $CEDULAExist,
                                                            'FTPExist' => $FTPExist,
                                                            'CTExist' => $CTExist,
                                                            'CTEsExist' => $CTEsExist,
                                                            'CIMAYExist' => $CIMAYExist,
                                                            'CIMENExist' => $CIMENExist,
                                                            'PENSIONADOSExist' => $PENSIONADOSExist,
                                                            'CESANTESExist' => $CESANTESExist,
                                                            'CREDITOSExist' => $CREDITOSExist,
                                                            'SERVICIOSExist' => $SERVICIOSExist,
                                                            'DIVORCIOExist' => $DIVORCIOExist,
                                                            'OTHERExist' => $OTHERSExist,
                                                            'CLExist' => $CLExist,
                                                            'POLIZAxist' => $POLIZAExist,
                                                            'DISCAPACIDADESxist' => $DISCAPACIDADESExist]);
      }

    }

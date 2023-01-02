<?php
include_once '../class/Validaciones.php';
include_once '../class/Usuario.php';

/**
 * Metodo para consumo del servicio
 *
 * @param array $datos
 */
function ServicioData($datos)
{
  //Se formatea la entrada a JSON
  $json = file_get_contents('php://input');
  $parametros = json_decode($json, true);

  //Se instancian las clases
  $usuario = new Usuario();
  $val = new Validaciones();

  //Variables del servidor
  $method = $_SERVER['REQUEST_METHOD'];
  if ($method === 'OPTIONS') {
    return response(500, 'Error servicio', []);
  } else if ($method !== 'POST') {
    return response(500, 'metodo no permitido', []);
  } else {
    //Se obtiene el token y se llama al metodo que verifica que sea valido
    $token = $datos['token'];
    $is_valid_token = is_jwt_valid($token);

    switch ($datos['t']) {
      case 'mostrarUsuario': //Lista todos los usuarios

        //Se valida que el token sea valido
        if ($is_valid_token) {
          //Se consume la clase
          $resp = $usuario->mostrarUsuario($datos);
        } else {
          $resp = response(400, 'Token invalido', []);
        }
        break;
      case 'mostrarUsuarioId': //Lista los usuarios por el ID
        //Datos de entrada
        $id = isset($parametros['id']) ? $parametros['id'] : 0;

        //Se valida que el token sea valido
        if ($is_valid_token) {

          //Se valida el id
          $validar_id = $val->validacion('int', 'id', $id, true);

          //Se valida que haya pasado las validaciones
          if ($validar_id[ESTADO] != 1) {
            return response(400, $validar_id[MENSAJE], []);
          } else {
            //Se consume la clase
            $resp = $usuario->mostrarUsuarioId($datos);
          }
        } else {
          $resp = response(400, 'Token invalido', []);
        }
        break;
      case 'crearUsuario': //Almacena los usuarios
        //Datos de entrada
        $name = isset($parametros['name']) ? $parametros['name'] : '';
        $last_name = isset($parametros['last_name']) ? $parametros['last_name'] : '';
        $email = isset($parametros['email']) ? $parametros['email'] : '';
        $phone = isset($parametros['phone']) ? $parametros['phone'] : 0;
        $user = isset($parametros['user']) ? $parametros['user'] : '';
        $password = isset($parametros["password"]) ? $parametros["password"] : '';
        $confirm_password = isset($parametros['confirm_password']) ? $parametros['confirm_password'] : '';

        //Array de parametros
        $campos = array(
          array('tipo' => 'string', 'campo' => 'name', 'valor' => $name, 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'last_name', 'valor' => $last_name, 'obligatorio' => true),
          array('tipo' => 'email', 'campo' => 'email', 'valor' => $email, 'obligatorio' => true),
          array('tipo' => 'int', 'campo' => 'phone', 'valor' => json_encode($phone), 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'user', 'valor' => $user, 'obligatorio' => true),
          array('tipo' => 'password', 'campo' => 'password', 'valor' => $password, 'obligatorio' => true),
          array('tipo' => 'password', 'campo' => 'confirm_password', 'valor' => $confirm_password, 'obligatorio' => true)
        );

        //Se valida el array
        $validar_campos = $val->validarCampos($campos);

        //Array de parametros
        $longitudes = array(
          array('campo' => 'name', 'valor' => $name, 'longitud_minima' => 4, 'longitud_maxima' => 20),
          array('campo' => 'last_name', 'valor' => $last_name, 'longitud_minima' => 4, 'longitud_maxima' => 20),
          array('campo' => 'email', 'valor' => $email, 'longitud_minima' => 10, 'longitud_maxima' => 50),
          array('campo' => 'phone', 'valor' => $phone, 'longitud_minima' => 10, 'longitud_maxima' => 10),
          array('campo' => 'user', 'valor' => $user, 'longitud_minima' => 4, 'longitud_maxima' => 15),
          array('campo' => 'password', 'valor' => $password, 'longitud_minima' => 8, 'longitud_maxima' => 15),
          array('campo' => 'confirm_password', 'valor' => $confirm_password, 'longitud_minima' => 8, 'longitud_maxima' => 15)
        );

        //Se valida el array
        $validar_longitudes = $val->validarLongitudes($longitudes);

        //Se valida que el token sea valido
        if ($is_valid_token) {
          //Se valida que haya pasado las validaciones
          if ($validar_campos[ESTADO] != 1) {
            return response(400, $validar_campos[MENSAJE], []);
          } else if ($validar_longitudes[ESTADO] != 1) {
            return response(400, $validar_longitudes[MENSAJE], []);
          } else {
            //Se consume la clase
            $resp = $usuario->crearUsuario($datos);
          }
        } else {
          $resp = response(400, 'Token invalido', []);
        }
        break;
      case 'logueoUsuario': //Logueo del usuario
        //Datos de entrada
        $user = isset($parametros["user"]) ? $parametros["user"] : '';
        $password = isset($parametros["password"]) ? $parametros["password"] : '';

        //Se validan los campos
        $validar_user = $val->validacion('string', 'user', $user, true);
        $validar_password = $val->validacion('password', 'password', $password, true);

        //Se valida que el token sea valido
        if ($is_valid_token) {
          //Se valida que hayan pasado las validaciones
          if ($validar_user[ESTADO] != 1) {
            return response(400, $validar_user[MENSAJE], []);
          } else if ($validar_password[ESTADO] != 1) {
            return response(400, $validar_password[MENSAJE], []);
          } else {
            //Se consume la clase
            $resp = $usuario->logueoUsuario($datos);
          }
        } else {
          $resp = response(400, 'Token invalido', []);
        }
        break;
      case 'actualizarUsuario': //Actualiza los usuarios por su ID
        //Datos de entrada
        $id = isset($parametros['id']) ? $parametros['id'] : 0;
        $name = isset($parametros['name']) ? $parametros['name'] : '';
        $last_name = isset($parametros['last_name']) ? $parametros['last_name'] : '';
        $email = isset($parametros['email']) ? $parametros['email'] : '';
        $phone = json_encode(isset($parametros['phone']) ? $parametros['phone'] : 0);
        $user = isset($parametros['user']) ? $parametros['user'] : '';
        $password = isset($parametros["password"]) ? $parametros["password"] : '';
        $confirm_password = isset($parametros['confirm_password']) ? $parametros['confirm_password'] : '';

        //Array de parametros
        $campos = array(
          array('tipo' => 'int', 'campo' => 'id', 'valor' => $id, 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'name', 'valor' => $name, 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'last_name', 'valor' => $last_name, 'obligatorio' => true),
          array('tipo' => 'email', 'campo' => 'email', 'valor' => $email, 'obligatorio' => true),
          array('tipo' => 'int', 'campo' => 'phone', 'valor' => $phone, 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'user', 'valor' => $user, 'obligatorio' => true),
          array('tipo' => 'password', 'campo' => 'password', 'valor' => $password, 'obligatorio' => true),
          array('tipo' => 'password', 'campo' => 'confirm_password', 'valor' => $confirm_password, 'obligatorio' => true)
        );

        //Se valida el array
        $validar_campos = $val->validarCampos($campos);

        //Array de parametros
        $longitudes = array(
          array('campo' => 'name', 'valor' => $name, 'longitud_minima' => 4, 'longitud_maxima' => 20),
          array('campo' => 'last_name', 'valor' => $last_name, 'longitud_minima' => 4, 'longitud_maxima' => 20),
          array('campo' => 'email', 'valor' => $email, 'longitud_minima' => 10, 'longitud_maxima' => 50),
          array('campo' => 'phone', 'valor' => $phone, 'longitud_minima' => 10, 'longitud_maxima' => 10),
          array('campo' => 'user', 'valor' => $user, 'longitud_minima' => 4, 'longitud_maxima' => 15),
          array('campo' => 'password', 'valor' => $password, 'longitud_minima' => 8, 'longitud_maxima' => 15),
          array('campo' => 'password', 'valor' => $confirm_password, 'longitud_minima' => 8, 'longitud_maxima' => 15)
        );

        //Se valida el array
        $validar_longitudes = $val->validarLongitudes($longitudes);

        //Se valida que el token sea valido
        if ($is_valid_token) {
          //Se valida que haya pasado las validaciones
          if ($validar_campos[ESTADO] != 1) {
            return response(400, $validar_campos[MENSAJE], []);
          } else if ($validar_longitudes[ESTADO] != 1) {
            return response(400, $validar_longitudes[MENSAJE], []);
          } else {
            //Se consume la clase
            $resp = $usuario->actualizarUsuario($datos);
          }
        } else {
          $resp = response(400, 'Token invalido', []);
        }
        break;
      case 'eliminarUsuario': //Elimina los usuarios por su ID
        //Datos de entrada
        $id = isset($parametros['id']) ? $parametros['id'] : 0;

        //Se valida que el token sea valido
        if ($is_valid_token) {
          //Se valida el id
          $validar_id = $val->validacion('int', 'id', $id, true);

          //Se valida que haya pasado las validaciones
          if ($validar_id[ESTADO] != 1) {
            return response(400, $validar_id[MENSAJE], []);
          } else {
            //Se consume la clase
            $resp = $usuario->eliminarUsuario($datos);
          }
        } else {
          $resp = response(400, 'Token invalido', []);
        }
        break;
    }
  }
  //Se retorna la respuesta
  return $resp;
}

/**
 * Funcion encargada de retornar el response en JSON
 * @param integer $statusCode
 * @param string $message
 * @param mixed $data
 */
function response($statusCode, $message, $data)
{
  $response = [
    'statusCode' => $statusCode,
    'message' => $message,
    'data' => $data
  ];
  return json_encode($response);
}

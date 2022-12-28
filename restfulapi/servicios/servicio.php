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
    return response(500, 'metodo permitido: POST', []);
  } else {
    switch ($datos['t']) {
        //Lista todos los usuarios
      case 'mostrarUsuario':
        //Se consume la clase
        $resp = $usuario->mostrarUsuario();

        break;

        //Lista los usuarios por el ID
      case 'mostrarUsuarioId':
        //Se consume la clase
        $resp = $usuario->mostrarUsuarioId($datos);

        break;

        //Almacena los usuarios
      case 'crearUsuario':
        //Datos de entrada
        $name = isset($parametros['name']) ? $parametros['name'] : '';
        $last_name = isset($parametros['last_name']) ? $parametros['last_name'] : '';
        $email = isset($parametros['email']) ? $parametros['email'] : '';
        $phone = isset($parametros['phone']) ? $parametros['phone'] : 0;
        $user = isset($parametros['user']) ? $parametros['user'] : '';
        $password = isset($parametros["password"]) ? $parametros["password"] : '';

        //Se arma el array de parametros
        $campos = array(
          array('tipo' => 'string', 'campo' => 'name', 'valor' => $name, 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'last_name', 'valor' => $last_name, 'obligatorio' => true),
          array('tipo' => 'email', 'campo' => 'email', 'valor' => $email, 'obligatorio' => true),
          array('tipo' => 'int', 'campo' => 'phone', 'valor' => json_encode($phone), 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'user', 'valor' => $user, 'obligatorio' => true),
          array('tipo' => 'password', 'campo' => 'password', 'valor' => $password, 'obligatorio' => true)
        );

        //Se consume el metodo para validar cada parametro del array
        $validar_campos = $val->validarCampos($campos);

        //Se arma el array de parametros
        $longitudes = array(
          array('campo' => 'name', 'valor' => $name, 'longitud_minima' => 4, 'longitud_maxima' => 20),
          array('campo' => 'last_name', 'valor' => $last_name, 'longitud_minima' => 4, 'longitud_maxima' => 20),
          array('campo' => 'email', 'valor' => $email, 'longitud_minima' => 10, 'longitud_maxima' => 50),
          array('campo' => 'phone', 'valor' => $phone, 'longitud_minima' => 10, 'longitud_maxima' => 10),
          array('campo' => 'user', 'valor' => $user, 'longitud_minima' => 4, 'longitud_maxima' => 15),
          array('campo' => 'password', 'valor' => $password, 'longitud_minima' => 8, 'longitud_maxima' => 15)
        );

        //Se consume el metodo para validar cada parametro del array
        $validar_longitudes = $val->validarLongitudes($longitudes);

        //Se valida que haya pasado las validaciones
        if ($validar_campos[ESTADO] != 1) {
          return response(400, $validar_campos[MENSAJE], []);
        } else if ($validar_longitudes[ESTADO] != 1) {
          return response(400, $validar_longitudes[MENSAJE], []);
        } else {
          //Se consume la clase
          $resp = $usuario->crearUsuario($datos);
        }
        break;

        //Inicio de sesion del usuario
      case 'logueoUsuario':
        //Datos de entrada
        $user = isset($parametros["user"]) ? $parametros["user"] : '';
        $password = isset($parametros["password"]) ? $parametros["password"] : '';

        //Se arma el array de parametros
        $campos = array(
          array('tipo' => 'string', 'campo' => 'name', 'valor' => $user, 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'last_name', 'valor' => $password, 'obligatorio' => true)
        );

        //Se consume el metodo para validar cada parametro del array
        $validar_campos = $val->validarCampos($campos);

        //Se consume la clase
        $resp = $usuario->logueoUsuario($datos);

        break;

        //Actualiza los usuarios existentes
      case 'actualizarUsuario':
        //Datos de entrada
        $name = isset($parametros['name']) ? $parametros['name'] : '';
        $last_name = isset($parametros['last_name']) ? $parametros['last_name'] : '';
        $email = isset($parametros['email']) ? $parametros['email'] : '';
        $phone = json_encode(isset($parametros['phone']) ? $parametros['phone'] : 0);
        $user = isset($parametros['user']) ? $parametros['user'] : '';
        $password = isset($parametros["password"]) ? $parametros["password"] : '';

        //Se arma el array de parametros
        $campos = array(
          array('tipo' => 'string', 'campo' => 'name', 'valor' => $name, 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'last_name', 'valor' => $last_name, 'obligatorio' => true),
          array('tipo' => 'email', 'campo' => 'email', 'valor' => $email, 'obligatorio' => true),
          array('tipo' => 'int', 'campo' => 'phone', 'valor' => $phone, 'obligatorio' => true),
          array('tipo' => 'string', 'campo' => 'user', 'valor' => $user, 'obligatorio' => true),
          array('tipo' => 'password', 'campo' => 'password', 'valor' => $password, 'obligatorio' => true)
        );

        //Se consume el metodo para validar cada parametro del array
        $validar_campos = $val->validarCampos($campos);

        //Se arma el array de parametros
        $longitudes = array(
          array('campo' => 'name', 'valor' => $name, 'longitud_minima' => 4, 'longitud_maxima' => 20),
          array('campo' => 'last_name', 'valor' => $last_name, 'longitud_minima' => 4, 'longitud_maxima' => 20),
          array('campo' => 'email', 'valor' => $email, 'longitud_minima' => 10, 'longitud_maxima' => 50),
          array('campo' => 'phone', 'valor' => $phone, 'longitud_minima' => 10, 'longitud_maxima' => 10),
          array('campo' => 'user', 'valor' => $user, 'longitud_minima' => 4, 'longitud_maxima' => 15),
          array('campo' => 'password', 'valor' => $password, 'longitud_minima' => 8, 'longitud_maxima' => 15)
        );

        //Se consume el metodo para validar cada parametro del array
        $validar_longitudes = $val->validarLongitudes($longitudes);

        //Se valida que haya pasado las validaciones
        if ($validar_campos[ESTADO] != 1) {
          return response(400, $validar_campos[MENSAJE], []);
        } else if ($validar_longitudes[ESTADO] != 1) {
          return response(400, $validar_longitudes[MENSAJE], []);
        } else {
          $resp = $usuario->actualizarUsuario($datos);
        }
        break;

        //Elimina los usuarios existentes por el ID
      case 'eliminarUsuario':
        //Se consume la clase
        $resp = $usuario->eliminarUsuario($datos);

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

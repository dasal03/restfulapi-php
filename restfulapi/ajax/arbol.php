<?php
require_once '../servicios/servicio.php';
include_once '../class/Service.php';

//Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//Constantes generales
const ESTADO = 'estado';
const MENSAJE = 'mensaje';

//Se formatea la entrada a JSON
$json = file_get_contents('php://input');
$parametros = json_decode($json, true);

//Se instancia la clase
$servicio = new Service('gestion');

/**Información de las opciones - case
 * 1 - encargado de listar todos los usuarios.
 * 2 - encargado de listar usuarios por id
 * 3 - encargado de crear usuarios.
 * 4 - encargado de loguear al usuario
 * 5 - encargado de actualizar los usuarios.
 * 6 - encargado de eliminar usuarios
 */

$opc = trim($parametros['opc']);
switch ($opc) {
  case '1':
    //Se crea el array de datos
    $data = [
      't' => 'mostrarUsuario'
    ];

    //Se consume el servicio
    $resp = ServicioData($data);
    break;
  case '2':
    //Se reciben los datos
    $id = isset($parametros['id']) ? $parametros['id'] : 0;

    //Se crea el array de datos
    $data = [
      't' => 'mostrarUsuarioId',
      'id' => $id
    ];

    //Se consume el servicio
    $resp = ServicioData($data);
    break;
  case '3':
    //Se reciben los datos
    $name = isset($parametros['name']) ? $parametros['name'] : '';
    $last_name = isset($parametros['last_name']) ? $parametros['last_name'] : '';
    $email = isset($parametros['email']) ? $parametros['email'] : '';
    $phone = isset($parametros['phone']) ? $parametros['phone'] : 0;
    $user = isset($parametros['user']) ? $parametros['user'] : '';
    $password = isset($parametros['password']) ? $parametros['password'] : '';
    $confirm_password = isset($parametros['confirm_password']) ? $parametros['confirm_password'] : '';

    //Se crea el array de datos
    $data = [
      't' => 'crearUsuario',
      'name' => $name,
      'last_name' => $last_name,
      'email' => $email,
      'phone' => $phone,
      'user' => $user,
      'password' => $password,
      'confirm_password' => $confirm_password
    ];

    //Se consume el servicio
    $resp = ServicioData($data);
    break;
  case '4':
    //Se reciben los datos
    $user = isset($parametros['user']) ? $parametros['user'] : '';
    $password = isset($parametros['password']) ? $parametros['password'] : '';

    //Se crea el array de datos
    $data = [
      't' => 'logueoUsuario',
      'user' => $user,
      'password' => $password
    ];

    //Se consume el servicio
    $resp = ServicioData($data);
    break;
  case '5':
    //Se reciben los datos
    $id = isset($parametros['id']) ? $parametros['id'] : 0;
    $name = isset($parametros['name']) ? $parametros['name'] : '';
    $last_name = isset($parametros['last_name']) ? $parametros['last_name'] : '';
    $email = isset($parametros['email']) ? $parametros['email'] : '';
    $phone = isset($parametros['phone']) ? $parametros['phone'] : 0;
    $user = isset($parametros['user']) ? $parametros['user'] : '';
    $password = isset($parametros['password']) ? $parametros['password'] : '';
    $confirm_password = isset($parametros['confirm_password']) ? $parametros['confirm_password'] : '';

    //Se crea el array de datos
    $data = [
      't' => 'actualizarUsuario',
      'id' => $id,
      'name' => $name,
      'last_name' => $last_name,
      'email' => $email,
      'phone' => $phone,
      'user' => $user,
      'password' => $password,
      'confirm_password' => $confirm_password
    ];

    //Se consume el servicio
    $resp = ServicioData($data);
    break;
  case '6':
    //Se reciben los datos
    $id = isset($parametros['id']) ? $parametros['id'] : 0;

    //Se crea el array de datos
    $data = [
      't' => 'eliminarUsuario',
      'id' => $id
    ];

    //Se consume el servicio
    $resp = ServicioData($data);
    break;
    //Respuesta predeterminada
  default:
    $resp = response(500, 'La opción suministrada es incorrecta', []);
    break;
}
//Se retorna la respuesta
echo $resp;

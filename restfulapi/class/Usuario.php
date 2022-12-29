<?php
require_once '../class/BaseDatos.php';

class Usuario
{
  private $bd;

  public function __construct($name = null, $last_name = null, $email = null, $phone = null, $user = null, $password = null)
  {
    $this->bd = new BaseDatos();
    $this->name = $name;
    $this->last_name = $last_name;
    $this->email = $email;
    $this->phone = $phone;
    $this->user = $user;
    $this->password = $password;
  }

  /**
   * Obtiene los datos del usuario y crea el objeto
   */
  public function mostrarUsuario()
  {
    try {

      $query = "SELECT id, name, last_name, email, phone, user, created FROM users";

      $data = $this->bd->query($query); //Respuesta de la consulta SQL

      //Se valida que haya data en la bd
      if ($data) {
        return response(200, 'ok', $data); //Se retorna la data
      } else {
        return response(400, 'No hay usuarios en base de datos', []);
      }
    } catch (Exception $e) {
      return response(500, $e->getMessage(), []);
    }
  }

  /**
   * Obtiene los datos de cada usuario por su ID y crea el objeto
   *
   * @param $datos
   */
  public function mostrarUsuarioId($datos)
  {
    try {
      //Datos de entrada
      $id = $datos['id'];

      //Se valida que el id no sea vacio
      if (!empty($id)) {
        //Se valida que el ID sea numerico
        if (is_int($id)) {
          //Se valida que el id sea mayor y diferente que cero
          if (($id > 0) && ($id !== 0)) {
            $query = "SELECT id, name, last_name, email, phone, user, created FROM users WHERE id='$id'";

            $data = $this->bd->query($query); //Respuesta de la consulta SQL

            //Se valida que haya data en la bd
            if ($data) {
              return response(200, 'ok', $data[0]); //Se retorna la data
            } else {
              return response(400, 'No hay ningun usuario correspondiente a este ID', []);
            }
          } else {
            return response(400, 'El ID debe ser mayor a cero', []);
          }
        } else {
          return response(400, 'El ID debe ser numerico', []);
        }
      } else {
        return response(400, 'Debe digitar un ID', []);
      }
    } catch (Exception $e) {
      return response(500, $e->getMessage(), []);
    }
  }

  /**
   * Almacena el usuario en base de datos
   *
   * @param $datos
   */
  public function crearUsuario($datos)
  {
    try {
      //Datos de entrada
      $name = $datos['name'];
      $last_name = $datos['last_name'];
      $email = $datos['email'];
      $phone = $datos['phone'];
      $user = $datos['user'];
      $password = $datos['password'];
      $confirm_password = $datos['confirm_password'];

      //Se valida que la contraseña sea igual a la confirmación
      if ($password !== $confirm_password) {
        return response(400, 'Las contraseñas no coinciden', []);
      } else {

        $query = "SELECT * FROM users WHERE user='$user'";

        $resultado = $this->bd->query($query); //Respuesta de la consulta SQL

        //Se valida que el usuario no exista en la bd
        if ($resultado) {
          return response(400, 'Este usuario ya está en uso', []);
        } else {
          $hash = password_hash($password, PASSWORD_DEFAULT); //Se encripta la contraseña

          $columnas = "name, last_name, email, phone, user, password";
          $valores = "'$name', '$last_name', '$email', '$phone', '$user', '$hash'";
          $query = "INSERT INTO users ($columnas) VALUES ($valores)";

          $resultado = $this->bd->query($query); //Respuesta de la consulta SQL

          return response(200, 'ok', []);
        }
      }
    } catch (Exception $e) {
      return response(500, $e->getMessage(), []);
    }
  }

  /**
   * Valida el usuario y la contraseña suministrados
   *
   * @param $datos
   */
  public function logueoUsuario($datos)
  {
    try {
      //Datos de entrada
      $user = $datos['user'];
      $password = $datos['password'];

      $query = "SELECT * FROM users WHERE user='$user'";

      $resultado = $this->bd->query($query); //Resultado de la consulta SQL

      $hash = $resultado[0]['password']; //Contraseña encriptada de la bd

      //Se valida que el usuario exista
      if (($resultado)) {
        //Se valida que la contraseña almacenada coincida con la suministrada
        if (password_verify($password, $hash)) {
          $data = array(
            "id" => $resultado[0]['id'],
            "firstname" => $resultado[0]['name'],
            "lastname" => $resultado[0]['last_name'],
            "email" => $resultado[0]['email'],
            "phone" => $resultado[0]['phone'],
            "user" => $resultado[0]['user'],
          );

          return response(200, 'ok', $data);
        } else {
          return response(404, 'Contraseña incorrecta', []);
        }
      } else {
        return response(400, 'Usuario incorrecto', []);
      }
    } catch (Exception $e) {
      return response(500, $e->getMessage(), []);
    }
  }

  /**
   * Valida que el usuario con el ID suministrado exista en la base de datos
   *
   * @param $datos
   */
  public function validarExistencia($datos)
  {
    try {
      //Datos de entrada
      $id = $datos['id'];

      //Se valida que el ID no sea vacio
      if (!empty($id)) {
        //Se valida que el ID sea numerico
        if (is_int($id)) {
          //Se valida que el ID sea mayor y diferente que cero
          if (($id > 0) && ($id !== 0)) {

            $query = "SELECT * FROM users WHERE id=$id";

            $resultado = $this->bd->query($query); //Respuesta de la consulta

            //Se valida que el usuario exista
            if ($resultado) {
              $resp = response(200, 'Ok', []);
            } else {
              $resp = response(400, 'El usuario no existe', []);
            }
          } else {
            $resp = response(400, 'El ID debe ser mayor y diferente que cero', []);
          }
        } else {
          $resp = response(400, 'El ID debe ser numerico', []);
        }
      } else {
        $resp = response(400, 'Debe digitar un ID', []);
      }
    } catch (Exception $e) {
      $resp = response(500, $e->getMessage(), []);
    }
    return $resp;
  }

  /**
   * Actualiza cada usuario por su ID
   *
   * @param $datos
   */
  public function actualizarUsuario($datos)
  {
    try {
      //Se llama al metodo que valida que exista data relacionada con el ID
      $existencia = $this->validarExistencia($datos);

      //Datos de entrada
      $id = $datos['id'];
      $name = $datos['name'];
      $last_name = $datos['last_name'];
      $email = $datos['email'];
      $phone = $datos['phone'];
      $user = $datos['user'];
      $password = $datos['password'];
      $confirm_password = $datos['confirm_password'];

      //Se valida que la contraseña sea igual a la validación
      if ($password !== $confirm_password) {
        return response(400, 'Las contraseñas no coinciden', []);
      } else {
        $hash = password_hash($password, PASSWORD_DEFAULT); //Se encripta la contraseña

        $valores = "name='$name', last_name='$last_name', email='$email', phone='$phone', user='$user', password='$hash'";
        $query = "UPDATE users SET $valores WHERE id=$id";

        $resultado = $this->bd->query($query); //Resultado de la consulta
      }
      return $existencia;
    } catch (Exception $e) {
      return response(500, $e->getMessage(), []);
    }
  }

  /**
   * Elimina cada usuario por su ID
   *
   * @param $datos
   */
  public function eliminarUsuario($datos)
  {
    try {
      //Se llama al metodo que valida que exista data relacionada con el ID
      $existencia = $this->validarExistencia($datos);

      //Datos de entrada
      $id = $datos['id'];

      $query = "DELETE FROM users WHERE id=$id";

      $resultado = $this->bd->query($query); //Respuesta de la consulta

      return $existencia;
    } catch (Exception $e) {
      return response(500, $e->getMessage(), []);
    }
  }
}

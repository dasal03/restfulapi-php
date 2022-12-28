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
      //Se ejecuta la sentencia SQL
      $query = "SELECT id, name, last_name, email, phone, user, created FROM users";
      //Se almacena el resultado en la variable data
      $data = $this->bd->query($query);

      //Se retorna la respuesta
      if ($data) {
        return response(200, 'ok', $data);
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

      //Se valida que el id se esté enviando
      if (!empty($id)) {
        //Se valida que el ID sea numerico y mayor que cero
        if (is_numeric($id) && (is_int($id)) && ($id > 0)) {
          //Se ejecuta la sentencia SQL
          $query = "SELECT id, name, last_name, email, phone, user, created FROM users WHERE id='$id'";
          //Se almacena el resultado en la variable data
          $data = $this->bd->query($query);

          //Se retorna la respuesta
          if ($data) {
            return response(200, 'ok', $data[0]);
          } else {
            return response(400, 'No hay ningun usuario correspondiente a este ID', []);
          }
        } else {
          return response(400, 'El ID debe ser numerico y mayor que cero.', []);
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

      if ($password !== $confirm_password) {
        return response(400, 'Las contraseñas no coinciden', []);
      } else {
        //Se valida que el usuario no exista
        $query = "SELECT * FROM users WHERE user='$user'";
        $resultado = $this->bd->query($query);

        if ($resultado) {
          return response(400, 'Este usuario ya está en uso', []);
        } else {
          //Se encripta la contrasena
          $hash = password_hash($password, PASSWORD_DEFAULT);

          //Se hace la consulta SQL
          $columnas = "name, last_name, email, phone, user, password";
          $valores = "'$name', '$last_name', '$email', '$phone', '$user', '$hash'";
          $query = "INSERT INTO users ($columnas) VALUES ($valores)";
          //Se almacena la data
          $resultado = $this->bd->query($query);

          //Se retorna la respuesta
          return response(200, 'ok', []);
        }
      }
    } catch (Exception $e) {
      return response(500, $e->getMessage(), []);
    }
  }

  /**
   * Verifica que el usuario y la contraseña suministrada sean correctos y loguea al usuario
   *
   * @param $datos
   */
  public function logueoUsuario($datos)
  {
    try {
      //Datos de entrada
      $user = $datos['user'];
      $password = $datos['password'];

      //Se ejecuta la sentencia SQL
      $query = "SELECT * FROM users WHERE user='$user'";
      //Se almacena la data
      $resultado = $this->bd->query($query);
      //Se trae de la base de datos la contraseña encriptada
      $hash = $resultado[0]['password'];

      //Se arma el array con la data que se va a retornar
      $data = [
        'name' => $resultado[0]['name'],
        'last_name' => $resultado[0]['last_name'],
        'email' => $resultado[0]['email'],
        'phone' => $resultado[0]['phone'],
        'user' => $resultado[0]['user']
      ];

      //Se valida que el usuario exista
      if (($resultado)) {
        //Se valida que la contraseña almacenada coincida con la suministrada
        if (password_verify($password, $hash)) {
          //Se retorna la respuesta
          return response(200, 'ok', $data);
        } else {
          return response(400, 'Contraseña incorrecta', []);
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

      //Se valida que el ID se esté enviando
      if (!empty($id)) {
        //Se valida que el ID sea numerico y mayor a cero
        if (is_numeric($id) && (is_int($id)) && ($id > 0)) {
          //Se ejecuta la sentencia SQL
          $query = "SELECT * FROM users WHERE id=$id";
          //Se almacena la data
          $data = $this->bd->query($query);
        } else {
          $data = [];
        }
      } else {
        return response(400, 'Debe digitar un ID', []);
      }
    } catch (Exception $e) {
      return response(500, $e->getMessage(), []);
    }
    return $data;
  }

  /**
   * Actualiza cada usuario por su ID
   *
   * @param $datos
   */
  public function actualizarUsuario($datos)
  {
    try {
      //Se llama la función para validar que el usuario exista
      $existencia = $this->validarExistencia($datos);

      if ($existencia) {
        //Datos de entrada
        $id = $datos['id'];
        $name = $datos['name'];
        $last_name = $datos['last_name'];
        $email = $datos['email'];
        $phone = $datos['phone'];
        $user = $datos['user'];
        $password = $datos['password'];
        $confirm_password = $datos['confirm_password'];

        if ($password !== $confirm_password) {
          return response(400, 'Las contraseñas no coinciden', []);
        } else {
          //Se ejecuta la sentencia SQL
          $valores = "name='$name', last_name='$last_name', email='$email', phone='$phone', user='$user', password='$password'";
          $query = "UPDATE users SET $valores WHERE id=$id";
          //Se almacena la data
          $resultado = $this->bd->query($query);

          //Se retorna la respuesta
          return response(200, 'ok', []);
        }
      } else {
        return response(400, 'El usuario que intenta actualizar no existe', []);
      }
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
      //Se llama la función que valida que el usuario exista
      $existencia = $this->validarExistencia($datos);

      if ($existencia) {
        //Datos de entrada
        $id = $datos['id'];

        //Se ejecuta la sentencia SQL
        $query = "DELETE FROM users WHERE id=$id";
        //Se almacena la data
        $resultado = $this->bd->query($query);

        //Se retorna la respuesta
        return response(200, 'ok', []);
      } else {
        return response(400, 'El usuario que intenta eliminar no existe', []);
      }
    } catch (Exception $e) {
      return response(500, $e->getMessage(), []);
    }
  }
}

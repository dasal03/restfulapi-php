<?php
require_once '../servicios/servicio.php';

/**
 * Funcion encargada de validar los campos
 */
class Validaciones
{
  /**
   * Metodo encargado de validar los datos ingresados.
   * @param $tipo
   * @param $campo
   * @param $valor
   * @param bool $obligatorio
   */
  public function validacion($tipo, $campo, $valor, $obligatorio = false)
  {
    $tipo = trim($tipo);
    $campo = mb_strtoupper(trim($campo));
    $valor = trim($valor);
    $resultado = [ESTADO => 1, MENSAJE => 'OK'];
    if ($valor == "" and $obligatorio) {
      $resultado[ESTADO] = 2;
      $resultado[MENSAJE] = "El campo $campo es requerido.";
    } else {

      switch ($tipo) {
        case "int":

          if (!ctype_digit($valor)) {
            $resultado[ESTADO] = 2;
            $resultado[MENSAJE] = "El campo $campo debe ser entero.";
          }

          break;

        case "double":

          if (!is_numeric($valor)) {
            $resultado[ESTADO] = 2;
            $resultado[MENSAJE] = "El campo $campo debe ser un digito.";
          }

          break;

        case "string":

          if (!is_string($valor)) {
            $resultado[ESTADO] = 2;
            $resultado[MENSAJE] = "El campo $campo debe ser un string.";
          }

          break;

        case "email":

          if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $resultado[ESTADO] = 2;
            $resultado[MENSAJE] = "El campo $campo debe ser un email.";
          }

          break;

        case "password":

          //Se valida que la contraseña suministrada cumpla con los requerimientos
          $uppercase = preg_match('@[A-Z]@', $valor);
          $lowercase = preg_match('@[a-z]@', $valor);
          $number    = preg_match('@[0-9]@', $valor);
          $specialChars = preg_match('@[^\w]@', $valor);

          if (mb_strlen($valor) < 8) {
            $resultado[ESTADO] = 2;
            $resultado[MENSAJE] = "La contrasena debe tener minimo 8 caracteres";
          } else if (!$uppercase) {
            $resultado[ESTADO] = 2;
            $resultado[MENSAJE] = "La contraseña debe contener al menos una mayuscula";
          } else if (!$lowercase) {
            $resultado[ESTADO] = 2;
            $resultado[MENSAJE] = "La contraseña debe contener al menos una minuscula";
          } else if (!$number) {
            $resultado[ESTADO] = 2;
            $resultado[MENSAJE] = "La contraseña debe contener al menos un numero";
          } else if (!$specialChars) {
            $resultado[ESTADO] = 2;
            $resultado[MENSAJE] = "La contraseña debe contener al menos un caracter especial";
          }
          break;
      }
    }

    return $resultado;
  }

  /**
   * Metodo encargado de validar la longitud de los campos.
   * @param string $campo
   * @param mixed $valor
   * @param mixed $longitud_minima
   * @param mixed $longitud_maxima
   */
  public function Longitud($campo, $valor, $longitud_minima, $longitud_maxima)
  {
    $campo = mb_strtoupper(trim($campo));
    $valor = trim($valor);
    $resultado = [ESTADO => 1, MENSAJE => 'OK'];

    if (strlen($valor) >= $longitud_minima && strlen($valor) <= $longitud_maxima) {
      $resultado[ESTADO] = 1;
    } else {
      $resultado[ESTADO] = 2;
      $resultado[MENSAJE] = "Longitud del campo $campo debe contener entre $longitud_minima y $longitud_maxima caracteres.";
    }
    return $resultado;
  }

  /**
   * Metodo encargado de recorrer el array para las validaciones de los campos y retornar un error si lo hay
   *
   * @param array $array
   */
  public function validarCampos($array)
  {
    $result = [ESTADO => 1, MENSAJE => 'ok'];
    foreach ($array as $i) {
      $validacion = $this->validacion($i['tipo'], $i['campo'], $i['valor'], $i['obligatorio']);
      if ($validacion[ESTADO] != 1) {
        $result[MENSAJE] = $validacion[MENSAJE];
        $result[ESTADO] = $validacion[ESTADO];
      }
    }
    return $result;
  }

  /**
   * Metodo encargado de recorrer el array de las longitudes y retornar un error si lo hay
   *
   * @param array $array
   */
  public function validarLongitudes($array)
  {
    $result = [ESTADO => 1, MENSAJE => 'ok'];
    foreach ($array as $i) {
      $longitud = $this->Longitud($i['campo'], $i['valor'], $i['longitud_minima'], $i['longitud_maxima']);
      if ($longitud[ESTADO] != 1) {
        $result[MENSAJE] = $longitud[MENSAJE];
        $result[ESTADO] = $longitud[ESTADO];
      }
    }
    return $result;
  }
}

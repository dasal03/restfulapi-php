<?php

/**
 * Metodo encargado de hacer la conexion con la base de datos
 */
class BaseDatos
{
  private $host = 'localhost';
  private $user = 'root';
  private $pass = 'MyPass0319@';
  private $bd = 'restfulapi';
  private $sql = '';
  private $conn;

  private function connect()
  {
    $mysqli = mysqli_connect($this->host, $this->user, $this->pass, $this->bd);
    if (mysqli_connect_errno($mysqli)) {
      die('Error de Conexión (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }

    mysqli_set_charset($mysqli, "utf8");
    return $mysqli;
  }

  private function setCon()
  {
    $this->conn = $this->connect();
  }

  public function query($sql)
  {
    $resp = array();

    $this->setCon();

    $result = mysqli_query($this->conn, $sql) or
      error_log('Problemas en consulta.  Error: ' . $sql . " ");

    $this->diconnect();

    if (!is_bool($result)) {
      while ($row = mysqli_fetch_assoc($result)) {
        $resp[] = $row;
      }
    } else {
      $resp = ["msg" => "err"];
    }

    return $resp;
  }

  private function diconnect()
  {
    mysqli_close($this->conn);
    return true;
  }
}

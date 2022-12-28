<?php

class Service
{
  private $carpeta_actual;
  private $url_service;
  private $ip_servicio = "restfulapi";

  public function __construct($service_name)
  {
    //datos del proyecto
    $this->carpeta_actual = '/restfulapi';
    //datos básicos para usar servicio
    $this->url_service = $this->getUrl($service_name);
  }

  private function getUrl($service_name)
  {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $port_local = ":82";
    $url_actual = $protocol . $_SERVER["SERVER_NAME"] . $port_local;

    //obtener url actual
    return  $url_actual . "/servicios/$service_name/servicio.php";
  }

  public function useService($data)
  {
    // consumir servicios
    $array = http_build_query($data, '', '&');

    $ch = curl_init();

    $options = array(
      CURLOPT_URL => $this->url_service,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => ($array),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => 1
    );

    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);

    curl_close($ch);
    return $this->response($result);
  }

  private function response($data)
  {
    return json_decode(str_replace("\xef\xbb\xbf", "", $data));
  }
}

<?php
/**
 * Crear un charge a una tarjeta usando Culqi PHP.
 */

try {
  // Cargamos Requests y Culqi PHP
  include_once dirname(__FILE__).'/../libraries/Requests/library/Requests.php';
  Requests::register_autoloader();
  include_once dirname(__FILE__).'/../libraries/culqi-php/lib/culqi.php';
  include_once dirname(__FILE__).'/settings.php';



  // Configurar tu API Key y autenticación
  $culqi = new Culqi\Culqi(array('api_key' => SECRET_KEY));

  // Creando Cargo a una tarjeta
  $charge = $culqi->Charges->create(
      array(
        "amount" => 700,
        "capture" => true,
        "currency_code" => "PEN",
        "description" => "Venta de polera",
        "email" => EMAIL_CUSTOMER,
        "source_id" => $_POST['token']
      )
  );
  // Respuesta
  echo json_encode($charge);

} catch (Exception $e) { 
  
  error_log($e->getMessage()); 

  echo $e->getMessage(); 


}

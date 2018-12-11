<?php 
// Cargamos Requests y Culqi PHP
include_once dirname(__FILE__).'/libraries/Requests/library/Requests.php';
Requests::register_autoloader();
include_once dirname(__FILE__).'/libraries/culqi-php/lib/culqi.php';
include_once dirname(__FILE__).'/settings.php';

 
/**
 * Crear una orden previamente
 */
 
$culqi = new Culqi\Culqi(array('api_key' => SECRET_KEY));
$order = $culqi->Orders->create(
	array(
		"amount" => 700,
		"currency_code" => "PEN",
		"description" => 'Venta de polera',        
		"order_number" => "#id-".rand(1,9999),  
		"client_details" => array( 
				"first_name"=> "Nombre", 
				"last_name" => "Apellidos",
				"email" => EMAIL_CUSTOMER, 
				"phone_number" => "+51945145222"
		 ),
		"expiration_date" => time() + 24*60*60,   // Orden con un dia de validez
		"confirm" => false
	)
);
//echo json_encode($order); 
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Culqi Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
  </head>
  <body>
    <div class="container">
      <h1>Culqi Tarjetas + Efectivo - PHP Example</h1>
      <a id="miBoton" class="btn btn-primary" href="#" >Pagar 7.00</a>
      <br/><br/><br/>
      <div class="panel panel-default" id="response-panel">
        <div class="panel-heading">Respuesta</div>
        <div class="panel-body" id="response">
        </div>
      </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


    <!-- <script src="waitMe.min.js"></script> --> 
        
        <!--<script src="http://localhost:9000/js/v3"></script>-->

		<!--<script src="http://192.168.0.130:8082/lib/culqijs.js"></script>-->
		
		<script src="https://checkout.culqi.com/js/v3"></script>
		<!-- <script src="http://localhost:8000/js/v3"></script> -->
		<!-- <script src="/api/js.js"></script> -->
		<!-- <script src="/checkout.js"></script> -->

    <!-- <script type="text/javascript" src="js/app.js" ></script> -->
  </body>
</html>

<script>
	
	function resultdiv(message) {
        $('#response').html(message);
    } 
    function resultpe(message) {
        $('#response').html(message);
    }
        
	$(document).ready(function() {
		Culqi = new culqijs.Checkout();
		Culqi.publicKey = '<?php echo PUBLIC_KEY ?>'; 
		Culqi.options({
			lang: 'auto',
			modal: true,
			style: {
				bgcolor: '#f0f0f0',
				maincolor: '#53D3CA',
				disabledcolor: '#ffffff',
				buttontext: '#ffffff',
				maintext: '#4A4A4A',
				desctext: '#4A4A4A',
				logo: 'https://image.flaticon.com/icons/svg/25/25231.svg'		  
      		  }
		})
		Culqi.settings({
			title: 'Github',
			currency: 'PEN',
			description: 'Polera Culqi',
			amount: 700,
			order: '<?php echo trim($order->id); ?>'
		});
		
		$('#miBoton').on('click', function (e) {
				Culqi.open();
				e.preventDefault();
		});
		
	
	});
	function culqi() {
			if (Culqi.token) { 
                    
				    console.log("Token obtenido"); 
				    console.log(Culqi.token);  
					console.log("Respuesta desde iframe: " + Culqi.token);  
					//alert('llego token')
					$(document).ajaxStart(function(){
						// run_waitMe();
					});
					$.ajax({
							type: 'POST',
							url: 'ajax/charge.php',
							data: { token: Culqi.token.id, installments: Culqi.token.metadata.installments },
							datatype: 'json',
							success: function(data) {
								var result = "";
								if(data.constructor == String){
										result = JSON.parse(data);
								}
								if(data.constructor == Object){
										result = JSON.parse(JSON.stringify(data));
								}
								if(result.object === 'charge'){
								resultdiv(result.outcome.user_message);
								}
								if(result.object === 'error'){
										//resultdiv(result.user_message);
										resultdiv(result.user_message);
										console.log(result.merchant_message);
								}
							},
							error: function(error) {
								resultdiv(error)
							}
					});
			} else if (Culqi.order) { 
				 console.log("Order confirmada");
                 console.log(Culqi.order); 
				 resultpe(Culqi.order);
				 //alert('Se ha elegido el metodo de pago en efectivo:' + Culqi.order); 
			} 
			else if (Culqi.closeEvent){
				console.log(Culqi.closeEvent); 
			} 			
			else {
				$('#response-panel').show();
				$('#response').html(Culqi.error.merchant_message);
				// $('body').waitMe('hide');
			}
		};
</script>
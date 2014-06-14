<?php
	
	// filter incomplete requests
		if(!$_POST || !$_POST['transaction']){ 

			die('TRANSACTION NOT RECEIVED');
		}

	// required variables		
		require_once('utils.php');
		$connection = db_server_connect();
		$database = mysql_select_db(DB_NAME, $connection);

		$transaction = json_decode($_POST['transaction']);

		$response = array();

	// product transactions
		if($transaction->product){

			$response['product'] = array();

			foreach ($transaction->product as $product_id => $product) {

				$response['product'][$product_id] = array();
				
				// wholesale price updates
					if( property_exists($product, "WholesalePrice") ){

						$wholesale_price_update = update_price($product_id, $product->WholesalePrice, "wholesale");
						$response['product'][$product_id]["WholesalePrice"] = $product->WholesalePrice;
					}
				
				// retail price updates
					if( property_exists($product, "RetailPrice") ){

						$price_update = update_price($product_id, $product->RetailPrice, "retail");
						$response['product'][$product_id]["RetailPrice"] = $product->RetailPrice;
					}
			}
		}

		echo json_encode($response);
?>
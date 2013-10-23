<?php
	require_once('functions.php');
	require_once('./includes/dvd.inc');
	
	$connection = ConnectToDatabase();
	$user = User::GetCurrentUser($connection);
	$template = NewPage($user);
  	
  	// Include the receipt template fragment
    $template->addBlockFile("CONTENT", "RECEIPT", "receipt.tpl");
 
  	//Get movie and quantity from session.
	$checkout = $_SESSION["checkout"];
	$totalCost = 0.00;
	if(empty($checkout)){
		// Show empty cart message
		$template->setCurrentBlock("ERROR");
		$template->setVariable("MESSAGE", "Your currently have no DVD checked out.");		      	
		$template->parseCurrentBlock( );
	}else{
		//Show order
		foreach($checkout as $movie_id => $quantity){
			// Get inventory information
	    		$inventory = getInventory($movie_id, $connection);	
	
	    		// Get movie name
	    		$name = getMovieName($movie_id, $connection);
						
			// Work with the ITEM block
			$template->setCurrentBlock("ITEM");
				    
			$template->setVariable("MOVIE_NAME", $name);
			$template->setVariable("QTY", $quantity);
			$template->setVariable("PRICE", $inventory["cost"]);
			$template->setVariable("TOTAL", $inventory["cost"] * $quantity);
				    	
			$template->parseCurrentBlock( );
			
			// Get the total cost
			$totalCost += $inventory["cost"]*$quantity;
		
		}
		
		// Destroy session of order information
		session_destroy();
		
		// Display order date and total bill.
		$template->setCurrentBlock("ORDER");
		$template->setVariable("DATE", date("Y/m/d"));		    
		$template->setVariable("ORDER_TOTAL", $totalCost);	    	
		$template->parseCurrentBlock( );
	}	
	// Output the web page
  	$template->show( );

?>
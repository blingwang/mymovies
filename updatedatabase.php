<?php
	require_once('functions.php');
	require_once('./includes/dvd.inc');
	
	$connection = ConnectToDatabase();
	$user = User::GetCurrentUser($connection);
	$template = NewPage($user);
  	
  	// Include the showcart template fragment
    $template->addBlockFile("CONTENT", "ERROR", "error.tpl");
	
	// Get cart information from session
	$cart = $_SESSION["cart"];

	// Show error if no enough inventory.
	foreach($cart as $movie_id => $quantity){
		// Get inventory information
    	$inventory = getInventory($movie_id, $connection);	

    	// Get movie name
    	$name = getMovieName($movie_id, $connection);
    	
		if ($quantity > $inventory["on_hand"]){
			// Work with the ERROR block
			$template->setCurrentBlock("ERROR"); 
			$template->setVariable("MOVIE_NAME", "$name");
			$template->setVariable("ON_HAND", $inventory["on_hand"]);   	
			$template->parseCurrentBlock( );
			
			// Output the web page
  			$template->show( );
			exit;
		}
	}
	
	// Update inventory database.
	$connection->query("LOCK TABLES inventory WRITE");
	
	foreach($cart as $movie_id => $quantity){
		//Update database by deleting items involved in this transaction
		$connection->query("UPDATE inventory SET inventory.on_hand = inventory.on_hand - $quantity
				          	WHERE movieid = '$movie_id'");

		// Unset the cart session
		unset($cart[$movie_id]);
				
		// Store the checkout information
		$checkout[$movie_id] = $quantity;	
	}
	
	// Unlock the table
	$connection->query("UNLOCK TABLES");
	
	// Store the checkout information in session
	$_SESSION["checkout"] = $checkout;

	// Renew the cart session. 
	$_SESSION["cart"] = $cart;

	// Show the receipt
	header("Location: receipt.php");
?>
<?php
// Obtain the inventory information of the selected movie_id and return it as an array.
function getInventory($movie_id, $connection){
	// Compose the query to be issued.
	$result = $connection->query("SELECT cost, on_hand 
					   FROM inventory
					   WHERE movieid = '{$movie_id}'");		
	// Get the data
	showError($result);	
	$inventory = array("cost"=>null, "on_hand"=>0);
	// Generate the inventory information
	while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
		$inventory = array("cost" => $row["cost"], "on_hand" => $row["on_hand"]);
	// Return the inventory information as an array

	return $inventory;
}

// Display shopping cart
function showCart($connection, &$template, $cart){
    // Show empty cart if no items in it.
    if(empty($cart)){
        $template->setCurrentBlock("EMPTYCART");        
        $template->setVariable("TEXT", "Your shopping cart is empty.");    
        $template->parseCurrentBlock();
    }
    // Display items in the shopping cart if there's any
    else{
        $totalItems = 0;
        $totalCost = 0.00;
        
        //Show shopping cart
        foreach($cart as $movie_id => $quantity){
            // Get movie data.
            $movie = Movie::Load($connection, $movie_id);
            
	     // Get inventory information
           $inventory = getInventory($movie->GetId(), $connection);
            
            // Work with the ITEM block
            $template->setCurrentBlock("ITEM");

            $template->setVariable("MOVIE_NAME", $movie->GetName());
            $template->setVariable("ITEM_PRICE", $inventory["cost"]);
            $template->setVariable("QUANTITY_NAME", $movie->GetId());
            $template->setVariable("QUANTITY_VALUE", $quantity);
            $template->setVariable("TOTAL_VALUE", $inventory["cost"] * $quantity);
                    
            $template->parseCurrentBlock();

            // Add to total
            $totalItems += $quantity;
            $totalCost += $inventory["cost"] * $quantity;    
        }

        // Show total items and total cost
        $template->setCurrentBlock("CART");        
        $template->setVariable("TOTAL_ITEMS", $totalItems);
        $template->setVariable("TOTAL_COST", $totalCost);
        $template->parseCurrentBlock( );
    }
}

// Update shopping cart
function updateCart($cart){
	// Update the quantity of items if user requires
	foreach($cart as $movie_id => $quantity){
		if($_POST["$movie_id"] != 0){
			$quantity = $_POST["$movie_id"];
			$newCart[$movie_id] = $quantity;
		}
	}

	// Update session
	$_SESSION["cart"] = $newCart;
	
	// Return new cart
	return $newCart;
}

// Obtain the movie name of the selected movie_id
function getMovieName($movie_id, $connection){
	// Compose the query to be issued.
	$result = $connection->query("SELECT name
					   			  FROM movie
					   			  WHERE id = '{$movie_id}'");	
					   			  
	// Get the data
	showError($result);	
	
	// Generate the inventory information
	while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
		$inventory = array("name" => $row["name"]);
	// Return the inventory information as an array
	return $inventory['name'];
}
?>
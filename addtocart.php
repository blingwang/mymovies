<?php

    require_once("functions.php");

    // Add an item to shopping cart
    function addToCart($movie_id, $quantity){    
        // Start session
        $cart = (isset($_SESSION["cart"]) ? $_SESSION["cart"] : array());

        // Increase the quantity of chosen movie if it already exits
        if(isset($cart[$movie_id]))
            $cart[$movie_id] += $quantity;
            
        // Create a new item in the shopping cart if not.
        elseif($quantity != 0)
            $cart[$movie_id] = $quantity;
    
        // Add data to session.
        $_SESSION["cart"] = $cart;
    }



    // Create the connection to MySQL
    $connection = ConnectToDatabase();
    $user = User::GetCurrentUser($connection);
    $template = NewPage($user);

    $template->addBlockFile("CONTENT", "ADDTOCART", "addtocart.tpl");

    // Get the movie_id and quantity passed
    $movie_id = $_POST["movieId"];
    $quantity = (int) $_POST["quantity"];

    // Add items to shopping cart
    addToCart($movie_id, $quantity);

    // Get movie data.
    $movie = Movie::Load($connection, $movie_id);

    // Show successful message if items added to shopping cart        
    $template->setCurrentBlock("ADDTOCART");        
    $template->setVariable("MOVIE_NAME", $movie->GetName());
    $template->setVariable("QUANTITY", $quantity);
    $template->parseCurrentBlock( );
    
    // Output the web page
    $template->show( );
?>
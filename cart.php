<?php

require_once('functions.php');
require_once('./includes/dvd.inc');

$connection = ConnectToDatabase();
$user = User::GetCurrentUser($connection);
$template = NewPage($user);

// Include the showcart template fragment
$template->addBlockFile("CONTENT", "SHOWCART", "showcart.tpl");

// Get cart from session.
$cart = (isset($_SESSION["cart"]) ? $_SESSION["cart"] : null);

// Update shopping cart if customer requires
if (isset($_POST["update"]) && !empty($cart))
   $cart = updateCart($cart);

// Show shopping cart
showCart($connection, $template, $cart);

// Output the web page
$template->show( );

?>
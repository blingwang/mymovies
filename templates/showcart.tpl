<div class="content" id="cart">
<!-- BEGIN CART -->
<h3>Your Shopping Cart</h3>
<form action="cart.php" method="POST">
<table border="0" cellpadding="0" cellspacing="5">
   <tr>
      <th>Movie</th>
      <th>Unit Price</th>
	<th>Quantity</th>
	<th>Total</th>
   </tr>

<!-- BEGIN ITEM -->
   <tr>
       <td>{MOVIE_NAME}</td>
       <td>${ITEM_PRICE}</td>
	<td><input type="text" size=3 name="{QUANTITY_NAME}" value="{QUANTITY_VALUE}"></td>
       <td>${TOTAL_VALUE}</td>
   </tr>
<!-- END ITEM -->

   <tr></tr>
   <tr>
      <td></td>
     	<td><b>Subtotal</td><b>
	<td><b>{TOTAL_ITEMS} items</b></td>
      <td><b>${TOTAL_COST}</b></td>
   </tr>
</table>
Edit quantity of DVD and update cart
<input type="submit" name="update" value="Update Cart">
</form>
<form action="updatedatabase.php" method="POST">
Go on to place your order and check out
<input type="submit" name="checkout" value="Check out">
</form>
<!-- END CART -->

<!-- BEGIN EMPTYCART -->
<h3>{TEXT}</h3>
<form action="new.php" method="POST">
Want to buy some DVDs?
<input type="submit" name="backtostore" value="Go to store">
</form>
<!-- END EMPTYCART -->
</div>
</body>

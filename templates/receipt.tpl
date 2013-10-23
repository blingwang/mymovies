<div class="content" id="receipt">
<!-- BEGIN SUCCESS -->
<h3>Congratulations! Your order has been placed.</h3>
<p>Thank you for shopping at our DVD store. Here's your receipt.</p>
<!-- BEGIN ORDER -->
<table border=0 width=70% cellpadding=0 cellspacing=5>
<tr>
   <td><b>Movie</b></td>
   <td><b>Quantity</b></td>
   <td align="right"><b>Unit Price</b></td>
   <td align="right"><b>Total</b></td>
</tr>
<!-- BEGIN ITEM -->
<tr>
   <td>{MOVIE_NAME}</td>
   <td>{QTY}</td>
   <td align="right">{PRICE}</td>
   <td align="right">${TOTAL}</td>
</tr>
<!-- END ITEM -->
<tr></tr>
<tr>
   <td><b>Date<b>: {DATE}</td>
   <td></td>
   <td><b>Total of this order</b></td>
   <td align="right"><b>${ORDER_TOTAL}</b></td>
</tr>
</table>
<!-- END ORDER -->
<form action="new.php" method="POST">
Want to buy more?
<input type="submit" name="backtostore" value="Go to store">
<!-- END SUCCESS -->

<!-- BEGIN ERROR -->
	{MESSAGE}
<!-- END ERROR -->
</form>
</div>
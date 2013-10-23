<style>

h3 { margin-top: 0px;}

</style>

<div class="content">
    <table border=0 width="100%">
    <tr valign='top'>
        <td id="poster" width="160" align='center'>
            {MOVIE_IMAGE}
        </td>
        <td>
            <table width="100%">
              <tr valign='top'>
                  <th colspan=2><h3>{MOVIE_NAME}</h3><th>
              </tr>
              <tr valign='top'>
                  <td><b>Category</b>:</td>
                  <td>{CATEGORY}</td>
              </tr>
              <tr valign='top'>
                  <td><b>Year</b>:</td>
                  <td>{YEAR}</td>
              </tr>
              {OPTIONAL_RATING_PLACEHOLDER}
              <tr valign='top'>
                  <td colspan=2>&nbsp;</td>
              </tr>
              <tr valign='top'>
                  <td><b>Price</b>:</td>
                  <td>{PRICE}</td>
              </tr>
              <tr valign='top'>
                  <td colspan='2' align='center'>
                      <br />
                      <form action='addtocart.php' method='POST'>
                          <input type='hidden' name='movieId' size=1 value='{MOVIE_ID}'>
                          Qty
                          <input type='text' name='quantity' size=1 value='1'>
                          <input type='submit' name='addtocart' value='Add to cart'>
                      </form>
                  </td>
              </tr>
            </table>
         </td>
    </tr>
    </table>

    <h3>Movie Decription</h3>
    <p>{DESCRIPTION}</p>

    <br />
    
    <table cellpadding=0 cellspacing=0 width='100%'>
    <tr valign='top'>
    <td>
        <div align='center'>
            <b>Rating</b>:<br />
            {RATING}
        </div>
    </td>
    <td width='40%'>
        <div align='center'>
            <b>Favourite Movie</b><br />
            <table border='0'>
                <tr valign='top'><td align='center'>
                {FAVOURITE_BLOCK}
                </td></tr>
            </table>
        </div>
    </td>
    </tr>
    </table>



    <br />


    <br />


    <h3>Reviews</h3>

    {REVIEW_MESSAGE}

    <!-- BEGIN REVIEW -->
    
        <table width='100%' style='border:1px #000 solid;'>
          <tr>
            <td>Review written by <a name='review{REVIEW_ID}' href='profile.php?username={REVIEW_USERNAME}'>{REVIEW_USERNAME}</a>, on {REVIEW_TIME}.</td>
            <td align='right'>{REVIEW_OPTIONS}</td>
          </tr>
        </table>
        <p class='reviewText' style='padding:5px; border:1px #000 solid; margin-top:0px; margin-bottom:10px; padding-top:5px; border-top: 0px; background-color: #fff;'>{REVIEW_TEXT}</p>

    <!-- END REVIEW -->

</div>
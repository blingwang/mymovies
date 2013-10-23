<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>movie store</title>
    <link rel="stylesheet" type="text/css" href="./css/home.css"/>
    <link rel="stylesheet" type="text/css" href="./css/dvd.css"/>
    <style>
        .error        { margin: 10px; padding: 10px; background-color: #fee; color: red; border: 1px #f00 solid; }
        .notification { margin: 10px; padding: 10px; background-color: #eef; color: blue; border: 1px #00f solid; }
    </style>
</head>
<body>

    <div id="container">

        <table width='100%'>
        <tr>
        <td><img src="images/logo2.png" height='70' width='350'></td>
        </tr>
        </table>

        {NAVIGATION}


        <table width='100%'>
          <tr valign='top'>
            <td style='background-color:white; border:1px #ccc solid; padding:10px;'>
                {NOTIFICATION}

                {CONTENT}
            </td>
            <td style='width:180px; background-color:white; border:1px #ccc solid;'>
            
              <br />

              <h4>Search</h4>
              <form action='search.php' align='center'>
                <p align='center' style='margin:10px 25px; text-align:center;'>
                <small><b>Find for a movie</b></small>:<br />
                <input type="text" name='text' style='width:100%;' value= "" method="post"/><br />
                <input type='submit' value='Go!' />
                </p>
              </form>

              <!-- BEGIN SIDEBAR -->
                  {SIDEBAR_HOLDER}
              <!-- END SIDEBAR -->
              
              {ADMIN_LINKS}

              <!--
              <div id="topsellers">
                   <h4>Top Sellers</h4>
                     <ul>
                      <li><a href="#">1. 10,000 B.C.</a></li>
                  <li><a href="#">2. College Road Trip</a></li>
                  <li><a href="#">3. Vantage Point</a></li>
                  <li><a href="#">4. The Bank Job</a></li>
                  <li><a href="#">5. Semi-Pro</a></li>
                     </ul>
              </div>
              
              -->

            </td>
          </tr>
        </table>

       <div id="footer">
            <p>Copyright @ 2008 All rights reserved. </p>
       </div>

    </div>

</body>
</html>

</body>
</html>
<?php

session_start();

// Require site classes
require_once("includes/Movie.php");
require_once("includes/Categories.php");
require_once("includes/User.php");
require_once("includes/Review.php");

// Require files which define extra functions
require_once("includes/db-config.php");

require_once("includes/sidebar-functions.php");

define("SITE_NAME", "Movie Database");

// If the include path must be modified it should be done in this way:
// set_include_path('/home/bmi/pear/pear/php' . PATH_SEPARATOR . get_include_path());

// Require PEAR modules
require_once "DB.php";
require_once "HTML/Template/ITX.php";

function NewPage($user)
{
    // Create a new template
    $template = new HTML_Template_ITX('./templates');
    $template->loadTemplatefile('site.tpl', true, true);

    $nav = "<table width='100%' border='0'><tr>";
    
    $links = array(
       "New DVDs"=>"new.php",
       "New Reviews"=>"reviews.php",
       "Top Rated"=>"top_rated.php",
       );
    
    
    $links["Search"] = "search.php";
    
    if($user == null)
        $links["Log In"] = "login.php";
    else
    {
        $links["My Cart"] = "cart.php";
        $links["My Profile"] = "profile.php";
        $links["Log Out"] = "logout.php";
    }
    
    foreach($links as $title=>$link)
    {
        $nav .= "<td onclick='document.location=\"$link\"' style='cursor:pointer; padding-top:5px; background-color: #ccc; padding-bottom:5px; ' align='center'><a href='$link'>$title</a></td>";
    }
    $nav .= "</tr></table>";
    
    $template->setVariable('NAVIGATION', $nav);
    
    if($user != null && $user->IsAdministrator())
        $template->setVariable("ADMIN_LINKS", "<h4>Admin</h4><p style='padding:20px; padding-top:10px;'><a href='admin_category.php'>Categories</a><br /><a href='admin_users.php'>Users</a><br /><a href='admin_movies.php'>Movies</a></p>");

    return $template;
}

function requestVar($name)
{
    if(isset($_REQUEST[$name]))
        return $_REQUEST[$name];
    else
        return null;
}

function GetCategories($connection)
{
    $categories = array();
    $result = ExecuteQueryOrDie($connection, "SELECT id,name FROM category ORDER BY name");
    while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
        $categories[$row['id']] = $row['name'];
    return $categories;
}

/*
function StartGenericTemplate()
{
    global $template, $conn;
    
    if($conn == null)
        $conn = ConnectToDatabase();
        
    $user = User::GetCurrentUser($conn);

    $template = new HTML_Template_ITX("templates");
    $template->loadTemplatefile("generic.tpl", true, true);

    $template->setVariable("SITE_NAME", SITE_NAME);

    if($user == null)
    {
        $template->setVariable("LOGIN_BOX", "
            <div id='myaccount'>
            <h4>My Account</h4>
            <form action='login.php' method='POST'>
                 <p>Username: <input type='text' class='text' name='username' /></p>
                  <p>Password: <input type='password' class='text' name='password' /> </p>
                  <p><input type='submit' class='btn' value='Sign In' > </p>
            </form>

            <form action='register.php' method='POST'>
                  <p>No account? <input type='button' class='btn' value='Sign Up' > </p>
            </form>
            </div>
            ");
    }
    else
    {
        $template->setVariable("LOGIN_BOX", "
            <div style='text-align:center'>
            <h4>My Account</h4>
            <p style='text-align:center; margin:10px; '>Welcome back, ". $user->GetName() . ".<br /><br /><a href='profile.php?username=" . $user->GetUsername() . "'>My Profile</a> <br /> <a href='logout.php'>Log Out</a></p>
            </div>
            ");
    }

    $topRated = Movie::GetTopRated($conn, 5);
    $topRatedString = "";

    foreach($topRated as $movie)
        $topRatedString .= "<li>" . $movie->GetLinkedName() . "</li>\r\n";

    $template->setVariable("TOP_RATED_BOX", "
        <div id='toprated'>
          <h4>Top Rated</h4>
          <div style='margin-top:5px;'>
          <ol style='margin:0px; text-align:left;'>
          {$topRatedString}
          </ol>
          </div>
        </div>
         ");

    ob_start();
}

function FinishGenericTemplate()
{
    global $template;
    $template->setVariable("BODY", ob_get_contents());
    ob_end_clean();
    $template->show();
}
*/

?>
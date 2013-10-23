<?php

require_once("functions.php");

$username = requestVar("username");
$name = requestVar("name");
$p1 = requestVar("password1");
$p2 = requestVar("password2");

$errors = array();

if(requestVar("action") == "Register")
{
    $username = trim($username);
    if(empty($username))
        $errors[] = "You must enter a username.";

    $name = trim($name);
    if(empty($name))
        $errors[] = "You must enter your name.";

    $p1 = trim($p1);
    if(empty($p1))
        $errors[] = "You must enter a password.";
    else if($p1 != $p2)
        $errors[] = "You must enter the same password twice, as your password and confirmation password do not match.";

    $conn = ConnectToDatabase();

    $user = User::Load($conn, $username);
    
    if($user != null)
        $errors[] = "Sorry, that username is already in use and you must choose another one.";
        
    if(count($errors) == 0)
    {
        $user = User::Create($conn, $username, $name, $p1);
        session_destroy();
        User::LogIn($conn, $username, $p1);
        header("Location: login.php");
        die();
    }
}

$template = NewPage(null);

ob_start();

echo "<h1>Register</h1>";

if(count($errors) > 0)
{
    echo("<ul>");
    foreach($errors as $e)
        echo("<li>$e</li>");
    echo("</ul>");
}

echo "
    <form action='register.php' method='POST'>
    <table>
    <tr><td>Desired username:</td><td><input type='text' name='username' value='{$username}' /></td></tr>
    <tr><td>Full name:</td><td><input type='text' name='name' value='{$name}' /></td></tr>
    <tr><td>Password:</td><td><input type='password' name='password1' /></td></tr>
    <tr><td>Confirm password:</td><td><input type='password' name='password2' /></td></tr>
    </table>
    <input type='submit' name='action' value='Register' />
    </form>
";

$template->setVariable("CONTENT", ob_get_contents());
$template->parseCurrentBlock();
ob_end_clean();

$template->show();

?>
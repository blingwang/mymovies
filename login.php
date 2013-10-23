<?php

    require_once("functions.php");

    // Connect to the database, get the current user info, and start the template
    $connection = ConnectToDatabase();
    $user = User::GetCurrentUser($connection);
    $template = NewPage($user);
    
    // Try logging in. If this fails, add the error message to the page.
    // If this succeeds, redirect them to the main page.
    if(requestVar("username") != null && requestVar("password") != null)
    {
        $user = User::LogIn($connection, requestVar("username"), requestVar("password"));
        if($user == null)
            $template->setVariable("NOTIFICATION",  "<p class='error'>Invalid username/password.</p>");
        else
        {
            session_start();
            $_SESSION["username"] = $user->GetUsername();
            header("Location: index.php");
            die();
        }
    }
    
    // Add the login form content to the page.
    $template->addBlockFile('CONTENT', 'LOGINFORM', 'content-login-form.tpl');
    $template->setCurrentBlock('LOGINFORM');
    
    // If a username is supplied, write it in to the form. Otherwise,
    // touch the block so it will display.
    $u = requestVar("username");
    if(empty($u) == false)
    {
        $template->setVariable("USERNAME", requestVar("username"));
        $template->parseCurrentBlock();
    }
    else
    {
        $template->touchBlock("LOGINFORM");
    }
    $template->show();

?>
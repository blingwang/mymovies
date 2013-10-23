<?php

    include_once("functions.php");
    
    // Destroy the current session to log the user out
    session_destroy();
    
    $template = NewPage(null);
    $template->setVariable("NOTIFICATION", "<p class='notification'>You have been logged out.</p>");
    $template->addBlockFile('CONTENT', 'LOGOUT', 'content-logout.tpl');
    $template->touchBlock('LOGOUT');
    $template->show();

?>
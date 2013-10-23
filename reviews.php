<?php

require_once("functions.php");

$connection = ConnectToDatabase();
$user = User::GetCurrentUser($connection);
$template = NewPage($user);

ob_start();
echo("<h1>Newest Reviews</h1>");

$reviews = Review::LoadMostRecent($connection, 20);

if(count($reviews) == 0)
    echo("<p>Sorry, there are no recent reviews in the database.</p>");
else
{
    echo("<ul>");
    foreach($reviews as $review)
    {
        echo("<li><a href='details.php?movie=" . $review->GetMovieId() . "#review" . $review->GetId() . "'>" . $review->GetMovieName() . "</a> at " . date("h:i", strtotime($review->GetWriteTime())) . " on " .
        date("m/d/y", strtotime($review->GetWriteTime())) ." by <a href='profile.php?username=" . $review->GetUsername() . "'>" . $review->GetUsername() . "</a></li>");
    }
    echo("</ul>");
}


$template->setVariable("CONTENT", ob_get_contents());
ob_end_clean();

$template->show();

?>
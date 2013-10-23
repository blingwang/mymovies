<?php

require_once("functions.php");


$conn = ConnectToDatabase();
$cats = new Categories($conn);
$cats = $cats->GetList();

$movie = Movie::Load($conn, (int)requestVar("movie"));
$user = User::GetCurrentUser($conn);
$template = NewPage($user);

$viewing = User::Load($conn, requestVar("username"));

if($viewing == null && $user != null)
    $viewing = $user;

ob_start();
if($viewing == null)
    echo ("<p>Sorry, the user you are trying to view could not be found.</p>");
else
{
    $reviews = Review::LoadAllForUsername($conn, $viewing->GetUsername());
    if($user != null && $viewing->GetUsername() == $user->GetUsername())
    {
        $thisUserHasOrYouHave = "You have ";
        echo("<h1>My Profile</h1>");
    }
    else
    {
        $thisUserHasOrYouHave = "This user has ";
        echo("<h1>" . $viewing->GetUsername() . "'s Profile</h1>");
    }

    if(count($reviews) == 0)
        echo ("<p>" . $thisUserHasOrYouHave . " not written any reviews.</p>");
    else
    {
        echo ("<p>" . $thisUserHasOrYouHave . " written " . count($reviews) . " review" . (count($reviews) > 1 ? 's' : '') . ".</p>");
        echo("<ul>");
        $count = 0;
        foreach($reviews as $r)
        {
            echo("<li><a href='details.php?movie=" . $r->GetMovieId() . "#review" . $r->GetId() . "'>" . $r->GetMovieName() . "</a> written on " . $r->GetWriteTime() . "</li>");

            $count++;
            
            if($count == 5 && count($reviews) > 5 && (requestVar("showAllReviews") == null))
            {
                echo ("<li><i>There are " . (count($reviews) - 5) . " older reviews that are curently hidden. <a href='?username=" . requestVar("username") . "&showAllReviews=true'>Click here to see the complete list.</a></i></li>");
                break;
            }
        }
        echo("</ul>");
    }

    $favouriteMovies = $viewing->GetFavouriteMovies($conn);
    if(count($favouriteMovies) == 0)
        echo ("<p>" . $thisUserHasOrYouHave . " not chosen any favourite movies.</p>");
    else
    {
        echo ("<p>" . $thisUserHasOrYouHave . " chosen " . count($favouriteMovies) . " favourite movie" . (count($favouriteMovies) > 1 ? 's' : '') . ".</p>");
        echo("<ul>");
        $count = 0;
        foreach($favouriteMovies as $m)
            echo("<li><a href='details.php?movie=" . $m->GetID() . "'>" . $m->GetName() . "</a></li>");
        echo("</ul>");
    }

}
$template->setVariable("CONTENT", ob_get_contents());
ob_end_clean();


$template->show();

?>
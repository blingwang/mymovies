<?php

require_once('functions.php');
require_once('includes/dvd.inc');

// Connect to the database
$connection = ConnectToDatabase();

// Instantiate all the objects we will be working with
$user = User::GetCurrentUser($connection);
$movie = Movie::Load($connection, (int)requestVar('movie'));
$Categories = new Categories($connection);

$template = NewPage($user);

if($movie != null)
{
    $message = null;

    if($user != null)
    {
        switch(requestVar('favourite'))
        {
            case 'add':
                if($movie->AddFavourite($connection, $user))
                    $message .= ('<p class="notification">This movie has been added to your favourites list.</p>');
                else
                    $message .= ('<p class="error">This movie is already on your favourites list.</p>');
                break;
    
            case 'remove':
                if($movie->RemoveFavourite($connection, $user))
                    $message .= ('<p class="notification">This movie has been removed from your favourites list.</p>');
                break;
    
        }
    
        $deleteReview = requestVar('deleteReview');
        if($deleteReview != null)
        {
            if($user == null)
                $message = ('<p class="error">You must <a href="login.php">log in</a> before you can delete a review.</p>');
            else
            {
                $review = Review::Load($connection, $deleteReview);
                if($review == null)
                    $message .= ('<p class="error">The review you are trying to delete could not be found.</p>');
                else if($review->CanEdit($user) == false)
                    $message .= ('<p class="error">You do not have permission to modify the review you have requested.</p>');
                else
                {
                    $review->Delete($connection);
                    $message .= ('<p class="notification">The specified review has been deleted.</p>');
                }
            }
        }
    }
    
    if(isset($_REQUEST['myRating']))
    {
        if($user == null)
            $message .= "<p class='error'>You must <a href='login.php'>log in</a> before you can rate this movie.</p>";
        else
        {
            $rating = (int)$_REQUEST['myRating'];

            if($rating >= 0 && $rating <= 10)
            {
                if($movie->SaveUserRating($connection, $user, $rating))
                    $message .= ("<p class='notification'>Thanks for rating this movie!</p>");
                else
                    $message .= ("<p class='error'>Your rating of this movie could not be saved.</p>");
            }
            else
            {
                $message .= "<p class='error'>You must choose a valid rating for this movie.</p>";
            }
        }
    }
    
    if($message != null)
        $template->setVariable('NOTIFICATION', $message);

    $template->addBlockFile('CONTENT', 'DETAILS', 'content-movie-details.tpl');

    $template->setCurrentBlock('DETAILS');

    $image = $movie->GetImageName();
    
    // Get inventory information
    $inventory = getInventory($movie->GetId(), $connection);

    if($image == null)
        $template->setVariable('MOVIE_IMAGE', "<span style='color:gray;'><br /><div style='width:150px; width:100px; height:100px; text-align:center; vertical-align:middle; border:1px #CCC solid;'><small><br /><br /><br />No cover image<br />available</small></div></span>");
    else
        $template->setVariable('MOVIE_IMAGE', '<img src="./movie_images/' . $movie->GetId() . '.jpg" width="150">');

    $template->setVariable('MOVIE_NAME', $movie->GetName());
    $template->setVariable('DESCRIPTION', $movie->GetDescription());
    $template->setVariable('YEAR', "<a href='search.php?year=" . $movie->GetYear() . "'>" . $movie->GetYear() . "</a>");
    $template->setVariable('PRICE', "{$inventory['cost']}");
    $template->setVariable('CATEGORY', "<a href='search.php?category=" . $movie->GetCategoryId() . "'>" . $Categories->GetCategoryNameFromId($movie->GetCategoryId()) . "</a>");
    $template->setVariable('MOVIE_ID', $movie->GetId());
    
    $rating = $movie->GetRating();

    $ratingCell = "<small>On average, our users rate this movie:</small><br />" . $movie->MakeStarBox($rating, true);

    if($user == null)
        $ratingCell .= "<small><a href='login.php'>You must log in to rate this movie.</a></small>";
    else
    {
        $myRating = ExecuteScalar($connection, "SELECT rating FROM movie_ratings WHERE movieid=" .$movie->GetId() . " AND username='" . mysql_escape_string($user->GetUsername()) . "'", null);

        $descriptions = array(
            "0/10 (awful)",
            "1/10",
            "2/10",
            "3/10",
            "4/10",
            "5/10 (average)",
            "6/10",
            "7/10",
            "8/10",
            "9/10",
            "10/10 (fantastic)");

        $ratingCell .= "

        <script language='javascript'>
        
        function showRatingBox(elem)
        {
            document.getElementById('ratingBox').style.display='';
            document.getElementById('ratingLink').style.display='none';
        }
        
        function hilite(elem)
        {
            elem.style.backgroundColor = 'yellow';
        }
        
        function unhilite(elem)
        {
            elem.style.backgroundColor = '';
        }
        
        function rate(value)
        {
            this.location='details.php?movie=" . $movie->GetId() . "&myRating=' + value;
        }

        </script>


        <small>";
        
        if($myRating == null)
            $ratingCell .= "<a href='javascript:showRatingBox();' id='ratingLink'>Rate this movie!</a>";
        else
            $ratingCell .= "You rated this movie $myRating / 10.<br /><a href='javascript:showRatingBox();' id='ratingLink'>Click here to change your rating of this movie.</a>";

        $ratingCell .= "</small>

        <div id='ratingBox' style='display:none;'>
            <p align='center' style='text-align:center;'><br />I think this movie deserves:</p>

            <table cellpadding=3 cellspacing=0>";

        foreach($descriptions as $v=>$t)
            $ratingCell .= "<tr onmouseover='hilite(this)' onclick='rate({$v})' onmouseout='unhilite(this)'><td style='border:1px #ccc solid; border-right:none;'>" . Movie::MakeStarBox($v, false) . "</td><td style='padding-left:20px; border:1px #ccc solid; border-left:none;' align='center'><a href='?movie=" . $movie->GetId() . "&myRating={$v}'>{$t}</a></td></tr>\r\n";

        $ratingCell .= "
            </table>
        </div>
        ";
    }
    $template->setVariable('RATING', $ratingCell);

    $favCount = $movie->GetFavouritesCount($connection);
    $isMyFavorite = $movie->IsUserFavourite($connection, $user);

    $template->setVariable("FAVOURITE_BLOCK",
        $favCount . " user" . ($favCount == 1 ? '' : 's') . " say" . ($favCount == 1 ? 's' : '') . " this is one of their favourite movies.<br />" .
        "<table width='100%'><tr valign='top'><td width='32'>" .
        ($user == null
            ? ''
            :
              ($isMyFavorite
                  ? "<img src='images/favourite_on.png'></td><td align='center'><small>It's one of your favourites.<br /> <a href='?movie=" . requestVar('movie') . "&favourite=remove'>Not any more? Click here.</a>"
                  : "<img src='images/favourite_off.png'></td><td align='center'><small>One of your favourites? <a href='?movie=" . requestVar('movie') . "&favourite=add'>Click here to add it to your list.</a>")
            ) .

        "</small></td></tr></table>");


    $reviews = $movie->GetReviews($connection);

    if(count($reviews) == 0)
    {
        $reviewMessage = '<p>There are no user reviews for this movie yet. ';
        if($user != null)
            $reviewMessage .= ('<a href="review_write.php?movie=' . $movie->GetId() . '">Be the first to write one!</a>');
        else
            $reviewMessage .= ('<a href="login.php">Log in to write your own review.</a>');
        $reviewMessage .= ('</p>');
        $template->setVariable('REVIEW_MESSAGE', $reviewMessage);
    }
    else
    {
        $reviewMessage = ('<p>There ');
        if(count($reviews) == 1)
            $reviewMessage .= ('is <b>1</b> review');
        else
            $reviewMessage .=('are <b>' . count($reviews) . '</b> reviews');
            
        if($user == null)
            $reviewMessage .= ". You must <a href='login.php'>log in</a> to write a review.</p>";
        else
            $reviewMessage .=(". You can <a href='review_write.php?movie=" . $movie->GetId() . "'>add a review</a> if you would like.</p>");
            
        $template->setVariable('REVIEW_MESSAGE', $reviewMessage);

        foreach($reviews as $review)
        {
            $template->setCurrentBlock('REVIEW');
    
            $template->setVariable('REVIEW_ID', $review->GetId());
            $template->setVariable('REVIEW_USERNAME', $review->GetUsername());
            $template->setVariable('REVIEW_TIME', date('m/d/Y', strtotime($review->GetWriteTime())));
            $template->setVariable('REVIEW_TEXT', $review->GetText());
    
            if($review->CanEdit($user))
                $template->setVariable('REVIEW_OPTIONS', "<a href='?movie=" . $movie->GetId() . "&deleteReview=" . $review->GetId() . "' style='color:red;'>delete this review</a>");
    
            $template->parseCurrentBlock();
        }
    }

    $template->parseCurrentBlock();
}
else
{
    die('Movie not found.');

}

// Output the web page
$template->show();

?>
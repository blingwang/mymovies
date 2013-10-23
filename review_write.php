<?php

require_once("functions.php");

$conn = ConnectToDatabase();
$movie = Movie::Load($conn, (int)requestVar("movie"));
$user = User::GetCurrentUser($conn);
$template = NewPage($user);

ob_start();
if($user == null)
    echo ("<p>Sorry, you must be <a href='login.php'>logged in</a> to write a review.</p>");
else if($movie == null)
    echo ("<p>Sorry, the movie you are trying to review could not be found.</p>");
else
{
    switch(requestVar('action'))
    {
      
        case 'save':
        case 'preview':

            if(requestVar("subAction") != "No, I want to edit it")
            {
                $reviewtext = str_replace("\n", "<br />", strip_tags(requestVar("reviewtext")));

                if(requestVar('action') == "save")
                {
                    Review::Create($conn, $movie->GetId(), $user, $reviewtext);
                    header("Location: details.php?movie=" . $movie->GetId());
                    die();
                }
                
                $stripped = trim(strip_tags($reviewtext));
                if(empty($stripped) == false)
                {
                    echo("<p>This is how your review of <b>" . $movie->GetName() . "</b> will look:</p><p style='border:1px #ccc solid; padding:5px; background-color: #eee;'>");
                    echo($reviewtext);
                    echo("</p>");
                    echo("
                        <form action='review_write.php' method='post' style='display:inline;'>
                        <input type='hidden' name='movie' value='" . $movie->GetId() . "' />
                        <input type='hidden' name='action' value='save' />
                        <input type='hidden' name='reviewtext' value=\"" . str_replace('"',"'",requestVar("reviewtext")) . "\" />
                        <input type='submit' name='subAction' value='Yes, save my review' />
                        <input type='submit' name='subAction' value='No, I want to edit it' />
                        </form>
                    ");
                    break;
                }
    
                echo "<p class='error'>You must enter your review.</p>";
            }

        default:
            echo("
                <form action='review_write.php' method='POST'>
                <input type='hidden' name='action' value='preview' />
                <input type='hidden' name='movie' value='" . $movie->GetId() . "' />
                
                <h2>My Review of " . $movie->GetName() . "</h2>
            
                <textarea style='font-family: georgia; width:100%; height: 10em;' name='reviewtext'>" . requestVar("reviewtext") . "</textarea><br />
                
                <p align='right'><input type='submit' value='Save Review' /> or <a href='details.php?movie=" . $movie->GetId() . "'>go back</a> to the movie details page if you don't want to write a review.</p>
                </form>
                
                ");
    }
}

$template->setVariable("CONTENT", ob_get_contents());
ob_end_clean();

$template->parseCurrentBlock();
$template->show();

?>
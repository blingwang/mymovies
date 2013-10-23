<?php

define("THUMBNAILS_PER_PAGE", 10);               // How many thumbnails are there on each page? Should always be >= 1

class Thumbnailpage
{
    // Creates a "paged" page showing results
    static function Create($connection, $title, $movies, $errorMessage = "Sorry, there are no movies in the database that match this criteria.")
    {
        // Create a new template
        $user = User::GetCurrentUser($connection);
        $template = NewPage($user);

        $totalCount = count($movies);
        $page = requestVar("page");

        if(is_numeric($page) == false || (((int)$page) * THUMBNAILS_PER_PAGE) > count($movies) || ((int)$page) <= 0)
            $page = 1;
        
        $movies = array_slice($movies, ($page - 1) * THUMBNAILS_PER_PAGE, THUMBNAILS_PER_PAGE);

        $this_index = 1;
        $max = ($page * THUMBNAILS_PER_PAGE);
        if($max > $totalCount)
            $max = $totalCount;
        $link = "<div style='clear:left;'><table width='100%'><tr><td>Showing movies " . ((($page - 1) * THUMBNAILS_PER_PAGE) + 1) . "-" . $max . " of " . $totalCount . "</td>";
        
        $p = 1;
        while(($p * THUMBNAILS_PER_PAGE) <= $totalCount)
        {
            if($p == 1)
                $link .= "<td><td align='right'>Page: ";

            if($p == $page)
                $link .= " &lt;<i>{$p}</i>&gt; ";
            else
                $link .= "<a href='?page={$p}'>{$p}</a> ";
            $p++;
        }
        if($p > 1)
            $link .= "</td>";
            
        $link .= "</tr></table></div>";
        
        if(count($movies) == 0)
        {
            $template->addBlockFile("CONTENT", "RECOMMENDED", "generic-error-message.tpl");
            $template->setCurrentBlock("RECOMMENDED");
            $template->setVariable("ERROR_TITLE", $title);
            $template->setVariable("ERROR_MESSAGE", $errorMessage);
            $template->parseCurrentBlock( );
            $shown = true;
        }
        else
        {
            $template->addBlockFile("CONTENT", "RECOMMENDED", "generic-movie-thumbnails.tpl");
            $template->setCurrentBlock("RECOMMENDED");
            $template->setVariable("TITLE", $title);
            $template->setVariable("THUMBNAIL_NAV_TOP", $link);
            $template->setVariable("THUMBNAIL_NAV_BOTTOM", $link);
            foreach($movies as $movie)
            {
                // Add the movie information to the template
                $template->setCurrentBlock("THUMBNAIL_LIST");
                $template->setVariable("MOVIE_ID", $movie->GetId());
                $template->setVariable("MOVIE_NAME", $movie->GetName());
                $template->parseCurrentBlock( );
            }
            $template->parseCurrentBlock();
        }
        
        $template->show();
    
    }

}

?>
<?php

$sidebar_box_count = 0;

function SidebarStart($template, $title)
{
    global $sidebar_box_count;

    $name = "BOX_" . $sidebar_box_count;
    $sidebar_box_count++;

    $template->setCurrentBlock("SIDEBAR");
    $template->addBlockFile("SIDEBAR_HOLDER", $name, "generic-side-list-box.tpl");
    $template->setCurrentBlock($name);
    $template->setVariable("SIDEBAR_TITLE", $title);
    return $name;
}

function AddSidebarCategoryList($connection, $template)
{
    // Create the new sidebar box
    $box_name = SidebarStart($template, "Categories");

    // Fill the sidebar box with the category list
    $categories = new Categories($connection);
    $cats = $categories->GetList();
    $template->setCurrentBlock("ENTRY");
    foreach($cats as $id=>$name)
    {
        $template->setVariable("ENTRY_LINK", "view_category.php?category=" . $id);
        $template->setVariable("ENTRY_TEXT", $name);
        $template->parseCurrentBlock();
    }
    $template->parseCurrentBlock();

    // Cleanup by "closing" the sidebar box
    $template->setCurrentBlock($box_name);
    $template->parseCurrentBlock();
}

function AddSidebarTopSellers($connection, $template, $limit = 5) 
{
    AddSidebarOfMovies($connection, $template, "Top Sellers", Movie::GetTopSellers($connection, $limit), "Sorry, no information about top selling movies is available.");
}

function AddSidebarTopRated($connection, $template, $limit = 5)
{
    //AddSidebarOfMovies($connection, $template, "Top Rated", Movie::GetTopRated($connection, $limit), "No information about top rating movies is available.");
}


function AddSidebarOfMovies($connection, $template, $title, $movies, $emptyMessage)
{
    // Create the new sidebar box
    $box_name = SidebarStart($template, $title);

    // Fill the sidebar box with the movies
    if(count($movies) == 0)
    {
        $template->setVariable("SIDEBAR_MESSAGE", "<p style='margin:20px; margin-bottom:0px;'>" .  $emptyMessage . "</p>");
        $template->parseCurrentBlock();
    }
    else
    {
        $template->setCurrentBlock("ENTRY");
        foreach($movies as $m)
        {
            $template->setVariable("ENTRY_LINK", "details.php?movie=" . $m->GetId());
            $template->setVariable("ENTRY_TEXT", $m->GetName());
            $template->parseCurrentBlock();
        }
        $template->parseCurrentBlock();
    }

    // Cleanup by "closing" the sidebar box
    $template->setCurrentBlock($box_name);
    $template->parseCurrentBlock();
}


?>
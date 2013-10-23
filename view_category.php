<?php

require_once("functions.php");
require_once("includes/ThumbnailPage.php");

$connection = ConnectToDatabase();

$catId = (int)requestVar("category");

$Categories = new Categories($connection);
if($Categories->CategoryExists($catId))
    ThumbnailPage::Create($connection, "Movies In The Category: <i>". $Categories->GetCategoryNameFromId($catId) . "</i>", Movie::LoadAllInCategory($connection, $catId, -1));
else
    die("The category you are trying to view could not be found.");

?>
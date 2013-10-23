<?php

require_once("functions.php");
echo "hello";
require_once("includes/ThumbnailPage.php");
$connection = ConnectToDatabase();

ThumbnailPage::Create($connection, "Newest Movies", Movie::LoadNewestMovies($connection, -1));

?>

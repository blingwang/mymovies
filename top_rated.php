<?php

require_once("functions.php");
require_once("includes/ThumbnailPage.php");

$connection = ConnectToDatabase();

ThumbnailPage::Create($connection, "Top Rated Movies", Movie::GetTopRated($connection, -1));

?>
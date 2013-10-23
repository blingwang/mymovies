<?php

require_once("functions.php");

$conn = ConnectToDatabase();
$Categories = new Categories($conn);
$query = null;
$sortQuery = null;
$sortText = null;

switch(requestVar("sort"))
{
    case "rating":
        $sortQuery .= " ORDER BY rating DESC";
        $sortText = " ordered by rating";
        break;
}

$builtSql = null;
$builtDesc = null;

function AddQuery($sql, $desc)
{
    global $builtSql, $builtDesc;

    if($builtSql != null)
        $builtSql .= " AND ";
    $builtSql .= $sql;

    if($builtDesc != null)
        $builtDesc .= ", and ";
    $builtDesc .= $desc;
}



ob_start();

$year = requestVar("year");
if($year != null)
{
    if(is_numeric($year) == false)
       $error = "You must enter a valid year.";
    else
       AddQuery("year={$year}", "from the year <b>{$year}</b>");
}

$text = requestVar("text");
if($text != null && empty($text) == false)
{
   AddQuery("(name LIKE '%" . mysql_escape_string($text) . "%' OR description LIKE '%" . mysql_escape_string($text) . "%')", "containing the text '<b>{$text}</b>'");
}

$category = requestVar("category");
if($category != null)
{
     if(is_numeric($category) == false || $Categories->CategoryExists($category) == false)
         $error = "You must select a valid category.";
     else
     {
         $category = (int)$category;
         $catName = $Categories->GetCategoryNameFromId($category);
         AddQuery("category={$category}", "in the category <b>{$catName}</b>");
     }
}

$query .= $builtSql . $sortQuery;

$user = User::GetCurrentUser($conn);
$template = NewPage($user);
$showForm = true;
$message = null;

if(strlen($builtSql) > 0)
{
    $results = Movie::LoadMovies($conn, "SELECT * FROM movie WHERE " . $query);
    $showForm = false;

    if(count($results) == 0)
    {
        $message .= ("<p>Sorry, no results matching your query were found. You can refine your search below:</p>");
        $showForm = true;
    }
    else
    {
        echo("
        
<style>
th { text-align:left; }
</style>

");        


        echo("<p>Search results - <i>{$builtDesc}</i>" . $sortText . ".</p><table width='100%'><tr><th width='40'>&nbsp;</th><th>Movie</th><th width='100'>User Rating</th>");

        $i = 0;
        foreach($results as $m)
        {
            echo("<tr valign='top'><td align='right'>" . (++$i) . ".</td><td style='padding-left: 5px; padding-bottom:5px; '><a href='details.php?movie=" . $m->GetId() . "'>". $m->GetName() . "</a><br /><small>" . $Categories->GetCategoryNameFromId($m->GetCategoryId()) . ", " . $m->GetYear() . "</td><td align='right' style='padding-left:10px;'>" . $m->GetRating() . " / 10</tr>\r\n");
        }

        echo("</table>");
        echo("<p><a href='search.php'>Click here to start a new search</a></p>");
    }
}

if($showForm)
{
    echo "<h1>New Search</h1>";

    if(isset($error))
        echo("<p class='error'>{$error}</p>");
    
    if(isset($message))
        echo $message;

    ?>
    <form action='search.php'>
    <table>
    <tr><td>Containing the phrase:</td><td><input type='text' name='text' value='<?php echo requestVar("text"); ?>' /></td></tr>
    <tr><td>Movies from the year:</td><td><input type='text' name='year' value='<?php echo requestVar("year"); ?>' /></td></tr>
    <tr><td>Movies in the category:</td><td><select name='category'><option value=''>(any)</option><?php echo $Categories->CreateSelectOptions(requestVar('category')) ?></select></td></tr>
    </table>
    <input type='submit' value='Search' /></form>

    <?php
}

$template->setVariable("CONTENT", ob_get_contents());

ob_end_clean();

$template->show();

?>
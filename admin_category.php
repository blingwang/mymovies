<?php

require_once("functions.php");

$conn = ConnectToDatabase();
$user = User::GetCurrentUser($conn);

if($user->IsAdministrator() == false)
    die("You are not an administrator!");

$cats = new Categories($conn);
$cats = $cats->GetList();

$action = requestVar('action');
$id = requestVar('id');

$template = NewPage($user);

$showForm = false;

ob_start();

function ShowCreateOrEditForm($title, $action, $id, $oldValue, $newValue, $error)
{
    // Creating a new category
    echo("<h1>{$title}</h1>");
    if(isset($error))
        echo("<p class='error'>Error: {$error}</p>\r\n");
    echo("
        <form action='admin_category.php' method='post'>
        <input type='hidden' name='action' value='$action' />
        <input type='hidden' name='id' value='$id' />

        <table>
        ");
    if($oldValue != null)
        echo ("<tr><td>Old Category Name:</td><td>{$oldValue}</td></tr>");

    echo("
            <tr><td>New Category Name:</td><td><input type='text' name='name' value='" . $newValue . "' /></td></tr>
        </table>
        <input name='button' type='submit' value='Create' /> <input name='button' type='submit' value='Cancel' class='cancel' />
        </form><hr />");
}

if($action != null)
{
    if(requestVar("button") == "Cancel")
    {
        // Do nothing, they hit the cancel button. Show no form.
    }
    else
    {
        switch($action)
        {
            case 'delete':
                // Deleting an existing category.
                if(isset($cats[$id]))
                {
                    ExecuteQueryOrDie($conn, "DELETE FROM category WHERE id=" . (int)$id);
                    echo("<p class='notification'>The category <b>{$cats[$id]}</b> has been deleted.</p>");
                    $cats = GetCategories($conn);
                }
                else
                {
                    echo("<p class='error'>Error: The category you are trying to delete could not be found.</p>");
                }
                break;
                
            case 'new':
                // Creating a new category
                $name = requestVar("name");
                if(requestVar("button") == "Create")
                {
                    if(empty($name))
                        ShowCreateOrEditForm('New Category', 'new', null, null, $name, "You must enter the category name.");
                    else
                    {
                        // Attempt to create the new category;
                        $result = $conn->query("INSERT INTO category(name) VALUES ('" . mysql_escape_string(requestVar("name")) . "')");
                        if(DB::IsError($result) == false)
                            echo("<p class='notification'>The category <b>{$name}</b> was created successfully.</p>");
                        else
                            ShowCreateOrEditForm('New Category', 'new', null, null, $name, "The category could not be created. Please check the name is unique and try again.");
                    }
                }
                else
                {
                    ShowCreateOrEditForm('New Category', 'new', null, null, null, null);
                }
                break;
        
            case 'edit':
                // Editing an existing category
                if(isset($cats[$id]))
                {
                    $name = requestVar("name");
                    if(requestVar("button") == "Create")
                    {
                        if(empty($name))
                            ShowCreateOrEditForm('Edit Category', 'edit', $id, $cats[$id], $name, "You must enter the new category name.");
                        else
                        {
                            // Attempt to create the new category;
                            $result = $conn->query("UPDATE category SET name='" . mysql_escape_string($name) . "' WHERE id=" . (int)$id);
                            if(DB::IsError($result) == false)
                                echo("<p class='notification'>The category <b>$cats[$id]</b> was renamed to <b>{$name}</b> successfully.</p>");
                            else
                                ShowCreateOrEditForm('Edit Category', 'edit', $id, $cats[$id], $name, "The category could not be renamed. Please check that the new name is unique and try again.");
                        }
                    }
                    else
                    {
                        ShowCreateOrEditForm('Edit Category', 'edit', $id, $cats[$id], $name, null);
                    }
                }
                else
                {
                    echo("<p class='error'>Error: Cannot find the category you are trying to edit.</p>");
                }
                break;
    
        }
    }
    $cats = GetCategories($conn);
}

echo("<h1>Current Categories</h1>");
if(count($cats) > 0)
{
    echo("<table>\r\n" .
         "<tr><th>Category</th><th>Options</th></tr>\r\n");

    $i = 0;
    foreach($cats as $id=>$name)
    {
        echo "<tr><td class='row" . ($i++ & 1) . "'>{$name}</td><td><a href='?action=edit&id={$id}'>edit</a> <a href='?action=delete&id={$id}' style='color:red;' onclick='return(confirm(\"Are you sure you want to delete this category?\"))'>delete</a></td></tr>\r\n";
    }

    echo("</table>");
}
else
{
    echo("<p>There are no categories in the database.</p>");

}


echo("<p><a href='?action=new'>Add new category</a></p>");



$template->setVariable("CONTENT", ob_get_contents());
ob_end_clean();



$template->show();


?>
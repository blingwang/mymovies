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
        $qu = "'" . mysql_escape_string(requestVar("id")) . "'";

        switch($action)
        {
            case 'setPwd':
                $target = User::Load($conn, requestVar("id"));
                if($target == null)
                {
                    echo("<p class='error'>The user <b>{$qu}</b> was not found.</p>");
                }
                else
                {
                    ExecuteQueryOrDie($conn, "UPDATE user SET password=MD5('" . mysql_escape_string(requestVar("newPwd")) . "') WHERE username=" . $qu);
                    echo("<p class='notification'>The user <b>{$qu}</b> has had their password reset.</p>");
                }
                break;


            case 'delete':
                // Deleting an existing user.
                ExecuteQueryOrDie($conn, "DELETE FROM user WHERE username=" . $qu);
                echo("<p class='notification'>The user <b>{$qu}</b> has been deleted.</p>");
                $cats = GetCategories($conn);
                break;
                
            case 'promote':
                ExecuteQueryOrDie($conn, "UPDATE user SET accounttype=1 WHERE username=" . $qu);
                echo("<p class='notification'>The user <b>{$qu}</b> has been promoted.</p>");
                break;

            case 'remove':
                ExecuteQueryOrDie($conn, "UPDATE user SET accounttype=0 WHERE username=" . $qu);
                echo("<p class='notification'>The user <b>{$qu}</b> has been demoted to normal user.</p>");
                break;

    
        }
    }
    $cats = GetCategories($conn);
}

echo("<h1>Users</h1>");
$users = User::LoadAll($conn);
if(count($users) > 0)
{
    echo("<table>\r\n" .
         "<tr><th>Name</th><th>Username</th><th colspan=2>Options</th></tr>\r\n");

    $i = 0;
    $dropdown = "";
    foreach($users as $u)
    {
        $id = $u->GetUsername();

        echo "<tr><td class='row" . ($i++ & 1) . "'>" . $u->GetName() . "</td><td>$id</td><td>";

        if($u->getUsername() == $user->getUsername())
            echo "Administrator";
        else if($u->IsAdministrator())
            echo "Administrator (<a href='?action=remove&id={$id}'>remove</a>)";
        else
            echo "User (<a href='?action=promote&id={$id}'>promote</a>)";

        echo "</td><td><a href='?action=delete&id={$id}' style='color:red;' onclick='return(confirm(\"Are you sure you want to delete this user?\"))'>delete</a></td></tr>\r\n";
        
        $dropdown .= "<option>{$id}</option>";
    }

    echo("</table><h3>Reset Password</h3><form action='admin_users.php?action=setPwd' method='POST'><table><tr><td>User:</td><td><select name='id'><option></option>{$dropdown}</select></td></tr><tr><td>New Password:</td><td><input type='password' name='newPwd' /></td></tr></table><input type='submit' value='Set Password' /></form>");
}
else
{
    echo("<p>There are no users in the database.</p>");

}



$template->setVariable("CONTENT", ob_get_contents());
ob_end_clean();



$template->show();


?>
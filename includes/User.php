<?php

define("ACCOUNT_TYPE_ADMIN", 1);

class User
{
    public function IsAdministrator()
    {
        return ($this->_accounttype == ACCOUNT_TYPE_ADMIN);
    }

    public static function LogIn($conn, $username, $password)
    {
        $value = null;

        $result = ExecuteQueryOrDie($conn, "SELECT username,name,accounttype FROM user WHERE username='" . mysql_escape_string($username) . "' AND password=MD5('" . mysql_escape_string($password) . "')");
        if($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
            $value = new User($row['username'], $row['name'], $row['accounttype']);

        return $value;
    }

    public static function GetCurrentUser($conn)
    {
        if(isset($_SESSION["username"]))
            return User::Load($conn, $_SESSION["username"]);
        else
            return null;
    }

    public static function Load($conn, $username)
    {
        $value = null;
        $result = ExecuteQueryOrDie($conn, "SELECT username,name,accounttype FROM user WHERE username='" . mysql_escape_string($username) . "'");
        if($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
            $value = new User($row['username'], $row['name'], $row['accounttype']);
        return $value;
    }

    public static function LoadAll($conn)
    {
        $value = array();
        $result = ExecuteQueryOrDie($conn, "SELECT username,name,accounttype FROM user ORDER BY username");
        while($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
            $value[] = new User($row['username'], $row['name'], $row['accounttype']);
        return $value;
    }

    public static function Create($conn, $username, $name, $password)
    {
        $result = $conn->query("INSERT INTO user(username,name,accounttype,password) VALUES ('" . mysql_escape_string($username) . "','" . mysql_escape_string($name) . "',0,MD5('" . mysql_escape_string($password) . "'))");
        return true;
    }
    
    public function GetFavouriteMovies($conn)
    {
        return Movie::LoadFavouritesForUser($conn, $this->_username);
    }


    var $_username, $_name, $_accounttype;

    function __construct($username, $name, $accounttype)
    {
        $this->_username = $username;
        $this->_name =$name;
        $this->_accounttype = $accounttype;
    }
    
    public function GetUsername()    { return $this->_username; }
    public function GetName()        { return $this->_name; }
    public function GetAccountType() { return $this->_accounttype; }
}

?>
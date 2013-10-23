<?php

    // Connects to the database using the PEAR DB module.
    // If the connection fails, die with the error message.
    function ConnectToDatabase()
    {
        $hostName = "cse.unl.edu";
        $databaseName = "jianguow";
        $username = "jianguow";
        $password = "accord1990";
        $dsn = "mysql://{$username}:{$password}@{$hostName}/{$databaseName}";

        $connection = DB::Connect($dsn);
        if(DB::isError($connection))
            die($connection->getMessage());
        return $connection;
    }

    function showError($connection)
    {
        // Show an error if database connection fails.
        if (DB::isError($connection))
            die($connection->getMessage());
    }

    // Tries to execute the specified query on the database connection object,
    // if an error occurs then die with the error message displayed.
    function ExecuteQueryOrDie($connection, $query)
    {
        $result = $connection->query($query);
        showError($result);
        return $result;
    }

    // Executes the specified query, and if a row is returned this function will
    // return the contents of
    function ExecuteScalar($connection, $sql, $default = null)
    {
        $value = $default;
        $result = ExecuteQueryOrDie($connection, $sql);
        if($row = $result->fetchRow())
            $value = $row[0];
        return $value;
    }

?>

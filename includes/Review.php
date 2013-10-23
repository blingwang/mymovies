<?php

class Review
{
    var $_id, $_username, $_movieid, $_text, $_moviename;

    public function CanEdit($user)
    {
        if($user == null)
            return false;
            
        if($user->GetUsername() == $this->_username || $user->IsAdministrator())
            return true;
            
        return false;
    }
    
    public function Delete($connection)
    {
        $connection->query("DELETE FROM review WHERE reviewid=" . $this->_id);
    }

    private static function LoadRangeByQuery($connection, $query)
    {
        $values = array();
        $result = ExecuteQueryOrDie($connection, $query);
        while($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
            $values[] = new Review($row['reviewid'], $row['username'], $row['movieid'], $row['text'], $row['writetime'], $row['name']);
        return $values;
    }

    public static function LoadAllForMovie($connection, $movieid) {
        return Review::LoadRangeByQuery($connection, "SELECT reviewid, username, movieid, text, writetime, movie.name FROM review,movie WHERE review.movieid=" . (int)$movieid . " AND movie.id = review.movieid");
    }

    public static function LoadAllForUsername($connection, $username) {
        return Review::LoadRangeByQuery($connection, "SELECT reviewid, username, movieid, text, writetime, movie.name FROM review,movie WHERE username='" . mysql_escape_string($username) . "' AND movie.id = review.movieid ORDER BY writetime DESC");
    }

    public static function LoadMostRecent($connection, $limit) {
        return Review::LoadRangeByQuery($connection, "SELECT reviewid, username, movieid, text, writetime, movie.name FROM review,movie WHERE movie.id=review.movieid ORDER BY writetime DESC" . ($limit != -1 ? ' LIMIT ' . $limit : ''));
    }

    public static function Load($connection, $reviewid)
    {
        $value = Review::LoadRangeByQuery($connection, "SELECT reviewid, username, movieid, text, writetime, movie.name FROM review,movie WHERE reviewid=" . (int)$reviewid . " AND movie.id = review.movieid");

        if(count($value) == 0)
            return null;
        else
            return $value[0];
    }

    public static function Create($conn, $movieid, $user, $text)
    {
        ExecuteQueryOrDie($conn,"INSERT INTO review(username, movieid, text) VALUES ('" . mysql_escape_string($user->GetUsername()) . "'," . $movieid . ",'" . mysql_escape_string($text) . "')");
        return true;
    }

    private function Review($reviewid, $username, $movieid, $text, $writetime, $moviename)
    {
        $this->_id = $reviewid;
        $this->_username = $username;
        $this->_movieid = $movieid;
        $this->_text = $text;
        $this->_writetime = $writetime;
        $this->_moviename = $moviename;
    }

    public function GetId() { return $this->_id; }
    public function GetUsername() { return $this->_username; }
    public function GetMovieId() { return $this->_movieid; }
    public function GetText() { return $this->_text; }
    public function GetWriteTime() { return $this->_writetime; }

    public function GetMovieName() { return $this->_moviename; }

}

?>
<?php

class Movie
{
    var $_id, $_name, $_category, $_year, $_description, $rating;
    
    public static function GetTopRated($connection, $limit = 20) {
        return Movie::LoadMovies($connection, "SELECT * FROM movie ORDER BY rating DESC " . ($limit == -1 ? '' : "LIMIT " . $limit));
    }

    public static function LoadNewestMovies($connection, $limit = 20) {
        return Movie::LoadMovies($connection, "SELECT * FROM movie ORDER BY id DESC " . ($limit == -1 ? '' : "LIMIT " . $limit));
    }

    public static function LoadAllInCategory($connection, $categoryid, $limit = 20) {
        return Movie::LoadMovies($connection, "SELECT * FROM movie WHERE category=" . (int)$categoryid . " ORDER BY name ASC " . ($limit == -1 ? '' : "LIMIT " . $limit));
    }

    public static function GetTopSellers($connection, $limit = 5) {
        return Movie::LoadMovies($connection, "SELECT * FROM movie ORDER BY rating DESC " . ($limit == -1 ? '' : "LIMIT " . $limit));
    }

    public static function LoadFavouritesForUser($connection, $username) {
        return Movie::LoadMovies($connection, "SELECT movie.* FROM movie,favourite_movies WHERE movie.id=favourite_movies.movieid AND favourite_movies.username='" . mysql_escape_string($username) . "'");
    }

    private function __construct($id, $name, $category, $year, $description, $rating)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_category = $category;
        $this->_year = $year;
        $this->_description = $description;
        $this->_rating = $rating;
    }

    public function GetFavouritesCount($connection)
    {
        return ExecuteScalar($connection, "SELECT COUNT(*) AS ct FROM favourite_movies WHERE movieid=" . (int)$this->_id, 0);
    }

    public function IsUserFavourite($connection, $user)
    {
        if($user == null)
            return false;
        return ExecuteScalar($connection, "SELECT COUNT(*) FROM favourite_movies WHERE movieid=" . (int)$this->_id . " AND username='" . mysql_escape_string($user->GetUsername()) . "'", 0) > 0;
    }


    public function AddFavourite($conn, $user)
    {
        $result = $conn->query("INSERT INTO favourite_movies (movieid,username) VALUES (" . (int)$this->_id . ",'" . mysql_escape_string($user->GetUsername()) . "')");
        return true;
    }

    public function RemoveFavourite($conn, $user)
    {
        ExecuteQueryOrDie($conn, "DELETE FROM favourite_movies WHERE movieid=" . (int)$this->_id . " AND username='" . mysql_escape_string($user->GetUsername()) . "'");
        return true;
    }


    // Returns the specified Movie object, or null if it does not exist in the database.
    public static function Load($conn, $id)
    {
        $value = null;
        $result = ExecuteQueryOrDie($conn, "SELECT id, name, category, year, description, rating FROM movie WHERE id= " . (int)$id);
        if($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
            $value = new Movie($row['id'], $row['name'], $row['category'], $row['year'], $row['description'], $row['rating']);
        return $value;
    }

    public static function LoadMovies($conn, $query)
    {
        $value = array();
        $result = ExecuteQueryOrDie($conn, $query);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
            $value[] = new Movie($row['id'], $row['name'], $row['category'], $row['year'], $row['description'], $row['rating']);
        return $value;
    }

    
    // Returns the name of the image associated with this movie, or null if no image
    // is found.
    public function GetImageName()
    {
        $value = null;

        if ($handle = opendir('movie_images')) {
            $lookFor = $this->_id . '.';
            $len = strlen($lookFor);
            while (false !== ($file = readdir($handle)))
            {
                if(substr($file, 0, $len) == $lookFor)
                {
                    $value = 'movie_images/' . $file;
                    break;
                }
            }
            closedir($handle);
        }
        
        return $value;

    }
    
    public function GetId() { return $this->_id; }
    public function GetName() { return $this->_name; }
    public function GetCategoryId() { return $this->_category; }
    public function GetYear() { return $this->_year; }
    public function GetDescription() { return $this->_description; }
    public function GetRating() { return $this->_rating; }
    
    public function GetLinkedName() { return "<a href='details.php?movie=" . $this->_id . "'>" . $this->_name . "</a>"; }

    function MakeStarBox($rating, $showText = false)
    {
        return "<table cellpadding=0 cellspacing=0><tr><td><div style='width: 110px; height: 12px; background-color:green; background: url(images/rating_off.gif); '><div style='width:" . ($rating * 11) . "px; height:12px; background: url(images/rating_on.gif);'><img src='images/spacer.gif' width='1' height='1' /></div></div></td>" . ($showText ? "<td><small>&nbsp; " . $rating . " / 10</small></td>" : '') . "</tr></table>";
    }
    
    function SaveUserRating($conn, $user, $rating)
    {
        $result = $conn->query("INSERT INTO movie_ratings(movieid,username,rating) VALUES({$this->_id},'" . mysql_escape_string($user->GetUsername()) . "', " . ((int)$rating) . ")");
        if(DB::IsError($result))
        {
            $result = $conn->query("UPDATE movie_ratings SET rating=" . ((int)$rating) . " WHERE movieid={$this->_id} AND username='" . mysql_escape_string($user->GetUsername()) . "'");
            if(DB::IsError($result))
            {
                die("false");
                return false;
            }
        }

        // Update the cached rating value (so that to read the movie rating you
        // don't have to query however many ratings are in the database)
        ExecuteQueryOrDie($conn, "UPDATE movie SET rating=(SELECT SUM(rating) / COUNT(*) FROM movie_ratings WHERE movieid=" . $this->_id . ") WHERE id=" . $this->_id);

        // Update this object so it has the new value, so we don't have to reload the entire object        
        $this->_rating = ExecuteScalar($conn, "SELECT rating FROM movie WHERE id=" . $this->_id);

        return true;
    }
    
    public function GetReviews($conn)
    {
        return Review::LoadAllForMovie($conn, $this->_id);
    }

    public function GetRatingBox($connection, $user)
    {
        $result = ExecuteQueryOrDie($connection, "SELECT COUNT(*), SUM(rating) FROM movie_ratings WHERE movieid=" .$this->_id);
        if(($row = $result->fetchRow()) === FALSE)
            die("Couldn't get movie stats.");
        $count = $row[0];
        $total = $row[1];

        $myRating = null;

        if($myRating == null)
        {
            if($user == null)
            {
                $rateFeedback = "<a href='login.php'>Log in to rate this movie.</a>";
            }
            else
            {

            }

        }
        else
            $rateFeedback = "<small class='gray'>You rated this movie " . $myRating . " / 10.</small>";



        if($count == 0)
            return "<span class='gray'>No ratings yet.</span> " . $rateFeedback;
        else
        {
            $rating = round($total / $count, 1);
            return ($this->MakeStarBox($rating, true) . $rateFeedback);
        }

    }

}

?>
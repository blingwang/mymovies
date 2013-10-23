<?php

class Categories
{
    var $_categories;

    public function __construct($connection)
    {
        $categories = array();
        $result = ExecuteQueryOrDie($connection, "SELECT id,name FROM category ORDER BY name");
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC))
            $categories[$row['id']] = $row['name'];
        $this->_categories = $categories;
    }
    
    public function GetList()
    {
        return $this->_categories;
    }


    public function CreateSelectOptions($id)
    {
        $value = "";
        foreach($this->_categories as $k=>$v)
        {
            $value .= "<option value='$k'";
            if($k == $id)
                $value .= " selected";
            $value .= ">$v</option>";
        }
        return $value;
    }

    public function CategoryExists($id)
    {
        return isset($this->_categories[$id]);

    }
    
    public function LookUpId($id)
    {
        if(isset($this->_categories[$id]))
            return "<a href='search.php?method=category&category={$id}'>{$this->_categories[$id]}</a>";
        else
            return "N/A";
    }

    public function GetCategoryNameFromId($id)
    {
        if(isset($this->_categories[$id]))
            return $this->_categories[$id];
        else
            return "N/A";
    }

}

?>
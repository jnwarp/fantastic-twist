<?php

class Connect extends mysqli
{
    public function __construct()
    {
        global $config;

        // connect to the mysql server
        parent::__construct(
            $config['sql']['host'],
            $config['sql']['username'],
            $config['sql']['password'],
            $config['sql']['database'],
            $config['sql']['port']
        );
    }

    public function advSelect($table, $conditions = [], $select = [])
    {
        // strip escape characters to prevent sql injection attacks
        $table = $this->real_escape_string($table);
        $conditions = $this->escapeArray($conditions);

        // check if there are any elements that need to be selected
        if ($select !== []) {
            $select = '`' . implode('`,`', array_values($select)) . '`';
        } else {
            $select = "*";
        }

        // check if there are any conditions that need to be met
        if ($conditions !== []) {
            $conditions = "WHERE ('" .
                implode('\',\'', array_values($conditions)) . '\') = (`' .
                implode('`,`', array_keys($conditions)) . '`)';
        } else {
            $conditions = "";
        }

        $query = "SELECT $select FROM `$table` $conditions";
        $result = $this->query($query);

        // create an array of values
        $table = [];
        while ($row = $result->fetch_assoc()) {
            $table[] = $row;
        }

        return $table;
    }

    public function advSelectCount($table, $conditions = [])
    {
        // strip escape characters to prevent sql injection attacks
        $table = $this->real_escape_string($table);
        $conditions = $this->escapeArray($conditions);

        // combine safe array into query
        $query = "SELECT `id` FROM `$table` WHERE ('" .
            implode('\',\'', array_values($conditions)) . '\') = (`' .
            implode('`,`', array_keys($conditions)) . '`);';
        $result = $this->query($query);

        return $result->num_rows;
    }

    public function advUpdate($table, $array = [], $conditions = [])
    {
        // strip escape characters to prevent sql injection attacks
        $table = $this->real_escape_string($table);
        $conditions = $this->escapeArray($conditions);
        $array = $this->escapeArray($array);

        // loop through each value to be set
        $set = [];
        foreach ($array as $key => $value) {
            $set[] = "`$key` = '$value'";
        }

        // combine safe array into query
        $query = "UPDATE `$table` SET " .
            implode(',', array_values($set)) . " WHERE (`" .
            implode('`,`', array_keys($conditions)) . "`) = ('" .
            implode('\',\'', array_values($conditions)) . "');";
        $result = $this->query($query);

        return $result;
    }

    public function escapeArray($array)
    {
        $safe_array = [];
        foreach ($array as $key => $value) {
            // strip escape characters to prevent sql injection attacks
            $key = $this->real_escape_string($key);
            $value = $this->real_escape_string($value);

            $safe_array[$key] = $value;
        }

        return $safe_array;
    }

    public function simpleDelete($table, $find_col, $find_val)
    {
        // strip escape characters to prevent sql injection attacks
        $table = $this->real_escape_string($table);
        $find_col = $this->real_escape_string($find_col);
        $find_val = $this->real_escape_string($find_val);

        $query = "DELETE FROM `$table` WHERE `$find_col` = '$find_val';";
        $result = $this->query($query);

        return $result;
    }

    public function simpleInsert($table, $array)
    {
        // strip escape characters to prevent sql injection attacks
        $table = $this->real_escape_string($table);
        $array = $this->escapeArray($array);

        $query = "INSERT INTO `$table` (`" .
            implode('`,`', array_keys($array)) . '`) VALUES (\'' .
            implode('\',\'', array_values($array)) . '\');';

        $this->query($query);
    }

    public function simpleSelect($table, $find_col, $find_val, $select = '')
    {
        // strip escape characters to prevent sql injection attacks
        $table = $this->real_escape_string($table);
        $find_col = $this->real_escape_string($find_col);
        $find_val = $this->real_escape_string($find_val);
        $select = $this->real_escape_string($select);

        // send the query to the server and get the result
        $query = "SELECT " . (($select == '') ? '* ' : "`$select` ") .
            "FROM `$table` WHERE `$find_col` = '$find_val' LIMIT 1";
        $result = $this->query($query);

        // get the row of data
        return (($result->num_rows > 0) ? $result->fetch_assoc() : []);
    }

    public function simpleSelectCount($table, $find_col, $find_val)
    {
        // strip escape characters to prevent sql injection attacks
        $table = $this->real_escape_string($table);
        $find_col = $this->real_escape_string($find_col);
        $find_val = $this->real_escape_string($find_val);

        // send the query to the server and get the result
        $query = "SELECT `id` FROM `$table` WHERE `$find_col` = '$find_val'";
        $result = $this->query($query);

        // get the row of data
        return ($result->num_rows);
    }

    public function simpleUpdate(
        $table,
        $set_col,
        $set_val,
        $find_col,
        $find_val
    ) {
        // strip escape characters to prevent sql injection attacks
        $table = $this->real_escape_string($table);
        $set_col = $this->real_escape_string($set_col);
        $set_val = $this->real_escape_string($set_val);
        $find_col = $this->real_escape_string($find_col);
        $find_val = $this->real_escape_string($find_val);

        $query = "UPDATE `$table`" .
            " SET `$set_col` = '$set_val'" .
            " WHERE `$find_col` = '$find_val';";

        $this->query($query);
    }
}

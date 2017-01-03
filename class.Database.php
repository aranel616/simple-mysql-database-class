<?php
/*
 * Simple MySQL Database Class
 * Website: http://github.com/michaelday/simple-mysql-database-class/
 *
 * Copyright (C) 2012 by Michael Day
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class Database {
    private $server;
    private $username;
    private $password;
    private $database;

    protected $link;

    public function __construct($server, $username, $password, $database) {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        $this->connect();
    }

    public function __destruct() {
        mysql_close($this->link);
    }

    private function connect() {
        $this->link = mysql_connect($this->server, $this->username, $this->password);
        mysql_select_db($this->database, $this->link);
    }

    public function query($sql) {
        $sqlType = substr($sql, 0, 6);
        $getTypes = array_flip(array("SELECT"));
        $putTypes = array_flip(array("INSERT", "UPDATE", "DELETE", "TRUNCA"));

        if (isset($getTypes[$sqlType]))
            $queryType = "GET";
        else if (isset($putTypes[$sqlType]))
            $queryType = "PUT";
        else
            throw new Exception("Unable to determine query type in sql:\n\n$sql\n\n");

        $key = md5($sql);

        $resultArray = array();

        if ($queryType == "GET") {
            if (!$resultArray) {
                $result = mysql_query($sql, $this->link);

                $error = mysql_error($this->link);

                if (!empty($error)) {
                    throw new Exception("\n\nQuery Failed\n\n$sql\n\n$error\n\n");
                }

                while ($row = mysql_fetch_assoc($result)) {
                    $resultArray[] = $row;
                }

                mysql_free_result($result);

                if (substr($sql, -7) == 'LIMIT 1' && isset($resultArray[0]))
                    $resultArray = $resultArray[0];
            }

            return (count($resultArray) > 0) ? $resultArray : false;
        } else if ($queryType == "PUT") {
            mysql_query($sql, $this->link);

            $error = mysql_error($this->link);

            if (!empty($error))
                throw new Exception("\n\nQuery Failed\n\n$sql\n\n$error\n\n");
            
			$insertId = $this->insertId();
			    
            return (!empty($insertId)) ? $insertId : true;
        } else {
            throw new Exception("Unknown query type when preforming query: $sql");
        }
    }
    
    function insertId() {
        $insertId = mysql_insert_id($this->link);

        if ($insertId === 0)
            $insertId = false;

        return $insertId;
    }
}
?>

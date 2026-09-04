<?php
class DatabaseConnection
{
    function openConnection()
    {
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "university_lost_found_db";

        $conn = new mysqli($host, $username, $password, $database);

        if ($conn->connect_error) {
            die("Database connection failed");
        }

        return $conn;
    }
}
?>

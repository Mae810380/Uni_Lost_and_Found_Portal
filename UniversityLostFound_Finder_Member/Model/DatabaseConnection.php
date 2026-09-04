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
            die("Database connection failed. Please make sure MySQL is running in XAMPP and the university_lost_found_db database has been imported. Error: " . $conn->connect_error);
        }

        return $conn;
    }
}
?>

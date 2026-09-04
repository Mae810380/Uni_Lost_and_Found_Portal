<?php
class UserModel
{
    function registerUser($connection, $studentId, $name, $email, $password, $role)
    {
        $sql = "INSERT INTO university_accounts (student_id, name, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssss", $studentId, $name, $email, $password, $role);
        return $stmt->execute();
    }

    function findUser($connection, $studentId)
    {
        $sql = "SELECT * FROM university_accounts WHERE student_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    function getAllUsers($connection)
    {
        return $connection->query(
            "SELECT user_id, student_id, name, email, role, created_at
             FROM university_accounts ORDER BY user_id DESC"
        );
    }
}
?>

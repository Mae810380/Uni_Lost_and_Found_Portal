<?php
class ItemModel
{
    function addItem($connection, $userId, $type, $itemName, $category, $description, $location, $itemDate)
    {
        $sql = "INSERT INTO lost_found_records
                (user_id, item_type, item_name, category, description, location, item_date)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param(
            "issssss",
            $userId,
            $type,
            $itemName,
            $category,
            $description,
            $location,
            $itemDate
        );

        return $stmt->execute();
    }

    function getItems($connection, $keyword = "")
    {
        $keyword = "%" . $keyword . "%";

        $sql = "SELECT lost_found_records.*, university_accounts.name AS reporter_name, university_accounts.student_id
                FROM lost_found_records
                JOIN university_accounts ON lost_found_records.user_id = university_accounts.user_id
                WHERE lost_found_records.item_name LIKE ?
                   OR lost_found_records.category LIKE ?
                   OR lost_found_records.location LIKE ?
                ORDER BY lost_found_records.created_at DESC";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sss", $keyword, $keyword, $keyword);
        $stmt->execute();

        return $stmt->get_result();
    }

    function getMyItems($connection, $userId)
    {
        $sql = "SELECT * FROM lost_found_records
                WHERE user_id = ?
                ORDER BY created_at DESC";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result();
    }

    function getPendingItems($connection)
    {
        return $connection->query(
            "SELECT lost_found_records.*, university_accounts.name AS reporter_name, university_accounts.student_id
             FROM lost_found_records
             JOIN university_accounts ON lost_found_records.user_id = university_accounts.user_id
             WHERE lost_found_records.status = 'Pending'
             ORDER BY lost_found_records.created_at DESC"
        );
    }

    function getItemsReadyForReturn($connection)
    {
        $sql = "SELECT lost_found_records.*, university_accounts.student_id
                FROM lost_found_records
                JOIN claim_requests ON lost_found_records.item_id = claim_requests.item_id
                JOIN university_accounts ON claim_requests.user_id = university_accounts.user_id
                WHERE lost_found_records.status = 'Approved'
                  AND claim_requests.status = 'Approved'
                ORDER BY lost_found_records.created_at DESC";

        return $connection->query($sql);
    }

    function updateStatus($connection, $itemId, $status)
    {
        $sql = "UPDATE lost_found_records SET status = ? WHERE item_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $status, $itemId);

        return $stmt->execute();
    }

    function deleteItem($connection, $itemId)
    {
        $sql = "DELETE FROM lost_found_records WHERE item_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $itemId);

        return $stmt->execute();
    }

    function getStats($connection)
    {
        $stats = [];

        $result = $connection->query("SELECT COUNT(*) AS total FROM lost_found_records");
        $stats['total'] = $result->fetch_assoc()['total'];

        $result = $connection->query("SELECT COUNT(*) AS total FROM lost_found_records WHERE item_type = 'Lost'");
        $stats['lost'] = $result->fetch_assoc()['total'];

        $result = $connection->query("SELECT COUNT(*) AS total FROM lost_found_records WHERE item_type = 'Found'");
        $stats['found'] = $result->fetch_assoc()['total'];

        $result = $connection->query("SELECT COUNT(*) AS total FROM lost_found_records WHERE status = 'Returned'");
        $stats['returned'] = $result->fetch_assoc()['total'];

        return $stats;
    }
}
?>

<?php
class ClaimModel
{
    function createClaim($connection, $itemId, $userId, $message)
    {
        $sql = "INSERT INTO claim_requests (item_id, user_id, message)
                VALUES (?, ?, ?)";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iis", $itemId, $userId, $message);

        return $stmt->execute();
    }

    function getMyClaims($connection, $userId)
    {
        $sql = "SELECT claim_requests.*, lost_found_records.item_name, lost_found_records.status AS item_status
                FROM claim_requests
                JOIN lost_found_records ON claim_requests.item_id = lost_found_records.item_id
                WHERE claim_requests.user_id = ?
                ORDER BY claim_requests.created_at DESC";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result();
    }

    function getAllClaims($connection)
    {
        $sql = "SELECT claim_requests.*, lost_found_records.item_name, university_accounts.name, university_accounts.student_id
                FROM claim_requests
                JOIN lost_found_records ON claim_requests.item_id = lost_found_records.item_id
                JOIN university_accounts ON claim_requests.user_id = university_accounts.user_id
                ORDER BY claim_requests.created_at DESC";

        return $connection->query($sql);
    }

    function updateClaim($connection, $claimId, $status)
    {
        $sql = "UPDATE claim_requests SET status = ? WHERE claim_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $status, $claimId);

        return $stmt->execute();
    }

    function getApprovedClaimHistory($connection)
    {
        $sql = "SELECT claim_requests.claim_id, claim_requests.item_id,
                       claim_requests.created_at AS claim_date,
                       lost_found_records.item_name,
                       lost_found_records.item_type,
                       lost_found_records.status AS item_status,
                       claimer.student_id AS claimer_id,
                       claimer.name AS claimer_name
                FROM claim_requests
                JOIN lost_found_records
                  ON claim_requests.item_id = lost_found_records.item_id
                JOIN university_accounts AS claimer
                  ON claim_requests.user_id = claimer.user_id
                WHERE claim_requests.status = 'Approved'
                ORDER BY claim_requests.created_at DESC";

        return $connection->query($sql);
    }

}
?>

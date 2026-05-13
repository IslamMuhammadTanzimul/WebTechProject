<?php
// models/LoginModel.php

function getUserByUsernameOrEmail($conn, $identifier)
{
    $sql = "SELECT id, name, username, password_hash, role, is_active FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    $stmt->close();
    return false;
}
?>
<?php

function checkUserExists($conn, $username, $email)
{
    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return true;
    }

    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        return true;
    }

    $stmt->close();
    return false;
}

function registerUser($conn, $name, $username, $email, $password_hash, $dietary_json)
{
    $role = "user";
    $is_active = 1;
    $chef_verified = 0;

    $sql = "INSERT INTO users 
        (name, username, email, password_hash, role, dietary_prefs, is_active, chef_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ssssssii",
        $name,
        $username,
        $email,
        $password_hash,
        $role,
        $dietary_json,
        $is_active,
        $chef_verified
    );

    if ($stmt->execute()) {
        $new_user_id = $stmt->insert_id;
        $stmt->close();
        return $new_user_id;
    }

    $stmt->close();
    return false;
}
?>
<?php
session_start();
include 'config.php'; // Database connection

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle adding a new tag
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tag_name = mysqli_real_escape_string($conn, $_POST['tag_name']);

    // Check if the tag name already exists
    $check_sql = "SELECT * FROM tags WHERE name = '$tag_name'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Tag already exists.";
    } else {
        // Insert new tag
        $insert_sql = "INSERT INTO tags (name) VALUES ('$tag_name')";
        if (mysqli_query($conn, $insert_sql)) {
            $_SESSION['success'] = "Tag added successfully.";
        } else {
            $_SESSION['error'] = "Error adding tag.";
        }
    }
    header("Location: manage_communities.php");
    exit();
}
?>

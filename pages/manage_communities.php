
<?php


session_start();
include 'config.php'; // Database connection

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle tag deletion
if (isset($_GET['delete_tag_id'])) {
    $delete_tag_id = intval($_GET['delete_tag_id']);
    $delete_sql = "DELETE FROM tags WHERE id = $delete_tag_id";
    if (mysqli_query($conn, $delete_sql)) {
        $_SESSION['success'] = "Tag deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting tag.";
    }
    header("Location: manage_communities.php");
    exit();
}

// Fetch all tags
$sql = "SELECT * FROM tags";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/home.css">
    <title>Manage Tags</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }
        .delete-button {
            background-color: #FF5733;
        }
        .delete-button:hover {
            background-color: #C70039;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
    <a href="admin_home.php"><img src="../image/twitter.png" alt="Logo"></a>
    <ul>
        <li><a href="admin_home.php"><img src="../image/home (1).png" alt="">Home</a></li>
        <li><a href="manage_users.php"><img src="../image/user-gear.png" alt="">Manage Users</a></li>
        <li><a href="manage_communities.php"><img src="../image/users-alt.png" alt="">Manage Tags</a></li>
        <li><a href="manage_posts.php"><img src="../image/blog-text.png" alt="">Manage Posts</a></li>
        <li><a href="logout.php"><img src="../image/sign-out-alt (1).png" alt="">Logout</a></li>
    </ul>
</div>

    <!-- Main Content -->
    <div class="main">
        <h2>Manage Tags</h2>

        <!-- Display Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Tags Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tag Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tag = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $tag['id']; ?></td>
                        <td><?php echo htmlspecialchars($tag['name']); ?></td>
                        <td class="action-buttons">
                        <a href="manage_communities.php?delete_tag_id=<?php echo $tag['id']; ?>" 
                            class="delete-button" 
                            onclick="return confirm('Are you sure you want to delete this tag?');">Delete</a>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Add New Tag -->
        <h3>Add New Tag</h3>
        <form action="add_tag.php" method="POST">
            <label for="tag_name">Tag Name:</label>
            <input type="text" id="tag_name" name="tag_name" required>
            <button type="submit">Add Tag</button>
        </form>
    </div>
</body>
</html>

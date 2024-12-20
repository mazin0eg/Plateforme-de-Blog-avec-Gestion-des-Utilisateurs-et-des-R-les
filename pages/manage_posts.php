<?php
session_start();
include 'config.php'; // Database connection

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle post deletion
if (isset($_GET['delete_post_id'])) {
    $delete_post_id = intval($_GET['delete_post_id']);
    $delete_sql = "DELETE FROM posts WHERE id = $delete_post_id";
    if (mysqli_query($conn, $delete_sql)) {
        $_SESSION['success'] = "Post deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting post.";
    }
    header("Location: manage_posts.php");
    exit();
}

// Fetch all posts with user information
$sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.username 
        FROM posts 
        INNER JOIN users ON posts.user_id = users.id";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/home.css">
    <title>Manage Posts</title>
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
        <h2>Manage Posts</h2>

        <!-- Display Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Posts Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($post = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($post['content']); ?></td>
                        <td><?php echo htmlspecialchars($post['username']); ?></td>
                        <td><?php echo $post['created_at']; ?></td>
                        <td class="action-buttons">
                            <a href="manage_posts.php?delete_post_id=<?php echo $post['id']; ?>" 
                               class="delete-button" 
                               onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

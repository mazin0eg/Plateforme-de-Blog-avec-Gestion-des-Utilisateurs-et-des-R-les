<?php
session_start();
include 'config.php'; // Database connection

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); // Redirect if not logged in or not an admin
    exit();
}

// Fetch the total number of users
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = mysqli_query($conn, $sql_users);
$total_users = mysqli_fetch_assoc($result_users)['total_users'];

// Fetch the total number of communities (tags table used as communities)
$sql_communities = "SELECT COUNT(*) AS total_communities FROM tags";
$result_communities = mysqli_query($conn, $sql_communities);
$total_communities = mysqli_fetch_assoc($result_communities)['total_communities'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style/home.css">
  <title>Admin Dashboard</title>
  <style>
      .stats-box {
          background-color: #f9f9f9;
          border: 1px solid #ddd;
          padding: 20px;
          margin: 10px 0;
          border-radius: 8px;
          text-align: center;
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
        <h2>Welcome, Admin!</h2>
        
        <div class="dashboard-stats">
            <h3>Dashboard Overview</h3>
            <div class="stats-box">
                <h4>Total Users:</h4>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="stats-box">
                <h4>Total Communities:</h4>
                <p><?php echo $total_communities; ?></p>
            </div>
        </div>

        

    </div>

    <!-- Right Bar -->
    <div class="right-bar">
        <h3>Admin Tips</h3>
        <p>Ensure you review recent activity logs and manage users efficiently.</p>
    </div>

</body>
</html>

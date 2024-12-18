<?php
session_start();
include 'config.php'; // Database connection

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); // Redirect if not logged in or not an admin
    exit();
}

// Fetch admin-related data, e.g., number of users, recent activity, etc.
$sql = "SELECT COUNT(*) AS total_users FROM users";
$result = mysqli_query($conn, $sql);
$total_users = mysqli_fetch_assoc($result)['total_users'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style/home.css">
  <title>Admin Dashboard</title>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href=""><img src="../image/twitter.png" alt="Logo"></a>
        <ul>
            <li><a href="admin_home.php"><img src="../image/home (1).png" alt="">Dashboard</a></li>
            <li><a href="manage_users.php"><img src="../image/settings.png" alt="">Manage Users</a></li>
            <li><a href="reports.php"><img src="../image/report.png" alt="">Reports</a></li>
            <li><a href="logout.php"><img src="../image/logout.png" alt="">Logout</a></li>
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
            <!-- Add more widgets as necessary -->
            <div class="stats-box">
                <h4>Recent Activity:</h4>
                <p>See the latest user actions and system updates.</p>
            </div>
        </div>

        <div class="admin-actions">
            <h3>Quick Actions</h3>
            <div class="action-links">
                <a href="manage_users.php" class="button">Manage Users</a>
                <a href="reports.php" class="button">View Reports</a>
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

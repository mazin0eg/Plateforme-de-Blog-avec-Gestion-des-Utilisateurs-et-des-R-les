<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Get user role
$user_role = $_SESSION['user_role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style/home.css">
  <title>Home</title>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href=""><img src="../image/twitter.png" alt=""></a>
        <ul>
            <li><a href="#"><img src="../image/home (1).png" alt="">Home</a></li>
            <li><a href="#"><img src="../image/search.png" alt="">Explore</a></li>
            <li><a href="#"><img src="../image/bell.png" alt="">Notifications</a></li>
            <li><a href="#"><img src="../image/users.png" alt="">Communities</a></li>
            <li><a href="#"><img src="../image/user.png" alt="">Profile</a></li>
            <li><a href="#"><img src="../image/circle-ellipsis.png" alt="">More</a></li>

            <!-- Show admin-specific menu if user is an admin -->
            <?php if ($user_role === 'admin'): ?>
            <li><a href="manage_users.php"><img src="../image/settings.png" alt="">Manage Users</a></li>
            <li><a href="admin_dashboard.php"><img src="../image/dashboard.png" alt="">Admin Dashboard</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h2>

        <!-- Admin-specific content -->
        <?php if ($user_role === 'admin'): ?>
            <div class="admin-section">
                <h3>Admin Tools</h3>
                <p>Here you can manage users, view reports, and access the admin dashboard.</p>
                <a href="manage_users.php" class="button">Manage Users</a>
                <a href="admin_dashboard.php" class="button">Admin Dashboard</a>
            </div>
        <?php else: ?>
            <!-- User-specific content -->
            <div class="tweet-box">
                <textarea rows="3" placeholder="What’s happening?"></textarea>
                <button>Tweet</button>
            </div>

            <div class="tweets">
                <div class="tweet">
                    <h3>User Name</h3>
                    <p>This is a tweet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>
                <div class="tweet">
                    <h3>User Name</h3>
                    <p>Another tweet example. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Bar -->
    <div class="right-bar">
        <h3>Trends for you</h3>

        <div class="trends">
            <div class="trend">
                <h4>#TrendingTopic</h4>
                <p>12.4k Tweets</p>
            </div>
            <div class="trend">
                <h4>#AnotherTrend</h4>
                <p>8.1k Tweets</p>
            </div>
        </div>
    </div>
</body>
</html>

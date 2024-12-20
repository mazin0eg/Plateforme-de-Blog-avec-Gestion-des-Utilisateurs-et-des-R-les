<?php
session_start();

// Include the database connection file
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Get user role
$user_role = $_SESSION['user_role'];

// Fetch available tags from the database
$tags_query = "SELECT * FROM tags";
$tags_result = mysqli_query($conn, $tags_query);

// Process the form submission for creating a new post
if (isset($_POST['tweetButton'])) {  
    $title = mysqli_real_escape_string($conn, $_POST['tweettitle']);
    $content = mysqli_real_escape_string($conn, $_POST['tweetContent']);
    $tags = $_POST['postTags']; // Array of selected tag IDs
    
    if (!empty($title) && !empty($content) && !empty($tags)) {
        $email = $_SESSION['user_email'];
        $sql1 = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql1);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $user_id = $row['id'];
            
            // Insert the post
            $sql = "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'iss', $user_id, $title, $content);
            if (mysqli_stmt_execute($stmt)) {
                $post_id = mysqli_insert_id($conn); // Get the ID of the newly inserted post
                
                // Insert tags into post_tags
                $tag_sql = "INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $tag_sql);
                foreach ($tags as $tag_id) {
                    mysqli_stmt_bind_param($stmt, 'ii', $post_id, $tag_id);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
    }
}

// Process the form submission for deleting a post
if (isset($_POST['deletePost'])) {
    $post_id = $_POST['post_id'];
    $sql = "DELETE FROM posts WHERE id = ? AND user_id = (SELECT id FROM users WHERE email = ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $post_id, $_SESSION['user_email']);
    mysqli_stmt_execute($stmt);
}

// Handle liking a post
if (isset($_POST['likeButton'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has already liked the post
    $check_like_query = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = mysqli_prepare($conn, $check_like_query);
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // If the user hasn't liked the post yet, add the like
    if (mysqli_num_rows($result) == 0) {
        $like_query = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $like_query);
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $post_id);
        mysqli_stmt_execute($stmt);
    }
}

// Handle unliking a post
if (isset($_POST['unlikeButton'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Remove the like from the likes table
    $unlike_query = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = mysqli_prepare($conn, $unlike_query);
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $post_id);
    mysqli_stmt_execute($stmt);
}

// Fetch all posts with the count of likes
$posts_query = "
    SELECT posts.*, users.username, GROUP_CONCAT(tags.name SEPARATOR ', ') AS tag_names, 
           (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count
    FROM posts
    JOIN users ON posts.user_id = users.id
    LEFT JOIN post_tags ON posts.id = post_tags.post_id
    LEFT JOIN tags ON post_tags.tag_id = tags.id
    GROUP BY posts.id
    ORDER BY posts.created_at DESC
";

$posts_result = mysqli_query($conn, $posts_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/home.css">
    <title>Home</title>
    <style>
        /* Modify the tweet container */
.tweet {
    padding: 20px;
    border: 1px solid #ddd;
    margin-bottom: 10px;
    border-radius: 5px;
    position: relative;
}

/* Modify the menu container to position it at the top right */
.menu {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

.menu-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
    border-radius: 4px;
}

.menu-content button {
    color: black;
    padding: 8px 12px;
    text-decoration: none;
    display: block;
    border: none;
    background: none;
    cursor: pointer;
    width: 100%;
    text-align: left;
}

.menu-content button:hover {
    background-color: #ddd;
}

.menu-button {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
}

/* Hover effect */
.menu:hover .menu-content {
    display: block;
}

/* Make the menu responsive */
@media (max-width: 768px) {
    /* Adjust position for smaller screens */
    .menu {
        top: 5px;
        right: 5px;
    }
    
    .tweet {
        padding: 15px;
    }

    /* Reduce font size of the menu button */
    .menu-button {
        font-size: 16px;
    }
}

    </style>
</head>
<body>
    <div class="sidebar">
        <a href=""><img src="../image/twitter.png" alt=""></a>
        <ul>
            <li><a href="#"><img src="../image/home (1).png" alt="">Home</a></li>
            <li><a href="#"><img src="../image/search.png" alt="">Explore</a></li>
            <li><a href="#"><img src="../image/bell.png" alt="">Notifications</a></li>
            <li><a href="#"><img src="../image/users.png" alt="">Communities</a></li>
            <li><a href="#"><img src="../image/sign-out-alt (1).png" alt="">Logout</a></li>
            <li><a href="#"><img src="../image/circle-ellipsis.png" alt="">More</a></li>     
        </ul>
    </div>

    <div class="main">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h2>

        <div class="tweet-box">
            <form method="POST" action="home.php">
                <textarea name="tweettitle" rows="3" placeholder="What’s the topic?"></textarea>
                <textarea name="tweetContent" rows="3" placeholder="What’s happening?"></textarea>
                <label for="postTags">Select Tags:</label>
                <select name="postTags[]" id="postTags" required >
                    <?php while ($tag = mysqli_fetch_assoc($tags_result)) : ?>
                        <option value="<?php echo htmlspecialchars($tag['id']); ?>">
                            <?php echo htmlspecialchars($tag['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="tweetButton">Tweet</button>
            </form>
        </div>

        <div class="tweets">
            <?php if (mysqli_num_rows($posts_result) > 0) : ?>
                <?php while ($post = mysqli_fetch_assoc($posts_result)) : ?>
                    <div class='tweet'>
                        <h3><?php echo htmlspecialchars($post['username']); ?> - <?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo htmlspecialchars($post['content']); ?></p>
                        <p><strong>#</strong><?php echo htmlspecialchars($post['tag_names'] ?? 'No tags'); ?></p>
                        <small>Posted on: <?php echo htmlspecialchars($post['created_at']); ?></small>
                        
                        <!-- Likes -->
                        <p><strong>Likes:</strong> <?php echo htmlspecialchars($post['like_count']); ?></p>

                        <!-- Like/Unlike button -->
                        <?php 
                            // Check if the current user has already liked this post
                            $liked_query = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
                            $stmt = mysqli_prepare($conn, $liked_query);
                            mysqli_stmt_bind_param($stmt, 'ii', $_SESSION['user_id'], $post['id']);
                            mysqli_stmt_execute($stmt);
                            $liked_result = mysqli_stmt_get_result($stmt);
                            $liked = mysqli_num_rows($liked_result) > 0;
                        ?>

                        <form method="POST" action="home.php">
                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                            <?php if ($liked) : ?>
                                <button type="submit" name="unlikeButton" style="color: red;">Unlike</button>
                            <?php else : ?>
                                <button type="submit" name="likeButton" style="color: blue;">Like</button>
                            <?php endif; ?>
                        </form>

                        <!-- Delete post menu -->
                        <?php if ($post['user_id'] == $_SESSION['user_id']) : ?>
                            <div class="menu">
                                <button class="menu-button">⋮</button>
                                <div class="menu-content">
                                    <form method="POST" action="home.php">
                                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                                        <button type="submit" name="deletePost" style="background-color: #f44336; color: white; border: none; padding: 8px 12px; cursor: pointer; border-radius: 4px; width: 100px;">
                                            Delete Post
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </div>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuButtons = document.querySelectorAll('.menu-button');
            
            menuButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const menuContent = this.nextElementSibling;
                    menuContent.style.display = menuContent.style.display === 'block' ? 'none' : 'block';
                });
            });

            document.addEventListener('click', function () {
                const menus = document.querySelectorAll('.menu-content');
                menus.forEach(menu => menu.style.display = 'none');
            });
        });
    </script>
</body>
</html>

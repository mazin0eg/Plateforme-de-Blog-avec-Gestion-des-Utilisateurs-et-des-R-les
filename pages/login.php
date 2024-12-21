<?php
include 'config.php'; // Include the database connection

session_start(); // Start the session to store user data

// Check if the login form is submitted
if (isset($_POST['user-login'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['user-email']));
    $user_password = mysqli_real_escape_string($conn, trim($_POST['user_password']));

    // Query to check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($user_password, $user['password'])) {
          // Password is correct, login successful
          $_SESSION['user_id'] = $user['id']; // Add this line
          $_SESSION['user_email'] = $user['email'];
          $_SESSION['user_role'] = ($user['role_id'] == 1) ? 'admin' : 'user'; // Map role_id to 'admin' or 'user'
      
          // Redirect based on the user role
          if ($_SESSION['user_role'] === 'admin') {
              header("Location: admin_home.php"); // Redirect admin to the dashboard
          } else {
              header("Location: home.php"); // Redirect regular users to the home page
          }
          exit();
      }
       else {
            // Invalid password
            $_SESSION['error'] = "Incorrect password. Please try again.";
        }
    } else {
        // Email not found
        $_SESSION['error'] = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style/login.css">
  <title>Login</title>
</head>
<body>
  <div class="container">
    <input type="checkbox" id="check">
    <div class="login form">
      <header>Login</header>

      <!-- Display error messages -->
      <?php if (isset($_SESSION['error'])): ?>
        <div class="error">
          <?php 
            echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']); // Clear error after displaying it
          ?>
        </div>
      <?php endif; ?>

      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <input type="email" id="user-email" name="user-email" placeholder="Enter your email" required>
        <input type="password" id="user_password" name="user_password" placeholder="Enter your password" required>
        <input type="submit" class="button" name="user-login" value="Login">
      </form>
      <div class="signup">
        <span class="signup">Don't have an account?
         <a href="register.php">Signup</a>
        </span>
      </div>
    </div>
  </div>
</body>
</html>

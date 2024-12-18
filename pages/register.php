<?php
include 'config.php'; // Ensure this file has a valid $conn connection to the database

if (isset($_POST['user-register'])) {  

    // Collect and sanitize user inputs
    $email = mysqli_real_escape_string($conn, $_POST['user-email']);
    $password1 = mysqli_real_escape_string($conn, $_POST['user_password1']);
    $password2 = mysqli_real_escape_string($conn, $_POST['user_password2']);

    // Check if passwords match
    if ($password1 === $password2) {
        $user_password = password_hash($password1, PASSWORD_BCRYPT); // Encrypt password

        // Generate username from email
        function generateUsername($email) {
            $username = strstr($email, '@', true); // Extract before '@'
            return '@' . $username;
        }
        $username = generateUsername($email);

        // Insert user into the database if email is not empty
        if (!empty($email) && !empty($password1)) {
            $sql = "INSERT INTO users (id, username, email, password) 
                    VALUES (NULL, '$username', '$email', '$user_password')";
            
            if (mysqli_query($conn, $sql)) {
                echo "Registration successful! Your username is: $username";
                header("Location: login.php");
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Please fill in all fields.";
        }
    } else {
        echo "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/login.css">
    <title>Login & Registration Form</title>
</head>
<body>
    <div class="container">
        <input type="checkbox" id="check">
        <div class="registration form">
            <header>Signup</header>
            <form action="" method="POST">
                <input type="text" id="user-email" name="user-email" placeholder="Enter your email" required>
                <input type="password" id="user_password1" name="user_password1" placeholder="Enter your password" required>
                <input type="password" id="user_password2" name="user_password2" placeholder="Confirm your password" required>
                <input type="submit" class="button" name="user-register" value="Register">
            </form>
            <div class="signup">
                <span class="signup">Already have an account?
                
                    <a href="login.php">Login</a>
                </span>
            </div>
        </div>
    </div>
</body>
</html>

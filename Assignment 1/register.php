<?php
// register.php

require_once 'config.php';
require_once 'functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    global $conn;
    $username = mysqli_real_escape_string($_POST['username'], $conn);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Vulnerability: Weak Password Policy
    // There are no requirements for password strength
    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 1) {
        $error = "Password cannot be empty";
    } else{
        //Added a thing for setting the password strength and returning it to the user for display. 
        $password_strength = check_password_strength($password);
        if ($password_strength !== true) {
            $error = $password_strength; // Display the error message from the check
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                redirect('index.php');
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register - Simple Blog</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Register</h1>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </body>
</html>
<?php
// create_post.php

require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Vulnerability: CSRF
    // There's no CSRF token check here, making this form vulnerable to CSRF attacks
    
    //Checks for csrf validation token before beginning. 
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }


    if (empty($title) || empty($content)) {
        $error = "Both title and content are required.";
    } else {
        if (create_post($title, $content, $user_id)) {
            $success = "Post created successfully!";
            // Rotate CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } else {
            $error = "Failed to create post. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - Simple Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Create a New Post</h1>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>

        <button type="submit">Create Post</button>
    </form>
    <p><a href="index.php">Back to Home</a></p>
</body>
</html>
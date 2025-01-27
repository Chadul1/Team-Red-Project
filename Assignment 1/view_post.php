<?php
// view_post.php

require_once 'config.php';
require_once 'functions.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    redirect('index.php');
}

$post_id = $_GET['id'];
$post = get_post_by_id($post_id);
if (!$post) {
    die("Post not found.");
}


// Vulnerability: Insecure Direct Object Reference
// There's no check to see if the current user should have access to this post
// For example, if it's a draft post by another user

// Check if the user has access to the post
//An easy way to check for these things is to see if the private toggle is set by it being a draft/private & and the user is equal to the user of the post.  
if ($post['is_private'] && $post['user_id'] !== $_SESSION['user_id']) {
    die("You do not have permission to access this post.");
}



$comments = get_comments_by_post_id($post_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_logged_in()) {
    $comment_content = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    } 

    if (!empty($comment_content)) {
        add_comment($post_id, $user_id, $comment_content);

        // After a successful form submission
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        // Redirect to prevent duplicate submission on page refresh
        redirect("view_post.php?id=$post_id");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Simple Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p>By <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></p>
    
    <!-- Vulnerability: XSS -->
    <!-- The post content is not escaped, allowing for potential XSS attacks -->

     <!-- properly escapes the content to avoid malicious XSS Attacks --> 
    <div class="post-content"><?php echo htmlspecialchars($post['content']); ?></div>

    <h2>Comments</h2>
    <?php if (empty($comments)): ?>
        <p>No comments yet.</p>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong> on <?php echo $comment['created_at']; ?></p>
                <!-- Vulnerability: XSS -->
                <!-- The comment content is not escaped, allowing for potential XSS attacks -->

                <!-- properly escapes the content to avoid malicious XSS Attacks --> 
                <p><?php echo htmlspecialchars($comment['content']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (is_logged_in()): ?>
        <h3>Add a Comment</h3>
        <form method="POST" action="">
            <!-- Include the token in the input for the validation. -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <textarea name="comment" required></textarea>
            <button type="submit">Submit Comment</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Log in</a> to add a comment.</p>
    <?php endif; ?>

    <p><a href="index.php">Back to Home</a></p>
</body>
</html>



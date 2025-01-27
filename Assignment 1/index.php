<?php
// index.php

require_once 'config.php';
require_once 'functions.php';

// Fetch all blog posts
$posts = get_all_posts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Simple Blog</h1>
        <nav>
            <?php if (is_logged_in()): ?>
                <a href="create_post.php">Create Post</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php foreach ($posts as $post): ?>
            <article>
                <h2><a href="view_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                <p>By <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></p>
                <!-- Vulnerability: XSS -->
                <!-- The content is not properly escaped, allowing for potential XSS attacks -->

                <!-- By introducing an htmlspecialchars() function before the code is injected, you are able to clean the input to avoid XSS injections.-->
                <p><?php echo htmlspecialchars(substr($post['content'], 0, 200)); ?>...</p>
            </article>
        <?php endforeach; ?>
    </main>

    <footer>
        <p>&copy; 2023 Simple Blog</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
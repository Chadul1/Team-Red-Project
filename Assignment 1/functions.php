<?php
// functions.php

// Function to check if a user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to redirect user
function redirect($location) {
    header("Location: $location");
    exit;
}

// Function to display error messages
// Vulnerability: Sensitive Data Exposure
// This function may reveal too much information about the system's internals
function display_error() {
    //make the error generic and log the real elsewhere for viewing that isn't front facing. (E.g, a database for user errors).
    echo "<div class='error'An error occurred. Please try again later.</div>";
}

// Function to get user by ID
// Vulnerability: SQL Injection
// This function is vulnerable to SQL injection attacks

//Add a parameter to remove the possibility of injection.  
function get_user_by_id($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to create a new blog post
// Vulnerability: No CSRF protection

//By checking in the page before this is called
function create_post($title, $content, $user_id) {
    global $conn;
    $title = htmlspecialchars($title);
    $content = htmlspecialchars($content);
    $user_id = (int)$user_id;
    
    $query = "INSERT INTO posts (title, content, user_id) VALUES ('$title', '$content', $user_id)";
    return mysqli_query($conn, $query);
}

// Function to get all blog posts
function get_all_posts() {
    global $conn;
    $query = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to get a single post by ID
// Vulnerability: Insecure Direct Object Reference
// This function doesn't check if the user has permission to view the post
function get_post_by_id($post_id) {
    global $conn;
    $post_id = (int)$post_id;
    $stmt = $conn->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?" );
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to add a comment to a post
function add_comment($post_id, $user_id, $content) {
    global $conn;
    $post_id = (int)$post_id;
    $user_id = (int)$user_id;
    $content = htmlspecialchars($content);
    
    $query = "INSERT INTO comments (post_id, user_id, content) VALUES ($post_id, $user_id, '$content')";
    return mysqli_query($conn, $query);
}

// Function to get comments for a post
function get_comments_by_post_id($post_id) {
    global $conn;
    $post_id = (int)$post_id;
    $query = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = $post_id ORDER BY created_at ASC";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

//checks password
function check_password_strength($password) {
    // Check if password is empty
    if (strlen($password) < 1) {
        return "Password cannot be empty";
    }
    
    // Check password length (at least 8 characters)
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long";
    }

    // Check if password contains at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter";
    }

    // Check if password contains at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter";
    }

    // Check if password contains at least one number
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number";
    }

    // Check if password contains at least one special character
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        return "Password must contain at least one special character";
    }

    // Check for common patterns (e.g., "12345", "password", etc.)
    $common_patterns = ['password', '12345', 'qwerty', 'abc123'];
    foreach ($common_patterns as $pattern) {
        if (strpos(strtolower($password), $pattern) !== false) {
            return "Password is too weak and contains a common pattern";
        }
    }

    return true; // Password meets all strength requirements
}

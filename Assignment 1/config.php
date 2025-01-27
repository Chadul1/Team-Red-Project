<?php
// config.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'simple_blog');

// Attempt to connect to the database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if (!$conn) {
    // Error: Sensitive data exposure vulnerability
    // In a production environment, we should not expose detailed error messages

    //Better to use a generic message for front facing issue if needed, otherwise, store errors in a database or some sort of txt doc. 
    die("Connection failed: Error with connecting to the database");
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");

// Start session
session_start();
// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

//Makes the code a little nicer and more secure.
session_set_cookie_params([
    'samesite' => 'Strict',  
    'secure' => true,        
    'httponly' => true       
]);

//Removed the sanitize function, trying to create a function to handle a bunch of unique cases is a bit of waste of time. 
//Best to handle sanitization on each basis. Otherwise, probably just have a switch statement function that takes different types into account. 
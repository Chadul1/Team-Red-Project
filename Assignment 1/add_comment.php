<?php
// add_comment.php

require_once 'config.php';
require_once 'functions.php';

if (!is_logged_in()) {
    die("You must be logged in to add a comment.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Vulnerability: XSS
    // The content is not properly sanitized, potentially allowing XSS attacks
    //Clean the inputs properly. 
    $post_id = isset($_POST['post_id']) ? filter_var($_POST['post_id'], FILTER_VALIDATE_INT) : 0;
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    $content = strip_tags($content);
    if (empty($content_clean)){
        die("Content is not clean.");
    }

    $user_id = isset($_SESSION['user_id']) ? filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT) : 0;
    
    // Vulnerability: CSRF
    // There's no CSRF token check here, making this endpoint vulnerable to CSRF attacks
    //check if the current token is equal to the inputted one. 
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF Token Validation Failed.');
    }
    
    if ($post_id && $content) {
        if (add_comment($post_id, $user_id, $content)) {
            redirect("view_post.php?id=$post_id");
        } else {
            die("Error adding comment.");
        }
    } else {
        die("Invalid input.");
    }
} else {
    die("Invalid request method.");
}
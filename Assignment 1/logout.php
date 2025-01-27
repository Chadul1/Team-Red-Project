<?php
// logout.php

require_once 'config.php';
require_once 'functions.php';

// Vulnerability: CSRF
// There's no CSRF protection for the logout action
if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF Token Validation Failed.');
}

// Destroy the session, deletes the old session and destroys, just to be sure. 
session_regenerate_id(true);
session_destroy();

// Redirect to the home page
redirect('index.php');
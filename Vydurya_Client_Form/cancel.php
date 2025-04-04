<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to Basic_information.php
header("Location: Basic_information.php");
exit();
?>
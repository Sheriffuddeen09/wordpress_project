<?php
session_start();  // Start session

if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = "Session is working!";
} else {
    echo "Session exists: " . $_SESSION['test'];
    exit;
}

echo "New session started!";
?>

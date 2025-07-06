<?php 
// Start output buffering for better performance
ob_start();

// Enable gzip compression
if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
}

include "db.php";

// Set performance and caching headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Cache static resources for 1 hour
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '.css') !== false) {
    header('Cache-Control: public, max-age=3600');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PHP CRUD System - Manage users efficiently">
    
    <!-- Preload critical CSS for faster rendering -->
    <link rel="preload" href="includes/style.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="includes/style.css"></noscript>
    
    <!-- Optimized CSS loading -->
    <link rel="stylesheet" href="includes/style.css">
    
    <title>PHP CRUD System</title>
</head>
<body>
    

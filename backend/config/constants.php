<?php
// -------------------------------
// Database Settings (read from environment when available)
// -------------------------------
define("DB_HOST", getenv('DB_HOST') ? getenv('DB_HOST') : '127.0.0.1');
define("DB_USER", getenv('DB_USER') ? getenv('DB_USER') : 'root');
define("DB_PASS", getenv('DB_PASS') ? getenv('DB_PASS') : '');
define("DB_NAME", getenv('DB_NAME') ? getenv('DB_NAME') : 'love_bumble');

// -------------------------------
// Site Settings
// -------------------------------
define("SITE_NAME", "Love Bumble");
define("SITE_URL", "http://localhost/love_bumble"); // Change to your domain or localhost
define("UPLOAD_DIR", "uploads/"); // Profile pictures, etc.

// -------------------------------
// Chat Types
// -------------------------------
define("CHAT_GROUP", "group");
define("CHAT_PRIVATE", "private");

// -------------------------------
// Age Settings
// -------------------------------
define("MINIMUM_AGE", 18);

// -------------------------------
// Profanity Filter (basic list)
// -------------------------------
define("PROFANE_WORDS", serialize([
    "badword1",
    "badword2",
    "example1",
    "example2"
]));

// -------------------------------
// Other Constants
// -------------------------------
define("DEFAULT_PROFILE_PIC", "uploads/default.png"); // Default user profile picture
?>

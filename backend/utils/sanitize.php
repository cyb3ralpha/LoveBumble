<?php
// -------------------------------
// Sanitize a string (remove HTML tags, trim spaces)
// -------------------------------
function sanitize_string($string) {
    $string = trim($string);                  // Remove spaces at start/end
    $string = stripslashes($string);          // Remove backslashes
    $string = htmlspecialchars($string);      // Convert HTML special chars to entities
    return $string;
}

// -------------------------------
// Sanitize email
// -------------------------------
function sanitize_email($email) {
    $email = trim($email);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return $email;
}

// -------------------------------
// Sanitize integer (IDs, numeric input)
// -------------------------------
function sanitize_int($number) {
    return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
}

// -------------------------------
// Sanitize URL
// -------------------------------
function sanitize_url($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

// -------------------------------
// Validate email format
// -------------------------------
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// -------------------------------
// Validate age (18+)
// -------------------------------
function validate_age($dob) {
    $dobDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dobDate)->y;
    return $age >= 18;
}
?>

<?php
session_start();
require_once 'config.php'; // Database connection

$error = "";
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $dob = trim($_POST['dob']); // Date of birth YYYY-MM-DD
    $age = (int)((time() - strtotime($dob)) / (365.25*24*60*60));

    // Basic validations
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password) || empty($dob)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif ($age < 18) {
        $error = "You must be at least 18 years old to register.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Handle profile picture upload
            $profile_picture = NULL;
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
                $file_ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
                if (in_array($file_ext, $allowed_ext)) {
                    $file_name = uniqid() . '.' . $file_ext;
                    $upload_dir = 'uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $file_name);
                    $profile_picture = $upload_dir . $file_name;
                } else {
                    $error = "Invalid profile picture format.";
                }
            }

            if (empty($error)) {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert user into database
                $stmt_insert = $conn->prepare("INSERT INTO users (full_name, email, password, dob, profile_picture) VALUES (?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("sssss", $full_name, $email, $hashed_password, $dob, $profile_picture);
                if ($stmt_insert->execute()) {
                    $_SESSION['user_id'] = $stmt_insert->insert_id;
                    $_SESSION['full_name'] = $full_name;
                    header("Location: dashboard.html");
                    exit();
                } else {
                    $error = "Registration failed. Please try again.";
                }
                $stmt_insert->close();
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register | Love Bumble</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #ff416c, #ff4b2b);
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    background: #fff;
    color: #333;
    padding: 30px;
    border-radius: 12px;
    width: 360px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    text-align: center;
}

h2 {
    color: #ff416c;
    margin-bottom: 20px;
}

input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="file"] {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border-radius: 6px;
    border: 1px solid #ddd;
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    background: #ff416c;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
}

.error {
    color: red;
    font-size: 13px;
    margin-top: 10px;
}

a {
    color: #ff416c;
    text-decoration: none;
}
</style>
</head>
<body>

<div class="container">
    <h2>Create Your Love Bumble Account</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <input type="date" name="dob" placeholder="Date of Birth" required>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit">Register</button>
        <?php if(!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>

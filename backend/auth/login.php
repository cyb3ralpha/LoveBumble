<?php
session_start();
require_once 'config.php'; // DB connection

// Initialize variables
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // Prepare SQL to prevent SQL Injection
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];

                // Redirect to dashboard
                header("Location: dashboard.html");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
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
<title>Login | Love Bumble</title>
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
    height: 100vh;
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

input[type="email"], input[type="password"] {
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
</style>
</head>
<body>

<div class="container">
    <h2>Login to Love Bumble</h2>
    <form method="post" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <?php if(!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
    </form>
    <p>Don't have an account? <a href="register.html">Register here</a></p>
</div>

</body>
</html>

<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $admin_id = "adminid";
    $admin_password = "12345";

    $input_id = $_POST['admin_id'] ?? '';
    $input_pass = $_POST['admin_password'] ?? '';

    if ($input_id === $admin_id && $input_pass === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid Admin ID or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GreenCircle Admin Login</title>
    <style>
        body {
            background: #072808;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: #1b3a1a;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            width: 320px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-family: 'Cinzel', serif;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: none;
            border-radius: 8px;
            background: #d0edd3;
            color: #1b3a1a;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #a9d18e;
            color: #072808;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background: #8bc57f;
        }
        .error {
            color: #f8d7da;
            background: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>GreenCircle Admin Login</h2>
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="admin_id" placeholder="Admin ID" required>
        <input type="password" name="admin_password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>

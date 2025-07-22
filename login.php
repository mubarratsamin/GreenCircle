<?php
session_start();
include('db_connect.php');

$email = $password = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $error = "Please enter both email and password.";
    } else {
        // Prepare query to get user by email
        $stmt = $conn->prepare("SELECT id, user_name, role, password FROM users WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $user_name, $role, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Password matches, set session and redirect
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_role'] = $role;

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Email not registered.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>GreenCircle - Login</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f7f6;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    color: #333;
  }
  .container {
    background: white;
    padding: 40px 35px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    width: 400px;
  }
  h2 {
    color: #2E7D32;
    margin-bottom: 25px;
    text-align: center;
  }
  form {
    display: flex;
    flex-direction: column;
  }
  label {
    font-weight: 600;
    margin-bottom: 6px;
    font-size: 14px;
  }
  input {
    padding: 12px 10px;
    margin-bottom: 18px;
    border: 1.8px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s ease;
  }
  input:focus {
    border-color: #2E7D32;
    outline: none;
  }
  .btn-submit {
    background: #2E7D32;
    color: white;
    border: none;
    padding: 14px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  .btn-submit:hover {
    background: #256429;
  }
  .error {
    background: #f8d7da;
    color: #721c24;
    border: 1.5px solid #f5c6cb;
    padding: 10px 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    text-align: center;
  }
  .back-link {
    text-align: center;
    font-size: 14px;
  }
  .back-link a {
    color: #2E7D32;
    text-decoration: none;
    font-weight: 600;
  }
  .back-link a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>

<div class="container">
  <h2>Login to GreenCircle</h2>

  <?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="post" action="">
    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>" placeholder="example@mail.com">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required placeholder="Enter your password">

    <button type="submit" class="btn-submit">Login</button>
  </form>

  <div class="back-link">
    Don't have an account? <a href="register.php">Register here</a>
  </div>
</div>

</body>
</html>

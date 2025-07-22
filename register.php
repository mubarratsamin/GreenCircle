<?php
session_start();
include('db_connect.php');

$name = $email = $phone = $address = $role = "";
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function clean($data) {
        return htmlspecialchars(trim($data));
    }

    $name = clean($_POST['name']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $address = clean($_POST['address']);
    $role = clean($_POST['role']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (!$name) $errors[] = "Name is required.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!$phone) $errors[] = "Phone number is required.";
    if (!$address) $errors[] = "Address is required.";
    if (!$role) $errors[] = "Role selection is required.";
    if (!$password || strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed (email check): (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already registered.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Begin transaction for role_id assignment
        $conn->begin_transaction();

        try {
            // Prepare SELECT FOR UPDATE with error check
            $stmt = $conn->prepare("SELECT last_id FROM role_counters WHERE role = ? FOR UPDATE");
            if (!$stmt) {
                throw new Exception("Prepare failed (SELECT FOR UPDATE): (" . $conn->errno . ") " . $conn->error);
            }
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $stmt->bind_result($last_id);
            $found = $stmt->fetch();
            $stmt->close();

            if (!$found) {
                // No counter for this role, insert one with 0 and set last_id = 0
                $stmt = $conn->prepare("INSERT INTO role_counters (role, last_id) VALUES (?, 0)");
                if (!$stmt) {
                    throw new Exception("Prepare failed (INSERT role_counters): (" . $conn->errno . ") " . $conn->error);
                }
                $stmt->bind_param("s", $role);
                $stmt->execute();
                $stmt->close();
                $last_id = 0;
            }

            $new_role_id = $last_id + 1;

            // Update last_id in role_counters
            $stmt = $conn->prepare("UPDATE role_counters SET last_id = ? WHERE role = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed (UPDATE role_counters): (" . $conn->errno . ") " . $conn->error);
            }
            $stmt->bind_param("is", $new_role_id, $role);
            $stmt->execute();
            $stmt->close();

            // Insert new user with role_id and user_name column
            $stmt = $conn->prepare("INSERT INTO users (user_name, email, phone, address, role, password, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed (INSERT user): (" . $conn->errno . ") " . $conn->error);
            }
            $stmt->bind_param("ssssssi", $name, $email, $phone, $address, $role, $hashed_password, $new_role_id);
            $stmt->execute();
            $stmt->close();

            // Commit transaction
            $conn->commit();

            $success = "Registration successful! Your role-specific ID is: <strong>" . htmlspecialchars($role) . " #" . $new_role_id . "</strong>. <a href='login.php'>Login here</a>.";
            $name = $email = $phone = $address = $role = "";

        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>GreenCircle - Register</title>
<style>
    body {
        margin: 0; padding: 0;
        font-family: 'Segoe UI', Roboto, sans-serif;
        background: #f4f7f6;
        height: 100vh;
        display: flex; align-items: center; justify-content: center;
        color: #333;
    }
    .container {
        background: #fff;
        padding: 40px 35px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        width: 420px;
    }
    h2 {
        color: #2E7D32;
        margin-bottom: 25px;
        text-align: center;
    }
    form {
        display: flex; flex-direction: column;
    }
    label {
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 14px;
    }
    input, select {
        padding: 12px 10px;
        margin-bottom: 18px;
        border: 1.8px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        transition: border-color 0.3s ease;
    }
    input:focus, select:focus {
        border-color: #2E7D32;
        outline: none;
    }
    .btn-submit {
        background: #2E7D32;
        color: #fff;
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
    .back-link {
        margin-top: 18px;
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
    .error {
        background: #f8d7da;
        color: #721c24;
        border: 1.5px solid #f5c6cb;
        padding: 10px 12px;
        margin-bottom: 20px;
        border-radius: 8px;
    }
    .success {
        background: #d4edda;
        color: #155724;
        border: 1.5px solid #c3e6cb;
        padding: 10px 12px;
        margin-bottom: 20px;
        border-radius: 8px;
    }
    @media (max-width: 480px) {
        .container {
            width: 90%;
            padding: 30px 20px;
        }
    }
</style>
</head>
<body>

<div class="container">
    <h2>Create Your GreenCircle Account</h2>

    <?php if ($errors): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $e): ?>
                <li><?php echo $e ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" action="" novalidate>
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name) ?>" placeholder="Your full name">

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email) ?>" placeholder="example@mail.com">

        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($phone) ?>" placeholder="+8801XXXXXXXXX" pattern="^\+?\d{10,15}$" title="Enter valid phone number">

        <label for="address">Address</label>
        <input type="text" id="address" name="address" required value="<?php echo htmlspecialchars($address) ?>" placeholder="Your address">

        <label for="role">Select Role</label>
        <select id="role" name="role" required>
            <option value="" disabled <?php if(!$role) echo 'selected'; ?>>Select your role</option>
            <option value="Landlord" <?php if($role=='Landlord') echo 'selected'; ?>>Landlord</option>
            <option value="Farmer" <?php if($role=='Farmer') echo 'selected'; ?>>Farmer</option>
            <option value="Technician" <?php if($role=='Technician') echo 'selected'; ?>>Technician</option>
            <option value="Equipment Provider" <?php if($role=='Equipment Provider') echo 'selected'; ?>>Equipment Provider</option>
        </select>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="6" placeholder="Minimum 6 characters">

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Re-enter your password">

        <button type="submit" class="btn-submit">Register</button>
    </form>

    <div class="back-link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

<script>
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const pass = document.getElementById('password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        if(pass !== confirmPass) {
            alert('Passwords do not match!');
            e.preventDefault();
        }
    });
</script>

</body>
</html>

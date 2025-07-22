<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);

    // Prevent admin from deleting themselves
    if ($_SESSION['admin_logged_in'] == $user_id) {
        $message = "You cannot delete your own admin account.";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "User deleted successfully.";
        } else {
            $message = "Error deleting user.";
        }
        $stmt->close();
    }
}

// Fetch Users with proper columns
$result = $conn->query("SELECT id, user_name, email, role FROM users ORDER BY id DESC");

if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Users - GreenCircle Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #e8f5e9;
            margin: 0;
        }
        header {
            background: #072808;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-family: 'Cinzel', serif;
            font-size: 28px;
            font-weight: bold;
        }
        nav a {
            color: #a9d18e;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 600;
        }
        nav a:hover {
            color: #d0edd3;
        }
        main {
            max-width: 1100px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }
        h1 {
            font-family: 'Cinzel', serif;
            font-size: 32px;
            margin-bottom: 20px;
            color: #2e7d32;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ccc;
            text-align: center;
        }
        table th {
            background: #c8e6c9;
            color: #1b5e20;
        }
        .delete-btn {
            background: #c62828;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }
        .delete-btn:hover {
            background: #b71c1c;
        }
        .message {
            margin-top: 15px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">GreenCircle Admin</div>
    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_users.php" style="color:#d0edd3;">Manage Users</a>
        <a href="manage_lands.php">Manage Lands</a>
        <a href="manage_equipments.php">Manage Equipments</a>
        <a href="manage_farmers.php">Farmer Applications</a>
        <a href="manage_technicians.php">Technician Applications</a>
        <a href="manage_reviews.php">Manage Reviews</a>
        <a href="admin_logout.php">Logout</a>
    </nav>
</header>

<main>
    <h1>Manage Users</h1>

    <?php if (isset($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php while($user = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['user_name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td>
                <a class="delete-btn" href="manage_users.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</main>

</body>
</html>

<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $app_id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM repair_applications WHERE id = ?");
    $stmt->bind_param("i", $app_id);
    if ($stmt->execute()) {
        $message = "Technician application deleted successfully.";
    } else {
        $message = "Error deleting application.";
    }
    $stmt->close();
}

// Fetch technician applications with user_name via JOIN
$sql = "
SELECT ra.id, ra.experience, ra.skills, ra.photo, ra.applied_at, u.user_name AS applicant_name
FROM repair_applications ra
JOIN users u ON ra.user_id = u.id
ORDER BY ra.applied_at DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Technician Applications - GreenCircle Admin</title>
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
            max-width: 1200px;
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
            table-layout: fixed;
            word-wrap: break-word;
        }
        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
        }
        table th {
            background: #c8e6c9;
            color: #1b5e20;
        }
        .photo-thumb {
            max-width: 80px;
            max-height: 80px;
            border-radius: 6px;
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
        pre.skills, pre.experience {
            white-space: pre-wrap;
            text-align: left;
            max-height: 150px;
            overflow-y: auto;
            margin: 0;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">GreenCircle Admin</div>
    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_lands.php">Manage Lands</a>
        <a href="manage_equipments.php">Manage Equipments</a>
        <a href="manage_farmers.php">Farmer Applications</a>
        <a href="manage_technicians.php" style="color:#d0edd3;">Technician Applications</a>
        <a href="manage_reviews.php">Manage Reviews</a>
        <a href="admin_logout.php">Logout</a>
    </nav>
</header>

<main>
    <h1>Manage Technician Applications</h1>

    <?php if (isset($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Applicant</th>
                <th>Experience</th>
                <th>Skills</th>
                <th>Photo</th>
                <th>Applied At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($app = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($app['id']); ?></td>
                <td><?php echo htmlspecialchars($app['applicant_name']); ?></td>
                <td><pre class="experience"><?php echo htmlspecialchars($app['experience']); ?></pre></td>
                <td><pre class="skills"><?php echo htmlspecialchars($app['skills']); ?></pre></td>
                <td>
                    <?php if ($app['photo']): ?>
                        <img src="<?php echo htmlspecialchars($app['photo']); ?>" alt="Applicant Photo" class="photo-thumb" />
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($app['applied_at']); ?></td>
                <td>
                    <a class="delete-btn" href="manage_technicians.php?delete=<?php echo $app['id']; ?>" onclick="return confirm('Are you sure you want to delete this application?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</main>

</body>
</html>

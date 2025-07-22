<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

// Safe count function with error handling
function getCount($conn, $table) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM $table");

    if (!$result) {
        return 0; // Safe fallback if table not found
    }

    $row = $result->fetch_assoc();
    return $row['total'];
}

// Same function used for applications
function getApplicationsCount($conn, $table) {
    return getCount($conn, $table);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GreenCircle Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e8f5e9;
            color: #1b3a1a;
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
            font-size: 30px;
            font-weight: bold;
            font-family: 'Cinzel', serif;
        }
        nav a {
            color: #a9d18e;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 600;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #d0edd3;
        }
        main {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            font-family: 'Cinzel', serif;
            font-size: 36px;
            margin-bottom: 10px;
        }
        p {
            text-align: center;
            color: #3e6e41;
            margin-bottom: 40px;
        }
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .stat-card {
            background: #d0edd3;
            color: #1b3a1a;
            padding: 25px 30px;
            border-radius: 12px;
            width: 200px;
            text-align: center;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .stat-card p {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">GreenCircle Admin</div>
    <nav>
        <a href="admin_dashboard.php" style="color:#d0edd3;">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_lands.php">Manage Lands</a>
        <a href="manage_equipments.php">Manage Equipments</a>
        <a href="manage_farmers.php">Farmer Applications</a>
        <a href="manage_technicians.php">Technician Applications</a>
        <a href="manage_reviews.php">Manage Reviews</a>
        <a href="admin_logout.php">Logout</a>
    </nav>
</header>

<main>
    <h1>Welcome, Admin!</h1>
    <p>Here’s the current overview of GreenCircle platform:</p>

    <div class="stats-container">
        <div class="stat-card">
            <h2><?php echo getCount($conn, 'users'); ?></h2>
            <p>Total Users</p>
        </div>
        <div class="stat-card">
            <h2><?php echo getCount($conn, 'lands'); ?></h2>
            <p>Lands Listed</p>
        </div>
        <div class="stat-card">
            <h2><?php echo getCount($conn, 'equipment'); ?></h2>
            <p>Equipments Listed</p>
        </div>
        <div class="stat-card">
            <h2><?php echo getApplicationsCount($conn, 'farmer_applications'); ?></h2>
            <p>Farmer Applications</p>
        </div>
        <div class="stat-card">
            <h2><?php echo getApplicationsCount($conn, 'repair_applications'); ?></h2>
            <p>Technician Applications</p>
        </div>
        <div class="stat-card">
            <h2><?php echo getCount($conn, 'reviews'); ?></h2>
            <p>Reviews Submitted</p>
        </div>
    </div>
</main>

</body>
</html>

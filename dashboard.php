<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

include 'db_connect.php'; // Your DB connection file

// Initialize stats
$landsCount = 0;
$equipmentCount = 0;

// Fetch total lands count
$result = $conn->query("SELECT COUNT(*) AS count FROM lands");
if ($result) {
    $row = $result->fetch_assoc();
    $landsCount = $row['count'];
} else {
    echo "Error fetching lands count: " . $conn->error;
}

// Fetch total equipment count
$result = $conn->query("SELECT COUNT(*) AS count FROM equipment");
if ($result) {
    $row = $result->fetch_assoc();
    $equipmentCount = $row['count'];
} else {
    echo "Error fetching equipment count: " . $conn->error;
}

// Fetch total farmers count
$farmerCount = 0;
$result = $conn->query("SELECT COUNT(*) AS count FROM farmer_applications");

if ($result) {
    $row = $result->fetch_assoc();
    $farmerCount = $row['count'];
} else {
    echo "Error fetching farmers count: " . $conn->error;
}

// Fetch total technician count
$technicianCount = 0;
$result = $conn->query("SELECT COUNT(*) AS count FROM repair_applications");

if ($result) {
    $row = $result->fetch_assoc();
    $technicianCount = $row['count'];
} else {
    echo "Error fetching technicians count: " . $conn->error;
}


// Add farmer count to stats
$stats = [
    "Total Lands" => $landsCount,
    "Total Equipment" => $equipmentCount,
    "Total Farmers" => $farmerCount,
    "Total Technicians" => $technicianCount,
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>GreenCircle Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&display=swap" rel="stylesheet" />
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #e8f5e9;
        color: #1b3a1a;
        padding: 0;
    }

    /* Header */
    header {
        background: #072808ff;
        color: white;
        padding: 50px 100px;
        width: 100%;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }
    .logo {
        font-family: 'Cinzel', serif;
        font-size: 50px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* Menu wrapper for hover */
    .menu-wrapper {
        position: relative;
        display: inline-block;
    }

    /* Menu Icon */
    .menu-icon {
        font-size: 28px;
        cursor: pointer;
        user-select: none;
        transition: transform 0.3s ease;
    }
    .menu-wrapper:hover .menu-icon {
        transform: rotate(90deg);
    }

    /* Dropdown Menu */
    #menu {
        display: none;
        position: absolute;
        top: 30px;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        width: 220px;
        flex-direction: column;
        overflow: hidden;
        z-index: 999;
    }
    .menu-wrapper:hover #menu {
        display: flex;
    }
    #menu a {
        padding: 14px 24px;
        color: #2e7d32;
        text-decoration: none;
        font-weight: 600;
        border-bottom: 1px solid #f1f1f1;
        transition: background 0.3s ease;
    }
    #menu a:hover {
        background: #d0edd3;
    }

    /* Main */
    main {
        max-width: 1100px;
        margin: 40px auto;
        text-align: center;
        padding: 0 20px;
    }
    .welcome {
        font-size: 28px;
        font-weight: 700;
        color: #2e7d32;
    }
    .tagline {
        margin-top: 8px;
        font-size: 15px;
        color: #4a7c46;
        font-style: italic;
    }

    /* Stats */
    .stats-container {
        margin-top: 40px;
        display: flex;
        justify-content: center;
        gap: 26px;
        flex-wrap: wrap;
    }
    .stat-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(46,125,50,0.12);
        padding: 26px 30px;
        text-align: center;
        flex: 1 1 220px;
    }
    .stat-value {
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 6px;
    }
    .stat-label {
        font-size: 17px;
        color: #3c5e3c;
    }

    /* Common Actions */
    .btn-grid {
        margin-top: 50px;
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* 5 buttons in one row - removed Rent Tools */
        gap: 30px 36px;
    }
    .btn-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(46,125,50,0.15);
        padding: 26px 20px;
        text-decoration: none;
        color: #2e7d32;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: 0.3s ease;
        font-weight: 700;
        text-transform: uppercase;
    }
    .btn-card:hover {
        background: #d0edd3;
        transform: translateY(-5px);
    }
    .btn-icon {
        font-size: 50px;
        margin-bottom: 12px;
    }

    /* Role Actions */
    .role-action-container {
        margin-top: 80px;
        display: flex;
        justify-content: center;
        gap: 36px;
        flex-wrap: wrap;
    }
    .role-btn {
        background-color: #1e241eff;
        color: white;
        text-decoration: none;
        padding: 28px 34px;
        border-radius: 28px;
        box-shadow: 0 7px 20px rgba(40, 95, 43, 0.4);
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1 1 320px;
        max-width: 340px;
        min-width: 280px;
        min-height: 180px;
        transition: 0.3s ease;
        text-transform: uppercase;
    }
    .role-btn:hover {
        background: #102b12ff;
        transform: translateY(-5px);
    }
    .role-btn .btn-icon {
        font-size: 54px;
        margin-bottom: 10px;
    }
    .role-btn .btn-title {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 8px;
    }
    .role-btn .btn-desc {
        font-size: 14px;
        font-weight: 400;
        color: #c8e6c9;
        text-align: center;
    }

    /* Footer */
    footer {
        margin-top: 50px;
        text-align: center;
        font-size: 14px;
        color: #4a7c46;
        padding-bottom: 20px;
    }

    /* Responsive */
    @media(max-width: 900px) {
        .btn-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }
    }
    @media(max-width:600px) {
        .role-btn { flex: 1 1 100%; max-width: 100%; }
    }
</style>
</head>
<body>

<header>
    <div class="logo">GreenCircle</div>
    <div class="menu-wrapper">
        <div class="menu-icon">&#9776;</div>
        <nav id="menu">
            <a href="about_us.php">About Us</a>
            <a href="reviews.php">Reviews</a>
            <a href="contact.php">Contact Us</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </div>
</header>

<main>
    <div class="welcome">Welcome, <?php echo htmlspecialchars($user_name); ?>!</div>
    <div class="tagline">Empowering Sustainable Farming Through Collaboration</div>

    <div class="stats-container">
        <?php foreach ($stats as $label => $value): ?>
            <div class="stat-card">
                <div class="stat-value"><?php echo $value; ?></div>
                <div class="stat-label"><?php echo $label; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="btn-grid">
        <a href="land_list.php" class="btn-card"><div class="btn-icon">🌾</div>Land List</a>
        <a href="equipment_list.php" class="btn-card"><div class="btn-icon">🚜</div>Equipment List</a>
        <a href="farmer_list.php" class="btn-card"><div class="btn-icon">👨‍🌾</div>Farmer List</a>
        <a href="technician_list.php" class="btn-card"><div class="btn-icon">👷</div>Technician List</a>
    </div>

    <div class="role-action-container">
        <?php if ($user_role === 'Landlord'): ?>
            <a href="land_registration.php" class="role-btn">
                <div class="btn-icon">📍</div>
                <div class="btn-title">Land Registration</div>
                <div class="btn-desc">List your available lands for farming collaboration.</div>
            </a>
        <?php endif; ?>

        <?php if ($user_role === 'Farmer'): ?>
            <a href="apply_farming.php" class="role-btn">
                <div class="btn-icon">📝</div>
                <div class="btn-title">Apply for Farming</div>
                <div class="btn-desc">Submit applications for land jobs .</div>
            </a>
        <?php endif; ?>
        <?php if ($user_role === 'Technician'): ?>
            <a href="apply_work.php" class="role-btn">
                <div class="btn-icon">📝</div>
                <div class="btn-title">Apply for Repairing</div>
                <div class="btn-desc">Submit applications for equipment repairs.</div>
            </a>
        <?php endif; ?>

        <?php if ($user_role === 'Equipment Provider'): ?>
            <a href="add_equipment.php" class="role-btn">
                <div class="btn-icon">⚙️</div>
                <div class="btn-title">Add Equipment</div>
                <div class="btn-desc">Rent out farming tools and machinery to others.</div>
            </a>
        <?php endif; ?>
    </div>
</main>

<footer>
    &copy; <?php echo date("Y"); ?> GreenCircle. All rights reserved.
</footer>

</body>
</html>

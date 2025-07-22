<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$message = "";

// Handle delete request
if (isset($_GET['delete']) && $_GET['delete'] == 'yes') {
    // Delete the application for this user
    $stmt = $conn->prepare("SELECT photo FROM repair_applications WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($photoToDelete);
    if ($stmt->fetch()) {
        // Delete photo file if exists
        if ($photoToDelete && file_exists($photoToDelete)) {
            unlink($photoToDelete);
        }
    }
    $stmt->close();

    $del = $conn->prepare("DELETE FROM repair_applications WHERE user_id = ?");
    $del->bind_param("i", $user_id);
    if ($del->execute()) {
        $message = "Your application has been deleted successfully.";
    } else {
        $message = "Failed to delete your application.";
    }
    $del->close();
}

// Fetch technician's application
$stmt = $conn->prepare("SELECT experience, skills, photo, applied_at FROM repair_applications WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($experience, $skills, $photo, $applied_at);
$hasApplication = $stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>My Repair Applications - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #e8f5e9;
        color: #1b3a1a;
        margin: 0; padding: 0;
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
        font-size: 32px;
        font-weight: 700;
        letter-spacing: 1.5px;
    }
    nav a {
        color: #a9d18e;
        margin-left: 20px;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    nav a:hover {
        color: #d0edd3;
    }
    nav a.active {
        color: #d0edd3;
    }
    main {
        max-width: 800px;
        margin: 40px auto 60px;
        background: white;
        padding: 30px 25px;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(46,125,50,0.12);
    }
    h1 {
        font-family: 'Cinzel', serif;
        font-size: 36px;
        color: #2e7d32;
        margin-bottom: 25px;
        text-align: center;
    }
    .message {
        margin-bottom: 20px;
        padding: 12px 15px;
        border-radius: 8px;
        font-weight: 600;
    }
    .success {
        background: #d4edda;
        color: #155724;
        border: 1.5px solid #c3e6cb;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
        border: 1.5px solid #f5c6cb;
    }
    .application-details {
        font-size: 16px;
        line-height: 1.5;
    }
    .application-details strong {
        color: #2e7d32;
        font-weight: 700;
    }
    .photo-preview {
        margin-top: 20px;
        text-align: center;
    }
    .photo-preview img {
        max-width: 300px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .delete-btn {
        display: inline-block;
        margin-top: 30px;
        background: #c62828;
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        font-size: 16px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    .delete-btn:hover {
        background-color: #b71c1c;
    }
</style>
</head>
<body>

<header>
  <div class="logo">GreenCircle</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="technician_list.php">Technician List</a>
    <a href="apply_work.php">Apply for Repairing</a>
    <a href="my_applications.php" class="active">My Applications</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<main>
    <h1>My Repair Application</h1>

    <?php if ($message): ?>
        <div class="message success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($hasApplication): ?>
        <div class="application-details">
            <p><strong>Experience:</strong><br><?php echo nl2br(htmlspecialchars($experience)); ?></p>
            <p><strong>Skills:</strong><br><?php echo nl2br(htmlspecialchars($skills)); ?></p>
            <p><strong>Applied At:</strong> <?php echo htmlspecialchars($applied_at); ?></p>

            <?php if ($photo && file_exists($photo)): ?>
                <div class="photo-preview">
                    <img src="<?php echo htmlspecialchars($photo); ?>" alt="Application Photo">
                </div>
            <?php endif; ?>

            <a href="my_repair_application.php?delete=yes" class="delete-btn" onclick="return confirm('Are you sure you want to delete your application?');">Delete Application</a>
        </div>
    <?php else: ?>
        <p style="text-align:center; font-size:18px; color:#555;">You have not submitted any repair application yet.</p>
    <?php endif; ?>
</main>

</body>
</html>

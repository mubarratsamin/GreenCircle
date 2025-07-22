<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Handle delete request
if (isset($_GET['delete'])) {
    // Confirm ownership & get photo path
    $stmt = $conn->prepare("SELECT photo FROM farmer_applications WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Delete photo file if exists
        if (!empty($row['photo']) && file_exists($row['photo'])) {
            unlink($row['photo']);
        }

        // Delete application record
        $del = $conn->prepare("DELETE FROM farmer_applications WHERE user_id = ?");
        $del->bind_param("i", $user_id);
        if ($del->execute()) {
            $success = "Your application has been deleted successfully.";
        } else {
            $errors[] = "Failed to delete application.";
        }
        $del->close();
    } else {
        $errors[] = "Application not found or unauthorized.";
    }
    $stmt->close();
}

// Fetch current application
$stmt = $conn->prepare("SELECT experience, skills, photo, applied_at FROM farmer_applications WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$application = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>My Farming Application - GreenCircle</title>
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
    font-weight: 700;
  }
  main {
    max-width: 900px;
    margin: 40px auto 60px;
    background: white;
    padding: 30px 35px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
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
    padding: 15px;
    border-radius: 8px;
  }
  .error {
    background: #f8d7da;
    color: #721c24;
    border: 1.5px solid #f5c6cb;
  }
  .success {
    background: #d4edda;
    color: #155724;
    border: 1.5px solid #c3e6cb;
  }
  .application-details {
    font-size: 16px;
    line-height: 1.6;
  }
  .application-details strong {
    color: #2e7d32;
  }
  .photo {
    margin-top: 20px;
    text-align: center;
  }
  .photo img {
    max-width: 250px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .btn-delete {
    display: inline-block;
    margin-top: 30px;
    background: #c62828;
    color: white;
    padding: 12px 22px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
    text-decoration: none;
  }
  .btn-delete:hover {
    background: #b71c1c;
  }
  .no-application {
    text-align: center;
    font-size: 18px;
    color: #555;
    margin-top: 40px;
  }
</style>
</head>
<body>

<header>
  <div class="logo">GreenCircle</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="farmer_list.php">Farmers</a>
    <a href="apply_farming.php">Apply for Farming</a>
    <a href="my_applications.php" class="active">My Application</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<main>
  <h1>My Farming Application</h1>

  <?php if ($success): ?>
    <div class="message success"><?php echo $success; ?></div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div class="message error">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($application): ?>
    <div class="application-details">
      <p><strong>Experience:</strong><br><?php echo nl2br(htmlspecialchars($application['experience'])); ?></p>
      <p><strong>Skills:</strong><br><?php echo nl2br(htmlspecialchars($application['skills'])); ?></p>
      <p><strong>Applied At:</strong> <?php echo date("F j, Y, g:i a", strtotime($application['applied_at'])); ?></p>

      <?php if (!empty($application['photo']) && file_exists($application['photo'])): ?>
        <div class="photo">
          <img src="<?php echo htmlspecialchars($application['photo']); ?>" alt="Application Photo">
        </div>
      <?php endif; ?>

      <a href="my_applications.php?delete=1" class="btn-delete" onclick="return confirm('Are you sure you want to delete your application?');">Delete Application</a>
    </div>
  <?php else: ?>
    <p class="no-application">You have not submitted any farming application yet.</p>
  <?php endif; ?>
</main>

</body>
</html>

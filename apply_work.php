<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// Only Technicians can apply for repair work
if ($user_role !== 'Technician') {
    die("Access denied. Only technicians can apply.");
}

$experience = '';
$skills = '';
$photo = '';
$message = '';

// Check if user already applied
$stmt = $conn->prepare("SELECT experience, skills, photo FROM repair_applications WHERE user_id = ?");
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($exp_db, $skills_db, $photo_db);
if ($stmt->fetch()) {
    $experience = $exp_db;
    $skills = $skills_db;
    $photo = $photo_db;
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $experience = trim($_POST['experience']);
    $skills = trim($_POST['skills']);
    $uploadOk = 1;
    $photoName = $photo;

    // Handle photo upload if new photo provided
    if (isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $fileName = basename($_FILES["photo"]["name"]);
        $targetFile = $targetDir . uniqid() . "_" . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $validTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $validTypes)) {
            $message = "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk && move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $photoName = $targetFile;
        } elseif ($uploadOk) {
            $message = "Error uploading photo.";
            $uploadOk = 0;
        }
    }

    if (empty($experience) || empty($skills)) {
        $message = "Please fill in all fields.";
    } else {
        // Check if application exists
        $stmt = $conn->prepare("SELECT id FROM repair_applications WHERE user_id = ?");
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            $update = $conn->prepare("UPDATE repair_applications SET experience = ?, skills = ?, photo = ?, applied_at = NOW() WHERE user_id = ?");
            if ($update === false) {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }
            $update->bind_param("sssi", $experience, $skills, $photoName, $user_id);
            if ($update->execute()) {
                $message = "Your repair application has been updated successfully.";
            } else {
                $message = "Error updating application: " . $conn->error;
            }
            $update->close();
        } else {
            $stmt->close();
            $insert = $conn->prepare("INSERT INTO repair_applications (user_id, experience, skills, photo, applied_at) VALUES (?, ?, ?, ?, NOW())");
            if ($insert === false) {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }
            $insert->bind_param("isss", $user_id, $experience, $skills, $photoName);
            if ($insert->execute()) {
                $message = "Your repair application has been submitted successfully.";
            } else {
                $message = "Error submitting application: " . $conn->error;
            }
            $insert->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Apply for Repairing - GreenCircle</title>
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
  main {
    max-width: 1100px;
    margin: 40px auto 60px;
    padding: 0 20px;
  }
  h1 {
    font-family: 'Cinzel', serif;
    font-size: 40px;
    color: #2e7d32;
    margin-bottom: 30px;
    text-align: center;
    letter-spacing: 0.1em;
  }
    form label {
        display: block;
        margin: 15px 0 6px;
        font-weight: 700;
    }
    form textarea, form input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1.5px solid #2e7d32;
        border-radius: 8px;
        font-size: 15px;
    }
    form button {
        margin-top: 30px;
        background-color: #2e7d32;
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
        width: 100%;
    }
    form button:hover {
        background-color: #1b4d1b;
    }
    .message {
        margin-top: 20px;
        font-size: 16px;
        color: #1b4d1b;
        text-align: center;
        font-weight: 700;
    }
    .preview {
        margin-top: 15px;
        text-align: center;
    }
    .preview img {
        max-width: 200px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
</style>
</head>
<body>

<header>
  <div class="logo">GreenCircle</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="technician_list.php" >Technician List</a>
    <a href="apply_work.php" style="color:#d0edd3;">Apply for Repairing</a>
    <a href="my_repair_application.php">My Applications</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<main>
    <h1>Apply for Repairing Work</h1>
    <form method="POST" enctype="multipart/form-data">
        <label for="experience">Experience</label>
        <textarea id="experience" name="experience" rows="5" required><?php echo htmlspecialchars($experience); ?></textarea>

        <label for="skills">Skills</label>
        <textarea id="skills" name="skills" rows="4" required><?php echo htmlspecialchars($skills); ?></textarea>

        <label for="photo">Upload Photo (Optional)</label>
        <input type="file" name="photo" accept="image/*">

        <?php if ($photo): ?>
            <div class="preview">
                <p>Current Photo:</p>
                <img src="<?php echo htmlspecialchars($photo); ?>" alt="Your Photo">
            </div>
        <?php endif; ?>

        <button type="submit">Submit Application</button>
    </form>
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
</main>

</body>
</html>

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

$title = $location = $size = $crop_type = $description = $rent_price = "";
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function clean($data) {
        return htmlspecialchars(trim($data));
    }

    $title = clean($_POST['title']);
    $location = clean($_POST['location']);
    $size = floatval($_POST['size']);
    $crop_type = clean($_POST['crop_type']);
    $description = clean($_POST['description']);
    $rent_price = floatval($_POST['rent_price']);

    // Basic validation
    if (!$title) $errors[] = "Title is required.";
    if (!$location) $errors[] = "Location is required.";
    if (!$size || $size <= 0) $errors[] = "Valid size (positive number) is required.";
    if (!$rent_price || $rent_price <= 0) $errors[] = "Valid rent price (positive number) is required.";

    // Handle image upload
    $image_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = "Only JPG, PNG and GIF images are allowed.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) { // 2MB max
            $errors[] = "Image size must be less than 2MB.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid('land_') . '.' . $ext;
            $target_path = 'uploads/' . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO lands (landlord_id, title, location, size, crop_type, description, rent_price, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $errors[] = "Prepare failed: " . $conn->error;
        } else {
            // bind_param expects types: i = int, s = string, d = double (float)
            $stmt->bind_param("issdssds", $user_id, $title, $location, $size, $crop_type, $description, $rent_price, $image_name);

            if ($stmt->execute()) {
                $success = "Land registered successfully!";
                $title = $location = $size = $crop_type = $description = $rent_price = "";
                $image_name = "";
            } else {
                $errors[] = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Register Land - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  /* Reset */
  * {
    margin: 0; padding: 0; box-sizing: border-box;
  }
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #e8f5e9;
    color: #1b3a1a;
    min-height: 100vh;
  }
  /* Header */
  header {
    background: #072808;
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
  }
  .logo {
    font-family: 'Cinzel', serif;
    font-size: 32px;
    font-weight: 700;
    letter-spacing: 1.5px;
    cursor: default;
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
  /* Main form */
  main {
    max-width: 600px;
    margin: 40px auto 60px;
    background: white;
    padding: 30px 25px;
    border-radius: 14px;
    box-shadow: 0 8px 22px rgba(46,125,50,0.12);
  }
  h1 {
    font-family: 'Cinzel', serif;
    font-size: 32px;
    color: #2e7d32;
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
  input[type="text"],
  input[type="number"],
  input[type="file"],
  textarea {
    padding: 12px 10px;
    margin-bottom: 20px;
    border: 1.8px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    resize: vertical;
    transition: border-color 0.3s ease;
  }
  input[type="text"]:focus,
  input[type="number"]:focus,
  input[type="file"]:focus,
  textarea:focus {
    border-color: #2e7d32;
    outline: none;
  }
  textarea {
    min-height: 90px;
  }
  .btn-submit {
    background: #2e7d32;
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
    background: #1b4d1b;
  }
  .message {
    margin-bottom: 20px;
    padding: 12px 15px;
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
</style>
</head>
<body>

<header>
  <div class="logo">GreenCircle</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="land_list.php">Land List</a>
    <a href="land_registration.php" class="active">Register Land</a>
    <a href="my_lands.php">My Lands</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<main>
  <h1>Register Your Land</h1>

  <?php if ($errors): ?>
    <div class="message error">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?php echo $e; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="message success"><?php echo $success; ?></div>
  <?php endif; ?>

  <form action="" method="POST" enctype="multipart/form-data" novalidate>
    <label for="title">Land Title</label>
    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($title); ?>" placeholder="Enter land title">

    <label for="location">Location</label>
    <input type="text" id="location" name="location" required value="<?php echo htmlspecialchars($location); ?>" placeholder="Land location">

    <label for="size">Size (in acres)</label>
    <input type="number" id="size" name="size" min="0" step="1" required value="<?php echo htmlspecialchars($size); ?>" placeholder="e.g. 1.5">

    <label for="crop_type">Crop Type</label>
    <input type="text" id="crop_type" name="crop_type" value="<?php echo htmlspecialchars($crop_type); ?>" placeholder="e.g. Wheat, Corn (optional)">

    <label for="description">Description</label>
    <textarea id="description" name="description" placeholder="Describe your land"><?php echo htmlspecialchars($description); ?></textarea>

    <label for="rent_price">Rent Price (per acre)</label>
    <input type="number" id="rent_price" name="rent_price" min="0" step="50" required value="<?php echo htmlspecialchars($rent_price); ?>" placeholder="e.g. 100.00">

    <label for="image">Upload Image (optional)</label>
    <input type="file" id="image" name="image" accept="image/*">

    <button type="submit" class="btn-submit">Register Land</button>
  </form>
</main>

</body>
</html>

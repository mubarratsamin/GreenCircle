<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'] ?? '';
$user_phone = $_SESSION['user_phone'] ?? '';

$errors = [];
$success = "";

// Define categories (you can load this from DB instead)
$categories = ['Tractor', 'Harvester', 'Plow', 'Irrigation', 'Tools'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clean inputs
    function clean($data) {
        return htmlspecialchars(trim($data));
    }

    $equipment_name = clean($_POST['equipment_name'] ?? '');
    $category = clean($_POST['category'] ?? '');
    $description = clean($_POST['description'] ?? '');
    $price = clean($_POST['price'] ?? '');
    $terms = clean($_POST['terms'] ?? '');

    // Set contact info from session (no form input)
    $contact_email = $user_email;
    $contact_phone = $user_phone;

    // Validate required fields
    if (!$equipment_name) $errors[] = "Equipment Name is required.";
    if (!$category || !in_array($category, $categories)) $errors[] = "Valid Category is required.";
    if (!$description) $errors[] = "Description is required.";
    if (!$price || !is_numeric($price) || $price <= 0) $errors[] = "Valid Price is required.";

    // Handle image upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "Image must be jpg, jpeg, png, or gif.";
        } else {
            $image_name = uniqid('eq_') . '.' . $ext;
            $upload_dir = 'uploads/equipment_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            if (!move_uploaded_file($file_tmp, $upload_dir . $image_name)) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    if (empty($errors)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO equipment (user_id, equipment_name, category, description, price, image, contact_email, contact_phone, terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdssss", $user_id, $equipment_name, $category, $description, $price, $image_name, $contact_email, $contact_phone, $terms);
        if ($stmt->execute()) {
            $success = "Equipment posted successfully!";
            // Clear fields
            $equipment_name = $category = $description = $price = $terms = '';
            $image_name = '';
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Post Equipment - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f7f6;
    margin: 0; padding: 0;
    color: #333;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  header {
    background: #072808;
    color: white;
    padding: 15px 30px;
    width: 100%;
    box-sizing: border-box;
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
    font-weight: 700;
  }
  .container {
    background: white;
    padding: 40px 35px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    width: 450px;
    margin: 40px auto 60px;
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
  input[type="text"], input[type="email"], input[type="number"], select, textarea {
    padding: 12px 10px;
    margin-bottom: 18px;
    border: 1.8px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s ease;
    resize: vertical;
  }
  input[type="text"]:focus, input[type="email"]:focus, input[type="number"]:focus, select:focus, textarea:focus {
    border-color: #2E7D32;
    outline: none;
  }
  input[type="file"] {
    margin-bottom: 20px;
  }
  textarea {
    min-height: 80px;
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

<header>
  <div class="logo">GreenCircle</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="equipment_list.php">Equipment List</a>
     <a href="add_equipment.php" class="active">Add Equipment</a>
     <a href="my_equipment.php">My Equipment</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<div class="container">
  <h2>Add Your Equipment</h2>

  <?php if ($errors): ?>
    <div class="error">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?php echo $e; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success"><?php echo $success; ?></div>
  <?php endif; ?>

  <form action="" method="post" enctype="multipart/form-data" novalidate>
    <label for="equipment_name">Equipment Name</label>
    <input type="text" id="equipment_name" name="equipment_name" required value="<?php echo htmlspecialchars($equipment_name ?? ''); ?>" placeholder="E.g., Tractor Model X">

    <label for="category">Category</label>
    <select id="category" name="category" required>
      <option value="" disabled <?php if (empty($category)) echo 'selected'; ?>>Select category</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?php echo $cat; ?>" <?php if (($category ?? '') === $cat) echo 'selected'; ?>><?php echo $cat; ?></option>
      <?php endforeach; ?>
    </select>

    <label for="description">Description</label>
    <textarea id="description" name="description" required placeholder="Describe the equipment"><?php echo htmlspecialchars($description ?? ''); ?></textarea>

    <label for="price">Price per Day (in USD)</label>
    <input type="number" id="price" name="price" required min="50" step="50" value="<?php echo htmlspecialchars($price ?? ''); ?>" placeholder="E.g., 150.00">

    <label for="image">Upload Image (optional)</label>
    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif">

    <label for="terms">Terms & Conditions / Usage Notes (optional)</label>
    <textarea id="terms" name="terms" placeholder="Any special terms or notes"><?php echo htmlspecialchars($terms ?? ''); ?></textarea>

    <button type="submit" class="btn-submit">Add Equipment</button>
  </form>
</div>

</body>
</html>

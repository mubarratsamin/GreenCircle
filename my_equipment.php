<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


include 'db_connect.php';

// Sanitize user_id from session
$user_id = intval($_SESSION['user_id']);
$success = "";
$errors = [];

// Handle delete request
if (isset($_GET['delete'])) {
    $equipment_id = intval($_GET['delete']);

    // Check ownership
    $check = $conn->prepare("SELECT image FROM equipment WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $equipment_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Delete image file if exists
        if (!empty($row['image']) && file_exists('uploads/equipment_images/' . $row['image'])) {
            unlink('uploads/equipment_images/' . $row['image']);
        }

        // Delete equipment record
        $delete = $conn->prepare("DELETE FROM equipment WHERE id = ?");
        $delete->bind_param("i", $equipment_id);

        if ($delete->execute()) {
            $success = "Equipment deleted successfully!";
        } else {
            $errors[] = "Failed to delete equipment.";
        }
        $delete->close();
    } else {
        $errors[] = "Invalid equipment ID or unauthorized action.";
    }

    $check->close();
}

// Fetch user's equipment only
$stmt = $conn->prepare("SELECT id, equipment_name, category, price, image FROM equipment WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$equipment_list = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Equipment - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  /* Reset */
  * {
    margin: 0; padding: 0; box-sizing: border-box;
  }
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f7f6;
    color: #333;
    min-height: 100vh;
  }
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
    font-weight: 700;
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
    font-size: 32px;
    color: #2e7d32;
    margin-bottom: 25px;
    text-align: center;
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
  .equipment {
    display: flex;
    align-items: center;
    border: 1.5px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }
  .equipment img {
    width: 120px;
    height: 90px;
    object-fit: cover;
    margin-right: 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
  }
  .equipment-details {
    flex-grow: 1;
    font-size: 15px;
    line-height: 1.5;
  }
  .equipment-details strong {
    font-size: 18px;
    color: #2e7d32;
  }
  .delete-btn {
    background: #c62828;
    color: white;
    border: none;
    padding: 10px 14px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s ease;
  }
  .delete-btn:hover {
    background: #b71c1c;
  }
</style>
</head>
<body>

<header>
  <div class="logo">GreenCircle</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="equipment_list.php">Equipment List</a>
    <a href="add_equipment.php">Add Equipment</a>
    <a href="my_equipment.php" class="active">My Equipment</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<main>
  <h1>My Equipment</h1>

  <?php if ($success): ?>
    <div class="message success"><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div class="message error">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($equipment_list->num_rows > 0): ?>
    <?php while ($eq = $equipment_list->fetch_assoc()): ?>
      <div class="equipment">
        <?php if (!empty($eq['image']) && file_exists('uploads/equipment_images/' . $eq['image'])): ?>
          <img src="uploads/equipment_images/<?php echo htmlspecialchars($eq['image']); ?>" alt="Equipment Image">
        <?php else: ?>
          <img src="https://via.placeholder.com/120x90?text=No+Image" alt="No Image">
        <?php endif; ?>

        <div class="equipment-details">
          <strong><?php echo htmlspecialchars($eq['equipment_name']); ?></strong><br>
          Category: <?php echo htmlspecialchars($eq['category']); ?><br>
          Price: $<?php echo htmlspecialchars($eq['price']); ?> per day
        </div>

        <a class="delete-btn" href="my_equipment.php?delete=<?php echo $eq['id']; ?>" onclick="return confirm('Are you sure you want to delete this equipment?');">Delete</a>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p style="text-align:center; color: #555;">You have not added any equipment yet.</p>
  <?php endif; ?>
</main>

</body>
</html>

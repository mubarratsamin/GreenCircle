<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$land_id = intval($_GET['id']);

// Fetch land and landlord info
$sql = "SELECT lands.*, users.name AS landlord_name, users.contact AS landlord_contact, users.id AS landlord_id
        FROM lands
        JOIN users ON lands.landlord_id = users.id
        WHERE lands.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $land_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Land not found.");
}

$land = $result->fetch_assoc();

// Handle rent confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $insert = $conn->prepare("INSERT INTO rents (land_id, renter_id, landlord_id) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $land_id, $user_id, $land['landlord_id']);
    $insert->execute();

    if ($insert->affected_rows > 0) {
        echo "<script>alert('Land rented successfully!'); window.location.href='land_list.php';</script>";
        exit();
    } else {
        echo "<script>alert('Something went wrong!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Rent Land - GreenCircle</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #e8f5e9;
    color: #1b3a1a;
    padding: 40px;
}
.container {
    max-width: 600px;
    background: white;
    padding: 30px;
    margin: auto;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}
h2 {
    color: #2e7d32;
    text-align: center;
    margin-bottom: 20px;
}
.info {
    margin-bottom: 15px;
    font-size: 16px;
}
.info strong {
    color: #1b4d1b;
}
button {
    display: block;
    width: 100%;
    padding: 12px;
    background: #2e7d32;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.3s ease;
}
button:hover {
    background: #1b4d1b;
}
a {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #2e7d32;
    text-decoration: none;
}
</style>
</head>
<body>

<div class="container">
    <h2>Confirm Land Rent</h2>

    <div class="info"><strong>Land:</strong> <?php echo htmlspecialchars($land['title']); ?></div>
    <div class="info"><strong>Location:</strong> <?php echo htmlspecialchars($land['location']); ?></div>
    <div class="info"><strong>Size:</strong> <?php echo htmlspecialchars($land['size']); ?> acres</div>
    <div class="info"><strong>Crop Type:</strong> <?php echo htmlspecialchars($land['crop_type'] ?: 'N/A'); ?></div>
    <div class="info"><strong>Rent Price:</strong> $<?php echo htmlspecialchars($land['rent_price']); ?> / acre</div>

    <hr style="margin:20px 0;">

    <h3>Landlord Info</h3>
    <div class="info"><strong>Name:</strong> <?php echo htmlspecialchars($land['landlord_name']); ?></div>
    <div class="info"><strong>Contact:</strong> <?php echo htmlspecialchars($land['landlord_contact']); ?></div>

    <form method="POST">
        <button type="submit">Confirm Rent</button>
    </form>

    <a href="land_list.php">Cancel</a>
</div>

</body>
</html>

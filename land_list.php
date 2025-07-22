<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// Get search query safely
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Prepare SQL
if ($search !== '') {
    $sql = "SELECT lands.*, users.user_name AS landlord_name, users.phone AS landlord_phone, users.email AS landlord_email 
            FROM lands 
            JOIN users ON lands.landlord_id = users.id 
            WHERE lands.title LIKE ? OR lands.location LIKE ? OR lands.crop_type LIKE ?
            ORDER BY lands.id DESC";
    $likeSearch = '%' . $search . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $likeSearch, $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT lands.*, users.user_name AS landlord_name, users.phone AS landlord_phone, users.email AS landlord_email 
            FROM lands 
            JOIN users ON lands.landlord_id = users.id 
            ORDER BY lands.id DESC";
    $result = $conn->query($sql);
}

if (!$result) {
    echo "Database query failed: " . $conn->error;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Available Lands - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  /* Basic Reset */
  * {margin: 0; padding: 0; box-sizing: border-box;}
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #e8f5e9;
    color: #1b3a1a;
    min-height: 100vh;
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
  .search-box {
    max-width: 400px;
    margin: 0 auto 30px;
    display: flex;
  }
  .search-box input[type="text"] {
    flex-grow: 1;
    padding: 10px 15px;
    border: 2px solid #2e7d32;
    border-right: none;
    border-radius: 8px 0 0 8px;
    font-size: 16px;
  }
  .search-box button {
    padding: 10px 18px;
    background-color: #2e7d32;
    color: white;
    border: 2px solid #2e7d32;
    border-radius: 0 8px 8px 0;
    cursor: pointer;
    font-weight: 700;
    transition: background-color 0.3s ease;
  }
  .search-box button:hover {
    background-color: #1b4d1b;
  }
  .land-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
  }
  .land-card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 8px 22px rgba(46,125,50,0.12);
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .land-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-bottom: 1px solid #a5d6a7;
  }
  .land-details {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }
  .land-title {
    font-weight: 700;
    font-size: 22px;
    color: #2e7d32;
    margin-bottom: 6px;
  }
  .land-info {
    font-size: 15px;
    color: #4a7c46;
    margin-bottom: 10px;
  }
  .land-description {
    font-size: 14px;
    color: #3c5e3c;
    margin-bottom: 14px;
    flex-grow: 1;
  }
  .land-rent {
    font-weight: 700;
    color: #1b4d1b;
    font-size: 16px;
    margin-bottom: 12px;
  }
  .btn-rent {
    background: #2e7d32;
    color: white;
    border: none;
    padding: 10px 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    align-self: flex-start;
    transition: background-color 0.3s ease;
  }
  .btn-rent:hover {
    background: #1b4d1b;
  }
  /* Modal overlay */
  .modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }
  .modal-content {
    background: #fff; 
    border-radius: 12px; 
    padding: 25px 30px; 
    max-width: 320px; 
    width: 90%;
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    position: relative;
  }
  .modal-close {
    position: absolute; 
    top: 12px; 
    right: 16px; 
    font-weight: bold; 
    cursor: pointer; 
    font-size: 24px; 
    color: #555;
    transition: color 0.3s ease;
  }
  .modal-close:hover {
    color: #2e7d32;
  }
  .modal-content h4 {
    color: #2e7d32; 
    margin-bottom: 15px; 
    text-align:center;
  }
  .modal-content p {
    margin: 8px 0; 
    font-size: 15px; 
    color: #333;
  }
  @media(max-width: 600px) {
    .land-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
</head>
<body>

<header>
  <div class="logo">GreenCircle</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="land_list.php" style="color:#d0edd3;">Land List</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<main>
  <h1>Available Lands for Rent</h1>

  <form class="search-box" method="GET" action="">
    <input type="text" name="search" placeholder="Search by title, location or crop type" value="<?php echo htmlspecialchars($search); ?>" />
    <button type="submit">Search</button>
  </form>

  <?php if ($result && $result->num_rows > 0): ?>
    <div class="land-grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="land-card">
          <?php if ($row['image'] && file_exists('uploads/' . $row['image'])): ?>
            <img src="<?php echo 'uploads/' . htmlspecialchars($row['image']); ?>" alt="Land Image" class="land-image" />
          <?php else: ?>
            <img src="https://via.placeholder.com/400x180?text=No+Image" alt="No Image" class="land-image" />
          <?php endif; ?>

          <div class="land-details">
            <div class="land-title"><?php echo htmlspecialchars($row['title']); ?></div>
            <div class="land-info"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></div>
            <div class="land-info"><strong>Size:</strong> <?php echo htmlspecialchars($row['size']); ?> acres</div>
            <div class="land-info"><strong>Crop Type:</strong> <?php echo htmlspecialchars($row['crop_type'] ?: 'N/A'); ?></div>
            <div class="land-description"><?php echo nl2br(htmlspecialchars($row['description'] ?: 'No description provided.')); ?></div>
            <div class="land-rent">Rent Price: $<?php echo htmlspecialchars($row['rent_price']); ?> / acre</div>
            <button class="btn-rent" 
              data-name="<?php echo htmlspecialchars($row['landlord_name']); ?>"
              data-phone="<?php echo htmlspecialchars($row['landlord_phone']); ?>"
              data-email="<?php echo htmlspecialchars($row['landlord_email']); ?>"
            >Rent</button>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center; font-size:18px; color:#4a7c46;">No lands found<?php if ($search) echo " matching '<strong>" . htmlspecialchars($search) . "</strong>'"; ?>.</p>
  <?php endif; ?>
</main>

<!-- Modal -->
<div id="rentModal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <span class="modal-close">&times;</span>
    <h4>Contact Landlord</h4>
    <p><strong>Name:</strong> <span id="landlordName"></span></p>
    <p><strong>Phone:</strong> <span id="landlordPhone"></span></p>
    <p><strong>Email:</strong> <span id="landlordEmail"></span></p>
  </div>
</div>

<script>
  const modal = document.getElementById('rentModal');
  const modalClose = modal.querySelector('.modal-close');
  const landlordName = document.getElementById('landlordName');
  const landlordPhone = document.getElementById('landlordPhone');
  const landlordEmail = document.getElementById('landlordEmail');

  document.querySelectorAll('.btn-rent').forEach(button => {
    button.addEventListener('click', () => {
      landlordName.textContent = button.getAttribute('data-name');
      landlordPhone.textContent = button.getAttribute('data-phone');
      landlordEmail.textContent = button.getAttribute('data-email');

      modal.style.display = 'flex';
    });
  });

  modalClose.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
</script>

</body>
</html>

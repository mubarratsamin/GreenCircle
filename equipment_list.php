<?php
session_start();
include 'db_connect.php';

// Get and sanitize search query
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Prepare SQL with search filter
if ($search !== '') {
    $likeSearch = '%' . $search . '%';
    $stmt = $conn->prepare("SELECT e.*, u.user_name, u.phone AS user_phone, u.email AS user_email 
                            FROM equipment e 
                            JOIN users u ON e.user_id = u.id
                            WHERE e.equipment_name LIKE ? OR e.category LIKE ?
                            ORDER BY e.created_at DESC");
    $stmt->bind_param('ss', $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT e.*, u.user_name, u.phone AS user_phone, u.email AS user_email 
            FROM equipment e 
            JOIN users u ON e.user_id = u.id 
            ORDER BY e.created_at DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Equipment List - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #e8f5e9;
    margin: 0; padding: 0;
    color: #1b3a1a;
    min-height: 100vh;
  }
  header {
    background: #072808;
    color: white;
    padding: 15px 30px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
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
    
  }
  nav a:hover, nav a.active {
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

  /* Search box */
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
    outline: none;
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

  .equipment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    justify-content: center;
    max-width: 1100px;
    margin: 0 auto;
  }
  .equipment-card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 8px 22px rgba(46,125,50,0.12);
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .equipment-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-bottom: 1px solid #a5d6a7;
  }
  .equipment-details {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }
  .equipment-title {
    font-weight: 700;
    font-size: 22px;
    color: #2e7d32;
    margin-bottom: 6px;
  }
  .equipment-category {
    font-size: 15px;
    color: #4a7c46;
    margin-bottom: 10px;
  }
  .equipment-price {
    font-weight: 700;
    color: #1b4d1b;
    font-size: 16px;
    margin-bottom: 20px;
  }
  .rent-btn {
    background-color: #2e7d32;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 700;
    font-size: 16px;
    transition: background-color 0.3s ease;
    align-self: flex-start;
  }
  .rent-btn:hover {
    background-color: #1b4d1b;
  }
  /* Modal */
  .modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.5);

    justify-content: center;
    align-items: center;
  }
  .modal-content {
    background-color: white;
    padding: 30px 25px;
    border-radius: 12px;
    max-width: 400px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    position: relative;
    font-size: 16px;
    color: #1b3a1a;
  }
  .modal-close {
    position: absolute;
    top: 4px;
    right: 5px;
    color: #888;
    font-size: 20px;
    font-weight: 500;
    cursor: pointer;
    transition: color 0.3s ease;
  }
  .modal-close:hover {
    color: #2e7d32;
  }
  .modal h2 {
    margin-top: 0;
    font-family: 'Cinzel', serif;
    color: #2e7d32;
    margin-bottom: 15px;
  }
  .modal p {
    margin: 8px 0;
  }
  @media (max-width: 600px) {
    .equipment-grid {
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
    <a href="equipment_list.php" class="active">Equipment List</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<main>
  <h1>Available Equipment for Rent</h1>

  <form class="search-box" method="GET" action="">
    <input type="text" name="search" placeholder="Search by name or category" value="<?php echo htmlspecialchars($search); ?>" />
    <button type="submit">Search</button>
  </form>

  <?php if ($result && $result->num_rows > 0): ?>
    <div class="equipment-grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="equipment-card">
          <?php if ($row['image'] && file_exists('uploads/equipment_images/' . $row['image'])): ?>
            <img src="<?php echo 'uploads/equipment_images/' . htmlspecialchars($row['image']); ?>" alt="Equipment Image" class="equipment-image" />
          <?php else: ?>
            <img src="https://via.placeholder.com/400x180?text=No+Image" alt="No Image" class="equipment-image" />
          <?php endif; ?>
          <div class="equipment-details">
            <div class="equipment-title"><?php echo htmlspecialchars($row['equipment_name']); ?></div>
            <div class="equipment-category"><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></div>
            <div class="equipment-price">Price: $<?php echo htmlspecialchars($row['price']); ?> / day</div>
            <button class="rent-btn" 
              data-provider-name="<?php echo htmlspecialchars($row['user_name']); ?>" 
              data-provider-phone="<?php echo htmlspecialchars($row['user_phone']); ?>" 
              data-provider-email="<?php echo htmlspecialchars($row['user_email']); ?>"
            >Rent</button>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center; font-size:18px; color:#4a7c46;">No equipment found<?php if($search) echo " matching '<strong>" . htmlspecialchars($search) . "</strong>'"; ?>.</p>
  <?php endif; ?>
</main>

<!-- Modal -->
<div id="rentModal" class="modal" aria-hidden="true" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDesc">
  <div class="modal-content">
    <button aria-label="Close modal" class="modal-close" id="modalClose">&times;</button>
    <h2 id="modalTitle">Contact Equipment Provider</h2>
    <p id="modalDesc"><strong>Name:</strong> <span id="providerName"></span></p>
    <p><strong>Phone:</strong> <span id="providerPhone"></span></p>
    <p><strong>Email:</strong> <span id="providerEmail"></span></p>
  </div>
</div>

<script>
  const modal = document.getElementById('rentModal');
  const modalClose = document.getElementById('modalClose');
  const providerName = document.getElementById('providerName');
  const providerPhone = document.getElementById('providerPhone');
  const providerEmail = document.getElementById('providerEmail');

  document.querySelectorAll('.rent-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      providerName.textContent = btn.getAttribute('data-provider-name');
      providerPhone.textContent = btn.getAttribute('data-provider-phone');
      providerEmail.textContent = btn.getAttribute('data-provider-email');
      modal.style.display = 'flex';
      modal.setAttribute('aria-hidden', 'false');
    });
  });

  modalClose.addEventListener('click', () => {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
  });

  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
      modal.setAttribute('aria-hidden', 'true');
    }
  });
</script>

</body>
</html>

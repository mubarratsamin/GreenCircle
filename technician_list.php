<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Prepare SQL with join between repair_applications and users for contact info
if ($search !== '') {
    $sql = "SELECT ra.*, u.user_name, u.phone, u.email
            FROM repair_applications ra
            JOIN users u ON ra.user_id = u.id
            WHERE u.user_name LIKE ? OR ra.skills LIKE ?
            ORDER BY ra.applied_at DESC";
    $likeSearch = '%' . $search . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT ra.*, u.user_name, u.phone, u.email
            FROM repair_applications ra
            JOIN users u ON ra.user_id = u.id
            ORDER BY ra.applied_at DESC";
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
<title>Technician List - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  /* Reset & Basic */
  * { margin:0; padding:0; box-sizing: border-box; }
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
  .technician-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
  }
  .technician-card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 8px 22px rgba(46,125,50,0.12);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
  }
  .technician-photo {
    width: 160px;
    height: 160px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 18px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .technician-name {
    font-weight: 700;
    font-size: 22px;
    color: #2e7d32;
    margin-bottom: 10px;
    text-align: center;
  }
  .technician-experience,
  .technician-skills {
    font-size: 15px;
    color: #4a7c46;
    margin-bottom: 12px;
    white-space: pre-wrap;
    text-align: center;
  }
  .btn-hire {
    background: #2e7d32;
    color: white;
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 16px;
  }
  .btn-hire:hover {
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
    text-align: center;
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
  }
  .modal-content p {
    margin: 8px 0;
    font-size: 15px;
    color: #333;
  }
  @media(max-width: 600px) {
    .technician-grid {
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
    <a href="technician_list.php" style="color:#d0edd3;">Technicians</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<main>
  <h1>Technician List</h1>

  <form class="search-box" method="GET" action="">
    <input type="text" name="search" placeholder="Search by name or skills" value="<?php echo htmlspecialchars($search); ?>" />
    <button type="submit">Search</button>
  </form>

  <?php if ($result && $result->num_rows > 0): ?>
    <div class="technician-grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="technician-card">
          <?php if ($row['photo'] && file_exists($row['photo'])): ?>
            <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Technician Photo" class="technician-photo" />
          <?php else: ?>
            <img src="https://via.placeholder.com/160?text=No+Photo" alt="No Photo" class="technician-photo" />
          <?php endif; ?>

          <div class="technician-name"><?php echo htmlspecialchars($row['user_name']); ?></div>
          <div class="technician-experience"><strong>Experience:</strong><br><?php echo nl2br(htmlspecialchars($row['experience'])); ?></div>
          <div class="technician-skills"><strong>Skills:</strong><br><?php echo nl2br(htmlspecialchars($row['skills'])); ?></div>

          <button class="btn-hire"
            data-name="<?php echo htmlspecialchars($row['user_name']); ?>"
            data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
            data-email="<?php echo htmlspecialchars($row['email']); ?>"
          >Hire</button>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center; font-size:18px; color:#4a7c46;">
      No technicians have applied yet<?php if ($search) echo " matching '<strong>" . htmlspecialchars($search) . "</strong>'"; ?>.
    </p>
  <?php endif; ?>
</main>

<!-- Modal -->
<div id="hireModal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <span class="modal-close">&times;</span>
    <h4>Contact Technician</h4>
    <p><strong>Name:</strong> <span id="technicianName"></span></p>
    <p><strong>Phone:</strong> <span id="technicianPhone"></span></p>
    <p><strong>Email:</strong> <span id="technicianEmail"></span></p>
  </div>
</div>

<script>
  const modal = document.getElementById('hireModal');
  const modalClose = modal.querySelector('.modal-close');
  const technicianName = document.getElementById('technicianName');
  const technicianPhone = document.getElementById('technicianPhone');
  const technicianEmail = document.getElementById('technicianEmail');

  document.querySelectorAll('.btn-hire').forEach(button => {
    button.addEventListener('click', () => {
      technicianName.textContent = button.getAttribute('data-name');
      technicianPhone.textContent = button.getAttribute('data-phone');
      technicianEmail.textContent = button.getAttribute('data-email');

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

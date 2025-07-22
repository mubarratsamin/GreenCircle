<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = intval($_POST['rating']);
    $comment = htmlspecialchars($_POST['comment']);

    if ($rating && $comment) {
        $stmt = $conn->prepare("INSERT INTO reviews (name, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $user_name, $rating, $comment);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch reviews
$sql = "SELECT name, rating, comment FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reviews - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&display=swap" rel="stylesheet">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #e8f5e9;
        color: #1b3a1a;
    }

     /* Header */
    header {
        background: #072808ff;
        color: white;
        padding: 25px 60px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        font-weight: 600;
        letter-spacing: 0.04em;
    }
    .logo {
        font-family: 'Cinzel', serif;
        font-size: 36px;
        font-weight: 700;
        letter-spacing: 1.5px;
        cursor: default;
    }
    nav a {
        color: #a9d18e;
        text-decoration: none;
        margin-left: 28px;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    nav a:hover {
        color: #d0edd3;
    }

    main {
        flex: 1;
        max-width: 1100px;
        margin: 50px auto 70px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(46,125,50,0.12);
        padding: 40px 50px;
    }

    h1, h2 {
        font-family: 'Cinzel', serif;
        color: #2e7d32;
        margin-bottom: 18px;
    }
    h1 {
        font-size: 44px;
        text-align: center;
        margin-bottom: 36px;
        letter-spacing: 0.1em;
    }
    h2 {
        font-size: 28px;
    }

    .intro {
        font-size: 18px;
        font-weight: 600;
        color: #3c5e3c;
        text-align: center;
        max-width: 780px;
        margin: 0 auto 50px;
    }

    /* Section styling */
    section {
        margin-bottom: 45px;
        border-top: 2px solid #d0edd3;
        padding-top: 30px;
    }
    section p {
        color: #4a7c46;
        font-size: 16px;
        font-weight: 400;
        max-width: 900px;
        margin-bottom: 25px;
    }

    main {
        max-width: 1000px;
        margin: 50px auto;
        text-align: center;
        padding: 0 20px;
    }

    .title {
        font-size: 34px;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 15px;
    }

    .subtitle {
        font-size: 16px;
        color: #4a7c46;
        margin-bottom: 40px;
        font-style: italic;
    }

    .review-form {
        background: white;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(46,125,50,0.12);
        margin-bottom: 50px;
        text-align: left;
    }

    .review-form label {
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
    }

    .review-form select, .review-form textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 18px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 15px;
    }

    .review-form button {
        background: #2e7d32;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .review-form button:hover {
        background: #1b5e20;
    }

    .review-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
    }

    .review-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(46,125,50,0.12);
        padding: 25px;
        width: 280px;
        transition: 0.3s ease;
        text-align: left;
    }

    .review-card:hover {
        transform: translateY(-5px);
    }

    .reviewer {
        font-size: 18px;
        font-weight: 700;
        color: #1b3a1a;
        margin-bottom: 6px;
    }

    .rating {
        color: #f4c542;
        margin-bottom: 10px;
        font-size: 17px;
    }

    .comment {
        font-size: 14px;
        color: #4a7c46;
    }

    footer {
        margin-top: 60px;
        text-align: center;
        font-size: 14px;
        color: #4a7c46;
        padding-bottom: 20px;
        border-top: 1px solid #c8e6c9;
    }

    @media(max-width: 600px) {
        .review-grid { flex-direction: column; align-items: center; }
    }
</style>
</head>
<body>

<header>
    <div class="logo">GreenCircle</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="about_us.php">About Us</a>
        <a href="reviews.php" style="color:#d0edd3;">Reviews</a>
        <a href="contact.php">Contact Us</a>
        <a href="logout.php">Log Out</a>
    </nav>
</header>

<main>
    <div class="title">Community Reviews</div>
    <div class="subtitle">See what our members say about GreenCircle</div>

    <form method="POST" class="review-form">
        <label>Your Rating:</label>
        <select name="rating" required>
            <option value="">Select Rating</option>
            <option value="5">★★★★★ Excellent</option>
            <option value="4">★★★★☆ Good</option>
            <option value="3">★★★☆☆ Average</option>
            <option value="2">★★☆☆☆ Poor</option>
            <option value="1">★☆☆☆☆ Bad</option>
        </select>

        <label>Your Comment:</label>
        <textarea name="comment" placeholder="Write your feedback here..." rows="4" required></textarea>

        <button type="submit">Submit Review</button>
    </form>

    <div class="review-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="reviewer"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="rating"><?php echo str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']); ?></div>
                    <div class="comment"><?php echo htmlspecialchars($row['comment']); ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:#4a7c46; font-size:16px;">No reviews yet. Be the first to share your feedback!</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    &copy; <?php echo date("Y"); ?> GreenCircle. All rights reserved.
</footer>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>About Us - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet" />
<style>
    * {
        margin: 0; padding: 0; box-sizing: border-box;
    }
    body {
        font-family: 'Open Sans', sans-serif;
        background: #e8f5e9;
        color: #1b3a1a;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        line-height: 1.6;
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

    /* Values grid */
    .values-grid {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
        max-width: 900px;
        margin: 0 auto 50px;
    }
    .value-box {
        flex: 1 1 280px;
        background: #d0edd3;
        border-radius: 14px;
        padding: 22px 20px;
        box-shadow: 0 4px 14px rgba(46,125,50,0.15);
        text-align: center;
        transition: background-color 0.3s ease;
        cursor: default;
    }
    .value-box:hover {
        background-color: #b8d9af;
    }
    .value-box h3 {
        font-family: 'Cinzel', serif;
        font-size: 20px;
        margin-bottom: 14px;
        color: #1b3a1a;
    }
    .value-box p {
        font-size: 15px;
        color: #2e7d32;
    }

    /* Milestones timeline */
    .milestones {
        max-width: 900px;
        margin: 0 auto 60px;
        position: relative;
    }
    .milestone {
        padding-left: 30px;
        border-left: 3px solid #2e7d32;
        margin-bottom: 24px;
        position: relative;
    }
    .milestone::before {
        content: '';
        position: absolute;
        left: -11px;
        top: 8px;
        width: 18px;
        height: 18px;
        background: #2e7d32;
        border-radius: 50%;
        border: 3px solid #e8f5e9;
    }
    .milestone h4 {
        font-size: 18px;
        color: #1b3a1a;
        margin-bottom: 8px;
    }
    .milestone p {
        font-size: 15px;
        color: #3c5e3c;
    }

    /* Team grid */
    .team-section {
        max-width: 900px;
        margin: 0 auto 20px;
    }
    .team-grid {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 30px;
    }
    .team-member {
        background: #d0edd3;
        border-radius: 14px;
        padding: 22px;
        flex: 1 1 280px;
        box-shadow: 0 4px 14px rgba(46,125,50,0.15);
        text-align: center;
        transition: background-color 0.3s ease;
    }
    .team-member:hover {
        background-color: #b8d9af;
    }
    .team-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin-bottom: 14px;
        object-fit: cover;
        border: 3px solid #1b3a1a;
    }
    .team-name {
        font-family: 'Cinzel', serif;
        font-size: 20px;
        color: #1b3a1a;
        margin-bottom: 6px;
        font-weight: 700;
    }
    .team-role {
        font-size: 14px;
        font-weight: 600;
        color: #2e7d32;
        margin-bottom: 10px;
    }
    .team-bio {
        font-size: 14px;
        color: #3c5e3c;
    }

    footer {
        background: #072808ff;
        color: #a9d18e;
        text-align: center;
        padding: 25px 0;
        font-size: 14px;
        font-weight: 500;
        margin-top: auto;
        letter-spacing: 0.05em;
    }

    /* Responsive */
    @media(max-width:900px) {
        .values-grid, .team-grid {
            flex-direction: column;
            max-width: 100%;
        }
        .value-box, .team-member {
            max-width: 100%;
            flex: none;
        }
        main {
            padding: 30px 20px;
            margin: 30px 10px 50px;
        }
    }
</style>
</head>
<body>

<header>
    <div class="logo">GreenCircle</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="about_us.php" style="color:#d0edd3;">About Us</a>
        <a href="reviews.php">Reviews</a>
        <a href="contact.php">Contact Us</a>
        <a href="logout.php">Log Out</a>
    </nav>
</header>

<main>
    <h1>About GreenCircle</h1>
    <p class="intro">Welcome, <?php echo htmlspecialchars($user_name); ?>! At GreenCircle, we are passionate about revolutionizing agriculture by fostering collaboration between landlords, farmers, technicians, and equipment providers. Our platform is designed to create sustainable, efficient, and thriving farming communities.</p>

    <section>
        <h2>Our Mission</h2>
        <p>To empower sustainable farming through a dynamic online platform that connects all stakeholders involved in agriculture, promoting resource sharing, job opportunities, and knowledge exchange.</p>
    </section>

    <section>
        <h2>Our Vision</h2>
        <p>To build a global community where agricultural collaboration drives innovation, efficiency, and prosperity for farmers and landowners alike.</p>
    </section>

    <section>
        <h2>Core Values</h2>
        <div class="values-grid">
            <div class="value-box">
                <h3>Integrity</h3>
                <p>We believe in transparent, honest interactions fostering trust within our community.</p>
            </div>
            <div class="value-box">
                <h3>Sustainability</h3>
                <p>Encouraging eco-friendly farming practices that protect the environment for future generations.</p>
            </div>
            <div class="value-box">
                <h3>Collaboration</h3>
                <p>Connecting diverse stakeholders to work together towards common agricultural goals.</p>
            </div>
        </div>
    </section>

    <section>
        <h2>Milestones</h2>
        <div class="milestones">
            <div class="milestone">
                <h4>2023: GreenCircle Launched</h4>
                <p>Our platform was launched, connecting the first 100 users from the farming community.</p>
            </div>
            <div class="milestone">
                <h4>2024: Expanded Equipment Rental</h4>
                <p>Introduced a fully integrated marketplace for renting farming tools and machinery.</p>
            </div>
            <div class="milestone">
                <h4>2025: Community Workshops</h4>
                <p>Started hosting workshops and training for sustainable farming techniques.</p>
            </div>
        </div>
    </section>

    <section class="team-section">
        <h2>Meet Our Team</h2>
        <div class="team-grid">
            <div class="team-member">
                <img src="uploads/salsabil.jpeg" alt="Salsabil Al Mujib" class="team-photo" />
                <div class="team-name">Salsabil Al Mujib</div>
                <div class="team-role">Founder & CEO</div>
                <div class="team-bio">Salsabil is an agricultural expert with 15 years experience in sustainable farming and tech innovation.</div>
            </div>
            <div class="team-member">
                <img src="uploads/samin.jpeg" alt="Muammar Mubarrat Samin" class="team-photo" />
                <div class="team-name">Muammar Mubarrat Samin</div>
                <div class="team-role">CTO</div>
                <div class="team-bio">Samin leads the technical team building GreenCircle’s platform with a focus on usability and scalability.</div>
            </div>
            <div class="team-member">
                <img src="uploads/snighdha.jpeg" alt="Snighdha Chowdhury" class="team-photo" />
                <div class="team-name">Snighdha Chowdhury</div>
                <div class="team-role">Community Manager</div>
                <div class="team-bio">Snighdha coordinates community outreach programs and supports farmers and landowners in collaboration.</div>
            </div>
        </div>
    </section>
</main>

<footer>
    &copy; <?php echo date("Y"); ?> GreenCircle. All rights reserved.
</footer>

</body>
</html>

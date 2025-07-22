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
<title>Contact Us - GreenCircle</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Roboto&display=swap" rel="stylesheet" />
<!-- Font Awesome -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  integrity="sha512-p6PrzK4JfF3Vh2xMTKbrC+M7BQdL1oKOlvIr9uRVJTWly62P+lyvEDvNVg0x4J7S4TkHg+NzNwI5Lv2HpPxhqw=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer"
/>
<style>
    /* Reset and base */
    * {
        margin: 0; padding: 0; box-sizing: border-box;
    }
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f5faf4;
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

    /* Main content */
    main {
        max-width: 720px;
        background: #fff;
        margin: 60px auto 80px;
        border-radius: 18px;
        padding: 45px 50px 60px;
        box-shadow: 0 16px 48px rgba(46, 125, 50, 0.15);
        text-align: left;
    }
    .page-title {
        font-family: 'Cinzel', serif;
        font-weight: 700;
        font-size: 42px;
        margin-bottom: 14px;
        color: #1b3a1a;
        letter-spacing: 1px;
    }
    .page-subtitle {
        font-style: italic;
        font-weight: 500;
        font-size: 17px;
        margin-bottom: 38px;
        color: #4a7c46;
    }

    .contact-section {
        font-size: 16px;
        color: #1b3a1a;
    }
    .contact-section h3 {
        font-family: 'Cinzel', serif;
        font-weight: 700;
        font-size: 22px;
        margin-bottom: 24px;
        color: #2e7d32;
        border-bottom: 2px solid #2e7d32;
        padding-bottom: 8px;
    }
    .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 26px;
        gap: 18px;
    }
    .contact-item i {
        font-size: 28px;
        color: #2e7d32;
        width: 34px;
        text-align: center;
        flex-shrink: 0;
    }
    .contact-item a, .contact-item span {
        font-weight: 600;
        color: #2e7d32;
        text-decoration: none;
        transition: color 0.3s ease;
        font-size: 17px;
    }
    .contact-item a:hover {
        color: #145214;
        text-decoration: underline;
    }

    /* Footer */
    footer {
        background-color: #e0f1df;
        text-align: center;
        font-size: 14px;
        color: #4a7c46;
        padding: 22px 20px;
        border-top: 1px solid #c8e6c9;
        font-family: 'Roboto', sans-serif;
        margin-top: auto;
    }

    @media (max-width: 600px) {
        header {
            padding: 22px 30px;
        }
        main {
            margin: 40px 15px 50px;
            padding: 30px 25px;
            border-radius: 14px;
        }
    }
</style>
</head>
<body>

<header>
    <div class="logo">GreenCircle</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="about_us.php">About Us</a>
        <a href="reviews.php">Reviews</a>
        <a href="contact.php" style="color:#d0edd3;">Contact Us</a>
        <a href="logout.php">Log Out</a>
    </nav>
</header>

<main>
    <h1 class="page-title">Contact Us</h1>
    <p class="page-subtitle">We welcome your inquiries and feedback. Please use the following contact details to reach us.</p>

    <section class="contact-section" aria-label="Contact information">
        <h3>GreenCircle Company</h3>

        
        
        
        <div class="contact-item">
            <span>Email:</span>
            <a href="mailto:greencircle@gmail.com">greencircle@gmail.com</a>
        </div>

        <div class="contact-item">
            <span>Phone:</span>
            <a href="tel:01321868891">01321868891</a>
        </div>

        <div class="contact-item">
            <span>Facebook:</span>
            <a href="https://facebook.com/greencircle" target="_blank">GreenCircle</a>
        </div>

        <div class="contact-item">
            <span>WhatsApp:</span>
            <a href="https://wa.me/01321868891" target="_blank">01321868891</a>
        </div>
    
    </section>
</main>

<footer>
    &copy; <?php echo date("Y"); ?> GreenCircle. All rights reserved.
</footer>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>GreenCircle | Agricultural Collaboration Platform</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Roboto, sans-serif;
      background: #f4f7f6;
      color: #333;
    }

    .header {
      background-color: #2e7d32;
      padding: 40px 20px;
      text-align: center;
      color: white;
    }

    .header h1 {
      margin: 0;
      font-size: 38px;
      letter-spacing: 1px;
    }

    .header p {
      margin-top: 8px;
      font-size: 16px;
      color: #c8e6c9;
    }

    .cta-buttons {
      margin-top: 25px;
    }

    .button {
      display: inline-block;
      padding: 12px 30px;
      margin: 10px;
      border-radius: 8px;
      background: #ffffff;
      color: #2e7d32;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      border: 2px solid #ffffff;
      transition: all 0.3s ease;
    }

    .button:hover {
      background: #c8e6c9;
      color: #1b5e20;
    }

    .content {
      max-width: 900px;
      margin: 40px auto;
      padding: 0 20px;
      text-align: center;
    }

    .content h2 {
      color: #2e7d32;
      margin-bottom: 10px;
    }

    .content p {
      font-size: 15px;
      line-height: 1.8;
      margin-bottom: 25px;
    }

    .role-section {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      margin-top: 30px;
    }

    .role-box {
      width: 250px;
      margin: 15px;
      padding: 20px;
      border-radius: 10px;
      background: #ffffff;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
      text-align: left;
    }

    .role-box h3 {
      color: #2e7d32;
      margin-bottom: 10px;
    }

    .role-box p {
      font-size: 14px;
      color: #666;
    }

    .footer {
      text-align: center;
      font-size: 13px;
      color: #999;
      padding: 20px 0;
      margin-top: 50px;
    }

    @media(max-width: 600px){
      .role-box {
        width: 90%;
      }

      .button {
        width: 80%;
        margin: 10px auto;
      }
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>GreenCircle</h1>
    <p>Your Complete Agricultural Collaboration Platform</p>

    <div class="cta-buttons">
      <a href="login.php" class="button">Login</a>
      <a href="register.php" class="button">Register</a>
    </div>
  </div>

  <div class="content">
    <h2>What is GreenCircle?</h2>
    <p>
      GreenCircle is a digital platform designed to connect all stakeholders in the agriculture ecosystem. Whether you're a landowner looking for skilled farmers, a technician who repairs agricultural tools, or a provider of farming equipment and supplies — GreenCircle helps you find the right people, tools, and services, all in one place.
    </p>

    <h2>Who is it for?</h2>
    <div class="role-section">
      <div class="role-box">
        <h3>Landlords</h3>
        <p>Post your available land, hire skilled farmers, or rent necessary equipment with ease.</p>
      </div>
      <div class="role-box">
        <h3>Farmers</h3>
        <p>Find job opportunities, rent machinery, or get hired for land cultivation projects.</p>
      </div>
      <div class="role-box">
        <h3>Technicians</h3>
        <p>Get hired to repair and maintain farming tools and machinery in your area.</p>
      </div>
      <div class="role-box">
        <h3>Equipment Providers</h3>
        <p>Rent out or sell your farming tools, seeds, fertilizers, and other essentials to trusted users.</p>
      </div>
    </div>
  </div>

  <div class="footer">
    &copy; <?php echo date("Y"); ?> GreenCircle. All Rights Reserved.
  </div>

</body>
</html>

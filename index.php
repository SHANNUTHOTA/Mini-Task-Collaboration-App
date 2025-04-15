<?php
include 'config/db.php';
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mini Task Collaboration App</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <style>
     * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f4f4;
    }

    header {
      background: linear-gradient(to right, #2c3e50, #34495e);
      padding: 15px 30px;
      color: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-bottom: 3px solid #2980b9;
    }

    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .site-title {
      font-size: 2rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: white;
      text-decoration: none;
    }

    .site-title:hover {
      color: #1abc9c;
    }

    nav {
      display: flex;
      gap: 20px;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-size: 1rem;
      font-weight: 600;
      padding: 8px 16px;
      border-radius: 4px;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: background 0.3s, color 0.3s;
    }

    nav a:hover {
      color: #1abc9c;
    }

    .menu-toggle {
      display: none;
      font-size: 1.8rem;
      cursor: pointer;
      color: white;
    }

    @media (max-width: 768px) {
      .header-container {
        position: relative;
        width: 100%;
        flex-direction: column;
        align-items: center;
      }

      .site-title {
        text-align: center;
        width: 100%;
        margin: 0 auto;
        padding-right: 40px; /* Space for menu icon */
      }

      .menu-toggle {
        display: block;
        position: absolute;
        right: 20px;
        top: 15px;
      }

      nav {
        flex-direction: column;
        width: 100%;
        background: linear-gradient(to right, #2c3e50, #34495e);
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.4s ease-in-out;
      }

      nav.show {
        max-height: 500px;
      }

      nav a {
        width: 100%;
        padding: 10px 20px;
      }
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body, html {
      font-family: 'Arial', sans-serif;
      background-color: #f4f7fa;
      height: 100%;
      overflow: hidden;
    }

    #preloader {
      position: fixed;
      inset: 0;
      background-color: #1b1c1d;
      color: #00ffc8;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      transition: opacity 1s ease;
    }

    #preloader h1 {
      font-size: 2.5rem;
      font-weight: bold;
      animation: flicker 1.5s infinite alternate;
      text-transform: uppercase;
      margin-bottom: 20px;
    }

    @keyframes flicker {
      0% { opacity: 1; }
      100% { opacity: 0.3; }
    }

    #progressBar {
      width: 80%;
      height: 6px;
      background-color: #fff;
      margin-top: 20px;
      position: relative;
      overflow: hidden;
    }

    #progress {
      height: 100%;
      width: 0;
      background-color: #00ffc8;
      animation: progressAnimation 2s linear forwards;
    }

    @keyframes progressAnimation {
      100% { width: 100%; }
    }

    .stars {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('https://cdn.pixabay.com/photo/2016/11/22/18/16/stars-1852922_960_720.jpg') repeat center center;
      background-size: cover;
      opacity: 0.1;
      pointer-events: none;
      z-index: -1;
      animation: starsMove 10s infinite linear;
    }

    @keyframes starsMove {
      0% { background-position: 0 0; }
      100% { background-position: 1000px 1000px; }
    }

    .container {
      padding: 100px 20px;
      text-align: center;
    }

    .container h1 {
      font-size: 2.2rem;
      color: #2c3e50;
      margin-bottom: 15px;
    }

    .container p {
      font-size: 1.1rem;
    }

    .container a {
      font-weight: bold;
      color: #2980b9;
      text-decoration: none;
    }

    .container a:hover {
      text-decoration: underline;
    }

    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background: #2c3e50;
      color: white;
      text-align: center;
      padding: 12px 0;
      font-size: 0.95rem;
      box-shadow: 0 -2px 6px rgba(0,0,0,0.1);
    }

    header {
      background-color: #2c3e50;
      padding: 15px;
      color: white;
      text-align: center;
    }

    nav a {
      margin: 0 10px;
      color: white;
      font-weight: 500;
    }

    nav a:hover, nav .active {
      color: #00ffc8;
      border-bottom: 2px solid #00ffc8;
    }

    .dropdown-menu {
      background-color: #2c3e50;
      border: none;
    }

    .dropdown-item {
      color: white;
    }

    .dropdown-item:hover {
      background-color: #00ffc8;
      color: #1b1c1d;
    }
  </style>
</head>

<body>
  <!-- Stars Background -->
  <div class="stars"></div>

  <!-- Preloader -->
  <div id="preloader">
    <h1>Mini Task </h1>
    <div id="progressBar">
      <div id="progress"></div>
    </div>
  </div>

  <!-- Header -->
  <header>
    <div class="header-container">
      <a href="index.php" class="site-title">Task App</a>
      <div class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
      </div>
      <nav id="navMenu">
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) { 
            echo '<a href="index.php" onclick="closeMenu()"><i class="fas fa-home"></i> Home</a>';
            echo '<a href="login.php" onclick="closeMenu()"><i class="fas fa-sign-in-alt"></i> Login</a>';
            echo '<a href="register.php" onclick="closeMenu()"><i class="fas fa-user-plus"></i> Register</a>';
        } else { 
            echo '<a href="index.php" onclick="closeMenu()"><i class="fas fa-home"></i> Home</a>';
            echo '<a href="dashboard.php" onclick="closeMenu()"><i class="fas fa-tachometer-alt"></i> Dashboard</a>';
            echo '<a href="logout.php" onclick="closeMenu()"><i class="fas fa-sign-out-alt"></i> Logout</a>';
        }
        ?>
      </nav>
    </div>
  </header>

<!-- Main Content -->
<!-- Main Content -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
  <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%; border-radius: 20px; border: none; background: linear-gradient(145deg, #6c5ce7, #00cec9);">
    <div class="card-body text-center text-white">
      <h1 class="card-title mb-3" style="font-size: 2rem; color: #ffffff;">Welcome 👋</h1>
      <p class="card-text mb-4" style="font-size: 1.1rem; color: #f0f0f0;">
        Join the <strong>Mini Task Collaboration App</strong> to streamline your tasks efficiently!
      </p>
      <a href="login.php" class="btn btn-light btn-block mb-2" style="border-radius: 10px; font-weight: bold; color: #2c3e50;">
        <i class="fas fa-sign-in-alt"></i> Login
      </a>
      <a href="register.php" class="btn btn-outline-light btn-block" style="border-radius: 10px; font-weight: bold; color: #black;">
        <i class="fas fa-user-plus"></i> Register
      </a>
    </div>
  </div>
</div>


  <!-- Footer -->
  <footer>
    <p>&copy; <?php echo date("Y"); ?> Mini Task Collaboration App. All rights reserved.</p>
  </footer>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/gsap.min.js"></script>
  <script>
    setTimeout(() => {
      const preloader = document.getElementById("preloader");
      preloader.style.opacity = "0";
      setTimeout(() => {
        preloader.style.display = "none";
      }, 1000);
    }, 2000);

    gsap.from('.container h1', {
      duration: 1.5,
      opacity: 0,
      y: -50,
      ease: "power4.out"
    });
    gsap.from('.container p', {
      duration: 1.5,
      opacity: 0,
      y: 50,
      ease: "power4.out",
      delay: 0.5
    });

    function toggleMenu() {
      const nav = document.getElementById('navMenu');
      nav.classList.toggle('show');
    }

    function closeMenu() {
      const nav = document.getElementById('navMenu');
      nav.classList.remove('show');
    }
  </script>
</body>
</html>

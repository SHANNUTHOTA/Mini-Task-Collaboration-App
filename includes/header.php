<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Task Collaboration App</title>
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
  </style>
</head>
<body>
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
            if ($_SESSION['role'] == 'admin') { // Check if the user is an admin
                echo '<a href="index.php" onclick="closeMenu()"><i class="fas fa-home"></i> Home</a>';
                echo '<a href="admin.php" onclick="closeMenu()"><i class="fas fa-tasks"></i> All User Tasks</a>';
                echo '<a href="task.php" onclick="closeMenu()"><i class="fas fa-plus"></i> Create Task</a>';
                echo '<a href="logout.php" onclick="closeMenu()"><i class="fas fa-sign-out-alt"></i> Logout</a>';
            } else {
                echo '<a href="index.php" onclick="closeMenu()"><i class="fas fa-home"></i> Home</a>';
                echo '<a href="dashboard.php" onclick="closeMenu()"><i class="fas fa-tachometer-alt"></i> Dashboard</a>';
                echo '<a href="logout.php" onclick="closeMenu()"><i class="fas fa-sign-out-alt"></i> Logout</a>';
            }
        }
        ?>
      </nav>
    </div>
  </header>

  <script>
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

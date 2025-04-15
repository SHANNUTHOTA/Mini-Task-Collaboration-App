<?php
include 'config/db.php';
include 'includes/header.php';

session_start(); // Ensure sessions are started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the statement to select user details including role
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind the result variables
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;

            // Redirect based on the user's role
            if ($role == 'admin') {
                header('Location: admin.php');
                exit();
            } else {
                header('Location: dashboard.php');
                exit();
            }
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "User not found!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Collaboration App</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJv+M7FESf0sA7a9dGntt6hdcbwPmyZ4zYIKR5K8p0jwFHB9e3rDkl8YPn6l" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Optional custom CSS -->
    <style>
        /* Ensure full height for HTML and Body */
html, body {
    height: 100%; /* Full viewport height */
    margin: 0;
    padding: 0;
    overflow: hidden; /* Prevent scrolling */
    background: #f4f4f4;
}

/* Body font and styling */
body {
    font-family: 'Arial', sans-serif;
}

/* Center the login container in the middle of the screen */
.container.d-flex {
    height: 100%; /* Full height of the viewport */
    display: flex;
    justify-content: center;
    align-items: center; /* Center the container both vertically and horizontally */
}

/* Login container styling */
.login-container {
    padding: 2rem 4rem; /* Adjusted padding for better spacing */
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    max-width: 400px;
    width: 100%;
}


/* Responsive Design */
@media (max-width: 768px) {
    .container {
        width: 95%;
    }

    nav a {
        display: block;
        margin: 10px 0;
    }
}
</style>
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-container p-4 rounded shadow-lg bg-white w-100" style="max-width: 400px;">
            <h2 class="text-center mb-4">Login to Your Account</h2>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="text-center mt-3">
                    Don't have an account? <a href="register.php">Register</a>
                </div>
            </form>
        </div>
    </div>


    <!-- Bootstrap JS and Popper.js (for some components like dropdowns, tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-LcQb3VY0z5Vu2JrZCdpPXY8hNRuLkd+XybgZboqOPbmqS3RXYip8xtUu5WcVuXcJ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-cn7l7gDp0eyniUwwAZgrzZVfv4+AAW/8n7pmbm78zAw6p7IcJzP00j9qKNHUEk4J" crossorigin="anonymous"></script>



    <?php include 'includes/footer.php'; ?>

</body>
</html>

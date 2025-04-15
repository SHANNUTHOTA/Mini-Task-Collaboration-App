<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/db.php';
include 'includes/header.php';


$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!$email) {
        $error = "Invalid email format.";
    } else {
        // Check if the email already exists
        $stmtCheck = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $error = "The email address is already registered.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $passwordHash);

            if ($stmt->execute()) {
                $success = "Registered successfully!";
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
        $stmtCheck->close();
    }
}
?>

<style>
 .form-wrapper {
        max-width: 450px;
        margin: 80px auto;
        background: #ffffff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
        font-family: 'Segoe UI', sans-serif;
        animation: fadeIn 0.5s ease;
    }

    .form-wrapper h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
    }

    .form-wrapper label {
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
        color: #444;
    }

    .form-wrapper input {
        width: 100%;
        padding: 12px 14px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        transition: 0.3s border;
    }

    .form-wrapper input:focus {
        border-color: #2575fc;
        outline: none;
    }

    .form-wrapper button {
        width: 100%;
        padding: 12px;
        background: #2575fc;
        border: none;
        border-radius: 10px;
        color: white;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s background;
    }

    .form-wrapper button:hover {
        background: #1a5ed8;
    }

    .alert {
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 10px;
        text-align: center;
        font-weight: bold;
        animation: fadeAlert 0.5s ease-in-out;
    }

    .alert-success {
        background-color: #e1ffed;
        color: #007e33;
    }

    .alert-error {
        background-color: #ffe1e1;
        color: #b10000;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeAlert {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
</style>

<div class="form-wrapper">
    <h2>Register</h2>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-error" id="alertBox"><?= $error; ?></div>
    <?php elseif (!empty($success)) : ?>
        <div class="alert alert-success" id="alertBox"><?= $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="8">

        <button type="submit">Register</button>
    </form>

    <p style="text-align:center; margin-top: 20px;">
        Already have an account?
        <a href="login.php" style="color: #2575fc; text-decoration: none; font-weight: bold;">
            Login
        </a>
    </p>
</div>

<script>
    // Hide the alert after 1 second
    window.addEventListener("DOMContentLoaded", () => {
        const alertBox = document.getElementById("alertBox");
        if (alertBox) {
            setTimeout(() => {
                alertBox.style.display = "none";
            }, 2000);
        }
    });
</script>

<?php include 'includes/footer.php'; ?>

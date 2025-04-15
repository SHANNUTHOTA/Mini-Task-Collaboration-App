<?php
include 'config/db.php';
include 'includes/header.php';

session_start(); // Ensure sessions are started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;

            if ($role == 'admin') {
                header('Location: admin.php');
                exit();
            } else {
                header('Location: dashboard.php');
                exit();
            }
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
    $stmt->close();
}
?>

<div class="container">
    <h2>Login</h2>
    <form method="POST" action="">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
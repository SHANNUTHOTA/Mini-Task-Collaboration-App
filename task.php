<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$isEditing = $task_id !== null;
$error = "";

// Initialize form fields
$title = '';
$deadline = '';
$priority = '';
$status = '';

// Load task data if editing
if ($isEditing) {
    $stmt = $conn->prepare("SELECT title, deadline, priority, status, user_id FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $stmt->bind_result($title, $deadline, $priority, $status, $task_user_id);
    if ($stmt->fetch()) {
        if ($task_user_id != $user_id) {
            $error = "Unauthorized access.";
            $isEditing = false;
        }
    } else {
        $error = "Task not found.";
        $isEditing = false;
    }
    $stmt->close();
}

// 🛑 Handle delete task request BEFORE update/insert logic
if (isset($_POST['delete'])) {
    if ($isEditing) {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        if ($stmt->execute()) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Error deleting task: " . $stmt->error;
        }
        $stmt->close();
    }
}

// ✅ Handle task creation or update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $deadline = filter_input(INPUT_POST, 'deadline', FILTER_SANITIZE_STRING);
    $priority = filter_input(INPUT_POST, 'priority', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    if (empty($title) || empty($deadline) || empty($priority) || empty($status)) {
        $error = "All fields are required.";
    } else {
        if ($isEditing) {
            $stmt = $conn->prepare("UPDATE tasks SET title = ?, deadline = ?, priority = ?, status = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ssssii", $title, $deadline, $priority, $status, $task_id, $user_id);
            if ($stmt->execute()) {
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Error updating task: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, deadline, priority, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $user_id, $title, $deadline, $priority, $status);
            if ($stmt->execute()) {
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Error creating task: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

<div class="container mt-4">
    <h2><?php echo $isEditing ? 'Edit' : 'Create'; ?> Task</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
        </div>

        <div class="form-group">
            <label for="deadline">Deadline:</label>
            <input type="date" id="deadline" name="deadline" class="form-control" value="<?php echo htmlspecialchars($deadline); ?>" required>
        </div>

        <div class="form-group">
            <label for="priority">Priority:</label>
            <select id="priority" name="priority" class="form-control" required>
                <option value="High" <?php echo $priority == 'High' ? 'selected' : ''; ?>>High</option>
                <option value="Medium" <?php echo $priority == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="Low" <?php echo $priority == 'Low' ? 'selected' : ''; ?>>Low</option>
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status" class="form-control" required>
                <option value="Pending" <?php echo $status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Completed" <?php echo $status == 'Completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>

        <button type="submit" class="btn btn-<?php echo $isEditing ? 'success' : 'primary'; ?>">
            <?php echo $isEditing ? 'Update' : 'Create'; ?> Task
        </button>

        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>

        <?php if ($isEditing): ?>
            <button type="submit" name="delete" class="btn btn-danger mt-3" onclick="return confirm('Are you sure you want to delete this task?')">
                Delete Task
            </button>
        <?php endif; ?>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

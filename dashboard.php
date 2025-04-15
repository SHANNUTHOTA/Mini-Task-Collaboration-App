<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config/db.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'];

// Initialize filters (with default empty values)
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$priorityFilter = isset($_GET['priority']) ? $_GET['priority'] : '';
$deadlineFilter = isset($_GET['deadline']) ? $_GET['deadline'] : '';

// Build dynamic WHERE clause and parameter list
$whereClause = "";
$params = [$user_id];

if ($statusFilter) {
    $whereClause .= " AND status = ?";
    $params[] = $statusFilter;
}

if ($priorityFilter) {
    $whereClause .= " AND priority = ?";
    $params[] = $priorityFilter;
}

if ($deadlineFilter) {
    $whereClause .= " AND deadline = ?";
    $params[] = $deadlineFilter;
}

$sql = "SELECT * FROM tasks WHERE user_id = ?" . $whereClause;
$stmt = $conn->prepare($sql);

// Dynamically determine parameter types
$types = '';
foreach ($params as $param) {
    if (is_int($param)) {
        $types .= 'i';
    } elseif (is_float($param)) {
        $types .= 'd';
    } else {
        $types .= 's';
    }
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- Include Bootstrap and custom styles for better appearance -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
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
        color: white;
        text-decoration: none;
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

    /* Card Styling (Admin Card Style) */
    .card {
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        overflow: hidden;
        border-left: 5px solid #2980b9;
        transition: transform 0.3s ease-in-out;
    }

    .card:hover {
        transform: translateY(-10px);
    }

    .card-body {
        padding: 20px;
    }

    .task-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .task-info {
        font-size: 1.1rem;
        margin-top: 10px;
        color: #555;
    }

    .task-status,
    .task-priority {
        font-weight: bold;
        margin-top: 10px;
    }

    .task-status span,
    .task-priority span {
        padding: 6px 12px;
        border-radius: 5px;
        color: white;
        font-size: 0.9rem;
    }

    .task-status .badge-warning {
        background-color: #f39c12;
    }

    .task-status .badge-success {
        background-color: #27ae60;
    }

    .task-priority .badge-danger {
        background-color: #e74c3c;
    }

    .task-priority .badge-info {
        background-color: #3498db;
    }

    .task-priority .badge-primary {
        background-color: #9b59b6;
    }

    /* Hamburger Menu */
    @media (max-width: 768px) {
        .menu-toggle {
            display: block;
            font-size: 2rem;
            cursor: pointer;
            color: white;
        }

        nav {
            display: none;
            flex-direction: column;
            gap: 0;
            width: 100%;
            background: #2c3e50;
            padding: 10px 0;
        }

        nav.show {
            display: flex;
        }

        nav a {
            padding: 10px 20px;
            width: 100%;
            text-align: center;
        }
    }

    footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: linear-gradient(to right, #2c3e50, #34495e);
        color: #ecf0f1;
        text-align: center;
        padding: 12px 15px;
        font-size: 1rem;
        z-index: 1000;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
    }

    footer p {
        margin: 0;
    }
</style>
<div class="container mt-4">
    <h2>Task Dashboard</h2>
    <a href="task.php" class="btn btn-primary mb-3">Create New Task</a>

    <!-- Task Filters -->
    <form method="GET" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status" class="form-control">
                    <option value="">All</option>
                    <option value="Pending" <?php if ($statusFilter == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Completed" <?php if ($statusFilter == 'Completed') echo 'selected'; ?>>Completed</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="priority">Filter by Priority:</label>
                <select name="priority" id="priority" class="form-control">
                    <option value="">All</option>
                    <option value="High" <?php if ($priorityFilter == 'High') echo 'selected'; ?>>High</option>
                    <option value="Medium" <?php if ($priorityFilter == 'Medium') echo 'selected'; ?>>Medium</option>
                    <option value="Low" <?php if ($priorityFilter == 'Low') echo 'selected'; ?>>Low</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="deadline">Filter by Deadline:</label>
                <input type="date" name="deadline" id="deadline" class="form-control" value="<?php echo htmlspecialchars($deadlineFilter); ?>">
            </div>
            <div class="form-group col-md-4">
                <button type="submit" class="btn btn-success">Apply Filters</button>
                <a href="dashboard.php" class="btn btn-secondary reset-btn">Reset Filters</a>
            </div>
        </div>
    </form>

    <!-- Task Display -->
    <div class="row">
        <?php if (!empty($tasks)): ?>
            <?php
                $today = date('Y-m-d');
                $tomorrow = date('Y-m-d', strtotime('+1 day'));

                foreach ($tasks as $task):
                    // Badge color classes
                    $statusBadge = '';
                    $priorityBadge = '';
                    $dueAlert = '';

                    if ($task['status'] === 'Completed') {
                        $statusBadge = 'badge badge-success';
                        $priorityBadge = 'badge badge-secondary';
                    } else {
                        if ($task['status'] === 'Pending') {
                            $statusBadge = 'badge badge-warning';
                        } else {
                            $statusBadge = 'badge badge-secondary';
                        }

                        switch ($task['priority']) {
                            case 'High':
                                $priorityBadge = 'badge badge-danger';
                                break;
                            case 'Medium':
                                $priorityBadge = 'badge badge-info';
                                break;
                            case 'Low':
                                $priorityBadge = 'badge badge-primary';
                                break;
                            default:
                                $priorityBadge = 'badge badge-secondary';
                        }

                        $dueDate = $task['deadline'];
                        if ($dueDate == $today) {
                            $dueAlert = 'Due Today!';
                        } elseif ($dueDate == $tomorrow) {
                            $dueAlert = 'Due Tomorrow!';
                        }
                    }
            ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <!-- Task Title -->
                                <div class="col-12">
                                    <h5 class="task-title"><?= htmlspecialchars($task['title']); ?></h5>
                                </div>

                                <!-- Task Deadline -->
                                <div class="col-12">
                                    <p class="task-info">
                                        <strong>Deadline: </strong><?= $task['deadline']; ?>
                                        <span class="badge badge-warning"><?= $dueAlert; ?></span>
                                    </p>
                                </div>

                                <!-- Task Priority -->
                                <div class="col-12">
                                    <p class="task-priority">
                                        <strong>Priority: </strong><span class="<?= $priorityBadge ?>"><?= $task['priority']; ?></span>
                                    </p>
                                </div>

                                <!-- Task Status -->
                                <div class="col-12">
                                    <p class="task-status">
                                        <strong>Status: </strong><span class="<?= $statusBadge ?>"><?= $task['status']; ?></span>
                                    </p>
                                </div>

                                <!-- Edit and Delete Actions -->
                                <div class="col-12 d-flex justify-content-between">
                                    <a href="task.php?id=<?= $task['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="task.php?id=<?= $task['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">No tasks found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> Task Collaboration App. All rights reserved.</p>
</footer>

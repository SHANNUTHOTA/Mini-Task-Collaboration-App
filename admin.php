<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

include 'config/db.php';
include 'includes/header.php';

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$priorityFilter = isset($_GET['priority']) ? $_GET['priority'] : '';

$query = "SELECT tasks.*, users.name AS assigned_user 
          FROM tasks 
          JOIN users ON tasks.user_id = users.id 
          WHERE 1=1";
$params = [];

if ($statusFilter) {
    $query .= " AND tasks.status = ?";
    $params[] = $statusFilter;
}
if ($priorityFilter) {
    $query .= " AND tasks.priority = ?";
    $params[] = $priorityFilter;
}

$stmt = $conn->prepare($query);
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- Styles -->
<style>
html, body {
    height: 100%;
}
body {
    display: flex;
    flex-direction: column;
}
.main-content {
    flex: 0.5;
}
.card {
    transition: transform 0.3s ease-in-out;
}
.card:hover {
    transform: translateY(-10px);
}
.card-title {
    font-size: 1.25rem;
    font-weight: bold;
}
.card-text {
    font-size: 1rem;
    color: #555;
}
.btn-sm {
    font-size: 0.875rem;
}
.filter-section {
    background-color: #f8f9fa;
    padding: 15px;
    margin-bottom: 30px;
    border-radius: 8px;
}
.badge-success {
    background-color: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 5px;
}
.badge-warning {
    background-color: #ffc107;
    color: black;
    padding: 4px 8px;
    border-radius: 5px;
}
.card.border-success {
    border-left: 5px solid #28a745 !important;
}
.card.border-warning {
    border-left: 5\px solid #ffc107 !important;
}
.countdown {
    font-weight: bold;
    color: #dc3545;
    font-size: 0.9rem;
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

</style>

<!-- Admin Dashboard -->
<div class="container mt-5 main-content">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">Admin Dashboard</h2>
            <a href="task.php" class="btn btn-primary mb-3">Create New Task</a>
        </div>
    </div>

    <!-- Alert for Pending Tasks -->
    <div class="row mb-3">
        <div class="col-12">
            <?php
            $pendingQuery = "SELECT COUNT(*) AS pending_count FROM tasks WHERE status = 'Pending'";
            $pendingResult = $conn->query($pendingQuery);
            $pendingCount = $pendingResult->fetch_assoc()['pending_count'];

            if ($pendingCount > 0): ?>
                <div class="alert alert-warning" role="alert">
                    🚨 There are <strong><?php echo $pendingCount; ?></strong> pending tasks that need attention!
                </div>
            <?php else: ?>
                <div class="alert alert-success" role="alert">
                    ✅ All tasks are completed. Great job!
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12 filter-section">
            <form method="GET" action="admin.php" class="d-flex justify-content-between flex-wrap gap-3">
                <div class="form-group">
                    <label for="statusFilter">Status</label>
                    <select name="status" id="statusFilter" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo ($statusFilter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Completed" <?php echo ($statusFilter == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priorityFilter">Priority</label>
                    <select name="priority" id="priorityFilter" class="form-control">
                        <option value="">All Priorities</option>
                        <option value="High" <?php echo ($priorityFilter == 'High') ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo ($priorityFilter == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="Low" <?php echo ($priorityFilter == 'Low') ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>

                <div class="form-group d-flex align-items-end">
                    <button type="submit" class="btn btn-success mr-2">Filter</button>
                    <a href="admin.php" class="btn btn-secondary ml-2">Reset Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks Section -->
    <div class="row">
        <?php if (empty($tasks)): ?>
            <div class="col-12">
                <p class="text-center">No tasks found with the current filters. Try adjusting the filters.</p>
            </div>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
                <?php
                    $statusColor = ($task['status'] == 'Completed') ? 'success' : 'warning';
                    $borderColor = ($task['status'] == 'Completed') ? 'border-success' : 'border-warning';
                    $deadlineId = 'countdown-' . $task['id'];
                    $deadlineTimestamp = strtotime($task['deadline']);
                ?>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card shadow-sm rounded <?php echo $borderColor; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h5>
                            <p class="card-text">
                                <strong>Deadline:</strong> <?php echo htmlspecialchars($task['deadline']); ?><br>
                                <span class="countdown" id="<?php echo $deadlineId; ?>"></span><br>
                                <strong>Priority:</strong> <?php echo htmlspecialchars($task['priority']); ?><br>
                                <strong>Status:</strong> 
                                <span class="badge badge-<?php echo $statusColor; ?>">
                                    <?php echo htmlspecialchars($task['status']); ?>
                                </span><br>
                                <strong>Assigned to:</strong> <?php echo htmlspecialchars($task['assigned_user']); ?>
                            </p>
                            <div class="d-flex justify-content-between">
                                <a href="task.php?id=<?php echo $task['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                <a href="task.php?id=<?php echo $task['id']; ?>&delete=true" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </div>
                    </div>
                    <script>
                        const countdown<?php echo $task['id']; ?> = document.getElementById('<?php echo $deadlineId; ?>');
                        const deadlineTime<?php echo $task['id']; ?> = new Date("<?php echo date('Y-m-d H:i:s', $deadlineTimestamp); ?>").getTime();

                        function updateCountdown<?php echo $task['id']; ?>() {
                            const now = new Date().getTime();
                            const distance = deadlineTime<?php echo $task['id']; ?> - now;

                            if (distance < 0) {
                                countdown<?php echo $task['id']; ?>.innerHTML = "⏰ Deadline Passed";
                                return;
                            }

                            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            countdown<?php echo $task['id']; ?>.innerHTML = `⏳ ${days}d ${hours}h ${minutes}m ${seconds}s`;
                        }

                        updateCountdown<?php echo $task['id']; ?>();
                        setInterval(updateCountdown<?php echo $task['id']; ?>, 1000);
                    </script>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php
require_once 'functions.php';

// Handle Add Task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task-name'])) {
    addTask(trim($_POST['task-name']));
    header("Location: index.php");
    exit;
}

// Handle Task Status Toggle
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $tasks = getAllTasks();
    foreach ($tasks as $task) {
        if ($task['id'] === $id) {
            markTaskAsCompleted($id, !$task['completed']);
            break;
        }
    }
    header("Location: index.php");
    exit;
}

// Handle Delete Task
if (isset($_GET['delete'])) {
    deleteTask($_GET['delete']);
    header("Location: index.php");
    exit;
}

// Handle Email Subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    subscribeEmail(trim($_POST['email']));
    header("Location: index.php?subscribed=1");
    exit;
}

$tasks = getAllTasks();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Scheduler</title>
    <style>
        .completed {
            text-decoration: line-through;
            color: gray;
        }
    </style>
</head>
<body>

<h1>Task Scheduler</h1>

<!-- Add Task -->
<form method="POST">
    <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
    <button type="submit" id="add-task">Add Task</button>
</form>

<!-- Task List -->
<ul class="task-list">
<?php foreach ($tasks as $task): ?>
    <li class="task-item <?= $task['completed'] ? 'completed' : '' ?>">
        <form method="get" style="display:inline;">
            <input type="hidden" name="toggle" value="<?= $task['id'] ?>">
            <input type="checkbox" class="task-status" onchange="this.form.submit()" <?= $task['completed'] ? 'checked' : '' ?>>
        </form>
        <?= htmlspecialchars($task['name']) ?>
        <a href="?delete=<?= $task['id'] ?>"><button class="delete-task">Delete</button></a>
    </li>
<?php endforeach; ?>
</ul>

<!-- Email Subscription -->
<h2>Subscribe for Email Reminders</h2>
<form method="POST">
    <input type="email" name="email" required />
    <button type="submit" id="submit-email">Submit</button>
</form>

<?php if (isset($_GET['subscribed'])): ?>
    <p>Verification email sent. Please check your inbox.</p>
<?php endif; ?>

</body>
</html>


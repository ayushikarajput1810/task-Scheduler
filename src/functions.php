<?php

function addTask($task_name) {
    $file = __DIR__ . '/tasks.txt';

    // Load existing tasks
    $tasks = json_decode(file_get_contents($file), true);

    // Check for duplicates
    foreach ($tasks as $task) {
        if (strtolower($task['name']) === strtolower($task_name)) {
            return; // Do not add duplicates
        }
    }

    // Create unique ID and new task
    $new_task = [
        'id' => uniqid(),
        'name' => $task_name,
        'completed' => false
    ];

    // Append and save
    $tasks[] = $new_task;
    file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
}
function getAllTasks() {
    $file = __DIR__ . '/tasks.txt';

    if (!file_exists($file)) {
        return [];
    }

    $tasks = json_decode(file_get_contents($file), true);

    return is_array($tasks) ? $tasks : [];
}
function markTaskAsCompleted($task_id, $is_completed) {
    $file = __DIR__ . '/tasks.txt';

    // Load tasks
    $tasks = json_decode(file_get_contents($file), true);

    // Update matching task
    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
            break;
        }
    }

    // Save back
    file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
}
function deleteTask($task_id) {
    $file = __DIR__ . '/tasks.txt';

    // Load tasks
    $tasks = json_decode(file_get_contents($file), true);

    // Filter out the task with the given ID
    $updated_tasks = array_filter($tasks, function($task) use ($task_id) {
        return $task['id'] !== $task_id;
    });

    // Reindex array and save
    $updated_tasks = array_values($updated_tasks);
    file_put_contents($file, json_encode($updated_tasks, JSON_PRETTY_PRINT));
}
function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}
function subscribeEmail($email) {
    $pending_file = __DIR__ . '/pending_subscriptions.txt';
    $subscribers_file = __DIR__ . '/subscribers.txt';

    // Check if already subscribed
    $subscribers = json_decode(file_get_contents($subscribers_file), true);
    if (in_array($email, $subscribers)) {
        return; // Already verified subscriber
    }

    // Generate verification code
    $code = generateVerificationCode();
    $timestamp = time();

    // Load existing pending subscriptions
    $pending = json_decode(file_get_contents($pending_file), true);
    $pending[$email] = [
        'code' => $code,
        'timestamp' => $timestamp
    ];

    // Save back
    file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT));

    // Send verification email
    $verification_link = "http://yourdomain.com/src/verify.php?email=" . urlencode($email) . "&code=" . urlencode($code);

    $subject = "Verify subscription to Task Planner";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";
    $message = '
        <p>Click the link below to verify your subscription to Task Planner:</p>
        <p><a id="verification-link" href="' . $verification_link . '">Verify Subscription</a></p>
    ';

    // Use this temporarily for local testing:
    file_put_contents('emails_log.txt', "To: $to\nSubject: $subject\n$message\n\n", FILE_APPEND);
}
function verifySubscription($email, $code) {
    $pending_file = __DIR__ . '/pending_subscriptions.txt';
    $subscribers_file = __DIR__ . '/subscribers.txt';

    // Load pending subscriptions
    $pending = json_decode(file_get_contents($pending_file), true);

    // Validate email and code
    if (!isset($pending[$email]) || $pending[$email]['code'] !== $code) {
        return false; // Invalid
    }

    // Remove from pending
    unset($pending[$email]);
    file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT));

    // Add to verified subscribers
    $subscribers = json_decode(file_get_contents($subscribers_file), true);
    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
        file_put_contents($subscribers_file, json_encode($subscribers, JSON_PRETTY_PRINT));
    }

    return true;
}
function unsubscribeEmail($email) {
    $subscribers_file = __DIR__ . '/subscribers.txt';

    // Load current subscribers
    $subscribers = json_decode(file_get_contents($subscribers_file), true);

    // Remove the email if present
    $subscribers = array_filter($subscribers, function($e) use ($email) {
        return $e !== $email;
    });

    // Reindex and save
    $subscribers = array_values($subscribers);
    file_put_contents($subscribers_file, json_encode($subscribers, JSON_PRETTY_PRINT));
}
function sendTaskEmail($email, $pending_tasks) {
    // Build task list HTML
    $task_items = '';
    foreach ($pending_tasks as $task) {
        $task_items .= '<li>' . htmlspecialchars($task['name']) . '</li>';
    }

    $unsubscribe_link = "http://yourdomain.com/src/unsubscribe.php?email=" . urlencode($email);

    // Email content
    $subject = "Task Planner - Pending Tasks Reminder";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";

    $message = '
        <h2>Pending Tasks Reminder</h2>
        <p>Here are the current pending tasks:</p>
        <ul>' . $task_items . '</ul>
        <p><a id="unsubscribe-link" href="' . $unsubscribe_link . '">Unsubscribe from notifications</a></p>
    ';

    // Use this temporarily for local testing:
    file_put_contents('emails_log.txt', "To: $to\nSubject: $subject\n$message\n\n", FILE_APPEND);
}
function sendTaskReminders() {
    $subscribers_file = __DIR__ . '/subscribers.txt';
    $tasks_file = __DIR__ . '/tasks.txt';

    // Load subscribers and tasks
    $subscribers = json_decode(file_get_contents($subscribers_file), true);
    $tasks = json_decode(file_get_contents($tasks_file), true);

    // Filter only pending (incomplete) tasks
    $pending_tasks = array_filter($tasks, function($task) {
        return !$task['completed'];
    });

    // Send email to each verified subscriber
    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
    }
}


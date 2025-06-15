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


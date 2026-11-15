<?php

return [
    'resources' => [
        'project' => 'Project',
        'projects' => 'Projects',
        'task' => 'Task',
        'tasks' => 'Tasks',
        'user' => 'User',
        'users' => 'Users',
    ],

    'fields' => [
        'owner' => 'Owner',
        'name' => 'Name',
        'title' => 'Title',
        'icon' => 'Icon',
        'color' => 'Color',
        'priority' => 'Priority',
        'status' => 'Status',
        'sort_order' => 'Sort order',
        'archived_at' => 'Archived at',
        'tasks' => 'Tasks',
        'projects' => 'Projects',
        'project' => 'Project',
        'assignee' => 'Assignee',
        'description' => 'Description',
        'due_date' => 'Due date',
        'effort_score' => 'Effort (1–10)',
        'email' => 'Email',
        'email_verified' => 'Email verified',
        'invited_by' => 'Invited by',
        'expires_at' => 'Expires at',
        'created_at' => 'Created at',
        'file_name' => 'File name',
        'file_size' => 'Size',
        'password' => 'Password',
        'is_admin' => 'Admin',
        'is_admin_help' => 'Admins can access this panel and manage every project and task.',
    ],

    'status' => [
        'active' => 'Active',
        'archived' => 'Archived',
        'unassigned' => 'Unassigned',
        'overdue' => 'Overdue',
    ],

    'actions' => [
        'archive' => 'Archive',
        'unarchive' => 'Restore',
        'revoke' => 'Revoke',
    ],

    'invitation_status' => [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'declined' => 'Declined',
        'expired' => 'Expired',
    ],

    'widgets' => [
        'total_tasks' => 'Total tasks',
    ],
];

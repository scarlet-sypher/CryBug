<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <link href="../src/output.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1f2937;
            color: #f3f4f6;
        }
        .modal-container {
            min-height: 100vh;
            padding: 1rem;
            width: 100%;
        }

        
    </style>
</head>
<body class="w-full">
    <div class="modal-container w-full">
        <?php
        // Save this as project_modal_content.php

        session_start();

        if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            echo '<p class="text-red-500">Not authenticated</p>';
            exit;
        }

        include "connection.php";

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo '<p class="text-red-500">No project ID provided</p>';
            exit;
        }

        $project_id = mysqli_real_escape_string($con, $_GET['id']);

        // Join with emp table to get manager and team member information
        $query = "SELECT p.*, m.emp_name as manager_name
                FROM project p
                LEFT JOIN employee m ON p.project_alloc_mag = m.emp_id
                WHERE p.project_id = '$project_id'";


        $result = mysqli_query($con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            echo '<p class="text-red-500">Project not found</p>';
            exit;
        }

        $project = mysqli_fetch_assoc($result);

        // Query to get team members assigned to this project
        $team_query = "SELECT e.emp_id, e.emp_name, e.emp_profile 
                    FROM employee e
                    WHERE e.emp_id IN (
                        SELECT project_alloc_emp FROM project WHERE project_id = '$project_id'
                    ) OR e.emp_id IN (
                        SELECT project_alloc_cmp FROM project WHERE project_id = '$project_id'
                    )
                    LIMIT 5"; // Limit to 5 team members for display
        $team_result = mysqli_query($con, $team_query);
        $team_members = [];
        if ($team_result && mysqli_num_rows($team_result) > 0) {
            while ($member = mysqli_fetch_assoc($team_result)) {
                $team_members[] = $member;
            }
        }

        // Function to generate status badge
        function getStatusBadge($status) {
            $color = 'gray';
            
            switch(strtolower($status)) {
                case 'active':
                    $color = 'green';
                    break;
                case 'completed':
                    $color = 'blue';
                    break;
                case 'in review':
                    $color = 'yellow';
                    break;
                case 'on hold':
                    $color = 'orange';
                    break;
                case 'cancelled':
                    $color = 'red';
                    break;
            }
            
            return '<span class="inline-block bg-' . $color . '-500 text-white text-xs px-2 py-1 rounded-full">' . $status . '</span>';
        }

        // Function to generate priority indicator
        function getPriorityIndicator($priority) {
            $color = 'gray';
            
            switch(strtolower($priority)) {
                case 'high':
                    $color = 'red';
                    break;
                case 'medium':
                    $color = 'yellow';
                    break;
                case 'low':
                    $color = 'green';
                    break;
            }
            
            return '<div class="flex items-center">
                        <span class="inline-block w-2 h-2 bg-' . $color . '-500 rounded-full mr-1"></span>
                        <span>' . $priority . '</span>
                    </div>';
        }

        // Format date
        $endDate = !empty($project['project_end_date']) ? date('M d, Y', strtotime($project['project_end_date'])) : 'Not specified';
        $startDate = !empty($project['project_start_date']) ? date('M d, Y', strtotime($project['project_start_date'])) : 'Not specified';

        ?>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 w-full max-w-full mx-auto">
            <div class="flex justify-between items-center mb-5 border-b border-gray-700 pb-4">
                <h3 class="text-xl font-bold text-green-400"><?php echo htmlspecialchars($project['project_name']); ?></h3>
                
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5 w-full">
                <div class="bg-gray-900 p-3 rounded-lg">
                    <p class="text-gray-400 text-sm mb-1">Project ID</p>
                    <p class="font-medium text-white"><?php echo htmlspecialchars($project['project_id']); ?></p>
                </div>
                <div class="bg-gray-900 p-3 rounded-lg">
                    <p class="text-gray-400 text-sm mb-1">Status</p>
                    <div><?php echo getStatusBadge($project['project_status']); ?></div>
                </div>
                <div class="bg-gray-900 p-3 rounded-lg">
                    <p class="text-gray-400 text-sm mb-1">Priority</p>
                    <div><?php echo getPriorityIndicator($project['project_priority']); ?></div>
                </div>
                <div class="bg-gray-900 p-3 rounded-lg">
                    <p class="text-gray-400 text-sm mb-1">Manager</p>
                    <p class="font-medium text-white"><?php echo !empty($project['manager_name']) ? htmlspecialchars($project['manager_name']) : 'Not assigned'; ?></p>
                </div>
                <div class="bg-gray-900 p-3 rounded-lg">
                    <p class="text-gray-400 text-sm mb-1">Start Date</p>
                    <p class="font-medium text-white"><?php echo $startDate; ?></p>
                </div>
                <div class="bg-gray-900 p-3 rounded-lg">
                    <p class="text-gray-400 text-sm mb-1">End Date</p>
                    <p class="font-medium text-white"><?php echo $endDate; ?></p>
                </div>
                <div class="bg-gray-900 p-3 rounded-lg">
                    <p class="text-gray-400 text-sm mb-1">Budget</p>
                    <p class="font-medium text-white"><?php echo !empty($project['project_budget']) ? '$' . htmlspecialchars($project['project_budget']) : 'Not specified'; ?></p>
                </div>
                <div class="bg-gray-900 p-3 rounded-lg">
                    <p class="text-gray-400 text-sm mb-1">Progress</p>
                    <div class="flex items-center">
                        <div class="w-full bg-gray-700 rounded-full overflow-hidden mr-2">
                            <div class="bg-green-500 h-2" style="width: <?php echo (int)$project['project_progress']; ?>%"></div>
                        </div>
                        <span class="text-xs text-white"><?php echo (int)$project['project_progress']; ?>%</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 p-3 rounded-lg mb-5 w-full">
                <p class="text-gray-400 text-sm mb-1">Description</p>
                <p class="text-sm text-gray-300"><?php echo htmlspecialchars($project['project_descp']); ?></p>
            </div>

            <div class="bg-gray-900 p-3 rounded-lg mb-5 w-full">
                <p class="text-gray-400 text-sm mb-2">Team Members</p>
                <div class="flex flex-wrap items-center">
                    <?php if(count($team_members) > 0): ?>
                        <?php foreach($team_members as $index => $member): ?>
                            <?php if($index < 4): ?>
                                <div class="flex items-center mr-4 mb-2">
                                    <?php if(!empty($member['emp_profile'])): ?>
                                        <img src="<?php echo htmlspecialchars($member['emp_profile']); ?>" alt="<?php echo htmlspecialchars($member['emp_name']); ?>" class="w-8 h-8 rounded-full border-2 border-gray-700">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-gray-700 flex items-center justify-center text-xs text-white">
                                            <?php echo strtoupper(substr($member['emp_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="ml-2 text-sm text-white"><?php echo htmlspecialchars($member['emp_name']); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <?php if(count($team_members) > 4): ?>
                            <div class="w-8 h-8 rounded-full bg-gray-700 border-2 border-gray-600 flex items-center justify-center text-xs text-white">
                                +<?php echo count($team_members) - 4; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-400">No team members assigned</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex space-x-3 mt-6 w-full">
                <a href="update.php" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-sm font-medium text-white">Edit Project</a>

            </div>
        </div>

        <script>
        document.getElementById('closeModal').addEventListener('click', function() {
            // Close the modal
            const modal = this.closest('.bg-gray-800');
            if (modal && modal.parentElement) {
                modal.parentElement.style.display = 'none';
                // If this is in an iframe or Ajax loaded content
                window.parent.postMessage('closeModal', '*');
            }
        });
        </script>
    </div>
</body>
</html>
<?php
  session_start();

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../employee/emp-Login.php");
    exit;
}

include "../session_manager.php";

include "connection.php" ;

$empName = $_SESSION['emp_name'] ?? 'Company';
$empProfile = $_SESSION['emp_profile'] ?? '../images/Profile/guest.png';
$empEmail = $_SESSION['emp_email'] ?? 'company@example.com';
$empID = $_SESSION['emp_id'] ?? 'No ID available';
$empRole = $_SESSION['emp_role'] ?? 'No role';
$empPhone = $_SESSION['emp_phone'] ?? 'phone number';
$empDept = $_SESSION['emp_dept'];
$empExp = $_SESSION['emp_exp'];
$empDev = $_SESSION['dev'];
$empAuto = $_SESSION['auto'];
$empDesign = $_SESSION['design'];
$empVerbal = $_SESSION['verbal'];

$magID = $_SESSION['mag_id'] ?? '';
$magName = $_SESSION['mag_name'];
$magProfile = $_SESSION['mag_profile'];
$magRole = $_SESSION['mag_role'];
$magEmail = $_SESSION['mag_email'];
$magPhone = $_SESSION['mag_phone'];

$ManagerX = $_SESSION['x'] ;
$ManagerL = $_SESSION['linkedin'] ;
$ManagerG = $_SESSION['github'] ;


// 1. Count bugs assigned to logged in user
$bugCountQuery = "SELECT COUNT(*) as bug_count FROM bug WHERE bug_assigned_to = '$empID'";
$bugCountResult = mysqli_query($con, $bugCountQuery);
$bugCount = 0;
if ($bugCountResult) {
    $bugCountRow = mysqli_fetch_assoc($bugCountResult);
    $bugCount = $bugCountRow['bug_count'];
}

// 2. Count projects assigned to logged in user
$projectCountQuery = "SELECT COUNT(*) as project_count FROM project WHERE project_alloc_emp = '$empID'";
$projectCountResult = mysqli_query($con, $projectCountQuery);
$projectCount = 0;
if ($projectCountResult) {
    $projectCountRow = mysqli_fetch_assoc($projectCountResult);
    $projectCount = $projectCountRow['project_count'];
}

// 3. Total tasks (bugs + projects)
$totalTasks = $bugCount + $projectCount;

// 4. Count completed bugs and projects
$completedBugsQuery = "SELECT COUNT(*) as completed_bugs FROM bug WHERE bug_assigned_to = '$empID' AND (bug_progress = 100 OR bug_status = 'completed')";
$completedBugsResult = mysqli_query($con, $completedBugsQuery);
$completedBugs = 0;
if ($completedBugsResult) {
    $completedBugsRow = mysqli_fetch_assoc($completedBugsResult);
    $completedBugs = $completedBugsRow['completed_bugs'];
}

$completedProjectsQuery = "SELECT COUNT(*) as completed_projects FROM project WHERE project_alloc_emp = '$empID' AND (project_progress = 100 OR project_status = 'completed')";
$completedProjectsResult = mysqli_query($con, $completedProjectsQuery);
$completedProjects = 0;
if ($completedProjectsResult) {
    $completedProjectsRow = mysqli_fetch_assoc($completedProjectsResult);
    $completedProjects = $completedProjectsRow['completed_projects'];
}

$totalCompleted = $completedBugs + $completedProjects;

// Count in-progress projects
$inProgressQuery = "SELECT COUNT(*) as in_progress FROM project WHERE project_alloc_emp = '$empID' AND project_status = 'in progress'";
$inProgressResult = mysqli_query($con, $inProgressQuery);
$inProgressCount = 0;
if ($inProgressResult) {
    $inProgressRow = mysqli_fetch_assoc($inProgressResult);
    $inProgressCount = $inProgressRow['in_progress'];
}

// Get total projects under current manager
$totalManagerProjectsQuery = "SELECT COUNT(*) as total_manager_projects FROM project 
                             WHERE project_alloc_mag = '$magID'";
$totalManagerProjectsResult = mysqli_query($con, $totalManagerProjectsQuery);
$totalManagerProjects = 0;
if ($totalManagerProjectsResult) {
    $totalManagerProjectsRow = mysqli_fetch_assoc($totalManagerProjectsResult);
    $totalManagerProjects = $totalManagerProjectsRow['total_manager_projects'];
}

// Calculate sprint progress (average of all projects progress)
$sprintProgressQuery = "SELECT AVG(project_progress) as avg_progress 
                      FROM project WHERE project_alloc_emp = '$empID'";
$sprintProgressResult = mysqli_query($con, $sprintProgressQuery);
$sprintProgress = 0;
if ($sprintProgressResult) {
    $sprintProgressRow = mysqli_fetch_assoc($sprintProgressResult);
    $sprintProgress = round($sprintProgressRow['avg_progress'] ?? 0);
}

// Get leave information
$leaveInfoQuery = "SELECT leave_total_leave, leave_remaining_leave, leave_used 
                  FROM leaveapp WHERE leave_id = '$empID'";
$leaveInfoResult = mysqli_query($con, $leaveInfoQuery);
$totalLeave = 0;
$remainingLeave = 0;
$usedLeave = 0;
if ($leaveInfoResult && mysqli_num_rows($leaveInfoResult) > 0) {
    $leaveInfoRow = mysqli_fetch_assoc($leaveInfoResult);
    $totalLeave = $leaveInfoRow['leave_total_leave'];
    $remainingLeave = $leaveInfoRow['leave_remaining_leave'];
    $usedLeave = $leaveInfoRow['leave_used'];
} else {
    // Default values if no record exists
    $totalLeave = 22;
    $remainingLeave = 14;
    $usedLeave = 8;
}

// Get team members under same manager
$teamMembersQuery = "SELECT emp_id, emp_name, emp_profile FROM employee 
                    WHERE mag_id = '$magID' AND emp_id != '$empID'";
$teamMembersResult = mysqli_query($con, $teamMembersQuery);
$teamMembers = array();
if ($teamMembersResult) {
    while ($teamMember = mysqli_fetch_assoc($teamMembersResult)) {
        $teamMembers[] = $teamMember;
    }
}

// Process leave application form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_leave'])) {
    $leaveType = mysqli_real_escape_string($con, $_POST['leave_type']);
    $leaveDuration = mysqli_real_escape_string($con, $_POST['duration']);
    $fromDate = mysqli_real_escape_string($con, $_POST['from_date']);
    $toDate = mysqli_real_escape_string($con, $_POST['to_date']);
    $reason = mysqli_real_escape_string($con, $_POST['reason']);
    $handoverId = mysqli_real_escape_string($con, $_POST['handover_to']);
    $handoverNotes = mysqli_real_escape_string($con, $_POST['handover_notes']);
    $contactNumber = mysqli_real_escape_string($con, $_POST['contact']);
    
    // Calculate number of days between dates
    $from = new DateTime($fromDate);
    $to = new DateTime($toDate);
    $interval = $from->diff($to);
    $numberOfDays = $interval->days + 1; // Including both start and end day
    
    if ($leaveDuration === 'half') {
        $numberOfDays = $numberOfDays / 2;
    }
    
    // Update leave table
    $updateLeaveQuery = "UPDATE leaveapp SET 
                        leave_type = '$leaveType',
                        leave_duration = '$leaveDuration',
                        leave_from = '$fromDate',
                        leave_to = '$toDate',
                        leave_reason = '$reason',
                        leave_handover_id = '$handoverId',
                        leave_used = leave_used + $numberOfDays,
                        leave_remaining_leave = leave_total_leave - (leave_used + $numberOfDays)
                        WHERE leave_id = '$empID'";
    
    $updateLeaveResult = mysqli_query($con, $updateLeaveQuery);
    
    if ($updateLeaveResult) {
        // Refresh leave information
        $usedLeave += $numberOfDays;
        $remainingLeave = $totalLeave - $usedLeave;
        
        // Set success message
        $leaveMessage = "Leave application submitted successfully!";
    } else {
        $leaveMessage = "Error submitting leave application: " . mysqli_error($con);
    }
}




$teamLeavesOutput = "";

// Check connection
if ($con->connect_error) {
    $teamLeavesOutput = '<div class="text-center p-3 text-red-400">Unable to connect to database</div>';
} else {
    // Current date
    $currentDate = date('Y-m-d');
    
    // Query for upcoming leaves (where leave_from is today or in the future)
    $sql = "SELECT l.*, e.emp_name 
            FROM leaveapp l 
            JOIN employee e ON l.leave_id = e.emp_id
            WHERE l.leave_from >= '$currentDate' 
            ORDER BY l.leave_from ASC 
            LIMIT 5";
    
    $result = $con->query($sql);
    
    if ($result->num_rows > 0) {
        // Display each upcoming leave
        while($row = $result->fetch_assoc()) {
            // Generate a random color for each employee
            $colors = ['blue', 'green', 'purple', 'yellow', 'pink'];
            $randomColor = $colors[array_rand($colors)];
            
            // Format dates
            $leaveFrom = date('M d', strtotime($row['leave_from']));
            $leaveTo = date('M d', strtotime($row['leave_to']));
            
            // If same month, just show day numbers
            if (date('M', strtotime($row['leave_from'])) == date('M', strtotime($row['leave_to']))) {
                $dateDisplay = $leaveFrom . "-" . date('d', strtotime($row['leave_to']));
            } else {
                $dateDisplay = $leaveFrom . "-" . $leaveTo;
            }
            
            $teamLeavesOutput .= '<div class="flex justify-between items-center p-2 bg-gray-800 rounded">
                      <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-'.$randomColor.'-500 mr-2"></div>
                        <span class="text-sm">'.$row['emp_name'].'</span>
                      </div>
                      <span class="text-xs text-gray-400">'.$dateDisplay.'</span>
                    </div>';
        }
    } else {
        $teamLeavesOutput = '<div class="text-center p-3 text-gray-400">No upcoming team leaves scheduled</div>';
    }
    
    $con->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Employee Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="dashboard.js" defer></script>
</head>
<body class="bg-gradient-custom text-white min-h-screen font-sans bg-black antialiased">

  <div class="overlay" id="sidebarOverlay"></div>
  
  <div class="flex flex-col md:flex-row">
    
    <!-- Sidebar -->
    <aside class="sidebar w-64 bg-gray-900 p-4 md:fixed md:h-screen transition-all">
      <div class="flex items-center justify-between mb-8 p-2">
        <div class="flex items-center">
          <i class="fas fa-bug text-green-500 text-2xl mr-2"></i>
          <h1 class="text-xl font-bold text-green-500">CryBug</h1>
          
        </div>
        <button class="close-sidebar md:hidden text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <nav>
        <ul class="space-y-2">
          <li>
            <a href="../index.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Home">
              <i class="fas fa-home mr-3"></i>
              <span>Home</span>
            </a>
          </li>
          <li>
            <a href="dashboard.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Dashboard">
              <i class="fas fa-tachometer-alt mr-3"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="project.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Projects">
              <i class="fas fa-project-diagram mr-3"></i>
              <span>Projects</span>
            </a>
          </li>
          <li>
            <a href="bug.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Bugs">
              <i class="fas fa-bug mr-3"></i>
              <span>Bugs</span>
            </a>
          </li>
          <li>
            <a href="update.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Reports">
              <i class="fas fa-tasks mr-3"></i>
              <span>Update Progress</span>
            </a>
          
          </li>

          <li>
            <a href="setting.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Settings">
              <i class="fas fa-cog mr-3"></i>
              <span>Settings</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <div class="mt-auto pt-8">
        <div class="border-t border-gray-700 pt-4">
          <a href="help.php" class="flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Help Center">
            <i class="fas fa-question-circle mr-3"></i>
            <span>Help Center</span>
          </a>
          <a href="logout.php"><button class="mt-4 w-full bg-green-600 hover:bg-green-700 p-2 rounded flex items-center justify-center">
            <i class="fas fa-sign-out-alt mr-2"></i>
            <span>Logout</span>
          </button></a>
        </div>
      </div>
    </aside>
    
    <!-- Main Content -->
    <main class="md:ml-64 lg:ml-64 flex-1 p-4 md:p-6 transition-all">

      <!-- Mobile Menu Toggle -->
      <button class="menu-toggle md:hidden mb-4 bg-gray-800 p-2 rounded">
        <i class="fas fa-bars"></i>
      </button>
      
      <!-- Top Bar -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold">Welcome Back, <?php echo htmlspecialchars($empName); ?></h1>
          <p class="text-gray-400" id="currentDateTime">Loading date...</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
           
          <div class="relative">
            <button id="profileDropdownBtn" class="flex items-center">
            <?php if(!empty($empProfile) && file_exists($empProfile)): ?>
              <img src="<?php echo htmlspecialchars($empProfile); ?>" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
            <?php else: ?>
              <img src="../images/Profile/guest.png" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
            <?php endif; ?>
              <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
            </button>
            <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg p-2 hidden" id="profileDropdown">
              <a href="dashboard.php" class="block p-2 hover:bg-gray-700 rounded text-sm">
                <i class="fas fa-user mr-2"></i> My Profile
              </a>
              <a href="logout.php" class="block p-2 hover:bg-gray-700 rounded text-sm text-green-400">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Stats Cards Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-blue-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-tasks text-blue-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">My Tasks</p>
              <h3 class="text-xl font-bold"><?php echo $totalTasks; ?></h3>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-green-400">
            <i class="fas fa-arrow-up mr-1"></i> 5% <span class="text-gray-400 ml-1">from last week</span>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-red-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-bug text-red-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Assigned Bugs</p>
              <h3 class="text-xl font-bold"><?php echo $bugCount; ?></h3>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-red-400">
            <i class="fas fa-arrow-up mr-1"></i> 1% <span class="text-gray-400 ml-1">from yesterday</span>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-green-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Completed</p>
              <h3 class="text-xl font-bold"><?php echo $totalCompleted; ?></h3>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-green-400">
            <i class="fas fa-arrow-up mr-1"></i> 15% <span class="text-gray-400 ml-1">from last week</span>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-purple-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-project-diagram text-purple-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Projects</p>
              <h3 class="text-xl font-bold"><?php echo $projectCount; ?></h3>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-gray-400">
            <i class="fas fa-equals mr-1"></i> <span class="text-gray-400">No change</span>
          </div>
        </div>
      </div>
      
      <!-- Profile Section -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg text-center card-hover transition-all duration-300">
          <div class="relative inline-block">
          <?php if(!empty($empProfile) && file_exists($empProfile)): ?>
            <img src="<?php echo htmlspecialchars($empProfile); ?>" alt="Profile" class="w-32 h-32 mx-auto rounded-full border-4 border-green-500" />
            <?php else: ?>
              <img src="../images/Profile/guest.png" alt="Profile" class="w-32 h-32 mx-auto rounded-full border-4 border-green-500" />
            <?php endif; ?>
            
            <span class="absolute bottom-0 right-4 bg-green-500 p-1 rounded-full h-6 w-6 flex items-center justify-center">
              <i class="fas fa-check text-xs"></i>
            </span>
          </div>
          <h2 class="mt-4 text-xl font-bold"><?php echo htmlspecialchars($empName); ?></h2>
          <p class="text-green-400"><?php echo htmlspecialchars($empRole); ?></p>
          <div class="mt-4 flex justify-center space-x-2">
            <a href="<?php  echo htmlspecialchars($ManagerL) ?>" class="bg-blue-600 hover:bg-blue-700 p-2 rounded-full" target='_blank'>
              <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="<?php echo htmlspecialchars($ManagerX)   ?>" class="bg-gray-600 hover:bg-gray-700 p-2 rounded-full" target='_blank'>
              <i class="fab fa-github"></i>
            </a>
            <a href="<?php  echo htmlspecialchars($ManagerG)  ?>" class="bg-blue-400 hover:bg-blue-500 p-2 rounded-full" target='_blank'>
              <i class="fab fa-twitter"></i>
            </a>
          </div>
          <div class="mt-4 pt-4 border-t border-gray-700">
            <p class="text-md text-gray-300"><i class="fas fa-id-badge mr-2"></i>ID: <?php echo htmlspecialchars($empID); ?></p>
            <p class="text-md text-gray-300"><i class="fas fa-building mr-2"></i><?php echo htmlspecialchars($empDept); ?></p>
            <p class="text-md text-gray-300"><i class="fas fa-envelope mr-2"></i><?php echo htmlspecialchars($empEmail); ?></p>
            <p class="text-md text-gray-300"><i class="fas fa-phone mr-2"></i><?php echo htmlspecialchars($empPhone); ?></p>
          </div>
        </div>

        <div class="col-span-2 bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
          <h2 class="text-xl font-bold mb-4">My Assignments</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-900 p-4 rounded-lg text-center stat-card">
              <div class="flex items-center justify-center mb-2">
                <i class="fas fa-folder text-green-400 mr-2 text-lg"></i>
                <h3 class="text-lg font-semibold text-green-400">My Projects</h3>
              </div>
              <p class="text-3xl font-bold"><?php echo $projectCount; ?></p>
              <p class="text-sm text-gray-400 mt-2">Out of <?php echo $totalManagerProjects; ?> total projects</p>
            </div>
            <div class="bg-gray-900 p-4 rounded-lg text-center stat-card">
              <div class="flex items-center justify-center mb-2">
                <i class="fas fa-spinner text-yellow-400 mr-2 text-lg"></i>
                <h3 class="text-lg font-semibold text-yellow-400">In Progress</h3>
              </div>
              <p class="text-3xl font-bold"><?php echo $inProgressCount; ?></p>
              <p class="text-sm text-gray-400 mt-2">Currently active</p>
            </div>
            <div class="bg-gray-900 p-4 rounded-lg text-center stat-card">
              <div class="flex items-center justify-center mb-2">
                <i class="fas fa-check-circle text-green-400 mr-2 text-lg"></i>
                <h3 class="text-lg font-semibold text-green-400">Completed</h3>
              </div>
              <p class="text-3xl font-bold"><?php echo $totalCompleted; ?></p>
              <p class="text-sm text-gray-400 mt-2">This month</p>
            </div>
          </div>
          
          <div class="mt-6">
            <div class="flex justify-between items-center mb-2">
              <h3 class="font-medium">Current Sprint Progress</h3>
              <span class="text-sm text-gray-400"><?php echo $sprintProgress; ?>%</span>
            </div>
            <div class="bg-gray-900 rounded-full overflow-hidden mb-4">
              <div class="bg-gradient-to-r from-green-500 to-green-400 h-2" style="width: <?php echo $sprintProgress; ?>%"></div>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div>
              <h3 class="font-medium mb-2">Recent Activity</h3>
              <div class="space-y-2">
                <div class="flex items-center bg-gray-900 p-2 rounded-lg">
                  <div class="rounded-full bg-blue-500 bg-opacity-20 p-2 mr-3">
                    <i class="fas fa-code-branch text-blue-400 text-sm"></i>
                  </div>
                  <div class="text-sm">
                    <p>Submitted PR for login fix</p>
                    <p class="text-xs text-gray-400">1 hour ago</p>
                  </div>
                </div>
                <div class="flex items-center bg-gray-900 p-2 rounded-lg">
                  <div class="rounded-full bg-blue-500 bg-opacity-20 p-2 mr-3">
                    <i class="fas fa-code-branch text-blue-400 text-sm"></i>
                  </div>
                  <div class="text-sm">
                    <p>Submitted PR for login fix</p>
                    <p class="text-xs text-gray-400">1 hour ago</p>
                  </div>
                </div>
                <div class="flex items-center bg-gray-900 p-2 rounded-lg">
                  <div class="rounded-full bg-green-500 bg-opacity-20 p-2 mr-3">
                    <i class="fas fa-bug text-green-400 text-sm"></i>
                  </div>
                  <div class="text-sm">
                    <p>Fixed navbar responsiveness bug</p>
                    <p class="text-xs text-gray-400">3 hours ago</p>
                  </div>
                </div>
                <div class="flex items-center bg-gray-900 p-2 rounded-lg">
                  <div class="rounded-full bg-yellow-500 bg-opacity-20 p-2 mr-3">
                    <i class="fas fa-comment text-yellow-400 text-sm"></i>
                  </div>
                  <div class="text-sm">
                    <p>Commented on issue #42</p>
                    <p class="text-xs text-gray-400">Yesterday</p>
                  </div>
                </div>
              </div>
            </div>
            <div>
              <h3 class="font-medium mb-2">Skills</h3>
              <div class="space-y-3">
                <div>
                  <div class="flex justify-between text-sm mb-1">
                    <span>Development</span>
                    <span><?php echo htmlspecialchars($empDev); ?>%</span>
                  </div>
                  <div class="bg-gray-900 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-1.5" style="width: <?php echo htmlspecialchars($empDev); ?>%"></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between text-sm mb-1">
                    <span>Automation</span>
                    <span><?php echo htmlspecialchars($empAuto); ?>%</span>
                  </div>
                  <div class="bg-gray-900 rounded-full overflow-hidden">
                    <div class="bg-purple-500 h-1.5" style="width: <?php echo htmlspecialchars($empAuto); ?>%"></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between text-sm mb-1">
                    <span>Design</span>
                    <span><?php echo htmlspecialchars($empDesign); ?>%</span>
                  </div>
                  <div class="bg-gray-900 rounded-full overflow-hidden">
                    <div class="bg-green-500 h-1.5" style="width: <?php echo htmlspecialchars($empDesign); ?>%"></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between text-sm mb-1">
                    <span>Communication</span>
                    <span><?php echo htmlspecialchars($empVerbal); ?>%</span>
                  </div>
                  <div class="bg-gray-900 rounded-full overflow-hidden">
                    <div class="bg-yellow-500 h-1.5" style="width: <?php echo htmlspecialchars($empVerbal); ?>%"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Manager & Team Section -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
        <h2 class="text-xl font-bold mb-4">My Manager</h2>
        <div class="flex items-center mb-4">
          <?php if(!empty($magProfile) && file_exists($magProfile)): ?>
            <img src="<?php echo htmlspecialchars($magProfile); ?>" alt="Manager" class="w-20 h-20 rounded-full border-2 border-blue-500" />
          <?php else: ?>
            <img src="../images/Profile/guest.png" alt="Manager" class="w-20 h-20 rounded-full border-2 border-blue-500" />
          <?php endif; ?>
          <div class="ml-4">
            <h3 class="font-bold text-lg"><?php echo htmlspecialchars($magName); ?></h3>
            <p class="text-blue-400"><?php echo htmlspecialchars($magRole); ?></p>
            <p class="text-sm text-gray-400"><i class="fas fa-envelope mr-1"></i> <?php echo htmlspecialchars($magEmail); ?></p>
            <p class="text-sm text-gray-400"><i class="fas fa-phone mr-1"></i> <?php echo htmlspecialchars($magPhone); ?></p>
          </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-700">
          <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-900 p-3 rounded-lg text-center">
              <div class="text-xs text-gray-400">Experience</div>
              <div class="text-lg font-semibold text-blue-400"><?php echo htmlspecialchars($_SESSION['mag_exp']) ; ?>+ Years</div>
            </div>
            <div class="bg-gray-900 p-3 rounded-lg text-center">
              <div class="text-xs text-gray-400">Expertise</div>
              <div class="text-lg font-semibold text-blue-400">Full Stack</div>
            </div>
            <div class="bg-gray-900 p-3 rounded-lg text-center">
              <div class="text-xs text-gray-400">Projects</div>
              <div class="text-lg font-semibold text-blue-400"><?php echo $totalManagerProjects; ?></div>
            </div>
            <div class="bg-gray-900 p-3 rounded-lg text-center">
              <div class="text-xs text-gray-400">Team Size</div>
              <div class="text-lg font-semibold text-blue-400"><?php echo count($teamMembers) + 1; ?></div>
            </div>
          </div>
          <div class="mt-3 bg-gradient-to-r from-blue-600 to-blue-400 bg-opacity-20 p-2 rounded text-center">
            <div class="text-xs">Next Team Metting</div>
            <div class="font-semibold">Wednesday, 10:00 AM</div>
          </div>
        </div>
      </div>
        
        <div class="col-span-2 bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Your Team</h2>
            <div class="text-sm text-gray-400">
              <span class="text-green-400 font-medium"><?php echo count($teamMembers); ?></span> Members
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach($teamMembers as $member): ?>
            <div class="bg-gray-900 p-3 rounded-lg flex items-center">
              <?php if(!empty($member['emp_profile']) && file_exists($member['emp_profile'])): ?>
                <img src="<?php echo htmlspecialchars($member['emp_profile']); ?>" alt="Team Member" class="w-10 h-10  rounded-full" />
              <?php else: ?>
                <img src="../images/Profile/guest.png" alt="Team Member" class="w-10 h-10 rounded-full" />
              <?php endif; ?>
              <div class="ml-3">
                <h3 class="font-medium"><?php echo htmlspecialchars($member['emp_name']); ?></h3>
                <div class="text-xs text-gray-400 mt-3">
                  <span class="bg-gray-800 rounded px-2 py-1">ID: <?php echo htmlspecialchars($member['emp_id']); ?></span>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
      
      <!-- Leave Management & Quick Actions -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
        <h2 class="text-xl font-bold mb-4">Leave Status</h2>
        <div class="space-y-4">
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>Total Leave</span>
              <span><?php echo $totalLeave; ?> days</span>
            </div>
            <div class="bg-gray-900 rounded-full overflow-hidden h-2">
              <div class="bg-blue-500 h-full" style="width: 100%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>Used Leave</span>
              <span><?php echo $usedLeave; ?> days</span>
            </div>
            <div class="bg-gray-900 rounded-full overflow-hidden h-2">
              <div class="bg-red-500 h-full" style="width: <?php echo ($usedLeave / $totalLeave) * 100; ?>%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>Remaining Leave</span>
              <span><?php echo $remainingLeave; ?> days</span>
            </div>
            <div class="bg-gray-900 rounded-full overflow-hidden h-2">
              <div class="bg-green-500 h-full" style="width: <?php echo ($remainingLeave / $totalLeave) * 100; ?>%"></div>
            </div>
          </div>
        </div>
        
        <div class="mt-6">
          <div class="bg-gray-900 p-4 rounded-lg">
            <h3 class="font-medium text-sm mb-2 text-gray-300">Upcoming Team Leaves</h3>
            <div class="space-y-2">
              <?php echo $teamLeavesOutput; ?>
            </div>
            <div class="text-center mt-3">
              <a href="#leaveApplicationForm" class="text-green-400 text-sm hover:text-green-300 inline-block">
                <i class="fas fa-calendar-plus mr-1"></i> Schedule your leave
              </a>
            </div>
          </div>
        </div>
      </div>
        
        <div class="col-span-2 bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300" id="leaveApplicationForm">
          <h2 class="text-xl font-bold mb-4">Leave Application</h2>
          <?php if(isset($leaveMessage)): ?>
          <div class="bg-green-500 bg-opacity-20 text-green-400 p-3 rounded mb-4">
            <?php echo $leaveMessage; ?>
          </div>
          <?php endif; ?>
          <form action="" method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-400 mb-1">Leave Type</label>
                <select name="leave_type" class="bg-gray-900 text-white p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-green-500">
                  <option value="casual">Casual Leave</option>
                  <option value="sick">Sick Leave</option>
                  <option value="personal">Personal Leave</option>
                  <option value="vacation">Vacation</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-400 mb-1">Duration</label>
                <select name="duration" class="bg-gray-900 text-white p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-green-500">
                  <option value="full">Full Day</option>
                  <option value="half">Half Day</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-400 mb-1">From Date</label>
                <input type="date" name="from_date" class="bg-gray-900 text-white p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-green-500" required>
              </div>
              <div>
                <label class="block text-gray-400 mb-1">To Date</label>
                <input type="date" name="to_date" class="bg-gray-900 text-white p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-green-500" required>
              </div>
            </div>
            <div>
              <label class="block text-gray-400 mb-1">Reason</label>
              <textarea name="reason" rows="2" class="bg-gray-900 text-white p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-400 mb-1">Handover To</label>
                <select name="handover_to" class="bg-gray-900 text-white p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-green-500">
                  <?php foreach($teamMembers as $member): ?>
                  <option value="<?php echo htmlspecialchars($member['emp_id']); ?>"><?php echo htmlspecialchars($member['emp_name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="block text-gray-400 mb-1">Contact During Leave</label>
                <input type="text" name="contact" value="<?php echo htmlspecialchars($empPhone); ?>" class="bg-gray-900 text-white p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-green-500">
              </div>
            </div>
            <div>
              <label class="block text-gray-400 mb-1">Handover Notes</label>
              <textarea name="handover_notes" rows="2" class="bg-gray-900 text-white p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
              <button type="submit" name="submit_leave" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">
                <i class="fas fa-paper-plane mr-2"></i> Submit Application
              </button>
            </div>
          </form>
        </div>
      </section>
      
      <!-- Quick Access Cards -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <a href="project.php" class="bg-gray-800 p-4 rounded-xl shadow-lg hover:bg-gray-700 transition-all duration-300 text-center group">
          <div class="bg-blue-500 bg-opacity-20 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-opacity-30">
            <i class="fas fa-project-diagram text-blue-400 text-2xl"></i>
          </div>
          <h3 class="font-bold">My Projects</h3>
          <p class="text-sm text-gray-400 mt-1">Manage all your projects</p>
        </a>
        <a href="bug.php" class="bg-gray-800 p-4 rounded-xl shadow-lg hover:bg-gray-700 transition-all duration-300 text-center group">
          <div class="bg-red-500 bg-opacity-20 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-opacity-30">
            <i class="fas fa-bug text-red-400 text-2xl"></i>
          </div>
          <h3 class="font-bold">Bug Tracker</h3>
          <p class="text-sm text-gray-400 mt-1">Track and fix bugs</p>
        </a>
        <a href="update.php" class="bg-gray-800 p-4 rounded-xl shadow-lg hover:bg-gray-700 transition-all duration-300 text-center group">
          <div class="bg-green-500 bg-opacity-20 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-opacity-30">
            <i class="fas fa-tasks text-green-400 text-2xl"></i>
          </div>
          <h3 class="font-bold">Update Progress</h3>
          <p class="text-sm text-gray-400 mt-1">Update task completion</p>
        </a>
        <a href="help.php" class="bg-gray-800 p-4 rounded-xl shadow-lg hover:bg-gray-700 transition-all duration-300 text-center group">
          <div class="bg-purple-500 bg-opacity-20 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-opacity-30">
            <i class="fas fa-question-circle text-purple-400 text-2xl"></i>
          </div>
          <h3 class="font-bold">Help Center</h3>
          <p class="text-sm text-gray-400 mt-1">Get support and help</p>
        </a>
      </section>
      
    </main>
  </div>

  <script>
    // Apply Leave Button
    document.getElementById('applyLeaveBtn').addEventListener('click', function() {
      document.getElementById('leaveApplicationForm').scrollIntoView({
        behavior: 'smooth'
      });
    });
  </script>
</body>
</html>
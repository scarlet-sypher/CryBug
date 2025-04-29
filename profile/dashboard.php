<?php 
  session_start();

  if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../leaders/manager-Login.php");
    exit;
  }

  $ManagerName = $_SESSION['mag_name'] ?? 'Company';
  $ManagerProfile = $_SESSION['mag_profile'] ?? '../images/Profile/guest.png';
  $ManagerEmail = $_SESSION['mag_email'] ?? 'company@example.com';
  $ManagerID = $_SESSION['mag_id'] ?? 'No ID available';
  $ManagerRole = $_SESSION['mag_role'] ?? 'No role';
  $ManagerPhone = $_SESSION['mag_phone'] ?? 'phone number';
  $Company = $_SESSION['cmp_name'];
  $CompanyID = $_SESSION['mag_cmp_id'] ?? 'No Company ID';

  $CompanyX = $_SESSION['x'] ;
  $CompanyL = $_SESSION['linkedin'] ;
  $CompanyG = $_SESSION['github'] ;


  // Database connection
  include "connection.php" ;
  include "../session_manager.php";
  
  // 1. Count total bugs from logged-in manager
  $query_total_bugs = "SELECT COUNT(*) as total_bugs FROM bug WHERE bug_alloc_mag = '$ManagerID'";
  $result_total_bugs = mysqli_query($con, $query_total_bugs);
  $total_bugs = 0;
  if ($result_total_bugs) {
    $row = mysqli_fetch_assoc($result_total_bugs);
    $total_bugs = $row['total_bugs'];
  }
  
  // 2. Count total projects under this manager
  $query_total_projects = "SELECT COUNT(*) as total_projects FROM project WHERE project_alloc_mag = '$ManagerID'";
  $result_total_projects = mysqli_query($con, $query_total_projects);
  $total_projects = 0;
  if ($result_total_projects) {
    $row = mysqli_fetch_assoc($result_total_projects);
    $total_projects = $row['total_projects'];
  }
  
  // 3. Count number of employees with same manager ID
  $query_team_size = "SELECT COUNT(*) as team_size FROM employee WHERE mag_id = '$ManagerID'";
  $result_team_size = mysqli_query($con, $query_team_size);
  $team_size = 0;
  if ($result_team_size) {
    $row = mysqli_fetch_assoc($result_team_size);
    $team_size = $row['team_size'];
  }
  
  // 4. Count resolved bugs
  $query_resolved_bugs = "SELECT COUNT(*) as resolved_bugs FROM bug WHERE bug_alloc_mag = '$ManagerID' AND bug_status = 'resolved'";
  $result_resolved_bugs = mysqli_query($con, $query_resolved_bugs);
  $resolved_bugs = 0;
  if ($result_resolved_bugs) {
    $row = mysqli_fetch_assoc($result_resolved_bugs);
    $resolved_bugs = $row['resolved_bugs'];
  }
  
  // 7. Count ongoing projects
  $query_ongoing_projects = "SELECT COUNT(*) as ongoing_projects FROM project WHERE project_alloc_mag = '$ManagerID' AND project_status = 'ongoing'";
  $result_ongoing_projects = mysqli_query($con, $query_ongoing_projects);
  $ongoing_projects = 0;
  if ($result_ongoing_projects) {
    $row = mysqli_fetch_assoc($result_ongoing_projects);
    $ongoing_projects = $row['ongoing_projects'];
  }
  
  // 8. Count completed projects
  $query_completed_projects = "SELECT COUNT(*) as completed_projects FROM project WHERE project_alloc_mag = '$ManagerID' AND project_status = 'complete'";
  $result_completed_projects = mysqli_query($con, $query_completed_projects);
  $completed_projects = 0;
  if ($result_completed_projects) {
    $row = mysqli_fetch_assoc($result_completed_projects);
    $completed_projects = $row['completed_projects'];
  }
  
  // 9. Calculate project completion percentage
  $completion_percentage = 0;
  if ($total_projects > 0) {
    $completion_percentage = round(($completed_projects / $total_projects) * 100);
  }
  
  // 11. Get employee details with this manager ID
  $query_employees = "SELECT e.emp_id, e.emp_name, e.emp_mail, e.emp_role, e.onLeave FROM employee e WHERE e.mag_id = '$ManagerID' LIMIT 3";
  $result_employees = mysqli_query($con, $query_employees);
  
  // 12. Get recent bugs
  $query_recent_bugs = "SELECT bug_id, bug_title, bug_descp, bug_severity, bug_assigned_to, bug_created_date FROM bug WHERE bug_alloc_mag = '$ManagerID' ORDER BY bug_created_date DESC LIMIT 3";
  $result_recent_bugs = mysqli_query($con, $query_recent_bugs);
  
  // 12. Get recent projects
  $query_recent_projects = "SELECT project_id, project_name, project_descp, project_status, project_progress FROM project WHERE project_alloc_mag = '$ManagerID' ORDER BY project_start_date DESC LIMIT 3";
  $result_recent_projects = mysqli_query($con, $query_recent_projects);
  
// Leave Management - Get leave data for the logged-in manager
$query_leave_data = "SELECT leave_total_leave, leave_remaining_leave, leave_used FROM leaveapp WHERE leave_id = '$ManagerID' LIMIT 1";
$result_leave_data = mysqli_query($con, $query_leave_data);

$totalLeave = 30; // Default value
$remainingLeave = 30; // Default value
$usedLeave = 0; // Default value

if ($result_leave_data && mysqli_num_rows($result_leave_data) > 0) {
  $leave_data = mysqli_fetch_assoc($result_leave_data);
  $totalLeave = $leave_data['leave_total_leave'] ?? 30;
  $remainingLeave = $leave_data['leave_remaining_leave'] ?? 30;
  $usedLeave = $leave_data['leave_used'] ?? 0;
}

// Get all team members with their leave status
$query_all_mag_members = "SELECT m.mag_id, m.mag_name, m.mag_email,m.mag_profile, m.mag_role, m.onLeave,
                           (SELECT COUNT(*) FROM leaveapp l WHERE l.leave_id = m.mag_id 
                            AND CURDATE() BETWEEN l.leave_from AND l.leave_to) as is_on_leave
                           FROM manager m 
                           WHERE m.mag_cmp_id = '$CompanyID'";
$result_all_mag_members = mysqli_query($con, $query_all_mag_members);


$query_all_team_members = "SELECT e.emp_id, e.emp_name, e.emp_mail,e.emp_profile, e.emp_role, e.onLeave,
                           (SELECT COUNT(*) FROM leaveapp l WHERE l.leave_id = e.emp_id 
                            AND CURDATE() BETWEEN l.leave_from AND l.leave_to) as is_on_leave
                           FROM employee e 
                           WHERE e.mag_id = '$ManagerID'";
$result_all_team_members = mysqli_query($con, $query_all_team_members);

// Get upcoming team leaves (next 30 days)
$query_mag_leaves = "SELECT m.mag_id, m.mag_name, l.leave_type, l.leave_from, l.leave_to,
                      DATEDIFF(l.leave_to, l.leave_from) + 1 as days
                      FROM manager m
                      JOIN leaveapp l ON m.mag_id = l.leave_id 
                      WHERE m.mag_cmp_id = '$CompanyID' 
                      AND l.leave_from BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                      ORDER BY l.leave_from ASC 
                      LIMIT 5";
$result_mag_leaves = mysqli_query($con, $query_mag_leaves);

$query_team_leaves = "SELECT e.emp_id, e.emp_name, l.leave_type, l.leave_from, l.leave_to,
                      DATEDIFF(l.leave_to, l.leave_from) + 1 as days
                      FROM employee e 
                      JOIN leaveapp l ON e.emp_id = l.leave_id 
                      WHERE e.mag_id = '$ManagerID' 
                      AND l.leave_from BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                      ORDER BY l.leave_from ASC 
                      LIMIT 5";
$result_team_leaves = mysqli_query($con, $query_team_leaves);

// Process leave application form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_leave'])) {
  $leave_type = mysqli_real_escape_string($con, $_POST['leave_type']);
  $duration = mysqli_real_escape_string($con, $_POST['duration']);
  $leave_from = mysqli_real_escape_string($con, $_POST['from_date']);
  $leave_to = mysqli_real_escape_string($con, $_POST['to_date']);
  $leave_reason = mysqli_real_escape_string($con, $_POST['reason']);
  $handover_id = mysqli_real_escape_string($con, $_POST['handover_to']);
  $handover_notes = mysqli_real_escape_string($con, $_POST['handover_notes']);
  
  // Calculate leave days
  $date1 = new DateTime($leave_from);
  $date2 = new DateTime($leave_to);
  $interval = $date1->diff($date2);
  $leave_days = $interval->days + 1; // Include both start and end dates
  
  if ($duration == 'half') {
    $leave_days = $leave_days / 2;
  }
  
  // Check if leave record exists for this user
  $check_query = "SELECT * FROM leaveapp WHERE leave_id = '$ManagerID'";
  $check_result = mysqli_query($con, $check_query);
  
  if (mysqli_num_rows($check_result) > 0) {
    // Update existing leave record
    $update_query = "UPDATE leaveapp SET 
                     leave_type = '$leave_type', 
                     leave_duration = '$duration', 
                     leave_from = '$leave_from', 
                     leave_to = '$leave_to', 
                     leave_reason = '$leave_reason',
                     leave_handover_id = '$handover_id',
                     leave_used = leave_used + $leave_days,
                     leave_remaining_leave = leave_total_leave - (leave_used + $leave_days)
                     WHERE leave_id = '$ManagerID'";
                     
    if (mysqli_query($con, $update_query)) {
      $leaveMessage = '<i class="fas fa-check-circle mr-2"></i> Leave application updated successfully!';
    } else {
      $leaveError = '<i class="fas fa-exclamation-circle mr-2"></i> Error updating leave: ' . mysqli_error($con);
    }
  } else {
    // Insert new leave record
    $insert_query = "INSERT INTO leaveapp 
                     (leave_id, leave_type, leave_duration, leave_from, leave_to, 
                     leave_reason, leave_handover_id, leave_total_leave, leave_remaining_leave, leave_used) 
                     VALUES 
                     ('$ManagerID', '$leave_type', '$duration', '$leave_from', '$leave_to', 
                     '$leave_reason', '$handover_id', 30, (30 - $leave_days), $leave_days)";
                     
    if (mysqli_query($con, $insert_query)) {
      $leaveMessage = '<i class="fas fa-check-circle mr-2"></i> Leave application submitted successfully!';
    } else {
      $leaveError = '<i class="fas fa-exclamation-circle mr-2"></i> Error submitting leave: ' . mysqli_error($con);
    }
  }
  
  // Update onLeave status in employee table
  $update_status = "UPDATE employee SET onLeave = 1 WHERE emp_id = '$ManagerID'";
  mysqli_query($con, $update_status);
}

// Prepare team leaves output
$teamLeavesOutput = '';
if ($result_mag_leaves && mysqli_num_rows($result_mag_leaves) > 0) {
  while ($leave = mysqli_fetch_assoc($result_mag_leaves)) {
    // Generate a random color for each employee
    $colors = ['blue', 'green', 'purple', 'yellow', 'pink'];
    $randomColor = $colors[array_rand($colors)];
    
    $start_date = date('M d', strtotime($leave['leave_from']));
    $end_date = date('M d', strtotime($leave['leave_to']));
    
    $teamLeavesOutput .= '
    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <div class="h-8 w-8 rounded-full bg-'.$randomColor.'-500 bg-opacity-20 flex items-center justify-center mr-2">
          <i class="fas fa-user-clock text-'.$randomColor.'-400 text-xs"></i>
        </div>
        <div>
          <p class="text-sm">' . htmlspecialchars($leave['mag_name']) . '</p>
          <p class="text-xs text-gray-400">' . $start_date . ' - ' . $end_date . '</p>
        </div>
      </div>
      <span class="text-xs bg-'.$randomColor.'-500 bg-opacity-20 text-'.$randomColor.'-400 px-2 py-1 rounded">
        ' . $leave['days'] . ' days
      </span>
    </div>';
  }
} else {
  $teamLeavesOutput = '<p class="text-gray-400 text-sm text-center">No upcoming leaves</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | User Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <link rel="stylesheet" href="../src/output.css">
  <script src="dashboard.js" defer></script>

  <style>
  /* Additional custom styles */
  .card-hover {
    transition: all 0.3s ease;
  }
  .card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
  }
  
  @keyframes pulse-animation {
    0% {
      box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
    }
    70% {
      box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
    }
  }
  
  .pulse-dot {
    animation: pulse-animation 2s infinite;
  }
</style>
</head>
<body class="bg-gradient-custom text-white min-h-screen font-sans bg-black antialiased">

  <div class="overlay" id="sidebarOverlay"></div>
  
  <div class="flex flex-col md:flex-row">
    
    <!-- Sidebar -->
    <aside class="sidebar w-64 bg-gray-900 p-4 md:fixed md:h-screen transition-all">
      <div class="flex items-center justify-between mb-8 p-2">
        <div class="flex items-center">
          <i class="fas fa-bug text-red-500 text-2xl mr-2"></i>
          <h1 class="text-xl font-bold text-red-500">CryBug</h1>
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
            <a href="project.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Projects" data-section="projects-section">
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
            <a href="team.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Team">
              <i class="fas fa-users mr-3"></i>
              <span>Team</span>
            </a>
          </li>
          <li>
            <a href="feedback.php" class="sidebar-link  flex items-center p-3 rounded text-white" data-title="Feedback">
              <i class="fas fa-comment-dots mr-3"></i>
              <span>Feedback</span>
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
          <a href="logout.php"><button class="mt-4 w-full bg-red-600 hover:bg-red-700 p-2 rounded flex items-center justify-center">
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
          <h1 class="text-2xl md:text-3xl font-bold">Welcome Back, <?php echo ucfirst(htmlspecialchars($ManagerName)); ?></h1>
          <p class="text-gray-400" id="currentDateTime">Loading date...</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
          
          <div class="relative">
            <button id="profileDropdownBtn" class="flex items-center">
              <?php if (!empty($ManagerProfile) && file_exists($ManagerProfile)): ?>
                <img src="<?php echo htmlspecialchars($ManagerProfile); ?>" alt="Profile" class="h-10 w-10 rounded-full border-2 border-red-500" />
              <?php else: ?>
                <img src="../images/Profile/guest.png" alt="Profile" class="h-10 w-10 rounded-full border-2 border-red-500" />
              <?php endif; ?>
              <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
            </button>
            <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg p-2 hidden" id="profileDropdown">
              <a href="dashboard.php" class="block p-2 hover:bg-gray-700 rounded text-sm">
                <i class="fas fa-user mr-2"></i> My Profile
              </a>
              <a href="setting.php" class="block p-2 hover:bg-gray-700 rounded text-sm">
                <i class="fas fa-cog mr-2"></i> Account Settings
              </a>
              <a href="logout.php" class="block p-2 hover:bg-gray-700 rounded text-sm text-red-400">
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
            <div class="rounded-full bg-red-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-bug text-red-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Total Bugs</p>
              <h3 class="text-xl font-bold"><?php echo $total_bugs; ?></h3>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-red-400">
            <i class="fas fa-bug mr-1"></i> <span class="text-gray-400 ml-1">bugs assigned to you</span>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-blue-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-project-diagram text-blue-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Total Projects</p>
              <h3 class="text-xl font-bold"><?php echo $total_projects; ?></h3>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-blue-400">
            <i class="fas fa-folder mr-1"></i> <span class="text-gray-400 ml-1">projects under management</span>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-green-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Resolved Bugs</p>
              <h3 class="text-xl font-bold"><?php echo $resolved_bugs; ?></h3>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-green-400">
            <i class="fas fa-check mr-1"></i> <span class="text-gray-400 ml-1">issues fixed</span>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-purple-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-users text-purple-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Team Size</p>
              <h3 class="text-xl font-bold"><?php echo $team_size; ?></h3>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-purple-400">
            <i class="fas fa-users mr-1"></i> <span class="text-gray-400">team members</span>
          </div>
        </div>
      </div>
      
      <!-- Profile Section -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg text-center card-hover transition-all duration-300">
          <div class="relative inline-block">
            <?php if (!empty($ManagerProfile) && file_exists($ManagerProfile)): ?>
              <img src="<?php echo htmlspecialchars($ManagerProfile); ?>" alt="Profile" class="w-32 h-32 mx-auto rounded-full border-4 border-red-500" />
            <?php else: ?>
              <img src="../images/Profile/guest.png" alt="Profile" class="w-32 h-32 mx-auto rounded-full border-4 border-red-500" />
            <?php endif; ?>
            <span class="absolute bottom-0 right-4 bg-green-500 p-1 rounded-full h-6 w-6 flex items-center justify-center">
              <i class="fas fa-check text-xs"></i>
            </span>
          </div>
          <h2 class="mt-4 text-2xl font-bold"><?php echo htmlspecialchars($ManagerName); ?></h2>
          <p class="text-red-400 text-lg"><?php echo htmlspecialchars($ManagerRole); ?></p>
          <div class="mt-4 flex justify-center space-x-2">
            <a href="<?php  echo htmlspecialchars($CompanyL) ?>" class="bg-blue-600 hover:bg-blue-700 p-2 rounded-full" target='_blank'>
              <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="<?php echo htmlspecialchars($CompanyL)   ?>" class="bg-gray-600 hover:bg-gray-700 p-2 rounded-full" target='_blank'>
              <i class="fab fa-github"></i>
            </a>
            <a href="<?php  echo htmlspecialchars($CompanyL)  ?>" class="bg-blue-400 hover:bg-blue-500 p-2 rounded-full" target='_blank'>
              <i class="fab fa-twitter"></i>
            </a>
          </div>
          <div class="mt-4 pt-4 border-t border-gray-700">
            <p class="text-md text-gray-300"><i class="fas fa-id-badge mr-2"></i>Manager ID: <?php echo htmlspecialchars($ManagerID); ?></p>
            <p class="text-md text-gray-300"><i class="fas fa-building mr-2"></i><?php echo htmlspecialchars($Company); ?></p>
            <p class="text-md text-gray-300"><i class="fas fa-building mr-2"></i>Company ID: <?php echo htmlspecialchars($CompanyID); ?></p>
            <p class="text-md text-gray-300"><i class="fas fa-envelope mr-2"></i><?php echo htmlspecialchars($ManagerEmail); ?></p>
            <p class="text-md text-gray-300"><i class="fas fa-phone mr-2"></i><?php echo htmlspecialchars($ManagerPhone); ?></p>
          </div>
        </div>

        <div class="col-span-2 bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
          <h2 class="text-xl font-bold mb-4">Project Statistics</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-900 p-4 rounded-lg text-center stat-card">
              <div class="flex items-center justify-center mb-2">
                <i class="fas fa-folder text-red-400 mr-2 text-lg"></i>
                <h3 class="text-lg font-semibold text-red-400">Total Projects</h3>
              </div>
              <p class="text-3xl font-bold"><?php echo $total_projects; ?></p>
              <p class="text-sm text-gray-400 mt-2">Managed by you</p>
            </div>
            <div class="bg-gray-900 p-4 rounded-lg text-center stat-card">
              <div class="flex items-center justify-center mb-2">
                <i class="fas fa-spinner text-yellow-400 mr-2 text-lg"></i>
                <h3 class="text-lg font-semibold text-yellow-400">Ongoing</h3>
              </div>
              <p class="text-3xl font-bold"><?php echo $ongoing_projects; ?></p>
              <p class="text-sm text-gray-400 mt-2">Currently in progress</p>
            </div>
            <div class="bg-gray-900 p-4 rounded-lg text-center stat-card">
              <div class="flex items-center justify-center mb-2">
                <i class="fas fa-check-circle text-green-400 mr-2 text-lg"></i>
                <h3 class="text-lg font-semibold text-green-400">Completed</h3>
              </div>
              <p class="text-3xl font-bold"><?php echo $completed_projects; ?></p>
              <p class="text-sm text-gray-400 mt-2"><?php echo $total_projects > 0 ? round(($completed_projects/$total_projects) * 100) : 0; ?>% success rate</p>
            </div>
          </div>
          
          <div class="mt-6">
            <div class="flex justify-between items-center mb-2">
              <h3 class="font-medium">Project Completion</h3>
              <span class="text-sm text-gray-400"><?php echo $completion_percentage; ?>%</span>
            </div>
            <div class="bg-gray-900 rounded-full overflow-hidden mb-4">
              <div class="bg-gradient-to-r from-red-500 to-red-400 h-2" style="width: <?php echo $completion_percentage; ?>%"></div>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div>
              <h3 class="font-medium mb-2">Recent Activity</h3>
              <div class="space-y-2">
                <?php
                // Get recent project activity
                $recentActivityQuery = "SELECT project_name, project_start_date, project_status 
                                        FROM project 
                                        WHERE project_alloc_mag = '$ManagerID' 
                                        ORDER BY created_at DESC LIMIT 3";
                $recentActivityResult = mysqli_query($con, $recentActivityQuery);
                
                if ($recentActivityResult && mysqli_num_rows($recentActivityResult) > 0) {
                    while ($activity = mysqli_fetch_assoc($recentActivityResult)) {
                        $activityDate = new DateTime($activity['project_start_date']);
                        $now = new DateTime();
                        $interval = $now->diff($activityDate);
                        
                        $timeAgo = '';
                        if ($interval->d == 0) {
                            $timeAgo = 'Today';
                        } elseif ($interval->d == 1) {
                            $timeAgo = 'Yesterday';
                        } else {
                            $timeAgo = $interval->d . ' days ago';
                        }
                        
                        // Choose icon based on status but with red theme
                        $iconClass = 'fas fa-project-diagram';
                        $iconBgClass = 'bg-red-500';
                        $iconTextClass = 'text-red-400';
                        
                        if (strtolower($activity['project_status']) == 'complete') {
                            $iconClass = 'fas fa-check-circle';
                            $iconBgClass = 'bg-red-600';
                            $iconTextClass = 'text-red-400';
                        } elseif (strtolower($activity['project_status']) == 'ongoing') {
                            $iconClass = 'fas fa-spinner';
                            $iconBgClass = 'bg-red-400';
                            $iconTextClass = 'text-red-300';
                        }
                ?>
                        <div class="flex items-center bg-gray-900 p-2 rounded-lg">
                          <div class="rounded-full <?php echo $iconBgClass; ?> bg-opacity-20 p-2 mr-3">
                            <i class="<?php echo $iconClass . ' ' . $iconTextClass; ?> text-sm"></i>
                          </div>
                          <div class="text-sm">
                            <p><?php echo htmlspecialchars($activity['project_name']); ?></p>
                            <p class="text-xs text-gray-400"><?php echo $timeAgo; ?></p>
                          </div>
                        </div>
                <?php
                    }
                } else {
                ?>
                    <div class="flex items-center bg-gray-900 p-2 rounded-lg">
                      <div class="text-sm">
                        <p>No recent activity</p>
                      </div>
                    </div>
                <?php
                }
                ?>
              </div>
            </div>
            <div>
              <h3 class="font-medium mb-2">Upcoming Holidays</h3>
              <div class="space-y-2">
                <?php
                // Get upcoming holidays
                $holidaysQuery = "SELECT holiday_name, holiday_date, holiday_cmp_id 
                                FROM holiday 
                                WHERE holiday_date >= CURDATE() 
                                AND (holiday_cmp_id IS NULL OR holiday_cmp_id = '$CompanyID') 
                                ORDER BY holiday_date ASC LIMIT 3";
                $holidaysResult = mysqli_query($con, $holidaysQuery);
                
                if ($holidaysResult && mysqli_num_rows($holidaysResult) > 0) {
                    while ($holiday = mysqli_fetch_assoc($holidaysResult)) {
                        $holidayDate = new DateTime($holiday['holiday_date']);
                        $formattedDate = $holidayDate->format('l, M j');
                        
                        // Determine if it's a company-specific holiday - with red theme
                        $isCompanySpecific = !empty($holiday['holiday_cmp_id']);
                        $borderClass = $isCompanySpecific ? 'border-red-600' : 'border-red-500';
                        $badgeClass = $isCompanySpecific ? 'bg-red-600' : 'bg-red-500';
                        $badgeText = $isCompanySpecific ? 'Company' : 'Public';
                ?>
                        <div class="flex items-center bg-gray-900 p-2 rounded-lg border-l-4 <?php echo $borderClass; ?>">
                          <div class="text-sm flex-1">
                            <p><?php echo htmlspecialchars($holiday['holiday_name']); ?></p>
                            <p class="text-xs text-gray-400"><?php echo $formattedDate; ?></p>
                          </div>
                          <span class="text-xs <?php echo $badgeClass; ?> px-2 py-1 rounded"><?php echo $badgeText; ?></span>
                        </div>
                <?php
                    }
                } else {
                ?>
                    <div class="flex items-center bg-gray-900 p-2 rounded-lg">
                      <div class="text-sm">
                        <p>No upcoming holidays</p>
                      </div>
                    </div>
                <?php
                }
                ?>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap justify-end gap-4 mt-6">
            <a href="project.php">
              <button class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 px-6 py-3 rounded-lg flex items-center shadow-lg transform transition-all duration-300 hover:scale-105">
                <i class="fas fa-plus mr-2"></i> Add New Project
              </button>
            </a>

            <a href="bug.php">
              <button class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 px-6 py-3 rounded-lg flex items-center shadow-lg transform transition-all duration-300 hover:scale-105">
                <i class="fas fa-bug mr-2"></i> Report New Bug
              </button>
            </a>
          </div>
        </div>
      </section>

      <!-- Team Table -->
          <!-- Team Table -->
          <section class="mb-8">
  <div class="bg-gray-800 rounded-xl p-6 shadow-lg card-hover transition-all duration-300">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold">Team Members</h2>
      <div class="flex space-x-2">
        <span class="flex items-center text-xs text-green-400 bg-green-500 bg-opacity-10 px-3 py-1 rounded-full">
          <span class="h-2 w-2 bg-green-400 rounded-full mr-2"></span> Active
        </span>
        <span class="flex items-center text-xs text-yellow-400 bg-yellow-500 bg-opacity-10 px-3 py-1 rounded-full">
          <span class="h-2 w-2 bg-yellow-400 rounded-full mr-2"></span> On Leave
        </span>
      </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <?php 
      if ($result_all_team_members && mysqli_num_rows($result_all_team_members) > 0) {
        mysqli_data_seek($result_all_team_members, 0);
        while ($member = mysqli_fetch_assoc($result_all_team_members)) {
          $is_on_leave = $member['is_on_leave'] > 0 || $member['onLeave'] > 0;
          $status_class = $is_on_leave ? "border-yellow-500 bg-yellow-500 bg-opacity-5" : "border-green-500 bg-green-500 bg-opacity-5";
          $status_dot = $is_on_leave ? "bg-yellow-500" : "bg-green-500";
          $status_text = $is_on_leave ? "On Leave" : "Active";
          $status_text_color = $is_on_leave ? "text-yellow-400" : "text-green-400";
      ?>
      <div class="relative bg-gray-900 rounded-xl p-4 border-l-4 <?php echo $status_class; ?> hover:transform hover:scale-105 transition-all duration-300">
        <div class="absolute top-4 right-4">
          <span class="flex items-center <?php echo $status_text_color; ?> text-xs">
            <span class="h-2 w-2 <?php echo $status_dot; ?> rounded-full mr-1 pulse-dot"></span>
            <?php echo $status_text; ?>
          </span>
        </div>
        <div class="flex items-center space-x-3">
          <div class="relative">
            <img src="<?php echo htmlspecialchars($member['emp_profile']) ; ?>" alt="<?php echo htmlspecialchars($member['emp_name']); ?>" class="h-16 w-16 rounded-full object-cover border-2 <?php echo $is_on_leave ? 'border-yellow-500' : 'border-green-500'; ?>">
            <span class="absolute bottom-0 right-0 h-4 w-4 <?php echo $status_dot; ?> rounded-full border-2 border-gray-900"></span>
          </div>
          <div>
            <h3 class="font-bold text-lg"><?php echo htmlspecialchars($member['emp_name']); ?></h3>
            <p class="text-sm text-gray-400"><?php echo htmlspecialchars($member['emp_role']); ?></p>
          </div>
        </div>
        <div class="mt-4 pt-3 border-t border-gray-800 text-sm">
          <p class="flex items-center mb-1">
            <i class="fas fa-id-badge text-gray-500 w-5"></i>
            <span class="text-gray-300"><?php echo htmlspecialchars($member['emp_id']); ?></span>
          </p>
          <p class="flex items-center overflow-hidden">
            <i class="fas fa-envelope text-gray-500 w-5"></i>
            <span class="text-gray-300 truncate"><?php echo htmlspecialchars($member['emp_mail']); ?></span>
          </p>
        </div>
        
      </div>
      <?php 
        }
      } else {
      ?>
      <div class="col-span-3 bg-gray-900 p-6 rounded-lg text-center">
        <div class="text-gray-400">
          <i class="fas fa-users text-4xl mb-3"></i>
          <p class="text-lg">No team members found</p>
          <p class="text-sm mt-2">Team members assigned to you will appear here</p>
        </div>
      </div>
      <?php } ?>
    </div>
    
    <div class="mt-6 flex justify-end">
      <a href="team.php" class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 px-4 py-2 rounded-lg flex items-center shadow-lg transform transition-all duration-300 hover:scale-105">
        <i class="fas fa-users mr-2"></i> Manage Team
      </a>
    </div>
  </div>
</section>

    <!-- Two Columns: Recent Bugs and Projects -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Recent Bugs -->
      <section>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg h-full card-hover transition-all duration-300">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Recent Bugs</h2>
            <a href="bug.php" class="text-red-400 hover:text-red-300 text-sm">View all</a>
          </div>
          
          <div class="space-y-4">
            <?php 
            if ($result_recent_bugs && mysqli_num_rows($result_recent_bugs) > 0) {
              while ($row = mysqli_fetch_assoc($result_recent_bugs)) {
                $severity_class = "";
                switch (strtolower($row['bug_severity'])) {
                  case 'critical': 
                    $severity_class = "bg-red-500"; 
                    break;
                  case 'high': 
                    $severity_class = "bg-orange-500"; 
                    break;
                  case 'medium': 
                    $severity_class = "bg-yellow-500"; 
                    break;
                  case 'low': 
                    $severity_class = "bg-blue-500"; 
                    break;
                  default: 
                    $severity_class = "bg-gray-500";
                }
                
                // Format date to be more readable
                $created_date = date('M d, Y', strtotime($row['bug_created_date']));
            ?>
            <div class="bg-gray-900 p-4 rounded-lg">
              <div class="flex justify-between">
                <div class="flex items-center">
                  <span class="<?php echo $severity_class; ?> w-3 h-3 rounded-full mr-2"></span>
                  <h3 class="font-semibold"><?php echo htmlspecialchars($row['bug_title']); ?></h3>
                </div>
                <span class="text-xs text-gray-400"><?php echo $created_date; ?></span>
              </div>
              <p class="text-sm text-gray-400 mt-2 line-clamp-2"><?php echo htmlspecialchars($row['bug_descp']); ?></p>
              <div class="mt-3 flex justify-between items-center">
                <span class="text-xs px-2 py-1 rounded bg-gray-800">
                  Assigned to: <?php echo htmlspecialchars($row['bug_assigned_to']); ?>
                </span>
                <span class="text-xs px-2 py-1 rounded <?php echo $severity_class; ?>">
                  <?php echo htmlspecialchars(ucfirst($row['bug_severity'])); ?>
                </span>
              </div>
            </div>
            <?php
              }
            } else {
            ?>
            <div class="bg-gray-900 p-4 rounded-lg text-center text-gray-400">
              <i class="fas fa-bug text-xl mb-2"></i>
              <p>No recent bugs reported</p>
            </div>
            <?php } ?>
          </div>
        </div>
      </section>
      
      <!-- Recent Projects -->
      <section>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg h-full card-hover transition-all duration-300">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Recent Projects</h2>
            <a href="project.php" class="text-blue-400 hover:text-blue-300 text-sm">View all</a>
          </div>
          
          <div class="space-y-4">
            <?php 
            if ($result_recent_projects && mysqli_num_rows($result_recent_projects) > 0) {
              while ($row = mysqli_fetch_assoc($result_recent_projects)) {
                $status_class = "";
                $status_bg = "";
                switch (strtolower($row['project_status'])) {
                  case 'complete': 
                    $status_class = "text-green-400";
                    $status_bg = "bg-green-500 bg-opacity-20";
                    break;
                  case 'ongoing': 
                    $status_class = "text-yellow-400";
                    $status_bg = "bg-yellow-500 bg-opacity-20";
                    break;
                  case 'pending': 
                    $status_class = "text-blue-400";
                    $status_bg = "bg-blue-500 bg-opacity-20";
                    break;
                  default: 
                    $status_class = "text-gray-400";
                    $status_bg = "bg-gray-500 bg-opacity-20";
                }
            ?>
            <div class="bg-gray-900 p-4 rounded-lg">
              <div class="flex justify-between">
                <h3 class="font-semibold"><?php echo htmlspecialchars($row['project_name']); ?></h3>
                <span class="text-xs <?php echo $status_bg; ?> <?php echo $status_class; ?> px-2 py-1 rounded">
                  <?php echo htmlspecialchars(ucfirst($row['project_status'])); ?>
                </span>
              </div>
              <p class="text-sm text-gray-400 mt-2 line-clamp-2"><?php echo htmlspecialchars($row['project_descp']); ?></p>
              <div class="mt-3">
                <div class="flex justify-between items-center mb-1">
                  <span class="text-xs text-gray-400">Progress</span>
                  <span class="text-xs text-gray-400"><?php echo htmlspecialchars($row['project_progress']); ?>%</span>
                </div>
                <div class="w-full bg-gray-800 rounded-full h-2">
                  <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo htmlspecialchars($row['project_progress']); ?>%"></div>
                </div>
              </div>
            </div>
            <?php
              }
            } else {
            ?>
            <div class="bg-gray-900 p-4 rounded-lg text-center text-gray-400">
              <i class="fas fa-project-diagram text-xl mb-2"></i>
              <p>No recent projects found</p>
            </div>
            <?php } ?>
          </div>
        </div>
      </section>
    </div>

    <!-- Leave Management -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
            <h3 class="font-medium text-sm mb-3 text-gray-300">Upcoming Team Leaves</h3>
            <div class="space-y-3">
              <?php echo $teamLeavesOutput; ?>
            </div>
            <div class="text-center mt-4">
              <a href="#leaveApplicationForm" class="text-green-400 text-sm hover:text-green-300 inline-flex items-center">
                <i class="fas fa-calendar-plus mr-1"></i> Schedule your leave
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-span-2 bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300" id="leaveApplicationForm">
        <h2 class="text-xl font-bold mb-4">Leave Application</h2>
        <?php if(isset($leaveMessage)): ?>
        <div class="bg-green-500 bg-opacity-20 text-green-400 p-3 rounded-lg mb-4">
          <?php echo $leaveMessage; ?>
        </div>
        <?php endif; ?>
        <?php if(isset($leaveError)): ?>
        <div class="bg-red-500 bg-opacity-20 text-red-400 p-3 rounded-lg mb-4">
          <?php echo $leaveError; ?>
        </div>
        <?php endif; ?>
        <form action="" method="POST" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-400 mb-1">Leave Type</label>
              <select name="leave_type" class="bg-gray-900 text-white p-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="Annual">Annual Leave</option>
                <option value="Sick">Sick Leave</option>
                <option value="Personal">Personal Leave</option>
                <option value="Vacation">Vacation</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-400 mb-1">Duration</label>
              <select name="duration" class="bg-gray-900 text-white p-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="full">Full Day</option>
                <option value="half">Half Day</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-400 mb-1">From Date</label>
              <input type="date" name="from_date" id="from_date" class="bg-gray-900 text-white p-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500" required>
            </div>
            <div>
              <label class="block text-gray-400 mb-1">To Date</label>
              <input type="date" name="to_date" id="to_date" class="bg-gray-900 text-white p-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500" required>
            </div>
          </div>
          <div>
            <label class="block text-gray-400 mb-1">Reason</label>
            <textarea name="reason" rows="2" class="bg-gray-900 text-white p-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-400 mb-1">Handover To</label>
              <select name="handover_to" class="bg-gray-900 text-white p-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500">
                <?php 
                if ($result_all_mag_members) {
                  mysqli_data_seek($result_all_mag_members, 0);
                  while ($member = mysqli_fetch_assoc($result_all_mag_members)) {
                    echo '<option value="'.htmlspecialchars($member['mag_id']).'">'.htmlspecialchars($member['mag_name']).'</option>';
                  }
                }
                ?>
              </select>
            </div>
            <div>
              <label class="block text-gray-400 mb-1">Contact During Leave</label>
              <input type="text" name="contact" value="<?php echo htmlspecialchars($ManagerPhone); ?>" class="bg-gray-900 text-white p-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
          </div>
          <div>
            <label class="block text-gray-400 mb-1">Handover Notes</label>
            <textarea name="handover_notes" rows="2" class="bg-gray-900 text-white p-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
          </div>
          <div class="flex justify-end space-x-3 mt-4">
            <button type="reset" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors duration-300">
              <i class="fas fa-undo mr-2"></i> Reset
            </button>
            <button type="submit" name="submit_leave" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 px-6 py-2 rounded-lg shadow-lg transform transition-all duration-300 hover:scale-105">
              <i class="fas fa-paper-plane mr-2"></i> Submit Application
            </button>
          </div>
        </form>
      </div>
    </section>

    <!-- Footer -->
    <footer class="mt-12 text-center py-4 border-t border-gray-800 text-gray-400">
      <p>© 2025 CryBug - Bug Tracking System. All rights reserved.</p>
      <div class="mt-2 flex justify-center space-x-4">
        <a href="https://github.com/scarlet-sypher" class="hover:text-white transition-colors"><i class="fab fa-github"></i></a>
        <a href="#" class="hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
        <a href="https://www.linkedin.com/in/ayush-jha-a2809b29a/" class="hover:text-white transition-colors"><i class="fab fa-linkedin"></i></a>
      </div>
    </footer>

    </main>
    </div>

    <script>

        const leaveFrom = document.getElementById('leave_from');
        const leaveTo = document.getElementById('leave_to');
        const leaveDuration = document.getElementById('leave_duration');
        
        if(leaveFrom && leaveTo && leaveDuration) {
          function updateDuration() {
            if(leaveFrom.value && leaveTo.value) {
              let fromDate = new Date(leaveFrom.value);
              let toDate = new Date(leaveTo.value);
              
              // Add one day to include both start and end dates
              let differenceInTime = toDate.getTime() - fromDate.getTime();
              let differenceInDays = Math.ceil(differenceInTime / (1000 * 3600 * 24)) + 1;
              
              if(differenceInDays > 0) {
                leaveDuration.value = differenceInDays;
              } else {
                leaveDuration.value = "";
              }
            }
          }
          
          leaveFrom.addEventListener('change', updateDuration);
          leaveTo.addEventListener('change', updateDuration);
        }
    </script>

</body>
</html>
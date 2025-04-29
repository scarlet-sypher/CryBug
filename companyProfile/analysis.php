<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../company/company-Login.php");
    exit;
}

// Get company data from session
$companyName = $_SESSION['cmp_name'] ?? 'Company';
$companyLogo = $_SESSION['cmp_logo'] ?? '../image/Profile/guest.png';
$companyEmail = $_SESSION['cmp_mail'] ?? 'company@example.com';
$companyId = $_SESSION['cmp_id'] ?? '';
$companyDesc = $_SESSION['cmp_descp'] ?? 'No description available';

include "connection.php";

// Calculate revenue from project table
$revenueQuery = "SELECT SUM(project_profit) as total_revenue FROM project WHERE project_alloc_cmp = '$companyId'";
$revenueResult = $con->query($revenueQuery);
$revenueData = $revenueResult->fetch_assoc();
$totalRevenue = $revenueData['total_revenue'] ?? 0;

// Get fixed clients from company table
$clientsQuery = "SELECT cmp_clients FROM company WHERE cmp_id = '$companyId'";
$clientsResult = $con->query($clientsQuery);
$clientsData = $clientsResult->fetch_assoc();
$totalClients = $clientsData['cmp_clients'] ?? 0;

// Count total projects for this company
$projectsQuery = "SELECT COUNT(*) as total_projects FROM project WHERE project_alloc_cmp = '$companyId'";
$projectsResult = $con->query($projectsQuery);
$projectsData = $projectsResult->fetch_assoc();
$totalProjects = $projectsData['total_projects'] ?? 0;

// Count managers under this company
$managersQuery = "SELECT COUNT(*) as total_managers FROM manager WHERE mag_cmp_id = '$companyId'";
$managersResult = $con->query($managersQuery);
$managersData = $managersResult->fetch_assoc();
$totalManagers = $managersData['total_managers'] ?? 0;

// Get bug distribution by priority
$bugPriorityQuery = "SELECT 
                        bug_severity,
                        COUNT(*) as count
                     FROM bug 
                     WHERE bug_alloc_cmp = '$companyId'
                     GROUP BY bug_severity";
$bugPriorityResult = $con->query($bugPriorityQuery);

// Prepare data for the bug chart
$priorities = [];
$counts = [];

if ($bugPriorityResult && $bugPriorityResult->num_rows > 0) {
    while ($bugPriority = $bugPriorityResult->fetch_assoc()) {
        $priorities[] = $bugPriority['bug_severity'];
        $counts[] = $bugPriority['count'];
    }
}

// Get recent bugs
$recentBugsQuery = "SELECT 
                        bug_id,
                        bug_title,
                        bug_status,
                        bug_severity,
                        bug_created_date
                    FROM bug
                    WHERE bug_alloc_cmp = '$companyId'
                    ORDER BY bug_created_date DESC
                    LIMIT 5";
$recentBugsResult = $con->query($recentBugsQuery);

// Get project distribution by status
$projectStatusQuery = "SELECT 
                          project_status,
                          COUNT(*) as count
                       FROM project 
                       WHERE project_alloc_cmp = '$companyId'
                       GROUP BY project_status";
$projectStatusResult = $con->query($projectStatusQuery);

// Get projects with their timelines
$projectTimelineQuery = "SELECT 
                        project_name,
                        project_start_date,
                        project_end_date,
                        project_progress
                      FROM project
                      WHERE project_alloc_cmp = '$companyId'
                      ORDER BY project_end_date ASC
                      LIMIT 5";
$projectTimelineResult = $con->query($projectTimelineQuery);

// Prepare data for charts
$statusLabels = [];
$statusCounts = [];

if ($projectStatusResult && $projectStatusResult->num_rows > 0) {
    while ($status = $projectStatusResult->fetch_assoc()) {
        $statusLabels[] = $status['project_status'];
        $statusCounts[] = $status['count'];
    }
}

// Get employees by department for this company
$employeeDepQuery = "SELECT 
                        emp_dept as department,
                        COUNT(*) as count
                     FROM employee e
                     JOIN manager m ON e.mag_id = m.mag_id
                     WHERE m.mag_cmp_id = '$companyId'
                     GROUP BY emp_dept";
$employeeDepResult = $con->query($employeeDepQuery);

$departments = [];
$depCounts = [];
if ($employeeDepResult && $employeeDepResult->num_rows > 0) {
    while ($dep = $employeeDepResult->fetch_assoc()) {
        $departments[] = $dep['department'];
        $depCounts[] = $dep['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analysis Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="dashboard.js" defer></script>
  <link rel="stylesheet" href="../src/output.css">
  <style>
    .analysis-card {
      background-color: #1e293b;
      border-radius: 0.5rem;
      transition: all 0.3s ease;
    }
    
    .analysis-card:hover {
      box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
      transform: translateY(-5px);
    }
    
    .stat-card {
      background-color: #1e293b;
      border-radius: 0.5rem;
      transition: all 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 10px rgba(99, 102, 241, 0.3);
    }
    
    .btn-primary {
      background-color: #4f46e5;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background-color: #4338ca;
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
    }
    
    .action-button {
      height: 40px;
      font-weight: 600;
      font-size: 0.85rem;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }
    
    .sidebar-link {
      transition: all 0.3s ease;
    }
    
    .sidebar-link:hover, .sidebar-link.active {
      background-color: rgba(79, 70, 229, 0.2);
      border-left: 3px solid #4f46e5;
      color: white;
    }
    
    .sidebar-link.active {
      background-color: rgba(79, 70, 229, 0.3);
    }
    
    .analysis-grid {
      max-height: calc(100vh - 250px);
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #4f46e5 #1e293b;
    }
    
    .progress-bar {
      height: 8px;
      border-radius: 4px;
      background-color: #1e293b;
      overflow: hidden;
    }
    
    .progress-fill {
      height: 100%;
      border-radius: 4px;
      transition: width 0.5s ease;
    }
    
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        z-index: 60;
        left: 0;
        top: 0;
        height: 100vh;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 50;
      }
      
      .sidebar-overlay.active {
        display: block;
      }
      
      main {
        margin-left: 0 !important;
      }
    }
    
    .glass-effect {
      background-color: rgba(23, 25, 35, 0.85);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .bg-gradient-custom {
      background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }
    
    .chart-container {
      height: 300px;
      position: relative;
    }
    
    .card-hover {
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .card-hover:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(59, 130, 246, 0.15);
    }
  </style>
</head>

<body class="bg-black bg-gradient-custom text-white min-h-screen font-sans antialiased">
<div class="sidebar-overlay" id="sidebarOverlay"></div>
  
<div class="flex flex-col md:flex-row">
  
  <!-- Sidebar -->
  <aside class="sidebar w-64 bg-gray-900 p-4 md:h-screen transition-all" id="sidebar">
    <div class="flex items-center justify-between mb-8 p-2">
      <div class="flex items-center">
        <i class="fas fa-bug text-indigo-500 text-2xl mr-2"></i>
        <h1 class="text-xl font-bold text-indigo-500">CryBug</h1>
      </div>
      <button class="close-sidebar md:hidden text-white" id="closeSidebar">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <nav>
      <ul class="space-y-2">
        <li>
          <a href="../index.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
            <i class="fas fa-home mr-3"></i>
            <span>Home</span>
          </a>
        </li>
        <li>
          <a href="dashboard.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
            <i class="fas fa-tachometer-alt mr-3"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li>
          <a href="team.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
            <i class="fas fa-users mr-3"></i>
            <span>Team</span>
          </a>
        </li>
        <li>
            <a href="holiday.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Clients">
            <i class="fas fa-calendar-alt mr-3"></i>
              <span>Add  Holiday</span>
            </a>
        </li>
        <li>
            <a href="feedback.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Feedback">
              <i class="fas fa-comments mr-3"></i>
              <span>Feedback</span>
            </a>
        </li>
        <li>
          <a href="analysis.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white">
            <i class="fas fa-chart-bar mr-3"></i>
            <span>Analytics</span>
          </a>
        </li>
        <li>
          <a href="settings.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
            <i class="fas fa-cog mr-3"></i>
            <span>Settings</span>
          </a>
        </li>
      </ul>
    </nav>
    
    <div class="mt-auto pt-8">
      <div class="border-t border-gray-700 pt-4">
        <a href="help.php" class="flex items-center p-3 rounded text-gray-300 hover:text-white">
          <i class="fas fa-question-circle mr-3"></i>
          <span>Help Center</span>
        </a>
        <a href="logout.php" class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 p-2 rounded flex items-center justify-center transition-all hover:transform hover:translate-y-[-2px]">
          <i class="fas fa-sign-out-alt mr-2"></i>
          <span>Logout</span>
        </a>
      </div>
    </div>
  </aside>

  <!-- Main Content Area -->
  <main class="md:ml-64 lg:ml-64 flex-1 p-4 md:p-6 transition-all">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold"><?php echo ucfirst(htmlspecialchars($companyName)); ?> Analytics</h1>
        <p class="text-gray-400">Comprehensive data analysis and insights</p>
      </div>
      
      <div class="mt-4 md:mt-0 flex space-x-2">
        <button class="menu-toggle md:hidden bg-gray-800 p-2 rounded-lg">
          <i class="fas fa-bars"></i>
        </button>

        <div class="relative">
          <button id="profileDropdownBtn" class="flex items-center">
            <?php if(!empty($_SESSION['cmp_logo']) && file_exists($_SESSION['cmp_logo'])): ?>
              <img src="<?php echo htmlspecialchars($_SESSION['cmp_logo']); ?>" alt="Profile" class="h-10 w-10 object-cover rounded-full border-2 border-indigo-500" />
            <?php else: ?>
              <img src="../images/Profile/guest.png" alt="Profile" class="h-10 w-10 rounded-full border-2 border-indigo-500" />
            <?php endif; ?>
            <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
          </button>
          <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg p-2 hidden" id="profileDropdown">
            <a href="dashboard.php" class="block p-2 hover:bg-gray-700 rounded text-sm">
              <i class="fas fa-user mr-2"></i> My Profile
            </a>
            <a href="settings.php" class="block p-2 hover:bg-gray-700 rounded text-sm">
              <i class="fas fa-cog mr-2"></i> Account Settings
            </a>
            <a href="logout.php" class="block p-2 hover:bg-gray-700 rounded text-sm text-red-400">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </div>
      </div>
    </div>
    
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-gray-800 rounded-xl p-4 stat-card">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-500 bg-opacity-20 mr-4">
            <i class="fas fa-project-diagram text-indigo-500"></i>
          </div>
          <div>
            <h3 class="text-gray-400 text-sm">Total Projects</h3>
            <p class="text-2xl font-bold"><?php echo number_format($totalProjects); ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-gray-800 rounded-xl p-4 stat-card">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-500 bg-opacity-20 mr-4">
            <i class="fas fa-dollar-sign text-green-500"></i>
          </div>
          <div>
            <h3 class="text-gray-400 text-sm">Total Revenue</h3>
            <p class="text-2xl font-bold">$<?php echo number_format($totalRevenue); ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-gray-800 rounded-xl p-4 stat-card">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-500 bg-opacity-20 mr-4">
            <i class="fas fa-users text-blue-500"></i>
          </div>
          <div>
            <h3 class="text-gray-400 text-sm">Total Clients</h3>
            <p class="text-2xl font-bold"><?php echo number_format($totalClients); ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-gray-800 rounded-xl p-4 stat-card">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-500 bg-opacity-20 mr-4">
            <i class="fas fa-user-tie text-purple-500"></i>
          </div>
          <div>
            <h3 class="text-gray-400 text-sm">Team Managers</h3>
            <p class="text-2xl font-bold"><?php echo number_format($totalManagers); ?></p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Analysis Grid -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <!-- Bug Tracking Overview -->
      <div class="bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
        <h2 class="text-xl font-bold mb-4">Bug Tracking Overview</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
          <?php
          // Define priority colors
          $priorityColors = [
              'low' => 'bg-blue-500',
              'medium' => 'bg-yellow-500',
              'high' => 'bg-orange-500',
              'critical' => 'bg-red-500'
          ];
          
          // Print priority stats
          foreach ($priorities as $index => $priority) {
              $count = $counts[$index];
              $colorClass = $priorityColors[strtolower($priority)] ?? 'bg-gray-500';
          ?>
              <div class="p-3 bg-gray-900 rounded-lg text-center">
                  <div class="mb-1 <?php echo $colorClass; ?> w-4 h-4 rounded-full mx-auto"></div>
                  <p class="text-xs text-gray-400"><?php echo ucfirst($priority); ?></p>
                  <p class="text-lg font-bold"><?php echo $count; ?></p>
              </div>
          <?php
          }
          ?>
        </div>
        
        <h3 class="font-medium text-sm mb-2 mt-4">Recent Bugs</h3>
        <div class="space-y-2">
        <?php
        if ($recentBugsResult && $recentBugsResult->num_rows > 0) {
            while ($bug = $recentBugsResult->fetch_assoc()) {
                // Determine status and priority styling
                $statusClass = 'bg-gray-500';
                if (strtolower($bug['bug_status']) == 'open') {
                    $statusClass = 'bg-red-500';
                } elseif (strtolower($bug['bug_status']) == 'in progress') {
                    $statusClass = 'bg-yellow-500';
                } elseif (strtolower($bug['bug_status']) == 'resolved') {
                    $statusClass = 'bg-green-500';
                }
                
                $priorityClass = $priorityColors[strtolower($bug['bug_severity'])] ?? 'bg-gray-500';
                
                // Format date
                $bugDate = new DateTime($bug['bug_created_date']);
                $formattedDate = $bugDate->format('M d, Y');
        ?>
                <div class="flex items-center bg-gray-900 p-3 rounded-lg">
                    <div class="rounded-full <?php echo $statusClass; ?> h-3 w-3 mr-3"></div>
                    <div class="flex-1">
                        <p class="font-medium text-sm"><?php echo htmlspecialchars($bug['bug_title']); ?></p>
                        <div class="flex items-center text-xs text-gray-400 mt-1">
                            <span class="mr-2">ID: <?php echo $bug['bug_id']; ?></span>
                            <span class="mr-2">|</span>
                            <span class="mr-2"><?php echo $formattedDate; ?></span>
                            <span class="mr-2">|</span>
                            <span class="px-1 rounded text-xs <?php echo $priorityClass; ?> bg-opacity-20"><?php echo ucfirst($bug['bug_severity']); ?></span>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
        ?>
            <div class="bg-gray-900 p-3 rounded-lg text-center">
                <p class="text-gray-400">No recent bugs reported</p>
            </div>
        <?php
        }
        ?>
        </div>
      </div>
      
      <!-- Project Analytics -->
      <div class="bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
        <h2 class="text-xl font-bold mb-4">Project Analytics</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
          <?php
          // Define status colors
          $statusColors = [
            'pending'      => 'bg-yellow-400',
            'in progress'  => 'bg-blue-500',
            'completed'    => 'bg-green-500',
            'cancelled'    => 'bg-red-500',
            'active'       => 'bg-emerald-500',
            'in review'    => 'bg-violet-500',
            'on hold'      => 'bg-orange-500',
            'started'      => 'bg-cyan-500'
        ];
        
        
          
          // Print status stats
          foreach ($statusLabels as $index => $status) {
              $count = $statusCounts[$index];
              $colorClass = $statusColors[strtolower($status)] ?? 'bg-gray-500';
          ?>
              <div class="p-3 bg-gray-900 rounded-lg text-center">
                  <div class="mb-1 <?php echo $colorClass; ?> w-4 h-4 rounded-full mx-auto"></div>
                  <p class="text-xs text-gray-400"><?php echo ucfirst($status); ?></p>
                  <p class="text-lg font-bold"><?php echo $count; ?></p>
              </div>
          <?php
          }
          ?>
        </div>
        
        <!-- Project Timeline -->
        <div class="mt-6">
          <h3 class="font-medium mb-2">Project Timeline</h3>
          <?php
          if ($projectTimelineResult && $projectTimelineResult->num_rows > 0) {
              while ($timeline = $projectTimelineResult->fetch_assoc()) {
                  $startDate = new DateTime($timeline['project_start_date']);
                  $endDate = new DateTime($timeline['project_end_date']);
                  $now = new DateTime();
                  
                  $progress = $timeline['project_progress'] ?? 0;
                  
                  // Calculate project duration in days
                  $duration = $startDate->diff($endDate)->days;
                  $elapsed = $startDate->diff($now)->days;
                  $percentComplete = min(100, max(0, ($elapsed / max(1, $duration)) * 100));
                  
                  // Determine if project is on time
                  $progressIndicator = '';
                  if ($progress >= $percentComplete) {
                      $progressIndicator = '<span class="text-green-400 text-xs ml-2"><i class="fas fa-check-circle"></i> On track</span>';
                  } else {
                      $progressIndicator = '<span class="text-yellow-400 text-xs ml-2"><i class="fas fa-exclamation-circle"></i> Behind</span>';
                  }
                  
                  $startFormatted = $startDate->format('M d');
                  $endFormatted = $endDate->format('M d');
          ?>
                  <div class="mb-3">
                      <div class="flex justify-between items-center mb-1">
                          <div class="text-sm font-medium"><?php echo htmlspecialchars($timeline['project_name']); ?> <?php echo $progressIndicator; ?></div>
                          <div class="text-xs text-gray-400"><?php echo $startFormatted; ?> - <?php echo $endFormatted; ?></div>
                      </div>
                      <div class="bg-gray-900 rounded-full overflow-hidden">
                          <div class="bg-indigo-500 h-2" style="width: <?php echo $progress; ?>%"></div>
                      </div>
                      <div class="flex justify-between mt-1">
                          <span class="text-xs text-gray-400">Progress</span>
                          <span class="text-xs text-gray-400"><?php echo $progress; ?>%</span>
                      </div>
                  </div>
          <?php
              }
          } else {
          ?>
              <div class="bg-gray-900 p-3 rounded-lg text-center">
                  <p class="text-gray-400">No projects available</p>
              </div>
          <?php
          }
          ?>
        </div>
      </div>
    </section>
    
    <!-- Detailed Analysis Section -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <!-- Employee Department Distribution -->
      <div class="bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
        <h2 class="text-xl font-bold mb-4">Employee Distribution</h2>
        
        <div class="space-y-3 mt-4">
          <?php
          $departmentColors = [
              'development' => 'bg-blue-500',
              'design' => 'bg-purple-500',
              'marketing' => 'bg-green-500',
              'hr' => 'bg-yellow-500',
              'sales' => 'bg-red-500',
              'engineering' => 'bg-indigo-500',
              'management' => 'bg-pink-500',
              'operations' => 'bg-cyan-500',
              'finance' => 'bg-emerald-500',
              'customer support' => 'bg-amber-500'
          ];
          
          // Calculate total for percentages
          $totalEmps = array_sum($depCounts);
          
          if ($totalEmps > 0 && count($departments) > 0) {
              foreach ($departments as $index => $dept) {
                  $count = $depCounts[$index];
                  $percentage = round(($count / $totalEmps) * 100);
                  $colorClass = $departmentColors[strtolower($dept)] ?? 'bg-gray-500';
          ?>
                  <div>
                      <div class="flex justify-between mb-1">
                          <span class="text-sm"><?php echo ucfirst($dept); ?></span>
                          <span class="text-sm text-gray-400"><?php echo $count; ?> (<?php echo $percentage; ?>%)</span>
                      </div>
                      <div class="bg-gray-900 rounded-full h-2">
                          <div class="h-2 rounded-full <?php echo $colorClass; ?>" style="width: <?php echo $percentage; ?>%"></div>
                      </div>
                  </div>
          <?php
              }
          } else {
          ?>
              <div class="bg-gray-900 p-3 rounded-lg text-center">
                  <p class="text-gray-400">No employee data available</p>
              </div>
          <?php
          }
          ?>
        </div>
      </div>
      
      <!-- Bug Severity Analysis -->
      <div class="bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
        <h2 class="text-xl font-bold mb-4">Bug Resolution Time</h2>
        
        <?php
        // Get average bug resolution time by severity
        $bugResolutionQuery = "SELECT 
                                  bug_severity,
                                  AVG(TIMESTAMPDIFF(HOUR, bug_created_date, bug_resolved_date)) as avg_resolution_time
                               FROM bug 
                               WHERE bug_alloc_cmp = '$companyId'
                                 AND bug_status = 'resolved'
                                 AND bug_resolved_date IS NOT NULL
                               GROUP BY bug_severity";
        $bugResolutionResult = $con->query($bugResolutionQuery);
        
        if ($bugResolutionResult && $bugResolutionResult->num_rows > 0) {
        ?>
            <div class="space-y-4 mt-4">
                <?php
                while ($resolution = $bugResolutionResult->fetch_assoc()) {
                    $severity = $resolution['bug_severity'];
                    $avgTime = round($resolution['avg_resolution_time']); // In hours
                    
                    // Convert to days if more than 24 hours
                    $timeText = $avgTime . ' hrs';
                    if ($avgTime >= 24) {
                        $days = floor($avgTime / 24);
                        $hours = $avgTime % 24;
                        $timeText = $days . ' days ' . $hours . ' hrs';
                    }
                    
                    $colorClass = $priorityColors[strtolower($severity)] ?? 'bg-gray-500';
                    
                    // Calculate visual scale (lower is better)
                    $maxTime = 168; // 1 week in hours
                    $visualScale = min(100, ($avgTime / $maxTime) * 100);
                ?>
                    <div>
                        <div class="flex justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full <?php echo $colorClass; ?> mr-2"></div>
                                <span class="text-sm font-medium"><?php echo ucfirst($severity); ?></span>
                            </div>
                            <span class="text-sm text-gray-400"><?php echo $timeText; ?></span>
                        </div>
                        <div class="bg-gray-900 rounded-full h-2">
                            <div class="h-2 rounded-full <?php echo $colorClass; ?>" style="width: <?php echo $visualScale; ?>%"></div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php
        } else {
        ?>
            <div class="bg-gray-900 p-4 rounded-lg text-center">
                <p class="text-gray-400">No bug resolution data available</p>
            </div>
        <?php
        }
        ?>
        
        <h3 class="font-medium text-sm mt-6 mb-3">Bug Distribution by Status</h3>
        
        <?php
        // Get bug distribution by status

        $bugStatusQuery = "SELECT 
                              bug_status,
                              COUNT(*) as count
                           FROM bug 
                           WHERE bug_alloc_cmp = '$companyId'
                           GROUP BY bug_status";
        $bugStatusResult = $con->query($bugStatusQuery);
        
        if ($bugStatusResult && $bugStatusResult->num_rows > 0) {
            // Define status colors
            $bugStatusColors = [
                'open' => 'bg-red-500',
                'in progress' => 'bg-yellow-500',
                'resolved' => 'bg-green-500',
                'closed' => 'bg-gray-500'
            ];
        ?>
            <div class="grid grid-cols-2 gap-3">
                <?php
                while ($status = $bugStatusResult->fetch_assoc()) {
                    $statusName = $status['bug_status'];
                    $count = $status['count'];
                    $colorClass = $bugStatusColors[strtolower($statusName)] ?? 'bg-blue-500';
                ?>
                    <div class="flex items-center p-3 bg-gray-900 rounded-lg">
                        <div class="w-3 h-3 rounded-full <?php echo $colorClass; ?> mr-2"></div>
                        <div>
                            <p class="text-sm font-medium"><?php echo ucfirst($statusName); ?></p>
                            <p class="text-xs text-gray-400"><?php echo $count; ?> bugs</p>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php
        } else {
        ?>
            <div class="bg-gray-900 p-3 rounded-lg text-center">
                <p class="text-gray-400">No bug status data available</p>
            </div>
        <?php
        }
        ?>
      </div>
    </section>
    
    <!-- Call to action -->
    
    
    <!-- Footer -->
    <footer class="text-center text-gray-500 text-sm py-4 border-t border-gray-800">
      <p>© <?php echo date('Y'); ?> CryBug. All rights reserved.</p>
    </footer>
  </main>
</div>

<script>
  // Mobile menu toggle
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const menuToggle = document.querySelector('.menu-toggle');
    const closeSidebar = document.getElementById('closeSidebar');
    
    menuToggle.addEventListener('click', function() {
      sidebar.classList.add('active');
      sidebarOverlay.classList.add('active');
    });
    
    closeSidebar.addEventListener('click', function() {
      sidebar.classList.remove('active');
      sidebarOverlay.classList.remove('active');
    });
    
    sidebarOverlay.addEventListener('click', function() {
      sidebar.classList.remove('active');
      sidebarOverlay.classList.remove('active');
    });
  });
</script>
</body>
</html>
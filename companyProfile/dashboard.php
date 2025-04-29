
<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../company/company-Login.php");
    exit;
}

include "connection.php";
include "../session_manager.php";

// Get company data from session
$companyId = $_SESSION['cmp_id'] ?? '';
$ManagerX = $_SESSION['x'] ;
$ManagerL = $_SESSION['linkedin'] ;
$ManagerG = $_SESSION['github'] ;

// Refresh all company data from database to ensure up-to-date information
if (!empty($companyId)) {
    $companyQuery = "SELECT * FROM company WHERE cmp_id = '$companyId'";
    $companyResult = $con->query($companyQuery);
    
    if ($companyResult && $companyResult->num_rows > 0) {
        $companyData = $companyResult->fetch_assoc();
        
        // Update session with fresh data
        $_SESSION['cmp_name'] = $companyData['cmp_name'];
        $_SESSION['cmp_logo'] = $companyData['cmp_logo'];
        $_SESSION['cmp_mail'] = $companyData['cmp_mail'];
        $_SESSION['cmp_descp'] = $companyData['cmp_descp'];
        
        // Get updated data from company table
        $companyName = $companyData['cmp_name'] ?? 'Company';
        $companyLogo = $companyData['cmp_logo'] ?? '';
        $companyEmail = $companyData['cmp_mail'] ?? 'company@example.com';
        $companyDesc = $companyData['cmp_descp'] ?? 'No description available';
        $totalClients = $companyData['cmp_clients'] ?? 0;
        $companyRevenue = $companyData['cmp_revenue'] ?? 0; // Get revenue from company table
        $companyGrowthRate = $companyData['cmp_growth_rate'] ?? 0; // Get growth rate from company table
    } else {
        // If company not found, use session data as fallback
        $companyName = $_SESSION['cmp_name'] ?? 'Company';
        $companyLogo = $_SESSION['cmp_logo'] ?? '';
        $companyEmail = $_SESSION['cmp_mail'] ?? 'company@example.com';
        $companyDesc = $_SESSION['cmp_descp'] ?? 'No description available';
        $totalClients = 0;
        $companyRevenue = 0;
        $companyGrowthRate = 0;
    }
} else {
    // No company ID in session
    $companyName = $_SESSION['cmp_name'] ?? 'Company';
    $companyLogo = $_SESSION['cmp_logo'] ?? '';
    $companyEmail = $_SESSION['cmp_mail'] ?? 'company@example.com';
    $companyDesc = $_SESSION['cmp_descp'] ?? 'No description available';
    $totalClients = 0;
    $companyRevenue = 0;
    $companyGrowthRate = 0;
}

// Calculate revenue from project table (as a verification, but use company table value for display)
$revenueQuery = "SELECT SUM(project_profit) as total_revenue FROM project WHERE project_alloc_cmp = '$companyId'";
$revenueResult = $con->query($revenueQuery);
$revenueData = $revenueResult->fetch_assoc();
$projectRevenue = $revenueData['total_revenue'] ?? 0;

// Use company table revenue or project revenue as fallback
$totalRevenue = ($companyRevenue > 0) ? $companyRevenue : $projectRevenue;

// Count total projects for this company - IMPROVED
$projectsQuery = "SELECT COUNT(*) as total_projects FROM project WHERE project_alloc_cmp = '$companyId'";
$projectsResult = $con->query($projectsQuery);
$projectsData = $projectsResult->fetch_assoc();
$totalProjects = intval($projectsData['total_projects'] ?? 0);

// Count managers under this company
$managersQuery = "SELECT COUNT(*) as total_managers FROM manager WHERE mag_cmp_id = '$companyId'";
$managersResult = $con->query($managersQuery);
$managersData = $managersResult->fetch_assoc();
$totalManagers = intval($managersData['total_managers'] ?? 0);

// IMPROVED GROWTH RATE CALCULATION
$growthRate = $companyGrowthRate;
if ($growthRate == 0) {
    // Count projects started in last year
    $projectsLastYearQuery = "SELECT COUNT(*) as last_year_projects 
                             FROM project 
                             WHERE project_alloc_cmp = '$companyId' 
                             AND project_start_date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
    $projectsLastYearResult = $con->query($projectsLastYearQuery);
    $projectsLastYearData = $projectsLastYearResult->fetch_assoc();
    $lastYearProjects = isset($projectsLastYearData['last_year_projects']) ? intval($projectsLastYearData['last_year_projects']) : 0;
    
    // Projects from previous year for comparison
    $projectsPrevYearQuery = "SELECT COUNT(*) as prev_year_projects 
                             FROM project 
                             WHERE project_alloc_cmp = '$companyId' 
                             AND project_start_date BETWEEN DATE_SUB(NOW(), INTERVAL 2 YEAR) AND DATE_SUB(NOW(), INTERVAL 1 YEAR)";
    $projectsPrevYearResult = $con->query($projectsPrevYearQuery);
    $projectsPrevYearData = $projectsPrevYearResult->fetch_assoc();
    $prevYearProjects = isset($projectsPrevYearData['prev_year_projects']) ? intval($projectsPrevYearData['prev_year_projects']) : 0;
    
    // Calculate growth with better handling for zero values
    if ($prevYearProjects > 0) {
        $growthRate = round((($lastYearProjects - $prevYearProjects) / $prevYearProjects) * 100);
        // Cap at reasonable values (-100% to 100%)
        $growthRate = max(-100, min(100, $growthRate));
    } else if ($lastYearProjects > 0) {
        // If no previous projects but some this year, show moderate growth
        $growthRate = 30; 
    } else {
        $growthRate = 0;
    }
}

// Get upcoming holidays
$holidaysQuery = "SELECT * FROM holiday WHERE holiday_date >= CURDATE() AND (holiday_cmp_id = '$companyId' OR holiday_cmp_id IS NULL) ORDER BY holiday_date ASC LIMIT 3";
$holidaysResult = $con->query($holidaysQuery);

// Get manager data for team table
$managersListQuery = "SELECT m.*, COUNT(p.project_id) as project_count FROM manager m 
                     LEFT JOIN project p ON m.mag_id = p.project_alloc_mag 
                     WHERE m.mag_cmp_id = '$companyId' 
                     GROUP BY m.mag_id";
$managersListResult = $con->query($managersListQuery);

// IMPROVED BUG STATISTICS CALCULATION
$bugStatsQuery = "SELECT 
                  COUNT(*) as total_bugs,
                  SUM(CASE WHEN bug_status = 'resolved' THEN 1 ELSE 0 END) as resolved_bugs,
                  SUM(CASE WHEN bug_status = 'open' THEN 1 ELSE 0 END) as open_bugs
                FROM bug WHERE bug_alloc_cmp = '$companyId'";
$bugStatsResult = $con->query($bugStatsQuery);
$bugStats = $bugStatsResult->fetch_assoc();

$totalBugs = intval($bugStats['total_bugs'] ?? 0);
$openBugs = intval($bugStats['open_bugs'] ?? 0);
$resolvedBugs = intval($bugStats['resolved_bugs'] ?? 0);

// More accurate calculation with proper type casting
$resolutionRate = ($totalBugs > 0) ? round(($resolvedBugs / $totalBugs) * 100) : 0;

// Calculate bug resolution rate - redundant but kept for backward compatibility
$bugResRate = $resolutionRate;

// IMPROVED PROJECT COMPLETION RATE CALCULATION
$completedProjectsQuery = "SELECT COUNT(*) as completed 
                          FROM project 
                          WHERE project_alloc_cmp = '$companyId' 
                          AND (project_progress = 100 OR project_status = 'completed')";
$completedProjectsResult = $con->query($completedProjectsQuery);
$completedProjectsData = $completedProjectsResult->fetch_assoc();
$completedProjects = intval($completedProjectsData['completed'] ?? 0);

$completionRate = ($totalProjects > 0) ? round(($completedProjects / $totalProjects) * 100) : 0;

// Calculate average project value
$avgValue = ($totalProjects > 0) ? $totalRevenue / $totalProjects : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crybug | Company Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="dashboard.js" defer></script>

</head>
<body class="bg-gradient-custom text-white min-h-screen font-sans bg-black antialiased" id="home">

  <div class="overlay" id="sidebarOverlay"></div>
  
  <div class="flex flex-col md:flex-row">
    
    <!-- Sidebar -->
    <aside class="sidebar w-64 bg-gray-900 p-4 md:fixed md:h-screen transition-all">
      <div class="flex items-center justify-between mb-8 p-2">
        <div class="flex items-center">
            <i class="fas fa-bug text-indigo-500 text-2xl mr-2"></i>
            <h1 class="text-xl font-bold text-indigo-500">CryBug</h1>
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
            <a href="team.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Clients">
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
            <a href="analysis.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Analytics">
              <i class="fas fa-chart-bar mr-3"></i>
              <span>Analytics</span>
            </a>
          </li>
          <li>
            <a href="settings.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Settings">
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
          <a href="logout.php"><button class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 p-2 rounded flex items-center justify-center">
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
    <h1 class="text-2xl md:text-3xl font-bold">Welcome, <?php echo ucfirst(htmlspecialchars($companyName)); ?>!</h1>
    <p class="text-gray-400" id="currentDateTime">Loading date...</p>
  </div>
  
  <div class="mt-4 md:mt-0 flex items-center space-x-4">
    
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

<!-- Stats Cards Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
    <div class="flex items-center">
      <div class="rounded-full bg-indigo-500 bg-opacity-20 p-3 mr-4">
        <i class="fas fa-chart-line text-indigo-400"></i>
      </div>
      <div>
        <p class="text-sm text-gray-400">Revenue</p>
        <h3 class="text-xl font-bold">$<?php echo number_format($totalRevenue); ?></h3>
      </div>
    </div>
    <div class="mt-2 flex items-center text-sm text-green-400">
      <i class="fas fa-arrow-up mr-1"></i> 12% <span class="text-gray-400 ml-1">from last month</span>
    </div>
  </div>
  
  <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
    <div class="flex items-center">
      <div class="rounded-full bg-blue-500 bg-opacity-20 p-3 mr-4">
        <i class="fas fa-users text-blue-400"></i>
      </div>
      <div>
        <p class="text-sm text-gray-400">Clients</p>
        <h3 class="text-xl font-bold"><?php echo $totalClients; ?></h3>
      </div>
    </div>
    <div class="mt-2 flex items-center text-sm text-green-400">
      <i class="fas fa-arrow-up mr-1"></i> 4% <span class="text-gray-400 ml-1">from last quarter</span>
    </div>
  </div>
  
  <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
    <div class="flex items-center">
      <div class="rounded-full bg-green-500 bg-opacity-20 p-3 mr-4">
        <i class="fas fa-project-diagram text-green-400"></i>
      </div>
      <div>
        <p class="text-sm text-gray-400">Active Projects</p>
        <h3 class="text-xl font-bold"><?php echo $totalProjects; ?></h3>
      </div>
    </div>
    <div class="mt-2 flex items-center text-sm text-green-400">
      <i class="fas fa-arrow-up mr-1"></i> 18% <span class="text-gray-400 ml-1">from last month</span>
    </div>
  </div>
  
  <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
    <div class="flex items-center">
      <div class="rounded-full bg-purple-500 bg-opacity-20 p-3 mr-4">
        <i class="fas fa-users-cog text-purple-400"></i>
      </div>
      <div>
        <p class="text-sm text-gray-400">Team Size</p>
        <h3 class="text-xl font-bold"><?php echo $totalManagers; ?></h3>
      </div>
    </div>
    <div class="mt-2 flex items-center text-sm text-green-400">
      <i class="fas fa-plus mr-1"></i> <span class="text-gray-400">3 new this quarter</span>
    </div>
  </div>
</div>

<!-- Profile Section -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <div class="bg-gray-800 p-6 rounded-xl shadow-lg text-center card-hover transition-all duration-300">
    <div class="relative inline-block">
      <?php if(!empty($companyLogo) && file_exists($companyLogo)): ?>
        <img src="<?php echo htmlspecialchars($companyLogo); ?>" alt="Profile" class="w-32 h-32 mx-auto rounded-full border-4 border-indigo-500" />
      <?php else: ?>
        <img src="<?php echo htmlspecialchars($companyLogo); ?>" alt="Profile" class="w-32 h-32 mx-auto rounded-full border-4 border-indigo-500" />
      <?php endif; ?>
      <span class="absolute bottom-0 right-4 bg-green-500 p-1 rounded-full h-6 w-6 flex items-center justify-center">
        <i class="fas fa-check text-xs"></i>
      </span>
    </div>
    <h2 class="mt-4 text-xl font-bold"><?php echo htmlspecialchars($companyName); ?></h2>
  
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
      <p class="text-sm text-gray-400 mb-2"><i class="fas fa-id-badge mr-2"></i>Company ID: <?php echo htmlspecialchars($companyId); ?></p>
      <p class="text-sm text-gray-400 mb-2"><i class="fas fa-envelope mr-2"></i><?php echo htmlspecialchars($companyEmail); ?></p>
      <div class="mt-3 p-3 bg-gray-900 rounded-lg">
        <p class="text-sm text-gray-300"><i class="fas fa-info-circle mr-2"></i>About:</p>
        <p class="text-sm text-gray-400 mt-1"><?php echo htmlspecialchars($companyDesc); ?></p>
      </div>
    </div>
    
    <!-- Bug Statistics Card -->
    <div class="mt-4 bg-gray-900 p-3 rounded-lg">
      <h3 class="text-md font-semibold mb-2"><i class="fas fa-bug text-red-400 mr-2"></i>Bug Report Status</h3>
      <?php
      // Get bug statistics
      $bugStatsQuery = "SELECT 
                            COUNT(*) as total_bugs,
                            SUM(CASE WHEN bug_status = 'open' THEN 1 ELSE 0 END) as open_bugs,
                            SUM(CASE WHEN bug_status = 'resolved' THEN 1 ELSE 0 END) as resolved_bugs
                        FROM bug WHERE bug_alloc_cmp = '$companyId'";
      $bugStatsResult = $con->query($bugStatsQuery);
      $bugStats = $bugStatsResult->fetch_assoc();
      
      $totalBugs = $bugStats['total_bugs'] ?? 0;
      $openBugs = $bugStats['open_bugs'] ?? 0;
      $resolvedBugs = $bugStats['resolved_bugs'] ?? 0;
      
      $resolutionRate = ($totalBugs > 0) ? ($resolvedBugs / $totalBugs) * 100 : 0;
      ?>
      <div class="grid grid-cols-2 gap-2 text-center">
        <div class="p-2 bg-red-900 bg-opacity-30 rounded-lg">
          <p class="text-xs text-gray-300">Open</p>
          <p class="text-lg font-bold text-red-400"><?php echo $openBugs; ?></p>
        </div>
        <div class="p-2 bg-green-900 bg-opacity-30 rounded-lg">
          <p class="text-xs text-gray-300">Resolved</p>
          <p class="text-lg font-bold text-green-400"><?php echo $resolvedBugs; ?></p>
        </div>
      </div>
      <div class="mt-2">
        <div class="flex justify-between items-center text-xs">
          <span>Resolution Rate</span>
          <span><?php echo round($resolutionRate); ?>%</span>
        </div>
        <div class="bg-gray-800 rounded-full overflow-hidden mt-1">
          <div class="bg-green-500 h-1" style="width: <?php echo $resolutionRate; ?>%"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-span-2 bg-gray-800 p-6 rounded-xl shadow-lg card-hover transition-all duration-300">
    <h2 class="text-xl font-bold mb-4">Company Performance</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-gray-900 p-4 rounded-lg text-center stat-card">
        <div class="flex items-center justify-center mb-2">
          <i class="fas fa-bug text-indigo-400 mr-2 text-lg"></i>
          <h3 class="text-lg font-semibold text-indigo-400">Bug Resolution</h3>
        </div>
        <?php
        // Calculate bug resolution rate
        $bugResRate = ($totalBugs > 0) ? ($resolvedBugs / $totalBugs) * 100 : 0;
        ?>
        <p class="text-3xl font-bold"><?php echo round($bugResRate); ?>%</p>
        <p class="text-sm text-gray-400 mt-2">Bug resolution rate</p>
      </div>
      <div class="bg-gray-900 p-4 rounded-lg text-center stat-card">
        <div class="flex items-center justify-center mb-2">
          <i class="fas fa-project-diagram text-blue-400 mr-2 text-lg"></i>
          <h3 class="text-lg font-semibold text-blue-400">Project Completion</h3>
        </div>
        <?php
        // Calculate project completion rate
        $completedProjectsQuery = "SELECT COUNT(*) as completed FROM project WHERE project_alloc_cmp = '$companyId' AND project_progress = 100";
        $completedProjectsResult = $con->query($completedProjectsQuery);
        $completedProjectsData = $completedProjectsResult->fetch_assoc();
        $completedProjects = $completedProjectsData['completed'] ?? 0;
        
        $completionRate = ($totalProjects > 0) ? ($completedProjects / $totalProjects) * 100 : 0;
        ?>
        <p class="text-3xl font-bold"><?php echo round($completionRate); ?>%</p>
        <p class="text-sm text-gray-400 mt-2">Of projects completed</p>
      </div>
      <div class="bg-gray-900 p-4 rounded-lg text-center stat-card">
        <div class="flex items-center justify-center mb-2">
          <i class="fas fa-dollar-sign text-green-400 mr-2 text-lg"></i>
          <h3 class="text-lg font-semibold text-green-400">Avg. Project Value</h3>
        </div>
        <?php
        // Calculate average project value
        $avgValue = ($totalProjects > 0) ? $totalRevenue / $totalProjects : 0;
        ?>
        <p class="text-3xl font-bold">$<?php echo number_format($avgValue); ?></p>
        <p class="text-sm text-gray-400 mt-2">Per project</p>
      </div>
    </div>
    
    <div class="mt-6">
      <div class="flex justify-between items-center mb-2">
        <h3 class="font-medium">Annual Growth Target</h3>
        <span class="text-sm text-gray-400"><?php echo $growthRate; ?>%</span>
      </div>
      <div class="bg-gray-900 rounded-full overflow-hidden mb-4">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2" style="width: <?php echo min(100, abs($growthRate)); ?>%"></div>
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
                                  WHERE project_alloc_cmp = '$companyId' 
                                  ORDER BY project_start_date DESC LIMIT 3";
          $recentActivityResult = $con->query($recentActivityQuery);
          
          if ($recentActivityResult && $recentActivityResult->num_rows > 0) {
              while ($activity = $recentActivityResult->fetch_assoc()) {
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
                  
                  // Choose icon based on status
                  $iconClass = 'fas fa-project-diagram';
                  $iconBgClass = 'bg-blue-500';
                  $iconTextClass = 'text-blue-400';
                  
                  if (strtolower($activity['project_status']) == 'completed') {
                      $iconClass = 'fas fa-check-circle';
                      $iconBgClass = 'bg-green-500';
                      $iconTextClass = 'text-green-400';
                  } elseif (strtolower($activity['project_status']) == 'in progress') {
                      $iconClass = 'fas fa-spinner';
                      $iconBgClass = 'bg-yellow-500';
                      $iconTextClass = 'text-yellow-400';
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
          if ($holidaysResult && $holidaysResult->num_rows > 0) {
              while ($holiday = $holidaysResult->fetch_assoc()) {
                  $holidayDate = new DateTime($holiday['holiday_date']);
                  $formattedDate = $holidayDate->format('l, M j');
                  
                  // Determine if it's a company-specific holiday
                  $isCompanySpecific = !empty($holiday['holiday_cmp_id']);
                  $borderClass = $isCompanySpecific ? 'border-indigo-500' : 'border-blue-500';
                  $badgeClass = $isCompanySpecific ? 'bg-indigo-500' : 'bg-blue-500';
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
  </div>
</section>


<!-- Team Members Section -->
<section class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8 card-hover transition-all duration-300">
  <h2 class="text-xl font-bold mb-4">Team Members</h2>
  
  <div class="overflow-x-auto">
    <table class="min-w-full bg-gray-900 rounded-lg overflow-hidden">
      <thead>
        <tr class="bg-gray-800 text-left">
          <th class="px-4 py-3 text-sm font-medium text-gray-300">Manager</th>
          <th class="px-4 py-3 text-sm font-medium text-gray-300">Department</th>
          <th class="px-4 py-3 text-sm font-medium text-gray-300">Email</th>
          <th class="px-4 py-3 text-sm font-medium text-gray-300">Status</th>
          <th class="px-4 py-3 text-sm font-medium text-gray-300">Projects</th>
          <th class="px-4 py-3 text-sm font-medium text-gray-300">Experience</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($managersListResult && $managersListResult->num_rows > 0) {
            while ($manager = $managersListResult->fetch_assoc()) {
                // Determine status badge based on onleave value
                $statusClass = ($manager['onLeave'] == 1) ? 'bg-orange-500' : 'bg-green-500';
                $statusText = ($manager['onLeave'] == 1) ? 'On Leave' : 'Active';
                
                // Format years of experience
                $experience = $manager['mag_exp'] ?? 0;
                $experienceText = $experience . ($experience == 1 ? ' year' : ' years');
        ?>
                <tr class="border-t border-gray-800 hover:bg-gray-800">
                  <td class="px-4 py-3">
                    <div class="flex items-center">
                      <?php if(!empty($manager['mag_profile']) && file_exists($manager['mag_profile'])): ?>
                        <img src="<?php echo htmlspecialchars($manager['mag_profile']); ?>" alt="Profile" class="h-8 w-8 rounded-full mr-3" />
                      <?php else: ?>
                        <img src="../images/Profile/guest.png" alt="Profile" class="h-8 w-8 rounded-full mr-3" />
                      <?php endif; ?>
                      <div>
                        <p class="font-medium"><?php echo htmlspecialchars($manager['mag_name']); ?></p>
                        <p class="text-xs text-gray-400">ID: <?php echo htmlspecialchars($manager['mag_id']); ?></p>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-sm"><?php echo htmlspecialchars($manager['mag_department'] ?? 'General'); ?></td>
                  <td class="px-4 py-3 text-sm"><?php echo htmlspecialchars($manager['mag_email'] ?? 'Not Available'); ?></td>
                  <td class="px-4 py-3 text-sm">
                    <span class="px-2 py-1 rounded-full text-xs <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                  </td>
                  <td class="px-4 py-3 text-sm">
                    <div class="flex items-center">
                      <span class="mr-2"><?php echo $manager['project_count']; ?></span>
                      <?php if($manager['project_count'] > 0): ?>
                      <div class="relative group">
                        <i class="fas fa-info-circle text-gray-400 cursor-pointer"></i>
                        <div class="absolute left-0 mt-2 w-48 bg-gray-700 rounded-lg shadow-lg p-2 z-10 hidden group-hover:block">
                          <?php
                          // Get project names for this manager
                          $projectNamesQuery = "SELECT project_name FROM project WHERE project_alloc_mag = '{$manager['mag_id']}' LIMIT 5";
                          $projectNamesResult = $con->query($projectNamesQuery);
                          
                          if ($projectNamesResult && $projectNamesResult->num_rows > 0) {
                              echo '<p class="text-xs font-medium mb-1">Current Projects:</p>';
                              echo '<ul class="text-xs text-gray-300">';
                              while ($project = $projectNamesResult->fetch_assoc()) {
                                  echo '<li class="mb-1">• ' . htmlspecialchars($project['project_name']) . '</li>';
                              }
                              echo '</ul>';
                          }
                          ?>
                        </div>
                      </div>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-sm"><?php echo $experienceText; ?></td>
                </tr>
        <?php
            }
        } else {
        ?>
            <tr>
              <td colspan="6" class="px-4 py-3 text-center text-gray-400">No team members found</td>
            </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
  </div>
</section>


<footer class="bg-gray-900 p-4 rounded-lg text-center text-gray-400 text-sm">
  <p>&copy; 2025 CryBug Bug Tracking System. All rights reserved.</p>
</footer>

</main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  

  
  // Card hover effects
  const cards = document.querySelectorAll('.card-hover');
  
  cards.forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.classList.add('card-active');
    });
    
    card.addEventListener('mouseleave', function() {
      this.classList.remove('card-active');
    });
  });
  

  
  
  // Team member hover effect
  const teamRows = document.querySelectorAll('tbody tr');
  teamRows.forEach(row => {
    row.addEventListener('mouseenter', function() {
      this.classList.add('bg-gray-800');
    });
    
    row.addEventListener('mouseleave', function() {
      this.classList.remove('bg-gray-800');
    });
  });
  
  // Project info tooltips
  const infoIcons = document.querySelectorAll('.fa-info-circle');
  infoIcons.forEach(icon => {
    const tooltip = icon.nextElementSibling;
    if (tooltip) {
      icon.addEventListener('mouseenter', function() {
        tooltip.classList.remove('hidden');
      });
      
      icon.addEventListener('mouseleave', function() {
        tooltip.classList.add('hidden');
      });
    }
  });
  
 
});

</script>
</body>
</html>
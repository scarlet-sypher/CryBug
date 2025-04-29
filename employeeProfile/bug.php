<?php
include "connection.php" ;

session_start() ;


$empNanme = $_SESSION['emp_name'] ?? 'Company';
$empProfile = $_SESSION['emp_profile'] ?? '../images/Profile/guest.png';
$empEmail = $_SESSION['emp_email']  ?? 'company@example.com';
$empID = $_SESSION['emp_id'] ?? 'No ID avaliable';
$empRole = $_SESSION['emp_role'] ?? 'No role' ;
$empPhone = $_SESSION['emp_phone']  ?? 'phone number' ;
$empDept = $_SESSION['emp_dept'] ;
$empExp = $_SESSION['emp_exp']  ; 
$empDev = $_SESSION['dev'] ; 
$empAuto = $_SESSION['auto'] ; 
$empDesign = $_SESSION['design']  ; 
$empVerbal = $_SESSION['verbal'] ;

$CompanyId = $_SESSION['cmp_id'] ;
$CompanyName = $_SESSION['cmp_name'] ;




// Default values
$orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'bug_id';
$orderDirection = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterSeverity = isset($_GET['severity']) ? $_GET['severity'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$onlyMyBugs = isset($_GET['my_bugs']) ? $_GET['my_bugs'] : false;

// Current user ID (replace with actual session user ID)
$current_user_id = $_SESSION['emp_id']; // This should be the actual logged-in user ID

// Build query
$sql = "SELECT b.*, 
               e.emp_name as assigned_to_name,
               er.emp_name as reported_by_name
        FROM bug b
        LEFT JOIN employee e ON b.bug_assigned_to = e.emp_id 
        LEFT JOIN employee er ON b.bug_reported_by = er.emp_id
        WHERE bug_alloc_cmp = '$CompanyId'";

// Add search condition if search is provided
if (!empty($search)) {
    $search = $con->real_escape_string($search);
    $sql .= " AND (b.bug_id LIKE '%$search%' OR b.bug_title LIKE '%$search%' OR b.bug_descp LIKE '%$search%')";
}

// Add filter conditions
if (!empty($filterSeverity)) {
    $filterSeverity = $con->real_escape_string($filterSeverity);
    $sql .= " AND b.bug_severity = '$filterSeverity'";
}

if (!empty($filterStatus)) {
    $filterStatus = $con->real_escape_string($filterStatus);
    $sql .= " AND b.bug_status = '$filterStatus'";
}

// Filter for My Bugs only
if ($onlyMyBugs) {
    $sql .= " AND b.bug_assigned_to = '$current_user_id'";
}

// Add order by
$sql .= " ORDER BY $orderBy $orderDirection";

// For pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 5;
$offset = ($page - 1) * $recordsPerPage;

// Get total records for pagination
$countSql = "SELECT COUNT(*) as total FROM ($sql) as filtered_bugs";
$countResult = $con->query($countSql);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Add limit for pagination
$sql .= " LIMIT $offset, $recordsPerPage";

// Execute the query
$result = $con->query($sql);

// Get severity options for dropdown
$severityQuery = "SELECT DISTINCT bug_severity FROM bug WHERE bug_severity IS NOT NULL";
$severityResult = $con->query($severityQuery);
$severities = [];
while ($row = $severityResult->fetch_assoc()) {
    $severities[] = $row['bug_severity'];
}

// Get status options for dropdown
$statusQuery = "SELECT DISTINCT bug_status FROM bug WHERE bug_status IS NOT NULL";
$statusResult = $con->query($statusQuery);
$statuses = [];
while ($row = $statusResult->fetch_assoc()) {
    $statuses[] = $row['bug_status'];
}

// Count total bugs
$bugCountQuery = "SELECT COUNT(*) as total FROM bug";
$bugCountResult = $con->query($bugCountQuery);
$totalBugs = $bugCountResult->fetch_assoc()['total'];

// Count open bugs
$openBugsQuery = "SELECT COUNT(*) as open FROM bug WHERE bug_status = 'Open'";
$openBugsResult = $con->query($openBugsQuery);
$openBugs = $openBugsResult->fetch_assoc()['open'];

// Count in progress bugs
$progressBugsQuery = "SELECT COUNT(*) as progress FROM bug WHERE bug_status = 'In Progress'";
$progressBugsResult = $con->query($progressBugsQuery);
$progressBugs = $progressBugsResult->fetch_assoc()['progress'];

// Count fixed bugs
$fixedBugsQuery = "SELECT COUNT(*) as fixed FROM bug WHERE bug_status = 'Fixed'";
$fixedBugsResult = $con->query($fixedBugsQuery);
$fixedBugs = $fixedBugsResult->fetch_assoc()['fixed'];

// Function to get sorting URL
function getSortUrl($field) {
    global $orderBy, $orderDirection, $search, $filterSeverity, $filterStatus, $onlyMyBugs, $page;
    
    $direction = ($orderBy === $field && $orderDirection === 'ASC') ? 'DESC' : 'ASC';
    
    $url = "bug.php?sort=$field&order=$direction";
    if (!empty($search)) $url .= "&search=$search";
    if (!empty($filterSeverity)) $url .= "&severity=$filterSeverity";
    if (!empty($filterStatus)) $url .= "&status=$filterStatus";
    if ($onlyMyBugs) $url .= "&my_bugs=1";
    if ($page > 1) $url .= "&page=$page";
    
    return $url;
}

// Function to get severity class
function getSeverityClass($severity) {
    switch (strtolower($severity)) {
        case 'critical':
            return 'bg-red-500' ;
        case 'high':
            return 'bg-teal-500';
        case 'medium':
            return 'bg-yellow-500';
        case 'low':
            return 'bg-blue-500';
        default:
            return 'bg-gray-500';
    }
}

// Function to get status class
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'open':
            return 'bg-green-500';
        case 'in progress':
            return 'bg-blue-500';
        case 'on hold':
            return 'bg-red-500';
        default:
            return 'bg-indigo-500';
    }
}

// Format date function
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Bugs</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="dashboard.css">
  <link rel="stylesheet" href="../src/output.css">
<script src="dashboard.js"></script>

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
            <a href="dashboard.php" class="sidebar-link  flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Dashboard">
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
            <a href="bug.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Bugs">
              <i class="fas fa-bug mr-3"></i>
              <span>Bugs</span>
            </a>
          </li>
          <li>
            <a href="update.php" class="sidebar-link  flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Reports">
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
          <h1 class="text-2xl md:text-3xl font-bold"><?php echo htmlspecialchars($CompanyName) ;?>'s Bugs</h1>
          <p class="text-gray-400" id="currentDateTime"><?php echo date('l, F j, Y'); ?></p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
          
          <div class="relative">
            <button id="profileDropdownBtn" class="flex items-center">
            <?php if(!empty($empProfile) && file_exists($empProfile)): ?>
              <img src="<?php echo htmlspecialchars($empProfile) ; ?>" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
            <?php else : ; ?>
              <img src="../images/Profile/guest.png" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
            <?php endif ; ?>
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
      
      <!-- Bugs Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-red-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-bug text-red-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Total Bugs</p>
              <h3 class="text-xl font-bold"><?php echo $totalBugs; ?></h3>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-yellow-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-exclamation-circle text-yellow-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Open Bugs</p>
              <h3 class="text-xl font-bold"><?php echo $openBugs; ?></h3>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-blue-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-spinner text-blue-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">In Progress</p>
              <h3 class="text-xl font-bold"><?php echo $progressBugs; ?></h3>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-green-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Fixed Bugs</p>
              <h3 class="text-xl font-bold"><?php echo $fixedBugs; ?></h3>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Bug Search and Filter -->
      <div class="bg-gray-800 p-4 rounded-xl mb-6 card-hover transition-all duration-300">
        <div class="flex flex-col md:flex-row justify-between space-y-4 md:space-y-0">
          <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
            <!-- Search Box -->
            <form action="bug.php" method="GET" class="flex items-center">
              <div class="relative w-full">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search bugs..." 
                  class="w-64 bg-gray-700 text-white rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-search text-gray-400"></i>
                </div>
                <?php if (!empty($orderBy)): ?>
                  <input type="hidden" name="sort" value="<?php echo htmlspecialchars($orderBy); ?>">
                <?php endif; ?>
                <?php if (!empty($orderDirection)): ?>
                  <input type="hidden" name="order" value="<?php echo htmlspecialchars($orderDirection); ?>">
                <?php endif; ?>
                <?php if (!empty($filterSeverity)): ?>
                  <input type="hidden" name="severity" value="<?php echo htmlspecialchars($filterSeverity); ?>">
                <?php endif; ?>
                <?php if (!empty($filterStatus)): ?>
                  <input type="hidden" name="status" value="<?php echo htmlspecialchars($filterStatus); ?>">
                <?php endif; ?>
                <?php if ($onlyMyBugs): ?>
                  <input type="hidden" name="my_bugs" value="1">
                <?php endif; ?>
              </div>
              <button type="submit" class="ml-2 bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Search</button>
            </form>
          </div>

          <!-- Filter Section -->
          <div class="flex flex-wrap items-center space-x-2">
            <!-- Severity Filter -->
            <div class="relative">
              <select id="severityFilter" onchange="applyFilter('severity', this.value)" class="bg-gray-700 text-white rounded px-4 py-2 appearance-none pr-8 focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">All Severities</option>
                <?php foreach ($severities as $severity): ?>
                <option value="<?php echo htmlspecialchars($severity); ?>" <?php echo ($filterSeverity === $severity ? 'selected' : ''); ?>><?php echo htmlspecialchars($severity); ?></option>
                <?php endforeach; ?>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <i class="fas fa-chevron-down text-gray-400"></i>
              </div>
            </div>

            <!-- Status Filter -->
            <div class="relative">
              <select id="statusFilter" onchange="applyFilter('status', this.value)" class="bg-gray-700 text-white rounded px-4 py-2 appearance-none pr-8 focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $status): ?>
                <option value="<?php echo htmlspecialchars($status); ?>" <?php echo ($filterStatus === $status ? 'selected' : ''); ?>><?php echo htmlspecialchars($status); ?></option>
                <?php endforeach; ?>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <i class="fas fa-chevron-down text-gray-400"></i>
              </div>
            </div>

            <!-- My Bugs Toggle -->
            <button id="myBugsToggle" onclick="toggleMyBugs()" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded <?php echo ($onlyMyBugs ? 'bg-green-600 hover:bg-green-700' : ''); ?>">
              My Bugs <?php echo ($onlyMyBugs ? '<i class="fas fa-check ml-1"></i>' : ''); ?>
            </button>

            <!-- Clear Filters -->
            <?php if (!empty($search) || !empty($filterSeverity) || !empty($filterStatus) || $onlyMyBugs): ?>
            <a href="bug.php" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded">
              <i class="fas fa-times mr-1"></i> Clear Filters
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <!-- Bug Listing Section with Table -->
      <div class="bg-gray-800 rounded-xl p-6 mb-8 card-hover transition-all duration-300">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-700">
            <thead>
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                  <a href="<?php echo getSortUrl('bug_id'); ?>" class="flex items-center hover:text-white">
                    ID
                    <span class="sort-icon ml-1 <?php echo ($orderBy === 'bug_id' ? ($orderDirection === 'ASC' ? 'sort-asc' : 'sort-desc') : ''); ?>"></span>
                  </a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                  <a href="<?php echo getSortUrl('bug_title'); ?>" class="flex items-center hover:text-white">
                    Title
                    <span class="sort-icon ml-1 <?php echo ($orderBy === 'bug_title' ? ($orderDirection === 'ASC' ? 'sort-asc' : 'sort-desc') : ''); ?>"></span>
                  </a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Project</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                  <a href="<?php echo getSortUrl('bug_severity'); ?>" class="flex items-center hover:text-white">
                    Severity
                    <span class="sort-icon ml-1 <?php echo ($orderBy === 'bug_severity' ? ($orderDirection === 'ASC' ? 'sort-asc' : 'sort-desc') : ''); ?>"></span>
                  </a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                  <a href="<?php echo getSortUrl('bug_status'); ?>" class="flex items-center hover:text-white">
                    Status
                    <span class="sort-icon ml-1 <?php echo ($orderBy === 'bug_status' ? ($orderDirection === 'ASC' ? 'sort-asc' : 'sort-desc') : ''); ?>"></span>
                  </a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                  <a href="<?php echo getSortUrl('bug_created_date'); ?>" class="flex items-center hover:text-white">
                    Reported
                    <span class="sort-icon ml-1 <?php echo ($orderBy === 'bug_created_date' ? ($orderDirection === 'ASC' ? 'sort-asc' : 'sort-desc') : ''); ?>"></span>
                  </a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                  <a href="<?php echo getSortUrl('bug_assigned_to'); ?>" class="flex items-center hover:text-white">
                    Assigned To
                    <span class="sort-icon ml-1 <?php echo ($orderBy === 'bug_assigned_to' ? ($orderDirection === 'ASC' ? 'sort-asc' : 'sort-desc') : ''); ?>"></span>
                  </a>
                </th>
                
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td class="px-4 py-4 whitespace-nowrap">
                      <div class="font-medium"><?php echo htmlspecialchars($row['bug_id']); ?></div>
                    </td>
                    <td class="px-4 py-4">
                      <div class="text-sm"><?php echo htmlspecialchars($row['bug_title']); ?></div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                      <div class="text-sm">
                        <?php 
                        // Project name would be joined from another table, but we're simplifying here
                        echo !empty($row['bug_alloc_cmp']) ? htmlspecialchars($row['bug_alloc_cmp']) : 'Project Alpha'; 
                        ?>
                      </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                      <div class="text-sm">
                        <span class="px-2 py-1 text-xs font-medium rounded-full text-white <?php echo getSeverityClass($row['bug_severity']); ?>">
                          <?php echo htmlspecialchars($row['bug_severity']); ?>
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                      <div class="text-sm">
                        <span class="px-2 py-1 text-xs font-medium rounded-full text-white <?php echo getStatusClass($row['bug_status']); ?>">
                          <?php echo htmlspecialchars($row['bug_status']); ?>
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                      <div class="text-sm"><?php echo formatDate($row['bug_created_date']); ?></div>

                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                      <div class="text-sm"><?php echo htmlspecialchars($row['assigned_to_name'] ?? 'Unassigned'); ?></div>
                    </td>
                    
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                    <div class="flex flex-col items-center">
                      <i class="fas fa-search mb-2 text-2xl"></i>
                      <p>No bugs found matching your criteria.</p>
                      <?php if (!empty($search) || !empty($filterSeverity) || !empty($filterStatus) || $onlyMyBugs): ?>
                        <a href="bug.php" class="text-green-500 hover:text-green-400 mt-2">Clear all filters</a>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="mt-6 flex justify-between items-center">
          <div class="text-sm text-gray-400">
            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $recordsPerPage, $totalRecords); ?> of <?php echo $totalRecords; ?> bugs
          </div>
          <div class="flex space-x-1">
            <?php if ($page > 1): ?>
              <a href="<?php echo str_replace('&page=' . $page, '&page=' . ($page - 1), $_SERVER['REQUEST_URI']); ?>" 
                class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm">
                <i class="fas fa-chevron-left"></i>
              </a>
            <?php endif; ?>
            
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $startPage + 4);
            if ($endPage - $startPage < 4) {
                $startPage = max(1, $endPage - 4);
            }
            ?>
            
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
              <a href="<?php 
                $uri = $_SERVER['REQUEST_URI'];
                if (strpos($uri, 'page=') !== false) {
                    $uri = preg_replace('/([&\?])page=\d+/', "$1page=$i", $uri);
                } else {
                    $uri .= (strpos($uri, '?') !== false ? '&' : '?') . "page=$i";
                }
                echo $uri;
              ?>" 
                class="<?php echo $i == $page ? 'bg-green-600 text-white' : 'bg-gray-700 hover:bg-gray-600'; ?> px-3 py-1 rounded text-sm">
                <?php echo $i; ?>
              </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
              <a href="<?php echo str_replace('&page=' . $page, '&page=' . ($page + 1), $_SERVER['REQUEST_URI']); ?>" 
                class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm">
                <i class="fas fa-chevron-right"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
      
      <!-- Add New Bug Button -->
      
      
      <!-- Footer -->
      <footer class="mt-auto py-4 text-center text-gray-500 text-sm">
        <p>&copy; 2025 CryBug - Bug Tracking System. All rights reserved.</p>
      </footer>
      
    </main>
  </div>

  <script>
   
    
    // Filter functions
    function applyFilter(type, value) {
      const currentUrl = new URL(window.location.href);
      
      // Remove page parameter to go back to first page when filtering
      currentUrl.searchParams.delete('page');
      
      if (value) {
        currentUrl.searchParams.set(type, value);
      } else {
        currentUrl.searchParams.delete(type);
      }
      
      window.location.href = currentUrl.toString();
    }
    
    function toggleMyBugs() {
      const currentUrl = new URL(window.location.href);
      
      // Remove page parameter to go back to first page when filtering
      currentUrl.searchParams.delete('page');
      
      if (currentUrl.searchParams.has('my_bugs')) {
        currentUrl.searchParams.delete('my_bugs');
      } else {
        currentUrl.searchParams.set('my_bugs', '1');
      }
      
      window.location.href = currentUrl.toString();
    }
    
    // Delete confirmation
    function confirmDelete(bugId) {
      const modal = document.getElementById('deleteModal');
      const confirmBtn = document.getElementById('confirmDeleteBtn');
      
      modal.classList.remove('hidden');
      confirmBtn.href = `delete_bug.php?id=${bugId}`;
    }
    
    document.getElementById('cancelDelete').addEventListener('click', function() {
      document.getElementById('deleteModal').classList.add('hidden');
    });
    
  </script>
</body>
</html>

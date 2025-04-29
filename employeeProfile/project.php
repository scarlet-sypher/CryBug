<?php
  session_start() ; 

  if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../employee/emp-Login.php");
    exit;
}

  
  $empProfile = $_SESSION['emp_profile'] ?? '../images/Profile/guest.png';
  $empID = $_SESSION['emp_id'] ?? 'No ID avaliable';
  $CompanyId = $_SESSION['cmp_id'] ;
  $CompanyName = $_SESSION['cmp_name'] ;


include "connection.php" ;
include "../session_manager.php";

$show_my_projects = isset($_GET['my_projects']) && $_GET['my_projects'] == '1';




$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$priority = isset($_GET['priority']) ? mysqli_real_escape_string($con, $_GET['priority']) : '';
$sort_by = isset($_GET['sort_by']) ? mysqli_real_escape_string($con, $_GET['sort_by']) : 'project_name';
$sort_order = isset($_GET['sort_order']) ? mysqli_real_escape_string($con, $_GET['sort_order']) : 'ASC';


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 5; 
$start_from = ($page - 1) * $per_page;

$query = "SELECT * FROM project WHERE project_alloc_cmp = '$CompanyId'";
// $query = "SELECT * FROM project WHERE 1 = 1";

if (!empty($search)) {
    $query .= " AND (project_name LIKE '%$search%' OR project_descp LIKE '%$search%')";
}

if (!empty($status)) {
    $query .= " AND project_status = '$status'";
}

if (!empty($priority)) {
    $query .= " AND project_priority = '$priority'";
}

if ($show_my_projects) {
    // Fetch only projects where the current employee is assigned
    $query .= " AND project_alloc_emp = '$empID'";
}

$query .= " ORDER BY $sort_by $sort_order";

$query_for_count = $query;
$query .= " LIMIT $start_from, $per_page";

$result = mysqli_query($con, $query);

$count_result = mysqli_query($con, $query_for_count);
$total_records = mysqli_num_rows($count_result);
$total_pages = ceil($total_records / $per_page);


$status_query = "SELECT DISTINCT project_status FROM project WHERE project_status IS NOT NULL";
$status_result = mysqli_query($con, $status_query);


$priority_query = "SELECT DISTINCT project_priority FROM project WHERE project_priority IS NOT NULL";
$priority_result = mysqli_query($con, $priority_query);


function getSortLink($field, $current_sort, $current_order) {
    $new_order = ($current_sort == $field && $current_order == 'ASC') ? 'DESC' : 'ASC';
    $icon = '';
    
    if ($current_sort == $field) {
        $icon = ($current_order == 'ASC') ? '<i class="fas fa-sort-up ml-1"></i>' : '<i class="fas fa-sort-down ml-1"></i>';
    } else {
        $icon = '<i class="fas fa-sort ml-1 text-gray-400"></i>';
    }
    
    $params = $_GET;
    $params['sort_by'] = $field;
    $params['sort_order'] = $new_order;
    $query_string = http_build_query($params);
    
    return '<a href="?' . $query_string . '" class="text-white hover:text-green-400">' . ucfirst(str_replace('project_', '', $field)) . $icon . '</a>';
}

// Function to generate status badge
function getStatusBadge($status) {
    $color = 'gray';
    
    switch(strtolower($status)) {
        case 'active':
            $color = 'red';
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
        case 'started':
            $color = 'green';
            break;
    }
    
    return '<span class="bg-' . $color . '-500 text-xs px-2 py-1 rounded-full">' . $status . '</span>';
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
?>
  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Projects</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../src/output.css">
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
            <a href="dashboard.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Dashboard">
              <i class="fas fa-tachometer-alt mr-3"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="project.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Projects">
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
          <a href="logout.php" class="mt-4 w-full bg-green-600 hover:bg-green-700 p-2 rounded flex items-center justify-center">
            <i class="fas fa-sign-out-alt mr-2"></i>
            <span>Logout</span>
          </a>
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
          <h1 class="text-2xl md:text-3xl font-bold"><?php echo htmlspecialchars($CompanyName) ; ?>'s Projects</h1>
          <p class="text-gray-400" id="currentDateTime">Loading date...</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
           
          <div class="relative">
            <button id="profileDropdownBtn" class="flex items-center">

              <?php if(!empty($empProfile) && file_exists($empProfile)): ?>
                <img src="<?php echo htmlspecialchars($empProfile) ; ?>" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
              <?php else: ?>
                <img src="../images/Profile/guest.png" alt="Profile" class="h-10 w-10 rounded-full border-2 border-indigo-500" />
              <?php endif; ?>
              
              <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
            </button>
            <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg p-2 hidden" id="profileDropdown">
              <a href="profile.php" class="block p-2 hover:bg-gray-700 rounded text-sm">
                <i class="fas fa-user mr-2"></i> My Profile
              </a>
              <a href="logout.php" class="block p-2 hover:bg-gray-700 rounded text-sm text-green-400">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Projects Overview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-purple-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-project-diagram text-purple-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Assigned Projects</p>
              <h3 class="text-xl font-bold">2</h3>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-blue-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-tasks text-blue-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Total Tasks</p>
              <h3 class="text-xl font-bold">15</h3>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-4 card-hover transition-all duration-300">
          <div class="flex items-center">
            <div class="rounded-full bg-green-500 bg-opacity-20 p-3 mr-4">
              <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div>
              <p class="text-sm text-gray-400">Completed Tasks</p>
              <h3 class="text-xl font-bold">8</h3>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Project Search and Filter -->
      <div class="bg-gray-800 p-4 rounded-xl mb-6 card-hover transition-all duration-300">
        <form method="GET" action="" id="filterForm">
            <input type="hidden" name="sort_by" value="<?php echo htmlspecialchars($sort_by); ?>">
            <input type="hidden" name="sort_order" value="<?php echo htmlspecialchars($sort_order); ?>">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center md:space-x-2 space-y-2 md:space-y-0">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search projects..." class="bg-gray-900 border border-gray-700 rounded-lg p-2 pl-10 w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                        <?php if (!empty($search)): ?>
                            <button type="button" onclick="document.querySelector('input[name=search]').value=''; document.getElementById('filterForm').submit();" class="absolute right-3 top-3 text-gray-500 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <!-- My Projects button -->
                    <button type="submit" name="my_projects" value="<?php echo $show_my_projects ? '0' : '1'; ?>" class="<?php echo $show_my_projects ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-700 hover:bg-gray-600'; ?> text-white rounded-lg p-2 text-sm font-medium">
                        <i class="fas fa-user-check mr-1"></i> 
                        <?php echo $show_my_projects ? 'All Projects' : 'My Projects'; ?>
                    </button>
                </div>
                
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
                    <select name="status" class="bg-gray-900 border border-gray-700 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Status: All</option>
                        <?php while ($status_row = mysqli_fetch_assoc($status_result)): ?>
                            <option value="<?php echo htmlspecialchars($status_row['project_status']); ?>" <?php echo ($status == $status_row['project_status']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status_row['project_status']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    
                    <select name="priority" class="bg-gray-900 border border-gray-700 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Priority: All</option>
                        <?php while ($priority_row = mysqli_fetch_assoc($priority_result)): ?>
                            <option value="<?php echo htmlspecialchars($priority_row['project_priority']); ?>" <?php echo ($priority == $priority_row['project_priority']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($priority_row['project_priority']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    
                    <?php if (!empty($search) || !empty($status) || !empty($priority) || $show_my_projects): ?>
                        <button type="button" onclick="window.location='project.php'" class="bg-gray-700 hover:bg-gray-600 text-white rounded-lg p-2 focus:outline-none">
                            <i class="fas fa-undo mr-1"></i> Reset Filters
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
      </div>

      <!-- Project Cards -->
      <!-- Projects Table -->
      <div class="bg-gray-800 rounded-xl p-6 mb-8 card-hover transition-all duration-300">
        <?php if ($total_records > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <?php echo getSortLink('project_name', $sort_by, $sort_order); ?>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <?php echo getSortLink('project_descp', $sort_by, $sort_order); ?>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <?php echo getSortLink('project_status', $sort_by, $sort_order); ?>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <?php echo getSortLink('project_priority', $sort_by, $sort_order); ?>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <?php echo getSortLink('project_progress', $sort_by, $sort_order); ?>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <?php echo getSortLink('project_end_date', $sort_by, $sort_order); ?>
                            </th>
                            
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-gray-700 transition-colors duration-200">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-medium"><?php echo htmlspecialchars($row['project_name']); ?></div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-400 truncate max-w-xs"><?php echo htmlspecialchars($row['project_descp']); ?></div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <?php echo getStatusBadge($row['project_status']); ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <?php echo getPriorityIndicator($row['project_priority']); ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-900 rounded-full overflow-hidden mr-2">
                                        <div class="bg-green-500 h-2" style="width: <?php echo (int)$row['project_progress']; ?>%"></div>
                                    </div>
                                    <span class="text-xs"><?php echo (int)$row['project_progress']; ?>%</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <?php 
                                    $date = new DateTime($row['project_end_date']);
                                    echo $date->format('M d, Y'); 
                                    ?>
                                </div>
                            </td>
                            
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <a href="#" data-project-id="<?php echo $row['project_id']; ?>" class="inline-block bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-xs font-medium text-white">
                                    View
                                </a>
                            </td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls -->
            <?php if ($total_pages > 1): ?>
                <div class="flex justify-center mt-6 space-x-1">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&priority=<?php echo urlencode($priority); ?>&sort_by=<?php echo urlencode($sort_by); ?>&sort_order=<?php echo urlencode($sort_order); ?>&my_projects=<?php echo $show_my_projects ? '1' : '0'; ?>" class="px-3 py-1 rounded bg-gray-700 hover:bg-gray-600 text-white">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php else: ?>
                        <button disabled class="px-3 py-1 rounded bg-gray-900 text-gray-500 cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php
                    // Display page numbers
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&priority=<?php echo urlencode($priority); ?>&sort_by=<?php echo urlencode($sort_by); ?>&sort_order=<?php echo urlencode($sort_order); ?>&my_projects=<?php echo $show_my_projects ? '1' : '0'; ?>" class="px-3 py-1 rounded <?php echo $i == $page ? 'bg-green-600 text-white' : 'bg-gray-700 hover:bg-gray-600 text-white'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&priority=<?php echo urlencode($priority); ?>&sort_by=<?php echo urlencode($sort_by); ?>&sort_order=<?php echo urlencode($sort_order); ?>&my_projects=<?php echo $show_my_projects ? '1' : '0'; ?>" class="px-3 py-1 rounded bg-gray-700 hover:bg-gray-600 text-white">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <button disabled class="px-3 py-1 rounded bg-gray-900 text-gray-500 cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- No projects found message -->
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-search text-gray-500 text-5xl mb-4"></i>
                <h3 class="text-xl font-medium text-gray-400 mb-2">No projects found</h3>
                <p class="text-gray-500">Try adjusting your search or filter criteria</p>
                <?php if (!empty($search) || !empty($status) || !empty($priority) || $show_my_projects): ?>
                    <button onclick="window.location='project.php'" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-undo mr-2"></i>Reset Filters
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        
    </div>
    
    <!-- Project Detail Modal -->
    <div id="projectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black opacity-75"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" class="close-modal bg-gray-800 rounded-md text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times p-2"></i>
                    </button>
                </div>
                
                <div class="px-6 py-5 border-b border-gray-700">
                    <h3 class="text-lg font-medium text-white" id="modalProjectTitle">Project Details</h3>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-col space-y-6">
                        <div id="projectDetails" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Project details will be loaded here via AJAX -->
                            <div class="text-center p-8">
                                <i class="fas fa-spinner fa-spin text-green-500 text-3xl"></i>
                                <p class="mt-2 text-gray-400">Loading project details...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-800 flex justify-end">
                    <button type="button" class="close-modal bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-600 focus:outline-none">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    </main>
  </div>

  <script>
document.addEventListener('DOMContentLoaded', function() {
  // Get references to modal elements
  const modal = document.getElementById('projectModal');
  const projectDetails = document.getElementById('projectDetails');
  const closeButtons = document.querySelectorAll('.close-modal');
  
  // Set up event listeners for all view buttons (selecting by data attribute)
  const viewButtons = document.querySelectorAll('[data-project-id]');
  viewButtons.forEach(function(button) {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const projectId = this.getAttribute('data-project-id');
      openProjectModal(projectId);
    });
  });
  
  // Function to open the project modal
  function openProjectModal(projectId) {
    // Show loading in the project details area
    projectDetails.innerHTML = `
      <div class="text-center p-8 col-span-2">
        <i class="fas fa-spinner fa-spin text-green-500 text-3xl"></i>
        <p class="mt-2 text-gray-400">Loading project details...</p>
      </div>
    `;
    
    // Show the modal
    modal.classList.remove('hidden');
    
    // Load project details via AJAX
    fetch('project_modal_content.php?id=' + projectId)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.text();
      })
      .then(html => {
        // Insert the HTML content into project details
        projectDetails.innerHTML = html;
      })
      .catch(error => {
        projectDetails.innerHTML = `
          <div class="text-center p-8 col-span-2">
            <i class="fas fa-exclamation-circle text-red-500 text-3xl"></i>
            <p class="mt-2 text-red-400">Error loading project details. Please try again.</p>
          </div>
        `;
        console.error('Error loading project details:', error);
      });
  }
  
  // Close modal function
  function closeModal() {
    modal.classList.add('hidden');
  }
  
  // Add event listeners to close buttons
  closeButtons.forEach(button => {
    button.addEventListener('click', closeModal);
  });
  
  // Close modal when clicking outside
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      closeModal();
    }
  });
});
  </script>

</body>
</html>
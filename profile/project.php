<?php
// Start session management
session_start();

// Check if manager is logged in, redirect to login if not
if (!isset($_SESSION['mag_id'])) {
    header("Location: login.php");
    exit();
}

// Get manager data
$mag_id = $_SESSION['mag_id'];
$mag_cmp_id = $_SESSION['mag_cmp_id'];

$ManagerProfile = $_SESSION['mag_profile'] ; // Assuming company ID is stored in session

include "connection.php" ;
// Generate unique project ID in CRYPROJ### format
function generateProjectID($con) {
    // Get the highest project number currently in the database
    $sql = "SELECT project_id FROM project WHERE project_id LIKE 'CRYPROJ%' ORDER BY project_id DESC LIMIT 1";
    $result = $con->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['project_id'];
        // Extract the number part
        $lastNumber = intval(substr($lastID, 7)); // "CRYPROJ" is 7 characters
        $newNumber = $lastNumber + 1;
    } else {
        // Start from 101 if no projects exist
        $newNumber = 101;
    }
    
    // Format the new project ID
    return 'CRYPROJ' . $newNumber;
}

// Handle project creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_project'])) {
  // Generate unique project ID
  $project_id = generateProjectID($con);
  
  // Form input validation and sanitization
  $project_name = trim($con->real_escape_string($_POST['project_name']));
  $project_descp = trim($con->real_escape_string($_POST['project_descp']));
  $project_start_date = trim($con->real_escape_string($_POST['project_start_date']));
  $project_end_date = trim($con->real_escape_string($_POST['project_end_date']));
  $project_budget = filter_var($_POST['project_budget'], FILTER_VALIDATE_FLOAT) ? 
                   $con->real_escape_string($_POST['project_budget']) : 0;
  $project_priority = trim($con->real_escape_string($_POST['project_priority']));
  $category = trim($con->real_escape_string($_POST['category']));
  
  // Validate required fields
  $errors = [];
  if (empty($project_name)) {
      $errors[] = "Project name is required.";
  }
  if (empty($project_start_date)) {
      $errors[] = "Start date is required.";
  }
  if (empty($project_end_date)) {
      $errors[] = "End date is required.";
  }
  
  // Validate date format and logic
  $start_timestamp = strtotime($project_start_date);
  $end_timestamp = strtotime($project_end_date);
  
  if ($start_timestamp === false) {
      $errors[] = "Invalid start date format.";
  }
  if ($end_timestamp === false) {
      $errors[] = "Invalid end date format.";
  }
  if ($start_timestamp && $end_timestamp && $start_timestamp > $end_timestamp) {
      $errors[] = "End date must be after start date.";
  }
  
  // Validate budget
  if ($project_budget < 0) {
      $errors[] = "Budget cannot be negative.";
  }
  
  // Validate category is one of the allowed options
  $valid_categories = ['Development', 'Design', 'Marketing', 'Research'];
  if (!in_array($category, $valid_categories)) {
      $errors[] = "Invalid category selected.";
  }
  
  // Validate priority is one of the allowed options
  $valid_priorities = ['Low', 'Medium', 'High', 'Critical'];
  if (!in_array($project_priority, $valid_priorities)) {
      $errors[] = "Invalid priority selected.";
  }
  
  // Validate team members
  $employees = isset($_POST['team_members']) ? $_POST['team_members'] : [];
  foreach ($employees as $key => $emp_id) {
      // Sanitize each employee ID
      $employees[$key] = $con->real_escape_string($emp_id);
      
      // Check if employee exists and belongs to this manager
      $check_emp_query = "SELECT emp_id FROM employee WHERE emp_id = '{$employees[$key]}' AND mag_id = '$mag_id'";
      $check_result = $con->query($check_emp_query);
      if ($check_result->num_rows == 0) {
          $errors[] = "Invalid employee selected: " . $employees[$key];
          unset($employees[$key]); // Remove invalid employee
      }
  }
  $emp_ids = implode(',', $employees);
  
  // Initial status and progress
  $project_status = "Started";
  $project_progress = 0;
  
  // If no errors, proceed with insertion
  if (empty($errors)) {
      // Insert project data
      $sql = "INSERT INTO project (
          project_id, project_name, project_descp, project_start_date, project_end_date, 
          project_budget, project_priority, category, project_progress, project_status,
          project_alloc_mag, project_alloc_cmp, project_alloc_emp
      ) VALUES (
          '$project_id', '$project_name', '$project_descp', '$project_start_date', '$project_end_date',
          '$project_budget', '$project_priority', '$category', $project_progress, '$project_status',
          '$mag_id', '$mag_cmp_id', '$emp_ids'
      )";
      
      if ($con->query($sql) === TRUE) {
          $success_message = "Project created successfully!";
      } else {
          $error_message = "Error: " . $con->error;
      }
  } else {
      // Combine all errors into one message
      $error_message = "Please correct the following errors:<br>• " . implode("<br>• ", $errors);
  }
}

// Handle project deletion
if (isset($_GET['delete'])) {
    $project_id = $con->real_escape_string($_GET['delete']);
    
    // Check if manager owns this project
    $check_sql = "SELECT * FROM project WHERE project_id = '$project_id' AND project_alloc_mag = '$mag_id'";
    $check_result = $con->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $delete_sql = "DELETE FROM project WHERE project_id = '$project_id'";
        if ($con->query($delete_sql) === TRUE) {
            $success_message = "Project deleted successfully!";
        } else {
            $error_message = "Error deleting project: " . $con->error;
        }
    } else {
        $error_message = "You don't have permission to delete this project.";
    }
}

// Handle project update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_project'])) {
    $project_id = $con->real_escape_string($_POST['project_id']);
    $project_name = $con->real_escape_string($_POST['project_name']);
    $project_descp = $con->real_escape_string($_POST['project_descp']);
    $project_start_date = $con->real_escape_string($_POST['project_start_date']);
    $project_end_date = $con->real_escape_string($_POST['project_end_date']);
    $project_budget = $con->real_escape_string($_POST['project_budget']);
    $project_priority = $con->real_escape_string($_POST['project_priority']);
    $category = $con->real_escape_string($_POST['category']);
    
    // Get selected employees for the project
    $employees = isset($_POST['team_members']) ? $_POST['team_members'] : [];
    $emp_ids = implode(',', $employees);
    
    // Update project data
    $sql = "UPDATE project SET 
        project_name = '$project_name', 
        project_descp = '$project_descp', 
        project_start_date = '$project_start_date', 
        project_end_date = '$project_end_date', 
        project_budget = '$project_budget', 
        project_priority = '$project_priority', 
        category = '$category',
        project_alloc_emp = '$emp_ids'
        WHERE project_id = '$project_id' AND project_alloc_mag = '$mag_id'";
    
    if ($con->query($sql) === TRUE) {
        $success_message = "Project updated successfully!";
        // Redirect to remove the edit parameter from URL
        header("Location: project.php");
        exit();
    } else {
        $error_message = "Error: " . $sql . "<br>" . $con->error;
    }
}

// Get projects for current manager
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $con->real_escape_string($_GET['status']) : '';
$sort_by = isset($_GET['sort']) ? $con->real_escape_string($_GET['sort']) : 'project_name';

// Build query based on filters
$query = "SELECT * FROM project WHERE project_alloc_mag = '$mag_id'";

// Add search filter if provided
if (!empty($search)) {
    $query .= " AND (project_name LIKE '%$search%' OR project_descp LIKE '%$search%')";
}

// Add status filter if provided
if (!empty($status_filter)) {
    $query .= " AND project_status = '$status_filter'";
}

// Add sorting
switch ($sort_by) {
    case 'name':
        $query .= " ORDER BY project_name";
        break;
    case 'date':
        $query .= " ORDER BY project_start_date";
        break;
    case 'priority':
        $query .= " ORDER BY project_priority";
        break;
    default:
        $query .= " ORDER BY project_name";
}

$result = $con->query($query);

// Get all employees under the manager who are not on leave
$emp_query = "SELECT e.* FROM employee e 
              WHERE e.mag_id = '$mag_id' AND (e.onLeave = 0 OR e.onLeave IS NULL)";
$employees = $con->query($emp_query);

// Get project details for edit modal if requested
$edit_project = null;
if (isset($_GET['edit'])) {
    $edit_id = $con->real_escape_string($_GET['edit']);
    $edit_query = "SELECT * FROM project WHERE project_id = '$edit_id' AND project_alloc_mag = '$mag_id'";
    $edit_result = $con->query($edit_query);
    if ($edit_result->num_rows > 0) {
        $edit_project = $edit_result->fetch_assoc();
        // Convert employee IDs string back to array for multi-select
        $edit_project['emp_ids_array'] = !empty($edit_project['project_alloc_emp']) ? 
                                        explode(',', $edit_project['project_alloc_emp']) : [];
    }
}

// Generate a new project ID for the Add Project form
$new_project_id = generateProjectID($con);

// Close connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Projects</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <link rel="stylesheet" href="../src/output.css">
  <script src="dashboard.js" defer></script>

  <style>
    /* Custom styles for the modal */
    
    /* Cool form styling */
    .form-input {
      transition: all 0.3s ease;
      background: rgba(17, 24, 39, 0.7);
      border: 1px solid rgba(75, 85, 99, 0.5);
    }

    .form-input:focus {
      border-color: #ef4444;
      box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }

    .form-label {
      font-weight: 500;
      letter-spacing: 0.025em;
      margin-bottom: 0.5rem;
    }

    .project-id-display {
      background: rgba(239, 68, 68, 0.1);
      border: 1px dashed rgba(239, 68, 68, 0.5);
      color: #ef4444;
      /* font-family: monospace; */
      font-weight: bold;
      letter-spacing: 0.05em;
      text-align: center;
    }

    .modal-header {
      border-bottom: 1px solid rgba(75, 85, 99, 0.2);
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
    }

    .modal-footer {
      border-top: 1px solid rgba(75, 85, 99, 0.2);
      margin-top: 1.5rem;
      padding-top: 1rem;
    }

    /* Custom select styles */
    select.form-input option {
      background-color: #1F2937;
      color: white;
    }

    

    /* Cool badge effects for priority */
    .priority-badge {
      position: relative;
      overflow: hidden;
    }

    .priority-badge::after {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      animation: shine 2s infinite;
    }

    @keyframes shine {
      100% {
        left: 100%;
      }
    }

    /* Form sections */
    .form-section {
      position: relative;
      padding: 1rem;
      border-radius: 0.5rem;
      background: rgba(31, 41, 55, 0.5);
      margin-bottom: 1.5rem;
    }

    .form-section::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: #ef4444;
      border-radius: 4px 0 0 4px;
    }

    /* Float labels */
    .float-label-input {
      position: relative;
    }

    .float-label-input input:focus + label,
    .float-label-input input:not(:placeholder-shown) + label {
      transform: translateY(-1.5rem) scale(0.85);
      color: #ef4444;
    }

    .float-label-input label {
      position: absolute;
      left: 1rem;
      top: 0.75rem;
      transition: all 0.2s ease;
      pointer-events: none;
      color: #9ca3af;
    }

    
  </style>
</head>
<body class="bg-gradient-custom text-white min-h-screen font-sans bg-black antialiased">
    <?php if(isset($success_message)): ?>
    <div class="fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 animate__animated animate__fadeIn" id="successAlert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo $success_message; ?>
        </div>
        <button class="absolute top-2 right-2 text-white" onclick="document.getElementById('successAlert').style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('successAlert').classList.add('animate__fadeOut');
            setTimeout(() => {
                document.getElementById('successAlert').style.display='none';
            }, 500);
        }, 5000);
    </script>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
    <div class="fixed top-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50 animate__animated animate__fadeIn" id="errorAlert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo $error_message; ?>
        </div>
        <button class="absolute top-2 right-2 text-white" onclick="document.getElementById('errorAlert').style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('errorAlert').classList.add('animate__fadeOut');
            setTimeout(() => {
                document.getElementById('errorAlert').style.display='none';
            }, 500);
        }, 5000);
    </script>
    <?php endif; ?>

    <div class="overlay" id="sidebarOverlay"></div>
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar (Unchanged - keep your original sidebar code) -->
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
                  <a href="dashboard.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Dashboard">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                  </a>
                </li>
                <li>
                  <a href="project.php" class="sidebar-link flex active items-center p-3 rounded text-gray-300 hover:text-white" data-title="Projects" data-section="projects-section">
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
                <a href="logout.php" class="mt-4 w-full bg-red-600 hover:bg-red-700 p-2 rounded flex items-center justify-center">
                  <i class="fas fa-sign-out-alt mr-2"></i>
                  <span>Logout</span>
                </a>
              </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="md:ml-64 lg:ml-64 flex-1 p-4 md:p-6 transition-all">
            <button class="menu-toggle md:hidden mb-4 bg-gray-800 p-2 rounded">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Page Header -->
<!-- Page Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <!-- Left Side: Title & Description -->
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl md:text-3xl font-bold">Projects Management</h1>
                    <p class="text-gray-400" id="currentDateTime">Loading Date and time...</p>
                    <p class="text-gray-400">View, create and manage all your projects</p>
                </div>

                <!-- Right Side: Button + Profile -->
                <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                    <!-- New Project Button -->
                    <button class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg flex items-center shadow-lg transform transition-transform hover:scale-105" id="addProjectBtn">
                        <i class="fas fa-plus mr-2"></i> New Project
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button id="profileDropdownBtn" class="flex items-center">
                            <?php if (!empty($ManagerProfile) && file_exists($ManagerProfile)): ?>
                                <img src="<?php echo htmlspecialchars($ManagerProfile); ?>" alt="Profile" class="h-10 w-10 rounded-full border-2 border-red-500" />
                            <?php else: ?>
                                <img src="../images/Profile/guest.png" alt="Profile" class="h-10 w-10 rounded-full border-2 border-red-500" />
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg p-2 hidden z-50" id="profileDropdown">
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

            <!-- Project Filter Row -->
            <div class="bg-gray-800 p-4 rounded-xl mb-6 shadow-lg">
                <form method="GET" class="flex flex-col md:flex-row gap-4">
                  <div class="flex-1">
                    <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Search projects..." class="w-full bg-gray-900 text-white border border-gray-700 rounded-lg p-2 focus:outline-none focus:border-red-500">
                  </div>
                  <div class="flex gap-2">
                    <select name="status" class="bg-gray-900 text-white border border-gray-700 rounded-lg p-2 focus:outline-none focus:border-red-500">
                      <option value="">All Status</option>
                      <option value="Started" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Started') ? 'selected' : ''; ?>>Started</option>
                      <option value="On Hold" <?php echo (isset($_GET['status']) && $_GET['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                      <option value="Completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                      <option value="Active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                      <option value="In Review" <?php echo (isset($_GET['status']) && $_GET['status'] == 'in review') ? 'selected' : ''; ?>>In Review</option>
                    </select>
                    <select name="sort" class="bg-gray-900 text-white border border-gray-700 rounded-lg p-2 focus:outline-none focus:border-red-500">
                      <option value="name" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : ''; ?>>Sort By Name</option>
                      <option value="date" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date') ? 'selected' : ''; ?>>Sort By Date</option>
                      <option value="priority" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'priority') ? 'selected' : ''; ?>>Sort By Priority</option>
                    </select>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg flex items-center shadow-md transform transition-transform hover:scale-105">
                      <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                  </div>
                </form>
            </div>


            <div class="flex flex-wrap gap-2 mb-6">
                <a href="?status=Started" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (isset($_GET['status']) && $_GET['status'] == 'Started') ? 'active' : ''; ?>">
                    <i class="fas fa-clock mr-2"></i> Started
                </a>
                <a href="?status=Completed" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle mr-2"></i>Completed
                </a>
                <a href="?status=On Hold" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (isset($_GET['status']) && $_GET['status'] == 'On Hold') ? 'active' : ''; ?>">
                    <i class="fas fa-spinner mr-2"></i>  On Hold
                </a>
                <a href="?status=active" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (isset($_GET['status']) && $_GET['status'] == 'Active') ? 'active' : ''; ?>">
                    <i class="fas fa-bolt mr-2"></i>  Active
                </a>
                <a href="?status=in review" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (isset($_GET['status']) && $_GET['status'] == 'In Review') ? 'active' : ''; ?>">
                      <i class="fas fa-eye mr-2"></i>  In Review
                </a>

                <a href="project.php" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (!isset($_GET['status']) || $_GET['status'] == '') ? 'active' : ''; ?>">
                    <i class="fas fa-list-ul mr-2"></i> All Projects
                </a>
            </div>
          
            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
              <?php 
              if ($result && $result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                      // Determine status color
                      $statusColor = '';
                      $statusBg = '';
                      switch(strtolower($row['project_status'])) {
                          case 'started':
                              $statusColor = 'green';
                              $progressColor = 'from-green-500 to-green-400';
                              break;
                          case 'on hold':
                              $statusColor = 'yellow';
                              $progressColor = 'from-yellow-500 to-yellow-400';
                              break;
                          case 'completed':
                              $statusColor = 'blue';
                              $progressColor = 'from-blue-500 to-blue-400';
                              break;
                          case 'active':
                              $statusColor = 'orange';
                              $progressColor = 'from-indigo-500 to-indigo-400';
                              break;
                          case 'completed':
                              $statusColor = 'blue';
                              $progressColor = 'from-green-500 to-green-400';
                              break;
                          case 'in review':
                              $statusColor = 'purple';
                              $progressColor = 'from-purple-500 to-purple-400';
                              break;
                          default:
                              $statusColor = 'gray';
                              $progressColor = 'from-gray-500 to-gray-400';
                      }
                      
                      // Generate icon based on category
                      $iconClass = 'fas fa-project-diagram';
                      $iconBg = 'bg-blue-500';
                      switch(strtolower($row['category'] ?? '')) {
                          case 'development':
                              $iconClass = 'fas fa-code';
                              $iconBg = 'bg-blue-500';
                              break;
                          case 'design':
                              $iconClass = 'fas fa-paint-brush';
                              $iconBg = 'bg-purple-500';
                              break;
                          case 'marketing':
                              $iconClass = 'fas fa-chart-line';
                              $iconBg = 'bg-green-500';
                              break;
                          case 'research':
                              $iconClass = 'fas fa-flask';
                              $iconBg = 'bg-yellow-500';
                              break;

                          default:
                              $iconClass = 'fas fa-project-diagram';
                              $iconBg = 'bg-gray-500';
                      }
              ?>
              <div class="bg-gray-800 rounded-xl p-5 card-hover transition-all duration-300 shadow-lg">
                <div class="flex justify-between items-start mb-4">
                  <div class="rounded-lg <?php echo $iconBg; ?> bg-opacity-20 p-3">
                    <i class="<?php echo $iconClass; ?> text-<?php echo str_replace('bg-', '', $iconBg); ?> text-xl"></i>
                  </div>
                  <div class="bg-<?php echo $statusColor; ?>-500 text-xs py-1 px-3 rounded-full">
                    <?php echo htmlspecialchars($row['project_status']); ?>
                  </div>
                </div>
                <div class="mb-2 text-xs text-gray-400"><?php echo htmlspecialchars($row['project_id']); ?></div>
                <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($row['project_name']); ?></h3>
                <p class="text-gray-400 text-sm mb-4"><?php echo htmlspecialchars($row['project_descp']); ?></p>
                
                <div class="mb-3">
                  <div class="flex justify-between items-center mb-1 text-sm">
                    <span>Progress</span>
                    <span><?php echo $row['project_progress']; ?>%</span>
                  </div>
                  <div class="bg-gray-900 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r <?php echo $progressColor; ?> h-2" style="width: <?php echo $row['project_progress']; ?>%"></div>
                  </div>
                </div>
                
                <div class="flex items-center justify-between">
                  <div class="flex -space-x-2">
                    <?php 
                    // Display team members (placeholder for now)
                    echo '<span class="w-8 h-8 pt-2 text-gray-400  text-xs">' . 
                      $row['project_alloc_emp']. 
                          '</span>';
                    ?>
                  </div>
                  <div class="text-xs text-gray-400">
                    Deadline: <?php echo date('M d, Y', strtotime($row['project_end_date'])); ?>
                  </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-700 flex justify-between">
                  <a href="?edit=<?php echo $row['project_id']; ?>" class="text-blue-400 hover:text-blue-300 transform transition-transform hover:scale-105">
                    <i class="fas fa-edit mr-1"></i> Edit
                  </a>
                  <a href="#" class="text-red-400 hover:text-red-300 transform transition-transform hover:scale-105 delete-project" data-id="<?php echo $row['project_id']; ?>">
                    <i class="fas fa-trash-alt mr-1"></i> Delete
                  </a>

                  </div>
            </div>
              <?php 
                  }
              } else {
              ?>
              <div class="col-span-full bg-gray-800 rounded-xl p-6 text-center">
                <i class="fas fa-folder-open text-gray-500 text-5xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">No Projects Found</h3>
                <p class="text-gray-400">You haven't created any projects yet, or none match your filters.</p>
                <button class="mt-4 bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg flex items-center mx-auto" id="addProjectBtnEmpty">
                  <i class="fas fa-plus mr-2"></i> Create Your First Project
                </button>
              </div>
              <?php } ?>
            </div>
            
            <!-- Add Project Modal -->
            <div class="modal" id="addProjectModal">
              <div class="modal-content glass-effect p-6">
                <div class="modal-header flex justify-between items-center">
                  <h2 class="text-xl font-bold text-red-500"><i class="fas fa-plus-circle mr-2"></i>Create New Project</h2>
                  <button class="close-modal text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                
                <form method="POST" action="">
                  <!-- Project ID Display -->
                  <div class="mb-4">
                    <label class="form-label pb-3">Project ID</label>
                    <div class="project-id-display p-2 rounded">
                      <?php echo $new_project_id; ?>
                    </div>
                  </div>
                  
                  <div class="form-section">
                    <div class="mb-4">
                      <label class="form-label">Project Name</label>
                      <input type="text" name="project_name" required class="form-input w-full p-2 rounded focus:outline-none" placeholder="Enter project name">
                    </div>
                    
                    <div class="mb-4">
                      <label class="form-label">Description</label>
                      <textarea name="project_descp" class="form-input w-full p-2 rounded focus:outline-none h-24" placeholder="Enter project description"></textarea>
                    </div>
                    
                    <div class="mb-4">
                      <label class="form-label">Category</label>
                      <select name="category" class="form-input w-full p-2 rounded focus:outline-none">
                        <option value="Development">Development</option>
                        <option value="Design">Design</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Research">Research</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-section">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="mb-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="project_start_date" required class="form-input w-full p-2 rounded focus:outline-none">
                      </div>
                      
                      <div class="mb-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="project_end_date" required class="form-input w-full p-2 rounded focus:outline-none">
                      </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="mb-4">
                        <label class="form-label">Budget ($)</label>
                        <input type="number" name="project_budget" class="form-input w-full p-2 rounded focus:outline-none" placeholder="Budget amount">
                      </div>
                      
                      <div class="mb-4">
                        <label class="form-label">Priority</label>
                        <select name="project_priority" class="form-input w-full p-2 rounded focus:outline-none">
                          <option value="Low">Low</option>
                          <option value="Medium">Medium</option>
                          <option value="High">High</option>
                          <option value="Critical">Critical</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-section">
                    <div class="mb-4">
                      <label class="form-label">Team Members</label>
                      <select name="team_members[]" multiple class="form-input w-full p-2 rounded focus:outline-none h-32">
                        <?php 
                        if ($employees && $employees->num_rows > 0) {
                            while($emp = $employees->fetch_assoc()) {
                                echo '<option value="' . $emp['emp_id'] . '">' . htmlspecialchars($emp['emp_name']) . ' - ' . htmlspecialchars($emp['emp_id']) . '</option>';
                            }
                        } else {
                            echo '<option disabled>No employees available</option>';
                        }
                        ?>
                      </select>
                      <p class="text-sm text-gray-400 mt-1">Hold Ctrl/Cmd to select multiple members</p>
                    </div>
                  </div>

                  <div class="modal-footer flex justify-end">
                    <button type="button" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded mr-2 close-modal">Cancel</button>
                    <button type="submit" name="create_project" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Create Project</button>
                  </div>
                </form>
              </div>
            </div>
            
            <!-- Edit Project Modal -->
            <?php if($edit_project): ?>
            <div class="modal flex" id="editProjectModal">
              <div class="modal-content glass-effect p-6">
                <div class="modal-header flex justify-between items-center">
                  <h2 class="text-xl font-bold text-blue-500"><i class="fas fa-edit mr-2"></i>Edit Project</h2>
                  <a href="project.php" class="close-modal text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                  </a>
                </div>
                
                <form method="POST" action="">
                  <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($edit_project['project_id']); ?>">
                  
                  <!-- Project ID Display -->
                  <div class="mb-4">
                    <label class="form-label">Project ID</label>
                    <div class="project-id-display p-2 rounded">
                      <?php echo htmlspecialchars($edit_project['project_id']); ?>
                    </div>
                  </div>
                  
                  <div class="form-section">
                    <div class="mb-4">
                      <label class="form-label">Project Name</label>
                      <input type="text" name="project_name" required class="form-input w-full p-2 rounded focus:outline-none" value="<?php echo htmlspecialchars($edit_project['project_name']); ?>" placeholder="Enter project name">
                    </div>
                    
                    <div class="mb-4">
                      <label class="form-label">Description</label>
                      <textarea name="project_descp" class="form-input w-full p-2 rounded focus:outline-none h-24" placeholder="Enter project description"><?php echo htmlspecialchars($edit_project['project_descp']); ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                      <label class="form-label">Category</label>
                      <select name="category" class="form-input w-full p-2 rounded focus:outline-none">
                        <option value="Development" <?php echo ($edit_project['category'] == 'Development') ? 'selected' : ''; ?>>Development</option>
                        <option value="Design" <?php echo ($edit_project['category'] == 'Design') ? 'selected' : ''; ?>>Design</option>
                        <option value="Marketing" <?php echo ($edit_project['category'] == 'Marketing') ? 'selected' : ''; ?>>Marketing</option>
                        <option value="Research" <?php echo ($edit_project['category'] == 'Research') ? 'selected' : ''; ?>>Research</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-section">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="mb-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="project_start_date" required class="form-input w-full p-2 rounded focus:outline-none" value="<?php echo htmlspecialchars($edit_project['project_start_date']); ?>">
                      </div>
                      
                      <div class="mb-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="project_end_date" required class="form-input w-full p-2 rounded focus:outline-none" value="<?php echo htmlspecialchars($edit_project['project_end_date']); ?>">
                      </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="mb-4">
                        <label class="form-label">Budget ($)</label>
                        <input type="number" name="project_budget" class="form-input w-full p-2 rounded focus:outline-none" placeholder="Budget amount" value="<?php echo htmlspecialchars($edit_project['project_budget']); ?>">
                      </div>
                      
                      <div class="mb-4">
                        <label class="form-label">Priority</label>
                        <select name="project_priority" class="form-input w-full p-2 rounded focus:outline-none">
                          <option value="Low" <?php echo ($edit_project['project_priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                          <option value="Medium" <?php echo ($edit_project['project_priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                          <option value="High" <?php echo ($edit_project['project_priority'] == 'High') ? 'selected' : ''; ?>>High</option>
                          <option value="Critical" <?php echo ($edit_project['project_priority'] == 'Critical') ? 'selected' : ''; ?>>Critical</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-section">
                    <div class="mb-4">
                      <label class="form-label">Team Members</label>
                      <select name="team_members[]" multiple class="form-input w-full p-2 rounded focus:outline-none h-32">
                        <?php 
                        if ($employees && $employees->num_rows > 0) {
                            // Reset pointer to beginning of result set
                            $employees->data_seek(0);
                            while($emp = $employees->fetch_assoc()) {
                                $selected = in_array($emp['emp_id'], $edit_project['emp_ids_array']) ? 'selected' : '';
                                echo '<option value="' . $emp['emp_id'] . '" ' . $selected . '>' . htmlspecialchars($emp['emp_name']) . ' - ' . htmlspecialchars($emp['emp_id']) . '</option>';
                            }
                        } else {
                            echo '<option disabled>No employees available</option>';
                        }
                        ?>
                      </select>
                      <p class="text-sm text-gray-400 mt-1">Hold Ctrl/Cmd to select multiple members</p>
                    </div>
                  </div>

                  <div class="modal-footer flex justify-end">
                    <a href="project.php" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded mr-2">Cancel</a>
                    <button type="submit" name="update_project" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">Update Project</button>
                  </div>
                </form>
              </div>
            </div>
            <?php endif; ?>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal" id="deleteProjectModal">
              <div class="modal-content glass-effect p-6 max-w-md">
                <div class="modal-header flex justify-between items-center">
                  <h2 class="text-xl font-bold text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete</h2>
                  <button class="close-modal text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                
                <div class="py-4">
                  <p class="text-center">Are you sure you want to delete this project? This action cannot be undone.</p>
                </div>

                <div class="modal-footer flex justify-center">
                  <button type="button" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded mr-2 close-modal">Cancel</button>
                  <a href="#" id="confirmDelete" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Delete Project</a>
                </div>
              </div>
            </div>
        </main>
    </div>

    <script>
      // Modal functionality
      document.addEventListener('DOMContentLoaded', function() {
        // Add project button
        const addProjectBtn = document.getElementById('addProjectBtn');
        const addProjectBtnEmpty = document.getElementById('addProjectBtnEmpty');
        const addProjectModal = document.getElementById('addProjectModal');
        
        if (addProjectBtn) {
          addProjectBtn.addEventListener('click', function() {
            addProjectModal.style.display = 'flex';
            setTimeout(() => {
              addProjectModal.classList.add('flex');
            }, 10);
          });
        }
        
        if (addProjectBtnEmpty) {
          addProjectBtnEmpty.addEventListener('click', function() {
            addProjectModal.style.display = 'flex';
            setTimeout(() => {
              addProjectModal.classList.add('flex');
            }, 10);
          });
        }
        
        // Delete project buttons
        const deleteButtons = document.querySelectorAll('.delete-project');
        const deleteProjectModal = document.getElementById('deleteProjectModal');
        const confirmDelete = document.getElementById('confirmDelete');
        
        deleteButtons.forEach(button => {
          button.addEventListener('click', function(e) {
            e.preventDefault();
            const projectId = this.getAttribute('data-id');
            deleteProjectModal.style.display = 'flex';
            setTimeout(() => {
              deleteProjectModal.classList.add('flex');
            }, 10);
            confirmDelete.href = '?delete=' + projectId;
          });
        });
        
        // Close modals
        const closeModalButtons = document.querySelectorAll('.close-modal');
        
        closeModalButtons.forEach(button => {
          button.addEventListener('click', function() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
              modal.classList.remove('flex');
              setTimeout(() => {
                modal.style.display = 'none';
              }, 300);
            });
          });
        });
        
        // Also close if clicked outside the modal
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
          modal.addEventListener('click', function(e) {
            if (e.target === this) {
              modal.classList.remove('flex');
              setTimeout(() => {
                modal.style.display = 'none';
              }, 300);
            }
          });
        });
      });

      const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('edit')) {
  const editProjectModal = document.getElementById('editProjectModal');
  if (editProjectModal) {
    editProjectModal.style.display = 'flex';
    setTimeout(() => {
      editProjectModal.classList.add('flex');
    }, 10);
  }
}
    </script>
</body>
</html>
<?php
// Start session management


session_start();


// Check if manager is logged in, redirect to login if not
if (!isset($_SESSION['mag_id'])) {
  header("Location: ../leaders/manager-Login.php");
  exit();
}


// Get manager data
$mag_id = $_SESSION['mag_id'];
$mag_cmp_id = $_SESSION['mag_cmp_id']; // Assuming company ID is stored in session
$ManagerProfile = $_SESSION['mag_profile'] ;

include "connection.php";

// Generate unique bug ID
function generateBugID($con) {
    $uniqueID = "BUG" . time() . rand(100, 999);
    
    // Check if this ID already exists
    $sql = "SELECT bug_id FROM bug WHERE bug_id = '$uniqueID'";
    $result = $con->query($sql);
    
    if ($result->num_rows > 0) {
        // If exists, generate a new one
        return generateBugID($con);
    }
    
    return $uniqueID;
}

// Handle bug creation with validation and sanitization
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_bug'])) {
  // Initialize error array
  $errors = array();
  
  // Validate and sanitize bug title
  if (empty($_POST['bug_title'])) {
      $errors[] = "Bug title is required";
  } else {
      $bug_title = filter_var($_POST['bug_title'], FILTER_SANITIZE_STRING);
      // Additional validation for title length
      if (strlen($bug_title) < 5 || strlen($bug_title) > 100) {
          $errors[] = "Bug title must be between 5 and 100 characters";
      }
  }
  
  // Validate and sanitize bug description
  if (empty($_POST['bug_descp'])) {
      $errors[] = "Bug description is required";
  } else {
      $bug_descp = filter_var($_POST['bug_descp'], FILTER_SANITIZE_STRING);
      // Additional validation for description length
      if (strlen($bug_descp) < 10) {
          $errors[] = "Bug description must be at least 10 characters";
      }
  }
  
  // Validate bug severity
  if (empty($_POST['bug_severity'])) {
      $errors[] = "Bug severity is required";
  } else {
      $bug_severity = $con->real_escape_string($_POST['bug_severity']);
      // Validate that severity is one of the allowed values
      $allowed_severities = array('Critical', 'High', 'Medium', 'Low');
      if (!in_array($bug_severity, $allowed_severities)) {
          $errors[] = "Invalid bug severity selected";
      }
  }
  
  // Validate assigned employee
  if (empty($_POST['bug_assigned_to'])) {
      $errors[] = "Bug must be assigned to an employee";
  } else {
      $bug_assigned_to = $con->real_escape_string($_POST['bug_assigned_to']);
      // Check if the employee exists and belongs to this manager
      $emp_check_sql = "SELECT emp_id FROM employee WHERE emp_id = '$bug_assigned_to' AND mag_id = '$mag_id'";
      $emp_check_result = $con->query($emp_check_sql);
      if ($emp_check_result->num_rows == 0) {
          $errors[] = "Invalid employee selected";
      }
  }
  
  // Validate reported by
  if (empty($_POST['bug_reported_by'])) {
      $errors[] = "Reported by field is required";
  } else {
      $bug_reported_by = $con->real_escape_string($_POST['bug_reported_by']);
  }
  
  // If no errors, proceed with insertion
  if (empty($errors)) {
      // Generate unique bug ID
      $bug_id = generateBugID($con);
      
      // Initial bug settings
      $bug_progress = 0; // Initial progress
      $bug_status = "Pending"; // Initial status
      $bug_created_date = date('Y-m-d H:i:s'); // Current date as created date
      
      // Insert bug data
      $sql = "INSERT INTO bug (
          bug_id, bug_title, bug_descp, bug_status, bug_progress, 
          bug_severity, bug_assigned_to, bug_reported_by, bug_created_date,
          bug_alloc_mag, bug_alloc_cmp, bug_alloc_emp
      ) VALUES (
          '$bug_id', '$bug_title', '$bug_descp', '$bug_status', $bug_progress,
          '$bug_severity', '$bug_assigned_to', '$bug_reported_by', '$bug_created_date',
          '$mag_id', '$mag_cmp_id', '$bug_assigned_to'
      )";
      
      if ($con->query($sql) === TRUE) {
          $success_message = "Bug created successfully!";
      } else {
          $error_message = "Error: " . $sql . "<br>" . $con->error;
      }
  } else {
      // If there are errors, join them into a single error message
      $error_message = "Please fix the following errors:<br>" . implode("<br>", $errors);
  }
}

// Handle bug deletion
if (isset($_GET['delete'])) {
    $bug_id = $con->real_escape_string($_GET['delete']);
    
    // Check if manager owns this bug
    $check_sql = "SELECT * FROM bug WHERE bug_id = '$bug_id' AND bug_alloc_mag = '$mag_id'";
    $check_result = $con->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $delete_sql = "DELETE FROM bug WHERE bug_id = '$bug_id'";
        if ($con->query($delete_sql) === TRUE) {
            $success_message = "Bug deleted successfully!";
        } else {
            $error_message = "Error deleting bug: " . $con->error;
        }
    } else {
        $error_message = "You don't have permission to delete this bug.";
    }
}

// Handle bug update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_bug'])) {
    $bug_id = $con->real_escape_string($_POST['bug_id']);
    $bug_title = $con->real_escape_string($_POST['bug_title']);
    $bug_descp = $con->real_escape_string($_POST['bug_descp']);
    $bug_status = $con->real_escape_string($_POST['bug_status']);
    $bug_progress = $con->real_escape_string($_POST['bug_progress']);
    $bug_severity = $con->real_escape_string($_POST['bug_severity']);
    $bug_assigned_to = $con->real_escape_string($_POST['bug_assigned_to']);
    $bug_reported_by = $con->real_escape_string($_POST['bug_reported_by']);
    
    // Update bug data
    $sql = "UPDATE bug SET 
        bug_title = '$bug_title', 
        bug_descp = '$bug_descp', 
        bug_status = '$bug_status', 
        bug_progress = '$bug_progress', 
        bug_severity = '$bug_severity', 
        bug_assigned_to = '$bug_assigned_to', 
        bug_reported_by = '$bug_reported_by',
        bug_alloc_emp = '$bug_assigned_to'
        WHERE bug_id = '$bug_id' AND bug_alloc_mag = '$mag_id'";
    
    if ($con->query($sql) === TRUE) {
        $success_message = "Bug updated successfully!";
        // Redirect to remove the edit parameter from URL
        header("Location: bug.php");
        exit();
    } else {
        $error_message = "Error: " . $sql . "<br>" . $con->error;
    }
}

// Get bugs for current manager
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $con->real_escape_string($_GET['status']) : '';
$sort_by = isset($_GET['sort']) ? $con->real_escape_string($_GET['sort']) : 'bug_created_date';

// Get employees under this manager
$emp_query = "SELECT e.* FROM employee e 
              WHERE e.mag_id = '$mag_id' AND (e.onLeave = 0 OR e.onLeave IS NULL)";
$employees = $con->query($emp_query);

// Build query based on filters
$query = "SELECT b.*, e.emp_name FROM bug b 
          LEFT JOIN employee e ON b.bug_assigned_to = e.emp_id
          WHERE b.bug_alloc_mag = '$mag_id'";

// Add search filter if provided
if (!empty($search)) {
    $query .= " AND (b.bug_title LIKE '%$search%' OR b.bug_descp LIKE '%$search%' OR e.emp_name LIKE '%$search%')";
}

// Add status filter if provided
if (!empty($status_filter)) {
    $query .= " AND b.bug_status = '$status_filter'";
}

// Add sorting
switch ($sort_by) {
    case 'title':
        $query .= " ORDER BY b.bug_title";
        break;
    case 'severity':
        $query .= " ORDER BY 
            CASE b.bug_severity 
                WHEN 'Critical' THEN 1 
                WHEN 'High' THEN 2 
                WHEN 'Medium' THEN 3 
                WHEN 'Low' THEN 4 
                ELSE 5 
            END";
        break;
    case 'status':
        $query .= " ORDER BY b.bug_status";
        break;
    case 'date':
    default:
        $query .= " ORDER BY b.bug_created_date DESC";
}

$result = $con->query($query);

// Get bug details for edit modal if requested
$edit_bug = null;
if (isset($_GET['edit'])) {
    $edit_id = $con->real_escape_string($_GET['edit']);
    $edit_query = "SELECT * FROM bug WHERE bug_id = '$edit_id' AND bug_alloc_mag = '$mag_id'";
    $edit_result = $con->query($edit_query);
    if ($edit_result->num_rows > 0) {
        $edit_bug = $edit_result->fetch_assoc();
    }
}

// Generate a new bug ID for the Add Bug form
$new_bug_id = generateBugID($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Bug Tracker</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <link rel="stylesheet" href="../src/output.css">
  <script src="dashboard.js" defer></script>

  <style>
    

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

    .bug-id-display {
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

  

    /* Badge effects for severity */
    .severity-badge {
      position: relative;
      overflow: hidden;
    }

    .severity-critical {
      background: rgba(220, 38, 38, 0.2);
      color: #ef4444;
    }

    .severity-high {
      background: rgba(249, 115, 22, 0.2);
      color: #f97316;
    }

    .severity-medium {
      background: rgba(245, 158, 11, 0.2);
      color: #f59e0b;
    }

    .severity-low {
      background: rgba(34, 197, 94, 0.2);
      color: #22c55e;
    }

    /* Status badges */
    .status-pending {
      background: rgba(245, 158, 11, 0.2);
      color: #f59e0b;
    }

    .status-progress {
      background: rgba(79, 70, 229, 0.2);
      color: #6366f1;
    }

    .status-resolved {
      background: rgba(34, 197, 94, 0.2);
      color: #22c55e;
    }

    .status-closed {
      background: rgba(107, 114, 128, 0.2);
      color: #9ca3af;
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

    /* Filter buttons with active states */
    .filter-btn {
      transition: all 0.3s ease;
    }

    .filter-btn.active {
      background-color: #ef4444;
      color: white;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5);
    }

    /* Bug card animations */
    @keyframes bugEntrance {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .bug-card {
      animation: bugEntrance 0.3s ease-out forwards;
    }

    .bug-card:nth-child(1) { animation-delay: 0.05s; }
    .bug-card:nth-child(2) { animation-delay: 0.1s; }
    .bug-card:nth-child(3) { animation-delay: 0.15s; }
    .bug-card:nth-child(4) { animation-delay: 0.2s; }
    .bug-card:nth-child(5) { animation-delay: 0.25s; }
    .bug-card:nth-child(6) { animation-delay: 0.3s; }
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
                  <a href="dashboard.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Dashboard">
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
                  <a href="bug.php" class="sidebar-link flex active items-center p-3 rounded text-gray-300 hover:text-white" data-title="Bugs">
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

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <!-- Left Side: Heading -->
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl md:text-3xl font-bold">Bug Tracker</h1>
                    <p class="text-gray-400" id="currentDateTime">Loading date and time.......</p>
                    <p class="text-gray-400">Track and manage bugs across all your projects</p>
                </div>

                <!-- Right Side: Button + Profile -->
                <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                    <!-- Add Bug Button -->
                    <button class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg flex items-center shadow-lg transform transition-transform hover:scale-105" id="addBugBtn">
                        <i class="fas fa-plus mr-2"></i> Report New Bug
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


            
            <!-- Bug Filter Row -->
            <div class="bg-gray-800 p-4 rounded-xl mb-6 shadow-lg">
                <form method="GET" class="flex flex-col md:flex-row gap-4">
                  <div class="flex-1">
                    <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Search bugs by title, description or assignee..." class="w-full bg-gray-900 text-white border border-gray-700 rounded-lg p-2 focus:outline-none focus:border-red-500">
                  </div>
                  <div class="flex gap-2 flex-wrap md:flex-nowrap">
                    <select name="status" class="bg-gray-900 text-white border border-gray-700 rounded-lg p-2 focus:outline-none focus:border-red-500">
                      <option value="">All Status</option>
                      <option value="Pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                      <option value="In Progress" <?php echo (isset($_GET['status']) && $_GET['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                      <option value="Resolved" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                      <option value="Closed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
                    </select>
                    <select name="sort" class="bg-gray-900 text-white border border-gray-700 rounded-lg p-2 focus:outline-none focus:border-red-500">
                      <option value="date" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date') ? 'selected' : ''; ?>>Sort By Date</option>
                      <option value="title" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'title') ? 'selected' : ''; ?>>Sort By Title</option>
                      <option value="severity" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'severity') ? 'selected' : ''; ?>>Sort By Severity</option>
                      <option value="status" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'status') ? 'selected' : ''; ?>>Sort By Status</option>
                    </select>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 z-0 px-4 py-2 rounded-lg flex items-center shadow-md transform transition-transform hover:scale-105">
                      <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                  </div>
                </form>
            </div>

            <!-- Status Filter Buttons -->
            <div class="flex flex-wrap gap-2 mb-6">
                <a href="?status=Pending" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'active' : ''; ?>">
                    <i class="fas fa-clock mr-2"></i> Pending
                </a>
                <a href="?status=In Progress" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (isset($_GET['status']) && $_GET['status'] == 'In Progress') ? 'active' : ''; ?>">
                    <i class="fas fa-spinner mr-2"></i> In Progress
                </a>
                <a href="?status=Resolved" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (isset($_GET['status']) && $_GET['status'] == 'Resolved') ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle mr-2"></i> Resolved
                </a>
                <a href="?status=Closed" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (isset($_GET['status']) && $_GET['status'] == 'Closed') ? 'active' : ''; ?>">
                    <i class="fas fa-archive mr-2"></i> Closed
                </a>
                <a href="bug.php" class="filter-btn px-4 py-2 bg-gray-800 rounded-lg hover:bg-red-600 transition-all <?php echo (!isset($_GET['status']) || $_GET['status'] == '') ? 'active' : ''; ?>">
                    <i class="fas fa-list-ul mr-2"></i> All Bugs
                </a>
            </div>
          
            <!-- Bugs Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
              <?php 
              if ($result && $result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                      // Determine status class
                      $statusClass = '';
                      switch(strtolower($row['bug_status'])) {
                          case 'pending':
                              $statusClass = 'status-pending';
                              break;
                          case 'in progress':
                              $statusClass = 'status-progress';
                              break;
                          case 'resolved':
                              $statusClass = 'status-resolved';
                              break;
                          case 'closed':
                              $statusClass = 'status-closed';
                              break;
                          default:
                              $statusClass = 'status-pending';
                      }
                      
                      // Determine severity class
                      $severityClass = '';
                      switch(strtolower($row['bug_severity'])) {
                          case 'critical':
                              $severityClass = 'severity-critical';
                              $severityIcon = 'fa-radiation';
                              break;
                          case 'high':
                              $severityClass = 'severity-high';
                              $severityIcon = 'fa-exclamation-triangle';
                              break;
                          case 'medium':
                              $severityClass = 'severity-medium';
                              $severityIcon = 'fa-exclamation-circle';
                              break;
                          case 'low':
                              $severityClass = 'severity-low';
                              $severityIcon = 'fa-info-circle';
                              break;
                          default:
                              $severityClass = 'severity-medium';
                              $severityIcon = 'fa-exclamation-circle';
                      }
              ?>
              <div class="bg-gray-800 rounded-xl p-5 card-hover transition-all duration-300 shadow-lg bug-card">
                <div class="flex justify-between items-start mb-4">
                  <div class="flex flex-col">
                    <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($row['bug_title']); ?></h3>
                    <span class="text-xs text-gray-400"><?php echo htmlspecialchars($row['bug_id']); ?></span>
                  </div>
                  <div class="flex space-x-2">
                    <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $statusClass; ?>">
                      <?php echo htmlspecialchars($row['bug_status']); ?>
                    </span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $severityClass; ?>">
                      <i class="fas <?php echo $severityIcon; ?> mr-1"></i>
                      <?php echo htmlspecialchars($row['bug_severity']); ?>
                    </span>
                  </div>
                </div>
                
                <p class="text-gray-300 text-sm mb-4 line-clamp-3">
                  <?php echo htmlspecialchars($row['bug_descp']); ?>
                </p>
                
                <div class="mb-4">
                  <div class="w-full bg-gray-700 rounded-full h-2">
                    <div class="bg-red-500 h-2 rounded-full" style="width: <?php echo intval($row['bug_progress']); ?>%"></div>
                  </div>
                  <div class="text-xs text-gray-400 mt-1 text-right"><?php echo $row['bug_progress']; ?>% Complete</div>
                </div>
                
                <div class="flex justify-between items-center text-xs text-gray-400 mb-4">
                  <span>
                    <i class="fas fa-user mr-1"></i> Assigned to: 
                    <span class="font-medium text-white"><?php echo htmlspecialchars($row['emp_name'] ?? 'Unassigned'); ?></span>
                  </span>
                  <span>
                    <i class="far fa-calendar-alt mr-1"></i> 
                    <?php echo date('M d, Y', strtotime($row['bug_created_date'])); ?>
                  </span>
                </div>
                
                <div class="flex justify-between pt-3 border-t border-gray-700">
                  <a href="?edit=<?php echo $row['bug_id']; ?>" class="text-blue-400 hover:text-blue-300">
                    <i class="fas fa-edit mr-1"></i> Edit
                  </a>
                  <a href="javascript:void(0)" onclick="confirmDelete('<?php echo $row['bug_id']; ?>')" class="text-red-400 hover:text-red-300">
                    <i class="fas fa-trash-alt mr-1"></i> Delete
                  </a>
                </div>
              </div>
              <?php
                  }
              } else {
              ?>
              <div class="col-span-1 md:col-span-2 lg:col-span-3">
                <div class="bg-gray-800 rounded-xl p-6 text-center">
                  <i class="fas fa-search text-5xl text-gray-600 mb-4"></i>
                  <h3 class="text-xl font-semibold mb-2">No bugs found</h3>
                  <p class="text-gray-400">There are no bugs matching your search criteria.</p>
                  <button class="mt-4 bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg" id="addBugBtnEmpty">
                    <i class="fas fa-plus mr-2"></i> Report New Bug
                  </button>
                </div>
              </div>
              <?php
              }
              ?>
            </div>
            
            <!-- Add Bug Modal -->
            <div class="modal" id="addBugModal">
              <div class="modal-content glass-effect p-6">
              <div class="modal-header flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center">
                  <i class="fas fa-bug text-red-500 mr-2"></i> Report New Bug
                </h3>
                <button type="button" class="close-modal text-gray-400 hover:text-white">
                  <i class="fas fa-times"></i>
                </button>
              </div>
                
                <form method="POST">
                  <input type="hidden" name="create_bug" value="1">
                  
                  <div class="form-section">
                    <div class="mb-4">
                      <label class="form-label text-gray-300">Bug ID</label>
                      <div class="bug-id-display p-2 rounded-lg"><?php echo $new_bug_id; ?></div>
                    </div>
                    
                    <div class="mb-4">
                      <label for="bug_title" class="form-label text-gray-300">Bug Title*</label>
                      <input type="text" name="bug_title" id="bug_title" class="form-input w-full p-2 rounded-lg" required>
                    </div>
                    
                    <div class="mb-4">
                      <label for="bug_descp" class="form-label text-gray-300">Description*</label>
                      <textarea name="bug_descp" id="bug_descp" rows="4" class="form-input w-full p-2 rounded-lg" required></textarea>
                    </div>
                  </div>
                  
                  <div class="form-section">
                    <div class="mb-4">
                      <label for="bug_severity" class="form-label text-gray-300">Severity*</label>
                      <select name="bug_severity" id="bug_severity" class="form-input w-full p-2 rounded-lg" required>
                        <option value="">Select Severity</option>
                        <option value="Critical">Critical</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                      </select>
                    </div>
                    
                    <div class="mb-4">
                      <label for="bug_assigned_to" class="form-label text-gray-300">Assign To*</label>
                      <select name="bug_assigned_to" id="bug_assigned_to" class="form-input w-full p-2 rounded-lg" required>
                        <option value="">Select Employee</option>
                        <?php
                        if ($employees && $employees->num_rows > 0) {
                            while($emp = $employees->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($emp['emp_id']) . "'>" . htmlspecialchars($emp['emp_name']) . "</option>";
                            }
                        }
                        ?>
                      </select>
                    </div>
                    
                    <div class="mb-4">
                      <label for="bug_reported_by" class="form-label text-gray-300">Reported By*</label>
                      <input type="text" name="bug_reported_by" id="bug_reported_by" class="form-input w-full p-2 rounded-lg" value="<?php echo htmlspecialchars($_SESSION['mag_id']); ?>" required>
                    </div>
                  </div>
                  
                  <div class="modal-footer flex justify-end space-x-3">
                    <button type="button" class="close-modal bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg">
                      Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg">
                      <i class="fas fa-save mr-2"></i> Create Bug
                    </button>
                  </div>
                </form>
              </div>
            </div>
            
            <!-- Edit Bug Modal -->
            <?php if ($edit_bug): ?>
            <div class="modal" id="editBugModal" style="display: flex;">
              <div class="modal-content glass-effect p-6">
              <div class="modal-header flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center">
                  <i class="fas fa-edit text-blue-500 mr-2"></i> Edit Bug
                </h3>
                <a href="bug.php" class="close-modal text-gray-400 hover:text-white">
                  <i class="fas fa-times"></i>
                </a>
              </div>
                
                <form method="POST">
                  <input type="hidden" name="update_bug" value="1">
                  <input type="hidden" name="bug_id" value="<?php echo htmlspecialchars($edit_bug['bug_id']); ?>">
                  
                  <div class="form-section">
                    <div class="mb-4">
                      <label class="form-label text-gray-300">Bug ID</label>
                      <div class="bug-id-display p-2 rounded-lg"><?php echo htmlspecialchars($edit_bug['bug_id']); ?></div>
                    </div>
                    
                    <div class="mb-4">
                      <label for="edit_bug_title" class="form-label text-gray-300">Bug Title*</label>
                      <input type="text" name="bug_title" id="edit_bug_title" value="<?php echo htmlspecialchars($edit_bug['bug_title']); ?>" class="form-input w-full p-2 rounded-lg" required>
                    </div>
                    
                    <div class="mb-4">
                      <label for="edit_bug_descp" class="form-label text-gray-300">Description*</label>
                      <textarea name="bug_descp" id="edit_bug_descp" rows="4" class="form-input w-full p-2 rounded-lg" required><?php echo htmlspecialchars($edit_bug['bug_descp']); ?></textarea>
                    </div>
                  </div>
                  
                  <div class="form-section">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="mb-4">
                        <label for="edit_bug_status" class="form-label text-gray-300">Status*</label>
                        <select name="bug_status" id="edit_bug_status" class="form-input w-full p-2 rounded-lg" required>
                          <option value="Pending" <?php echo ($edit_bug['bug_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                          <option value="In Progress" <?php echo ($edit_bug['bug_status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                          <option value="Resolved" <?php echo ($edit_bug['bug_status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                          <option value="Closed" <?php echo ($edit_bug['bug_status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
                        </select>
                      </div>
                      
                      <div class="mb-4">
                        <label for="edit_bug_progress" class="form-label text-gray-300">Progress*</label>
                        <input type="number" name="bug_progress" id="edit_bug_progress" value="<?php echo htmlspecialchars($edit_bug['bug_progress']); ?>" min="0" max="100" class="form-input w-full p-2 rounded-lg" required>
                        <div class="mt-2 w-full bg-gray-700 rounded-full h-2">
                          <div id="progress-bar" class="bg-red-500 h-2 rounded-full" style="width: <?php echo intval($edit_bug['bug_progress']); ?>%"></div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="mb-4">
                      <label for="edit_bug_severity" class="form-label text-gray-300">Severity*</label>
                      <select name="bug_severity" id="edit_bug_severity" class="form-input w-full p-2 rounded-lg" required>
                        <option value="Critical" <?php echo ($edit_bug['bug_severity'] == 'Critical') ? 'selected' : ''; ?>>Critical</option>
                        <option value="High" <?php echo ($edit_bug['bug_severity'] == 'High') ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo ($edit_bug['bug_severity'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="Low" <?php echo ($edit_bug['bug_severity'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                      </select>
                    </div>
                    
                    <div class="mb-4">
                      <label for="edit_bug_assigned_to" class="form-label text-gray-300">Assign To*</label>
                      <select name="bug_assigned_to" id="edit_bug_assigned_to" class="form-input w-full p-2 rounded-lg" required>
                        <?php
                        // Reset the employees result pointer
                        if ($employees) {
                            $employees->data_seek(0);
                            while($emp = $employees->fetch_assoc()) {
                                $selected = ($edit_bug['bug_assigned_to'] == $emp['emp_id']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($emp['emp_id']) . "' $selected>" . htmlspecialchars($emp['emp_name']) . "</option>";
                            }
                        }
                        ?>
                      </select>
                    </div>
                    
                    <div class="mb-4">
                      <label for="edit_bug_reported_by" class="form-label text-gray-300">Reported By*</label>
                      <input type="text" name="bug_reported_by" id="edit_bug_reported_by" value="<?php echo htmlspecialchars($edit_bug['bug_reported_by']); ?>" class="form-input w-full p-2 rounded-lg" required>
                    </div>
                  </div>
                  
                  <div class="modal-footer flex justify-end space-x-3">
                    <a href="bug.php" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg">
                      Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg">
                      <i class="fas fa-save mr-2"></i> Update Bug
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <?php endif; ?>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal" id="deleteModal">
              <div class="modal-content glass-effect p-6 max-w-md">
                <div class="text-center mb-6">
                  <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-600 text-white text-3xl mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white mb-2">Confirm Deletion</h3>
                  <p class="text-gray-300">Are you sure you want to delete this bug? This action cannot be undone.</p>
                </div>
                
                <div class="flex justify-center space-x-4">
                  <button id="cancelDelete" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg">
                    Cancel
                  </button>
                  <a href="#" id="confirmDeleteBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg">
                    <i class="fas fa-trash-alt mr-2"></i> Delete
                  </a>
                </div>
              </div>
            </div>
        </main>
    </div>
    
    <script>
  // Global function for delete confirmation
  function confirmDelete(bugId) {
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    // Show the modal
    deleteModal.style.display = 'flex';
    setTimeout(() => {
      deleteModal.classList.add('flex');
    }, 10);
    
    // Set the delete link
    confirmDeleteBtn.href = '?delete=' + bugId;
  }

  // Modal functionality
  document.addEventListener('DOMContentLoaded', function() {
    const addBugBtn = document.getElementById('addBugBtn');
    const addBugBtnEmpty = document.getElementById('addBugBtnEmpty');
    const addBugModal = document.getElementById('addBugModal');
    const deleteModal = document.getElementById('deleteModal');
    const closeModalButtons = document.querySelectorAll('.close-modal');
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    const closeSidebar = document.querySelector('.close-sidebar');
    const progressInput = document.getElementById('edit_bug_progress');
    const progressBar = document.getElementById('progress-bar');
    
    // Open Add Bug Modal
    if (addBugBtn) {
      addBugBtn.addEventListener('click', function() {
        addBugModal.style.display = 'flex';
        setTimeout(() => {
          addBugModal.classList.add('flex');
        }, 10);
      });
    }
    
    // Open Add Bug Modal from empty state button
    if (addBugBtnEmpty) {
      addBugBtnEmpty.addEventListener('click', function() {
        addBugModal.style.display = 'flex';
        setTimeout(() => {
          addBugModal.classList.add('flex');
        }, 10);
      });
    }
    
    // Close modals
    closeModalButtons.forEach(button => {
      button.addEventListener('click', function() {
        const modal = this.closest('.modal');
        modal.classList.remove('flex');
        setTimeout(() => {
          modal.style.display = 'none';
        }, 300);
      });
    });
    
    // Close modals when clicking outside
    [addBugModal, deleteModal].forEach(modal => {
      if (modal) {
        modal.addEventListener('click', function(e) {
          if (e.target === this) {
            modal.classList.remove('flex');
            setTimeout(() => {
              modal.style.display = 'none';
            }, 300);
          }
        });
      }
    });
    
    // Handle cancel button in delete modal
    const cancelDelete = document.getElementById('cancelDelete');
    if (cancelDelete) {
      cancelDelete.addEventListener('click', function() {
        deleteModal.classList.remove('flex');
        setTimeout(() => {
          deleteModal.style.display = 'none';
        }, 300);
      });
    }
    
    // Update progress bar in real-time if editing a bug
    if (progressInput && progressBar) {
      progressInput.addEventListener('input', function() {
        progressBar.style.width = this.value + '%';
      });
    }
  });
</script>
</body>
</html>
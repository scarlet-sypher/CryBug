<?php
// Start session
session_start();

// Check if user is logged in as manager
if (!isset($_SESSION['mag_id'])) {
    header("Location: login.php");
    exit();
}

include "connection.php" ;

// Get current manager ID from session
$current_manager_id = $_SESSION['mag_id'];
$ManagerProfile = $_SESSION['mag_profile'] ;

// Function to generate random employee ID
function generateEmployeeId() {
    $prefix = "CRYEMP";
    $random = mt_rand(10000000, 99999999);
    return $prefix . $random;
}

// Handle Add Employee form submission
if (isset($_POST['add_employee'])) {
  $errors = [];  // Array to store validation errors
  
  // Sanitize and validate employee details
  $emp_id = generateEmployeeId();
  
  // Validate full name (letters, spaces, and hyphens only)
  if (empty($_POST['fullName'])) {
      $errors[] = "Full name is required";
  } else {
      $emp_name = trim(filter_var($_POST['fullName'], FILTER_SANITIZE_STRING));
      if (!preg_match("/^[a-zA-Z \\-']+$/", $emp_name)) {
          $errors[] = "Name can only contain letters, spaces, hyphens and apostrophes";
      }
  }
  
  // Validate email
  if (empty($_POST['email'])) {
      $errors[] = "Email is required";
  } else {
      $emp_mail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
      if (!filter_var($emp_mail, FILTER_VALIDATE_EMAIL)) {
          $errors[] = "Please enter a valid email address";
      }
      
      // Check if email already exists in database
      $email_check_query = "SELECT emp_mail FROM employee WHERE emp_mail = '$emp_mail'";
      $email_result = $con->query($email_check_query);
      if ($email_result->num_rows > 0) {
          $errors[] = "Email already exists in the system";
      }
  }
  
  // Validate phone number
  if (empty($_POST['phone'])) {
      $errors[] = "Phone number is required";
  } else {
      $emp_phone = preg_replace('/[^0-9]/', '', $_POST['phone']); // Remove non-numeric characters
      if (strlen($emp_phone) < 10 || strlen($emp_phone) > 15) {
          $errors[] = "Please enter a valid phone number (10-15 digits)";
      }
  }
  
  // Validate password
  if (empty($_POST['password'])) {
      $errors[] = "Password is required";
  } else {
      if (strlen($_POST['password']) < 8) {
          $errors[] = "Password must be at least 8 characters long";
      } else {
          $emp_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
      }
  }
  
  // Validate gender
  if (empty($_POST['gender'])) {
      $errors[] = "Gender is required";
  } else {
      $emp_gender = htmlspecialchars($_POST['gender']);
      if (!in_array($emp_gender, ['Male', 'Female', 'Other'])) {
          $errors[] = "Invalid gender selection";
      }
  }
  
  // Validate role
  if (empty($_POST['role'])) {
      $errors[] = "Role is required";
  } else {
      $emp_role = htmlspecialchars($_POST['role']);
      $valid_roles = ['Frontend Dev', 'Backend Dev', 'QA Tester', 'Designer', 'Project Manager'];
      if (!in_array($emp_role, $valid_roles)) {
          $errors[] = "Invalid role selection";
      }
  }
  
  // Validate department
  if (empty($_POST['department'])) {
      $errors[] = "Department is required";
  } else {
      $emp_dept = htmlspecialchars($_POST['department']);
      $valid_departments = ['Development', 'Testing', 'Design', 'Management', 'Support'];
      if (!in_array($emp_dept, $valid_departments)) {
          $errors[] = "Invalid department selection";
      }
  }
  
  // Validate experience
  if (!isset($_POST['experience']) || $_POST['experience'] === '') {
      $errors[] = "Years of experience is required";
  } else {
      $emp_exp = filter_var($_POST['experience'], FILTER_VALIDATE_INT);
      if ($emp_exp === false || $emp_exp < 0 || $emp_exp > 50) {
          $errors[] = "Experience must be between 0 and 50 years";
      }
  }
  
  // Validate salary
  if (!isset($_POST['salary']) || $_POST['salary'] === '') {
      $errors[] = "Salary is required";
  } else {
      $salary = filter_var($_POST['salary'], FILTER_VALIDATE_FLOAT);
      if ($salary === false || $salary <= 0) {
          $errors[] = "Please enter a valid salary amount";
      }
  }
  
  // Validate join date
  if (empty($_POST['startDate'])) {
    $errors[] = "Start date is required";
} else {
    $emp_join_date_raw = trim($_POST['startDate']);
    
    // Validate format first: YYYY-MM-DD (basic)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $emp_join_date_raw)) {
        $errors[] = "Invalid date format. Please use YYYY-MM-DD.";
    } else {
        $date_parts = explode('-', $emp_join_date_raw);

        if (!checkdate((int)$date_parts[1], (int)$date_parts[2], (int)$date_parts[0])) {
            $errors[] = "Please enter a valid date.";
        } else {
            $emp_join_date = htmlspecialchars($emp_join_date_raw);
        }
    }
}

  
  // Skills rating validation and sanitization
  $webD = isset($_POST['webD']) ? filter_var($_POST['webD'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 10]]) : 0;
  $auto = isset($_POST['auto']) ? filter_var($_POST['auto'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 10]]) : 0;
  $design = isset($_POST['design']) ? filter_var($_POST['design'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 10]]) : 0;
  $verbal = isset($_POST['verbal']) ? filter_var($_POST['verbal'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 10]]) : 0;
  
  // Default values for skills if validation fails
  if ($webD === false) $webD = 0;
  if ($auto === false) $auto = 0;
  if ($design === false) $design = 0;
  if ($verbal === false) $verbal = 0;
  
  $mag_id = $current_manager_id;
  $emp_profile = "default_profile.jpg"; // Default value
  
  // Profile picture handling - keeping this part unchanged as requested
  $target_path = "";
  if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
      $pic_tmp_name = $_FILES['profile_picture']['tmp_name'];
      $pic_name = basename($_FILES['profile_picture']['name']);
      $upload_dir = "../uploads/employee_images/";

      if (!file_exists($upload_dir)) {
          mkdir($upload_dir, 0777, true);
      }

      $target_path = $upload_dir . $pic_name;
      move_uploaded_file($pic_tmp_name, $target_path);
  }

  // If no validation errors, proceed with database insertion
  if (empty($errors)) {
      // Insert new employee
      $sql = "INSERT INTO employee (emp_id, emp_name, emp_mail, emp_phone, emp_password, 
              emp_gender, emp_profile, emp_role, emp_dept, emp_exp, webD, auto, design, 
              verbal, mag_id, onLeave, emp_join_date, salary) 
              VALUES ('$emp_id', '$emp_name', '$emp_mail', '$emp_phone', '$emp_password', 
              '$emp_gender', '$target_path', '$emp_role', '$emp_dept', '$emp_exp', $webD, $auto, $design, 
              $verbal, '$mag_id', 0, '$emp_join_date', $salary)";
              
      if ($con->query($sql) === TRUE) {
          // Add initial leave record for the employee
          $leave_sql = "INSERT INTO leaveapp (leave_id, leave_total_leave, leave_remaining_leave, leave_used) 
                        VALUES ('$emp_id', 30, 30, 0)";
          $con->query($leave_sql);
          
          $success_message = "New employee added successfully!";
      } else {
          $error_message = "Error: " . $sql . "<br>" . $con->error;
      }
  } else {
      // Combine all error messages
      $error_message = "Please fix the following errors:<br>" . implode("<br>", $errors);
  }
}

// Handle Promotion form submission
if (isset($_POST['promote_employee'])) {
    $emp_id = $_POST['emp_id'];
    $new_role = $_POST['newRole'];
    
    $sql = "UPDATE employee SET emp_role = '$new_role' WHERE emp_id = '$emp_id'";
    
    if ($con->query($sql) === TRUE) {
        $success_message = "Employee promoted successfully!";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $con->error;
    }
}

// Handle Salary Increase form submission
if (isset($_POST['salary_increase'])) {
    $emp_id = $_POST['emp_id'];
    $percentage_increase = $_POST['percentage_increase'];
    $current_salary = $_POST['current_salary'];
    
    // Calculate new salary with percentage increase
    $new_salary = $current_salary + ($current_salary * ($percentage_increase / 100));
    
    $sql = "UPDATE employee SET salary = $new_salary WHERE emp_id = '$emp_id'";
    
    if ($con->query($sql) === TRUE) {
        $success_message = "Salary updated successfully!";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $con->error;
    }
}

// Handle Remove Employee form submission
if (isset($_POST['remove_employee'])) {
    $emp_id = $_POST['emp_id'];
    $transfer_to = isset($_POST['transfer_projects']) ? $_POST['transfer_to'] : null;
    
    // Begin transaction
    $con->begin_transaction();
    
    try {
        // If transfer projects is checked and a valid employee is selected
        if ($transfer_to) {
            // Update projects assigned to this employee
            $transfer_sql = "UPDATE project SET project_alloc_emp = '$transfer_to' 
                             WHERE project_alloc_emp = '$emp_id'";
            $con->query($transfer_sql);
        }
        
        // Remove employee
        $delete_sql = "DELETE FROM employee WHERE emp_id = '$emp_id'";
        $con->query($delete_sql);
        
        // Remove from leave table
        $delete_leave_sql = "DELETE FROM leaveapp WHERE leave_id = '$emp_id'";
        $con->query($delete_leave_sql);
        
        // Commit transaction
        $con->commit();
        $success_message = "Employee removed successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $con->rollback();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Get team statistics
$total_members_query = "SELECT COUNT(*) as total FROM employee WHERE mag_id = '$current_manager_id'";
$total_members_result = $con->query($total_members_query);
$total_members = $total_members_result->fetch_assoc()['total'];

$developers_query = "SELECT COUNT(*) as total FROM employee 
                    WHERE mag_id = '$current_manager_id' 
                    AND emp_role LIKE '%Developer%'";
$developers_result = $con->query($developers_query);
$developers_count = $developers_result->fetch_assoc()['total'];

$qa_testers_query = "SELECT COUNT(*) as total FROM employee 
                    WHERE mag_id = '$current_manager_id' 
                    AND emp_role LIKE '%QA%' OR emp_role LIKE '%Tester%'";
$qa_testers_result = $con->query($qa_testers_query);
$qa_testers_count = $qa_testers_result->fetch_assoc()['total'];

$project_managers_query = "SELECT COUNT(*) as total FROM employee 
                          WHERE mag_id = '$current_manager_id' 
                          AND emp_role LIKE '%Manager%'";
$project_managers_result = $con->query($project_managers_query);
$project_managers_count = $project_managers_result->fetch_assoc()['total'];

// Get all team members under current manager
$search = isset($_GET['search']) ? $_GET['search'] : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'All Roles';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'Name';

// Build query based on filters
$team_query = "SELECT * FROM employee WHERE mag_id = '$current_manager_id'";

// Add search condition if provided
if (!empty($search)) {
    $team_query .= " AND (emp_name LIKE '%$search%' OR emp_mail LIKE '%$search%' OR emp_role LIKE '%$search%')";
}

// Add role filter if not "All Roles"
if ($role_filter != 'All Roles') {
    $team_query .= " AND emp_role LIKE '%$role_filter%'";
}

// Add sorting
switch ($sort_by) {
    case 'Name':
        $team_query .= " ORDER BY emp_name ASC";
        break;
    case 'Role':
        $team_query .= " ORDER BY emp_role ASC";
        break;
    case 'Date Joined':
        $team_query .= " ORDER BY emp_join_date ASC";
        break;
    default:
        $team_query .= " ORDER BY emp_name ASC";
}

$team_result = $con->query($team_query);

function getCompletedProjectsCount($con, $emp_id) {
    $query = "SELECT COUNT(*) as count FROM project 
              WHERE project_alloc_emp = '$emp_id' AND project_status = 'Complete'";
    $result = $con->query($query);
    return $result->fetch_assoc()['count'];
}


function getResolvedBugsCount($con, $emp_id) {
    $query = "SELECT COUNT(*) as count FROM bug 
              WHERE bug_alloc_emp = '$emp_id' AND bug_status = 'Resolved'";
    $result = $con->query($query);
    return $result->fetch_assoc()['count'];
}

function getProjectsCount($con, $emp_id) {
  $query = "SELECT COUNT(*) as count FROM project 
            WHERE project_alloc_emp = '$emp_id'";
  $result = $con->query($query);
  if ($result) {
      return $result->fetch_assoc()['count'];
  }
  return 0;
}


function getBugsCount($con, $emp_id) {
  $query = "SELECT COUNT(*) as count FROM bug 
            WHERE bug_alloc_emp = '$emp_id'";
  $result = $con->query($query);
  if ($result) {
      return $result->fetch_assoc()['count'];
  }
  return 0;
}

// Get current date and time for header
$current_date = date("F j, Y");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Team Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="dashboard.js" defer></script>
  
  <style>
    /* Custom styles for the modal */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      opacity: 0;
      transition: opacity 0.3s ease;
      max-width: 90%;
      width: 700px;
      max-height: 90vh;
      overflow-y: auto;
    }

    .glass-effect {
      background-color: rgba(23, 25, 35, 0.85);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Active state for sidebar links */
    .sidebar-link.active {
      background-color: rgba(239, 68, 68, 0.1);
      border-left: 3px solid #ef4444;
      color: white;
    }
    
    /* Gradient background */
    .bg-gradient-custom {
      background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }
    
    /* Card hover effects */
    .card-hover {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    /* Team member card */
    .team-member-card {
      position: relative;
      overflow: hidden;
    }

    /* Member action buttons */
    .team-member-actions {
      padding-top: 0.75rem;
      margin-top: 0.75rem;
      border-top: 1px solid rgba(107, 114, 128, 0.4);
      display: flex;
      justify-content: center;
      gap: 0.5rem;
    }

    /* Error message style */
    .error-msg {
      color: #ef4444;
      font-size: 0.75rem;
      margin-top: 0.25rem;
      display: none;
    }
  </style>
</head>

<body class="bg-gradient-custom text-white min-h-screen font-sans antialiased">

  <?php if (isset($success_message)): ?>
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="success-alert">
      <?php echo $success_message; ?>
      <button class="ml-2" onclick="document.getElementById('success-alert').style.display='none'">×</button>
    </div>
    <script>
      setTimeout(() => {
        document.getElementById('success-alert').style.display = 'none';
      }, 5000);
    </script>
  <?php endif; ?>
  
  <?php if (isset($error_message)): ?>
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="error-alert">
      <?php echo $error_message; ?>
      <button class="ml-2" onclick="document.getElementById('error-alert').style.display='none'">×</button>
    </div>
    <script>
      setTimeout(() => {
        document.getElementById('error-alert').style.display = 'none';
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
              <a href="bug.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Bugs">
                <i class="fas fa-bug mr-3"></i>
                <span>Bugs</span>
              </a>
            </li>
            <li>
              <a href="team.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Team">
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
              <!-- Left Side: Heading & Description -->
              <div class="flex flex-col gap-2">
                  <h1 class="text-2xl md:text-3xl font-bold">Team Management</h1>
                  <p class="text-gray-400" id="currentDateTime"><?php echo $current_date; ?></p>
                  <p class="text-gray-400">View, add and manage team members</p>
              </div>

              <!-- Right Side: Add Button + Profile Dropdown -->
              <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                  <!-- Add Member Button -->
                  <button class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded flex items-center shadow-md transform transition-transform hover:scale-105" id="addMemberBtn">
                      <i class="fas fa-user-plus mr-2"></i> Add Member
                  </button>

                  <!-- Profile Dropdown -->
                  <div class="relative">
                      <button id="profileDropdownBtn" class="flex items-center">
                          <?php if (!empty($ManagerProfile) && file_exists($ManagerProfile)): ?>
                              <img src="<?php echo htmlspecialchars($ManagerProfile); ?>" alt="Profile" class="w-10 h-10 object-cover rounded-full border-2 border-red-500" />
                          <?php else: ?>
                              <img src="../images/Profile/guest.png" alt="Profile" class="w-10 h-10 object-cover rounded-full border-2 border-red-500" />
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

      
      <!-- Team Filter Row -->
      <div class="bg-gray-800 p-4 rounded-xl mb-6">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <input type="text" name="search" placeholder="Search team members..." 
                   value="<?php echo htmlspecialchars($search); ?>"
                   class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
          </div>
          <div class="flex gap-2">
            <select name="role" class="bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <option <?php echo ($role_filter == 'All Roles') ? 'selected' : ''; ?>>All Roles</option>
              <option <?php echo ($role_filter == 'Frontend Dev') ? 'selected' : ''; ?>>Frontend Dev</option>
              <option <?php echo ($role_filter == 'Backend Dev') ? 'selected' : ''; ?>>Backend Dev</option>
              <option <?php echo ($role_filter == 'QA Tester') ? 'selected' : ''; ?>>QA Tester</option>
              <option <?php echo ($role_filter == 'Designer') ? 'selected' : ''; ?>>Designer</option>
              <option <?php echo ($role_filter == 'Project Manager') ? 'selected' : ''; ?>>Project Manager</option>
            </select>
            <select name="sort" class="bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <option value="Sort By" disabled>Sort By</option>
              <option <?php echo ($sort_by == 'Name') ? 'selected' : ''; ?>>Name</option>
              <option <?php echo ($sort_by == 'Role') ? 'selected' : ''; ?>>Role</option>
              <option <?php echo ($sort_by == 'Date Joined') ? 'selected' : ''; ?>>Date Joined</option>
            </select>
            <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">
              <i class="fas fa-search mr-1"></i> Filter
            </button>
          </div>
        </form>
      </div>
      
      <!-- Team Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-800 p-4 rounded-xl flex items-center">
          <div class="rounded-lg bg-blue-500 bg-opacity-20 p-3 mr-4">
            <i class="fas fa-users text-blue-400 text-xl"></i>
          </div>
          <div>
            <p class="text-gray-400 text-sm">Total Members</p>
            <p class="text-xl font-bold"><?php echo $total_members; ?></p>
          </div>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-xl flex items-center">
          <div class="rounded-lg bg-purple-500 bg-opacity-20 p-3 mr-4">
            <i class="fas fa-laptop-code text-purple-400 text-xl"></i>
          </div>
          <div>
            <p class="text-gray-400 text-sm">Developers</p>
            <p class="text-xl font-bold"><?php echo $developers_count; ?></p>
          </div>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-xl flex items-center">
          <div class="rounded-lg bg-green-500 bg-opacity-20 p-3 mr-4">
            <i class="fas fa-vial text-green-400 text-xl"></i>
          </div>
          <div>
            <p class="text-gray-400 text-sm">QA Testers</p>
            <p class="text-xl font-bold"><?php echo $qa_testers_count; ?></p>
          </div>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-xl flex items-center">
          <div class="rounded-lg bg-yellow-500 bg-opacity-20 p-3 mr-4">
            <i class="fas fa-tasks text-yellow-400 text-xl"></i>
          </div>
          <div>
            <p class="text-gray-400 text-sm">Project Managers</p>
            <p class="text-xl font-bold"><?php echo $project_managers_count; ?></p>
          </div>
        </div>
      </div>
      
      <!-- Team Members Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        <?php 
        if ($team_result->num_rows > 0) {
            while($employee = $team_result->fetch_assoc()) {
                // Get projects and bugs count
                $projects_count = getCompletedProjectsCount($con, $employee['emp_id']);
                $bugs_count = getResolvedBugsCount($con, $employee['emp_id']);
                $total_project_count = getProjectsCount($con , $employee['emp_id']) ;
                $total_bug_count = getBugsCount($con , $employee['emp_id']) ;
                
                // Format role with appropriate color
                $role_color = "text-blue-400"; // Default color
                if (strpos(strtolower($employee['emp_role']), 'qa') !== false || 
                    strpos(strtolower($employee['emp_role']), 'tester') !== false) {
                    $role_color = "text-green-400";
                } elseif (strpos(strtolower($employee['emp_role']), 'manager') !== false) {
                    $role_color = "text-red-400";
                } elseif (strpos(strtolower($employee['emp_role']), 'backend') !== false) {
                    $role_color = "text-purple-400";
                }
                
                // Format join date
                $join_date = date("M j, Y", strtotime($employee['emp_join_date']));
                
                // Display employee card
                ?>
                <div class="bg-gray-800 rounded-xl p-5 card-hover team-member-card">
                  <div class="flex justify-center mb-4">
                    <img src="<?php echo !empty($employee['emp_profile']) ? $employee['emp_profile'] : '../images/Profile/guest.png'; ?>" 
                         alt="<?php echo htmlspecialchars($employee['emp_name']); ?>" 
                         class="w-24 h-24 rounded-full border-4 border-gray-700">
                  </div>
                  <div class="text-center mb-4">
                    <h3 class="text-lg font-bold"><?php echo htmlspecialchars($employee['emp_name']); ?></h3>
                    <p class="<?php echo $role_color; ?>"><?php echo htmlspecialchars($employee['emp_role']); ?></p>
                    <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($employee['emp_mail']); ?></p>
                    <p class="text-gray-500 text-xs mt-1"><?php echo htmlspecialchars($employee['emp_id']); ?></p>
                  </div>
                  
                  <div class="border-t border-gray-700 pt-3">
                    <div class="flex justify-between text-sm mb-2">
                      <span class="text-gray-400">Projects</span>
                      <span><?php echo $projects_count; ?>/<?php echo $total_project_count ;?></span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                      <span class="text-gray-400">Bugs Fixed</span>
                      <span><?php echo $bugs_count; ?>/<?php echo $total_bug_count ; ?> </span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                      <span class="text-gray-400">Experience</span>
                      <span><?php echo $employee['emp_exp']; ?> years</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-400">Joined</span>
                      <span><?php echo $join_date; ?></span>
                    </div>
                  </div>
                  
                  <div class="team-member-actions">
                    <button class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded-lg promote-btn" 
                            data-member="<?php echo htmlspecialchars($employee['emp_name']); ?>"
                            data-id="<?php echo $employee['emp_id']; ?>">
                      <i class="fas fa-arrow-up mr-1"></i> Promote
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded-lg salary-btn" 
                            data-member="<?php echo htmlspecialchars($employee['emp_name']); ?>"
                            data-id="<?php echo $employee['emp_id']; ?>"
                            data-salary="<?php echo $employee['salary']; ?>">
                      <i class="fas fa-dollar-sign mr-1"></i> Salary
                    </button>
                    <button class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded-lg remove-btn" 
                            data-member="<?php echo htmlspecialchars($employee['emp_name']); ?>"
                            data-id="<?php echo $employee['emp_id']; ?>">
                      <i class="fas fa-user-minus mr-1"></i> Remove
                    </button>
                  </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-span-full text-center p-8 bg-gray-800 rounded-xl'>
                    <p class='text-gray-400'>No team members found. Add your first team member!</p>
                  </div>";
        }
        ?>
      </div>
    </main>
  </div>

  <!-- Add Member Modal -->
  <div id="addMemberModal" class="modal">
    <div class="modal-content glass-effect p-6">
        <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-white"><i class="fas fa-user-plus mr-2"></i>Add Team Member</h2>
        <button class="close-modal text-gray-400 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
        </div>
        <form id="addMemberForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block text-gray-400">Employee ID</label>
              <input type="text" name="emp_id" value="<?php echo generateEmployeeId(); ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" readonly>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Full Name*</label>
              <input type="text" name="fullName" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <p class="error-msg">Please enter full name</p>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Email Address*</label>
              <input type="email" name="email" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <p class="error-msg">Please enter a valid email</p>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Phone Number*</label>
              <input type="tel" name="phone" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <p class="error-msg">Please enter phone number</p>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Password*</label>
              <input type="password" name="password" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <p class="error-msg">Password is required</p>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Gender*</label>
              <select name="gender" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
              <p class="error-msg">Please select gender</p>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Profile Picture</label>
              <input type="file" name="profile_picture" accept="image/*" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Role*</label>
              <select name="role" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
                <option value="">Select Role</option>
                <option value="Frontend Dev">Frontend Developer</option>
                <option value="Backend Dev">Backend Developer</option>
                <option value="QA Tester">QA Tester</option>
                <option value="Designer">Designer</option>
                <option value="Project Manager">Project Manager</option>
              </select>
              <p class="error-msg">Please select role</p>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Department*</label>
              <select name="department" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
                <option value="">Select Department</option>
                <option value="Development">Development</option>
                <option value="Testing">Testing</option>
                <option value="Design">Design</option>
                <option value="Management">Management</option>
                <option value="Support">Support</option>
              </select>
              <p class="error-msg">Please select department</p>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Years of Experience*</label>
              <input type="number" name="experience" min="0" max="50" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <p class="error-msg">Please enter years of experience</p>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Salary (per month)*</label>
              <input type="number" name="salary" min="0" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <p class="error-msg">Please enter salary amount</p>
            </div>
            <div class="space-y-2">
              <label class="block text-gray-400">Start Date*</label>
              <input type="date" name="startDate" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <p class="error-msg">Please enter start date</p>
            </div>
          </div>
          
          <div class="pt-4 border-t border-gray-700">
            <label class="block text-gray-400 mb-3">Skills Rating (0-10)</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-400 text-sm">Web Development</label>
                <input type="range" name="webD" min="0" max="10" value="0" class="w-full">
                <div class="flex justify-between text-xs text-gray-500">
                  <span>0</span>
                  <span>5</span>
                  <span>10</span>
                </div>
              </div>
              <div>
                <label class="block text-gray-400 text-sm">Automation</label>
                <input type="range" name="auto" min="0" max="10" value="0" class="w-full">
                <div class="flex justify-between text-xs text-gray-500">
                  <span>0</span>
                  <span>5</span>
                  <span>10</span>
                </div>
              </div>
              <div>
                <label class="block text-gray-400 text-sm">Design</label>
                <input type="range" name="design" min="0" max="10" value="0" class="w-full">
                <div class="flex justify-between text-xs text-gray-500">
                  <span>0</span>
                  <span>5</span>
                  <span>10</span>
                </div>
              </div>
              <div>
                <label class="block text-gray-400 text-sm">Communication</label>
                <input type="range" name="verbal" min="0" max="10" value="0" class="w-full">
                <div class="flex justify-between text-xs text-gray-500">
                  <span>0</span>
                  <span>5</span>
                  <span>10</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex justify-end mt-6">
            <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2 close-modal">Cancel</button>
            <button type="submit" name="add_employee" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Add Employee</button>
          </div>
        </form>
    </div>
  </div>

  <!-- Promote Employee Modal -->
  <div id="promoteModal" class="modal">
    <div class="modal-content glass-effect p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-white"><i class="fas fa-arrow-up mr-2"></i>Promote Employee</h2>
        <button class="close-modal text-gray-400 hover:text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form action="" method="POST" class="space-y-4">
        <input type="hidden" name="emp_id" id="promoteEmpId">
        <div class="mb-4">
          <p class="text-gray-300 mb-2">You are about to promote: <span id="promoteEmpName" class="font-semibold text-white"></span></p>
          <div class="space-y-2">
            <label class="block text-gray-400">New Role*</label>
            <select name="newRole" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <option value="">Select New Role</option>
              <option value="Senior Frontend Dev">Senior Frontend Developer</option>
              <option value="Senior Backend Dev">Senior Backend Developer</option>
              <option value="Lead QA Tester">Lead QA Tester</option>
              <option value="Lead Designer">Lead Designer</option>
              <option value="Senior Project Manager">Senior Project Manager</option>
              <option value="Team Lead">Team Lead</option>
            </select>
          </div>
        </div>
        <div class="flex justify-end mt-6">
          <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2 close-modal">Cancel</button>
          <button type="submit" name="promote_employee" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Confirm Promotion</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Salary Increment Modal -->
  <div id="salaryModal" class="modal">
    <div class="modal-content glass-effect p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-white"><i class="fas fa-dollar-sign mr-2"></i>Salary Update</h2>
        <button class="close-modal text-gray-400 hover:text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form action="" method="POST" class="space-y-4">
        <input type="hidden" name="emp_id" id="salaryEmpId">
        <input type="hidden" name="current_salary" id="currentSalary">
        <div class="mb-4">
          <p class="text-gray-300 mb-2">Update salary for: <span id="salaryEmpName" class="font-semibold text-white"></span></p>
          <p class="text-gray-400 mb-4">Current salary: $<span id="salaryCurrentAmount"></span></p>
          
          <div class="space-y-2">
            <label class="block text-gray-400">Percentage Increase (%)*</label>
            <div class="flex items-center">
              <input type="number" name="percentage_increase" min="1" max="100" required class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <span class="ml-2 text-gray-400">%</span>
            </div>
          </div>
          
          <div class="mt-4 p-3 bg-gray-900 rounded">
            <p class="text-gray-400">New salary will be: $<span id="newSalaryPreview">--</span></p>
          </div>
        </div>
        <div class="flex justify-end mt-6">
          <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2 close-modal">Cancel</button>
          <button type="submit" name="salary_increase" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update Salary</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Remove Employee Modal -->
  <div id="removeModal" class="modal">
    <div class="modal-content glass-effect p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-white"><i class="fas fa-user-minus mr-2"></i>Remove Employee</h2>
        <button class="close-modal text-gray-400 hover:text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form action="" method="POST" class="space-y-4">
        <input type="hidden" name="emp_id" id="removeEmpId">
        <div class="mb-4">
          <div class="flex items-center p-3 bg-red-900 bg-opacity-30 rounded border border-red-700 mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
            <p class="text-white">You are about to remove <span id="removeEmpName" class="font-semibold"></span> from your team. This action cannot be undone.</p>
          </div>
          
          <div class="mb-4">
            <label class="flex items-center">
              <input type="checkbox" name="transfer_projects" id="transferProjects" class="mr-2 bg-gray-900 border-gray-700">
              <span class="text-gray-300">Transfer this employee's projects to another team member</span>
            </label>
          </div>
          
          <div id="transferOptions" class="mt-3 hidden">
            <label class="block text-gray-400 mb-2">Transfer projects to:</label>
            <select name="transfer_to" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
              <option value="">Select Team Member</option>
              <?php
              // Get all other team members
              $other_members_query = "SELECT emp_id, emp_name, emp_role FROM employee 
                                    WHERE mag_id = '$current_manager_id' AND emp_id != '$current_manager_id'";
              $other_members_result = $con->query($other_members_query);
              
              if ($other_members_result->num_rows > 0) {
                  while($member = $other_members_result->fetch_assoc()) {
                      echo "<option value='" . $member['emp_id'] . "'>" . 
                           htmlspecialchars($member['emp_name']) . " (" . htmlspecialchars($member['emp_role']) . ")</option>";
                  }
              }
              ?>
            </select>
          </div>
        </div>
        <div class="flex justify-end mt-6">
          <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2 close-modal">Cancel</button>
          <button type="submit" name="remove_employee" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Remove Employee</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Modal control
        const openModal = (modalId) => {
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.querySelector('.modal-content').style.opacity = '1';
            }, 10);
        };
        
        const closeModal = (modal) => {
            modal.querySelector('.modal-content').style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        };
        
        // Add Member button
        document.getElementById('addMemberBtn').addEventListener('click', function() {
            openModal('addMemberModal');
        });
        
        // Close modal buttons
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                closeModal(modal);
            });
        });
        
        // Promote buttons
        document.querySelectorAll('.promote-btn').forEach(button => {
            button.addEventListener('click', function() {
                const empName = this.getAttribute('data-member');
                const empId = this.getAttribute('data-id');
                
                document.getElementById('promoteEmpName').textContent = empName;
                document.getElementById('promoteEmpId').value = empId;
                
                openModal('promoteModal');
            });
        });
        
        // Salary buttons
        document.querySelectorAll('.salary-btn').forEach(button => {
            button.addEventListener('click', function() {
                const empName = this.getAttribute('data-member');
                const empId = this.getAttribute('data-id');
                const salary = this.getAttribute('data-salary');
                
                document.getElementById('salaryEmpName').textContent = empName;
                document.getElementById('salaryEmpId').value = empId;
                document.getElementById('currentSalary').value = salary;
                document.getElementById('salaryCurrentAmount').textContent = salary;
                
                openModal('salaryModal');
            });
        });
        
        // Remove buttons
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function() {
                const empName = this.getAttribute('data-member');
                const empId = this.getAttribute('data-id');
                
                document.getElementById('removeEmpName').textContent = empName;
                document.getElementById('removeEmpId').value = empId;
                
                openModal('removeModal');
            });
        });
        
        // Transfer projects checkbox
        const transferCheckbox = document.getElementById('transferProjects');
        const transferOptions = document.getElementById('transferOptions');
        
        transferCheckbox.addEventListener('change', function() {
            if (this.checked) {
                transferOptions.classList.remove('hidden');
            } else {
                transferOptions.classList.add('hidden');
            }
        });
        
        // Salary calculation preview
        const percentageInput = document.querySelector('input[name="percentage_increase"]');
        if (percentageInput) {
            percentageInput.addEventListener('input', function() {
                const currentSalary = parseFloat(document.getElementById('currentSalary').value);
                const percentage = parseFloat(this.value);
                
                if (!isNaN(currentSalary) && !isNaN(percentage)) {
                    const increase = currentSalary * (percentage / 100);
                    const newSalary = currentSalary + increase;
                    document.getElementById('newSalaryPreview').textContent = newSalary.toFixed(2);
                } else {
                    document.getElementById('newSalaryPreview').textContent = '--';
                }
            });
        }
        
        // Form validation
        const addMemberForm = document.getElementById('addMemberForm');
        if (addMemberForm) {
            addMemberForm.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Basic validation
                const requiredFields = addMemberForm.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        const errorMsg = field.nextElementSibling;
                        if (errorMsg && errorMsg.classList.contains('error-msg')) {
                            errorMsg.style.display = 'block';
                        }
                    } else {
                        const errorMsg = field.nextElementSibling;
                        if (errorMsg && errorMsg.classList.contains('error-msg')) {
                            errorMsg.style.display = 'none';
                        }
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        }
        
        // Close modals when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this);
                }
            });
        });
        
        // Update current date and time
        const updateDateTime = () => {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        };
        
        updateDateTime();
        setInterval(updateDateTime, 60000); // Update every minute
    });
  </script>
</body>
</html>
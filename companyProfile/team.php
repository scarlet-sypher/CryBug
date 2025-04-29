<?php
// Start session
session_start();

include "connection.php";

// Get current company ID from session
$cmp_id = isset($_SESSION['cmp_id']) ? $_SESSION['cmp_id'] : '';

// Handle Add Member form submission
if (isset($_POST['add_member_submit'])) {

  $fullName = trim(htmlspecialchars($_POST['fullName']));
  $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
  $role = trim(htmlspecialchars($_POST['role']));
  $experience = filter_var($_POST['experience'], FILTER_VALIDATE_INT);
  $gender = isset($_POST['gender']) ? trim(htmlspecialchars($_POST['gender'])) : 'Male';
  $startDate = trim(htmlspecialchars($_POST['startDate']));
  $skills = isset($_POST['skills']) ? trim(htmlspecialchars($_POST['skills'])) : '';
  $salary = filter_var($_POST['salary'], FILTER_VALIDATE_FLOAT) ?: 0;

  // Validation
  $errors = [];
  if (empty($fullName)) {
      $errors[] = "Full name is required";
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Valid email is required";
  }
  if (empty($role)) {
      $errors[] = "Role is required";
  }
  if ($experience === false || $experience < 0) {
      $errors[] = "Valid experience is required";
  }

  if (!empty($errors)) {
      $error_message = "Errors: " . implode(", ", $errors);
  } 
  else {
    // Generate unique mag_id
    $prefix = "CRYMAG";
    $random = sprintf("%08d", rand(10000000, 99999999));
    $mag_id = $prefix . $random;
    
    // Handle profile picture upload
   


    $profile_pic = "";
    $target_path = "";
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $pic_tmp_name = $_FILES['profile_pic']['tmp_name'];
        $pic_name = basename($_FILES['profile_pic']['name']);
        $upload_dir = "../uploads/manager_images/";

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $target_path = $upload_dir . $pic_name;
        move_uploaded_file($pic_tmp_name, $target_path);
    }
    
    // Only proceed if no errors from file upload
    if (empty($errors)) {
      // Insert into manager table
      $insert_query = "INSERT INTO manager (mag_id, mag_name, mag_email, mag_role, mag_profile, mag_gender, mag_cmp_id, mag_salary, mag_join_date, mag_exp , mag_password) 
                      VALUES ('$mag_id', '$fullName', '$email', '$role', '$target_path', '$gender', '$cmp_id', '$salary', NOW(), '$experience',123456)";
      
      if(mysqli_query($con, $insert_query)) {
        // Also insert into leaveapp table for leave tracking
        $leave_insert = "INSERT INTO leaveapp (leave_id, leave_total_leave, leave_remaining_leave, leave_used) 
                        VALUES ('$mag_id', 30, 30, 0)";
        mysqli_query($con, $leave_insert);
        
        $success_message = "New team member added successfully!";
      } else {
        $error_message = "Error: " . mysqli_error($con);
      }
    }
  }
}

// Handle promotion form submission
if (isset($_POST['promote_submit'])) {

  $mag_id = trim(htmlspecialchars($_POST['manager_id']));
  $new_role = trim(htmlspecialchars($_POST['newRole']));
  $effective_date = trim(htmlspecialchars($_POST['effectiveDate']));
  $reason = isset($_POST['promoteReason']) ? trim(htmlspecialchars($_POST['promoteReason'])) : '';

  // Validation
  $errors = [];
  if (empty($mag_id)) {
      $errors[] = "Manager ID is required";
  }
  if (empty($new_role)) {
      $errors[] = "New role is required";
  }

  if (!empty($errors)) {
      $error_message = "Errors: " . implode(", ", $errors);
  } else {
      // Escape before using in query
      $mag_id = mysqli_real_escape_string($con, $mag_id);
      $new_role = mysqli_real_escape_string($con, $new_role);

      $update_query = "UPDATE manager SET mag_role = '$new_role' WHERE mag_id = '$mag_id'";

      if (mysqli_query($con, $update_query)) {
          $success_message = "Team member promoted successfully!";
      } else {
          $error_message = "Error: " . mysqli_error($con);
      }
  }
}

// Handle salary adjustment form submission

if (isset($_POST['salary_submit'])) {

    // Sanitize input
    $mag_id = trim(htmlspecialchars($_POST['manager_id']));
    $new_salary = filter_var($_POST['newSalary'], FILTER_VALIDATE_FLOAT);
    $effective_date = trim(htmlspecialchars($_POST['salaryDate']));

    // Validation
    $errors = [];
    if (empty($mag_id)) {
        $errors[] = "Manager ID is required";
    }
    if ($new_salary === false || $new_salary < 0) {
        $errors[] = "Valid salary amount is required";
    }
    if (empty($effective_date)) {
        $errors[] = "Effective date is required";
    }

    if (!empty($errors)) {
        $error_message = "Errors: " . implode(", ", $errors);
    } else {
        // Escape before using in query
        $mag_id = mysqli_real_escape_string($con, $mag_id);
        $new_salary = mysqli_real_escape_string($con, $new_salary);
        $effective_date = mysqli_real_escape_string($con, $effective_date);

        $update_query = "UPDATE manager SET mag_salary = '$new_salary' WHERE mag_id = '$mag_id'";

        if (mysqli_query($con, $update_query)) {
            $success_message = "Salary updated successfully!";
        } else {
            $error_message = "Error: " . mysqli_error($con);
        }
    }
}



// Handle team member removal
if (isset($_POST['remove_submit'])) {

    // Sanitize input
    $mag_id = trim(htmlspecialchars($_POST['manager_id']));
    $transfer_to = (isset($_POST['transferProjects']) && !empty($_POST['transfer_manager'])) ? 
                   trim(htmlspecialchars($_POST['transfer_manager'])) : '';

    // Validation
    $errors = [];
    if (empty($mag_id)) {
        $errors[] = "Manager ID is required";
    }
    if (isset($_POST['transferProjects']) && empty($transfer_to)) {
        $errors[] = "Transfer manager is required when transfer is checked";
    }

    if (!empty($errors)) {
        $error_message = "Errors: " . implode(", ", $errors);
    } else {
        // Escape inputs before using in queries
        $mag_id = mysqli_real_escape_string($con, $mag_id);
        $transfer_to = mysqli_real_escape_string($con, $transfer_to);

        // If transfer is checked, reassign records
        if (!empty($transfer_to)) {
            // Update employee records
            $update_emp = "UPDATE employee SET mag_id = '$transfer_to' WHERE mag_id = '$mag_id'";
            mysqli_query($con, $update_emp);

            // Update projects
            $update_projects = "UPDATE project SET project_alloc_mag = '$transfer_to' WHERE project_alloc_mag = '$mag_id'";
            mysqli_query($con, $update_projects);

            // Update bugs
            $update_bugs = "UPDATE bug SET bug_alloc_mag = '$transfer_to' WHERE bug_alloc_mag = '$mag_id'";
            mysqli_query($con, $update_bugs);
        }

        // Now delete from manager table
        $delete_manager = "DELETE FROM manager WHERE mag_id = '$mag_id'";
        if (mysqli_query($con, $delete_manager)) {
            // Also delete from leave application table
            $delete_leave = "DELETE FROM leaveapp WHERE leave_id = '$mag_id'";
            mysqli_query($con, $delete_leave);

            $success_message = "Team member removed successfully!";
        } else {
            $error_message = "Error: " . mysqli_error($con);
        }
    }
}



// Handle search, filter and sort
$search = isset($_GET['search']) ? $_GET['search'] : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'All Roles';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'mag_name';

// Build the query based on search, filter, and sort parameters
$query = "SELECT * FROM manager WHERE mag_cmp_id = '$cmp_id'";

if(!empty($search)) {
  $query .= " AND (mag_name LIKE '%$search%' OR mag_id LIKE '%$search%')";
}

if($role_filter != 'All Roles') {
  $query .= " AND mag_role = '$role_filter'";
}

$query .= " ORDER BY $sort_by";
$result = mysqli_query($con, $query);

// Get team statistics
$total_query = "SELECT COUNT(*) as total FROM manager WHERE mag_cmp_id = '$cmp_id'";
$total_result = mysqli_query($con, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_members = $total_row['total'];

// Get role-wise statistics
$roles_query = "SELECT mag_role, COUNT(*) as role_count FROM manager WHERE mag_cmp_id = '$cmp_id' GROUP BY mag_role";
$roles_result = mysqli_query($con, $roles_query);
$role_stats = [];
while($role_row = mysqli_fetch_assoc($roles_result)) {
  $role_stats[$role_row['mag_role']] = $role_row['role_count'];
}

// Get all roles for filter dropdown
$all_roles_query = "SELECT DISTINCT mag_role FROM manager WHERE mag_cmp_id = '$cmp_id'";
$all_roles_result = mysqli_query($con, $all_roles_query);
$all_roles = [];
while($role_row = mysqli_fetch_assoc($all_roles_result)) {
  $all_roles[] = $role_row['mag_role'];
}

// Get managers list for transfer dropdown
$managers_query = "SELECT mag_id, mag_name FROM manager WHERE mag_cmp_id = '$cmp_id'";
$managers_result = mysqli_query($con, $managers_query);
$managers = [];
while($manager_row = mysqli_fetch_assoc($managers_result)) {
  $managers[$manager_row['mag_id']] = $manager_row['mag_name'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="dashboard.js" defer></script>

  <style>
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 1000;
      overflow-y: auto;
      padding: 20px;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .modal.active {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      opacity: 1;
    }

    .modal-content {
      background-color: #1f2937;
      border-radius: 0.75rem;
      margin-top: 40px;
      width: 100%;
      max-width: 500px;
      position: relative;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
      animation: modalFadeIn 0.3s;
      max-height: calc(100vh - 100px);
      overflow-y: auto;
    }

    .glass-effect {
      background-color: rgba(31, 41, 55, 0.95);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(75, 85, 99, 0.3);
    }

    @keyframes modalFadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Team Member Card Styles */
    .card-hover {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    }

    .team-member-card {
      position: relative;
      overflow: hidden;
    }

    .team-member-actions {
      display: flex;
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      padding: 15px;
      background: linear-gradient(to top, rgba(31, 41, 55, 0.95), rgba(31, 41, 55, 0));
      justify-content: center;
      gap: 8px;
      transition: all 0.3s ease;
    }
    /* For mobile - always show actions */
    @media (max-width: 640px) {
      .team-member-actions {
        display: flex;
        padding: 10px;
      }
    }

    /* Form Validation Styles */
    .error-msg {
      display: none;
      color: #f87171;
      font-size: 0.75rem;
      margin-top: 0.25rem;
    }

    /* Sidebar Responsive Styles */
    .sidebar {
      transition: transform 0.3s ease;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        transform: translateX(-100%);
        z-index: 50;
        height: 100vh;
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
        z-index: 40;
      }
      
      .sidebar-overlay.active {
        display: block;
      }
      
      main {
        margin-left: 0 !important;
      }
    }

    /* Active sidebar link */
    .sidebar-link.active {
      background-color: rgba(99, 102, 241, 0.1);
      color: white;
      border-left: 3px solid #6366f1;
    }

    /* Button hover effects */
    button {
      transition: all 0.2s ease;
    }

    .sidebar-link:hover {
      background-color: rgba(75, 85, 99, 0.3);
    }

    /* Gradient background for page */
    .bg-gradient-custom {
      background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
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
          <a href="team.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Clients">
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
        <a href="logout.php"><button class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 p-2 rounded flex items-center justify-center transition-all hover:transform hover:translate-y-[-2px]">
          <i class="fas fa-sign-out-alt mr-2"></i>
          <span>Logout</span>
        </button></a>
      </div>
    </div>
  </aside>

   <!-- Main Content Area -->
   <main class="md:ml-64 lg:ml-64 flex-1 p-4 md:p-6 transition-all">

    <button class="menu-toggle md:hidden mb-4 bg-gray-800 p-2 rounded" id="menuToggle">
        <i class="fas fa-bars"></i>
      </button>
  <!-- Page Header -->
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
  <div>
    <h1 class="text-2xl md:text-3xl font-bold">Team Management</h1>
    <p class="text-gray-400">View, add and manage team members</p>
  </div>

  <div class="flex items-center gap-4 mt-4 md:mt-0">
    <button class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded flex items-center" id="addMemberBtn">
      <i class="fas fa-user-plus mr-2"></i> Add Member
    </button>

    <div class="relative">
      <button id="profileDropdownBtn" class="flex items-center focus:outline-none">
        <?php if (!empty($_SESSION['cmp_logo']) && file_exists($_SESSION['cmp_logo'])): ?>
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

  
  <?php if(isset($success_message)): ?>
  <div class="bg-green-500 bg-opacity-20 text-green-400 p-4 rounded-xl mb-6 flex items-center">
    <i class="fas fa-check-circle mr-2"></i>
    <?php echo $success_message; ?>
  </div>
  <?php endif; ?>
  
  <?php if(isset($error_message)): ?>
  <div class="bg-red-500 bg-opacity-20 text-red-400 p-4 rounded-xl mb-6 flex items-center">
    <i class="fas fa-exclamation-circle mr-2"></i>
    <?php echo $error_message; ?>
  </div>
  <?php endif; ?>
  
  <!-- Team Filter Row -->
  <div class="bg-gray-800 p-4 rounded-xl mb-6">
    <form method="GET" action="team.php" class="flex flex-col md:flex-row gap-4">
      <div class="flex-1">
        <input type="text" name="search" placeholder="Search team members..." value="<?php echo $search; ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
      </div>
      <div class="flex gap-2">
        <select name="role" class="bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
          <option value="All Roles" <?php echo $role_filter == 'All Roles' ? 'selected' : ''; ?>>All Roles</option>
          <?php foreach($all_roles as $role): ?>
          <option value="<?php echo $role; ?>" <?php echo $role_filter == $role ? 'selected' : ''; ?>><?php echo $role; ?></option>
          <?php endforeach; ?>
        </select>
        <select name="sort" class="bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
          <option value="mag_name" <?php echo $sort_by == 'mag_name' ? 'selected' : ''; ?>>Sort By Name</option>
          <option value="mag_role" <?php echo $sort_by == 'mag_role' ? 'selected' : ''; ?>>Sort By Role</option>
          <option value="mag_join_date" <?php echo $sort_by == 'mag_join_date' ? 'selected' : ''; ?>>Sort By Date Joined</option>
          <option value="emp_exp" <?php echo $sort_by == 'emp_exp' ? 'selected' : ''; ?>>Sort By Experience</option>
        </select>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded">
          <i class="fas fa-filter mr-1"></i> Filter
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
        <p class="text-xl font-bold"><?php echo isset($role_stats['Frontend Developer']) + isset($role_stats['Backend Developer']); ?></p>
      </div>
    </div>
    
    <div class="bg-gray-800 p-4 rounded-xl flex items-center">
      <div class="rounded-lg bg-green-500 bg-opacity-20 p-3 mr-4">
        <i class="fas fa-vial text-green-400 text-xl"></i>
      </div>
      <div>
        <p class="text-gray-400 text-sm">QA Testers</p>
        <p class="text-xl font-bold"><?php echo isset($role_stats['QA Tester']) ? $role_stats['QA Tester'] : 0; ?></p>
      </div>
    </div>
    
    <div class="bg-gray-800 p-4 rounded-xl flex items-center">
      <div class="rounded-lg bg-yellow-500 bg-opacity-20 p-3 mr-4">
        <i class="fas fa-tasks text-yellow-400 text-xl"></i>
      </div>
      <div>
        <p class="text-gray-400 text-sm">Project Managers</p>
        <p class="text-xl font-bold"><?php echo isset($role_stats['Project Manager']) ? $role_stats['Project Manager'] : 0; ?></p>
      </div>
    </div>
  </div>
  
  <!-- Team Members Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
    
    <?php if(mysqli_num_rows($result) > 0): ?>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <?php
          // Get completed projects count
          $projects_query = "SELECT COUNT(*) as completed_projects FROM project WHERE project_alloc_mag = '{$row['mag_id']}' AND project_status = 'Completed'";
          $projects_result = mysqli_query($con, $projects_query);
          $projects_row = mysqli_fetch_assoc($projects_result);
          $completed_projects = $projects_row['completed_projects'];
          
          // Get resolved bugs count
          $bugs_query = "SELECT COUNT(*) as resolved_bugs FROM bug WHERE bug_alloc_mag = '{$row['mag_id']}' AND bug_status = 'Resolved'";
          $bugs_result = mysqli_query($con, $bugs_query);
          $bugs_row = mysqli_fetch_assoc($bugs_result);
          $resolved_bugs = $bugs_row['resolved_bugs'];
          
          // Format join date
          $join_date = date('M d, Y', strtotime($row['mag_join_date']));
        ?>
        <!-- Team Member Card -->
        <div class="bg-gray-800 rounded-xl p-5 card-hover team-member-card">
          <div class="flex justify-center mb-4">
            <?php if(!empty($row['mag_profile']) && file_exists($row['mag_profile'])): ?>
              <img src="<?php echo $row['mag_profile']; ?>" alt="<?php echo $row['mag_name']; ?>" class="w-24 h-24  rounded-full border-4 border-gray-700">
            <?php else: ?>
              <img src="../images/Profile/guest.png" alt="<?php echo $row['mag_name']; ?>" class="w-24 h-24 rounded-full border-4 border-gray-700">
            <?php endif; ?>
          </div>
          <div class="text-center mb-4">
            <h3 class="text-lg font-bold"><?php echo $row['mag_name']; ?></h3>
            <p class="text-blue-400"><?php echo $row['mag_role']; ?></p>
            <p class="text-gray-400 text-sm"><?php echo $row['mag_email']; ?></p>
            <p class="text-gray-500 text-xs mt-1"><?php echo $row['mag_id']; ?></p>
          </div>
          
          <div class="border-t border-gray-700 pt-3">
            <div class="flex justify-between text-sm mb-2">
              <span class="text-gray-400">Projects</span>
              <span><?php echo $completed_projects; ?></span>
            </div>
            <div class="flex justify-between text-sm mb-2">
              <span class="text-gray-400">Bugs Fixed</span>
              <span><?php echo $resolved_bugs; ?></span>
            </div>
            <div class="flex justify-between text-sm mb-2">
              <span class="text-gray-400">Experience</span>
              <span><?php echo $row['emp_exp'] ?? '0'; ?> Years</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-400">Joined</span>
              <span><?php echo $join_date; ?></span>
            </div>
          </div>
          
          <div class="team-member-actions">
            <button class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded-lg promote-btn" data-member="<?php echo $row['mag_name']; ?>" data-id="<?php echo $row['mag_id']; ?>" data-role="<?php echo $row['mag_role']; ?>">
              <i class="fas fa-arrow-up mr-1"></i> Promote
            </button>
            <button class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded-lg salary-btn" data-member="<?php echo $row['mag_name']; ?>" data-id="<?php echo $row['mag_id']; ?>" data-salary="<?php echo $row['mag_salary']; ?>">
              <i class="fas fa-dollar-sign mr-1"></i> Salary
            </button>
            <button class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded-lg remove-btn" data-member="<?php echo $row['mag_name']; ?>" data-id="<?php echo $row['mag_id']; ?>">
              <i class="fas fa-user-minus mr-1"></i> Remove
            </button>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-span-full text-center py-10">
        <div class="bg-gray-800 p-8 rounded-xl">
          <i class="fas fa-users text-gray-600 text-5xl mb-4"></i>
          <h3 class="text-xl font-bold mb-2">No Team Members Found</h3>
          <p class="text-gray-400 mb-4">There are no team members matching your criteria.</p>
          <button class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded" id="addMemberBtnEmpty">
            <i class="fas fa-user-plus mr-2"></i> Add New Member
          </button>
        </div>
      </div>
    <?php endif; ?>
    
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
    <form id="addMemberForm" method="POST" action="team.php" enctype="multipart/form-data">
    <div class="mb-4">
        <label class="block text-gray-400 text-sm font-medium mb-2">Manager ID</label>
        <input type="text" class="w-full bg-gray-800 text-white border border-gray-700 rounded p-2" value="<?php echo 'CRYMAG' . rand(10000000, 99999999); ?>" disabled>
        <p class="text-gray-500 text-xs mt-1">ID will be auto-generated on submission</p>
    </div>
    
    <div class="mb-4">
        <label class="block text-gray-400 text-sm font-medium mb-2">Full Name*</label>
        <input type="text" name="fullName" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" placeholder="Enter full name" required>
        <span class="error-msg" id="nameError">Name is required</span>
    </div>
    
    <div class="mb-4">
        <label class="block text-gray-400 text-sm font-medium mb-2">Email Address*</label>
        <input type="email" name="email" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" placeholder="Enter email address" required>
        <span class="error-msg" id="emailError">Valid email is required</span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-gray-400 text-sm font-medium mb-2">Role*</label>
            <select name="role" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" required>
                <option value="">Select Role</option>
                <option value="Frontend Developer">Frontend Developer</option>
                <option value="Backend Developer">Backend Developer</option>
                <option value="QA Tester">QA Tester</option>
                <option value="Project Manager">Project Manager</option>
                <option value="UI/UX Designer">UI/UX Designer</option>
                <option value="DevOps Engineer">DevOps Engineer</option>
            </select>
            <span class="error-msg" id="roleError">Role is required</span>
        </div>
        <div>
            <label class="block text-gray-400 text-sm font-medium mb-2">Experience (Years)*</label>
            <input type="number" name="experience" min="0" max="50" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" required>
            <span class="error-msg" id="expError">Experience is required</span>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-gray-400 text-sm font-medium mb-2">Gender</label>
            <div class="flex space-x-4 mt-1">
                <label class="inline-flex items-center">
                    <input type="radio" name="gender" value="Male" class="text-red-500" checked>
                    <span class="ml-2 text-gray-300">Male</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="gender" value="Female" class="text-red-500">
                    <span class="ml-2 text-gray-300">Female</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="gender" value="Other" class="text-red-500">
                    <span class="ml-2 text-gray-300">Other</span>
                </label>
            </div>
        </div>
        <div>
            <label class="block text-gray-400 text-sm font-medium mb-2">Start Date</label>
            <input type="date" name="startDate" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
        </div>
    </div>
    
    <div class="mb-4">
        <label class="block text-gray-400 text-sm font-medium mb-2">Skills</label>
        <input type="text" name="skills" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" placeholder="Skills separated by commas">
        <p class="text-gray-500 text-xs mt-1">E.g. JavaScript, PHP, React, Testing</p>
    </div>
    
    <div class="mb-4">
        <label class="block text-gray-400 text-sm font-medium mb-2">Salary</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">$</span>
            <input type="number" name="salary" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 pl-8 focus:outline-none focus:border-red-500" placeholder="Enter salary amount">
        </div>
    </div>
    
    <div class="mb-4">
        <label class="block text-gray-400 text-sm font-medium mb-2">Profile Picture</label>
        <input type="file" name="profile_pic" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" accept="image/jpeg,image/png,image/jpg">
        <p class="text-gray-500 text-xs mt-1">JPG, JPEG or PNG. Max size 5MB.</p>
    </div>
    
    <div class="flex justify-end mt-6">
        <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2 close-modal">Cancel</button>
        <button type="submit" name="add_member_submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Add Member</button>
    </div>
    </form>
</div>
</div>

<!-- Promote Member Modal -->
<div id="promoteModal" class="modal">
    <div class="modal-content glass-effect p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-arrow-up mr-2"></i>Promote Team Member</h2>
            <button class="close-modal text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="promoteForm" method="POST" action="team.php">
            <input type="hidden" name="manager_id" id="promoteManagerId">
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Team Member</label>
                <input type="text" id="promoteMemberName" class="w-full bg-gray-800 text-white border border-gray-700 rounded p-2" disabled>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Current Role</label>
                <input type="text" id="promoteCurrentRole" class="w-full bg-gray-800 text-white border border-gray-700 rounded p-2" disabled>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">New Role*</label>
                <select name="newRole" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" required>
                    <option value="">Select New Role</option>
                    <option value="Team Lead">Team Lead</option>
                    <option value="Senior Developer">Senior Developer</option>
                    <option value="Project Manager">Project Manager</option>
                    <option value="QA Lead">QA Lead</option>
                    <option value="Engineering Manager">Engineering Manager</option>
                    <option value="Technical Director">Technical Director</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Effective Date</label>
                <input type="date" name="effectiveDate" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Reason for Promotion</label>
                <textarea name="promoteReason" rows="3" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" placeholder="Enter promotion reason"></textarea>
            </div>
            
            <div class="flex justify-end mt-6">
                <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2 close-modal">Cancel</button>
                <button type="submit" name="promote_submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Confirm Promotion</button>
            </div>
        </form>
    </div>
</div>

<!-- Adjust Salary Modal -->
<div id="salaryModal" class="modal">
    <div class="modal-content glass-effect p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-dollar-sign mr-2"></i>Adjust Salary</h2>
            <button class="close-modal text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="salaryForm" method="POST" action="team.php">
            <input type="hidden" name="manager_id" id="salaryManagerId">
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Team Member</label>
                <input type="text" id="salaryMemberName" class="w-full bg-gray-800 text-white border border-gray-700 rounded p-2" disabled>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Current Salary</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">$</span>
                    <input type="text" id="currentSalary" class="w-full bg-gray-800 text-white border border-gray-700 rounded p-2 pl-8" disabled>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">New Salary*</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">$</span>
                    <input type="number" name="newSalary" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 pl-8 focus:outline-none focus:border-red-500" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Effective Date</label>
                <input type="date" name="salaryDate" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500" required>
            </div>
            
            <div class="flex justify-end mt-6">
                <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2 close-modal">Cancel</button>
                <button type="submit" name="salary_submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update Salary</button>
            </div>
        </form>
    </div>
</div>

<!-- Remove Member Modal -->
<div id="removeModal" class="modal">
    <div class="modal-content glass-effect p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-user-minus mr-2"></i>Remove Team Member</h2>
            <button class="close-modal text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="removeForm" method="POST" action="team.php">
            <input type="hidden" name="manager_id" id="removeManagerId">
            
            <div class="mb-4">
                <div class="bg-red-500 bg-opacity-20 text-red-400 p-4 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <p>You are about to remove <strong id="removeMemberName"></strong> from the team. This action cannot be undone.</p>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-medium mb-2">Transfer Responsibilities</label>
                <label class="flex items-center">
                    <input type="checkbox" name="transferProjects" class="text-red-500" id="transferCheckbox">
                    <span class="ml-2 text-gray-300">Transfer projects and bugs to another team member</span>
                </label>
            </div>
            
            <div id="transferToField" class="mb-4 hidden">
                <label class="block text-gray-400 text-sm font-medium mb-2">Transfer To</label>
                <select name="transfer_manager" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 focus:outline-none focus:border-red-500">
                    <option value="">Select Team Member</option>
                    <?php foreach($managers as $id => $name): ?>
                    <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex justify-end mt-6">
                <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2 close-modal">Cancel</button>
                <button type="submit" name="remove_submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Remove Member</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    
    // Close modal when clicking on close button or outside the modal
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    });
    
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    });
    
    // Open Add Member Modal
    document.getElementById('addMemberBtn').addEventListener('click', function() {
        openModal('addMemberModal');
    });
    
    if (document.getElementById('addMemberBtnEmpty')) {
        document.getElementById('addMemberBtnEmpty').addEventListener('click', function() {
            openModal('addMemberModal');
        });
    }
    
    // Open Promote Modal
    document.querySelectorAll('.promote-btn').forEach(button => {
        button.addEventListener('click', function() {
            const memberId = this.getAttribute('data-id');
            const memberName = this.getAttribute('data-member');
            const memberRole = this.getAttribute('data-role');
            
            document.getElementById('promoteManagerId').value = memberId;
            document.getElementById('promoteMemberName').value = memberName;
            document.getElementById('promoteCurrentRole').value = memberRole;
            
            openModal('promoteModal');
        });
    });
    
    // Open Salary Modal
    document.querySelectorAll('.salary-btn').forEach(button => {
        button.addEventListener('click', function() {
            const memberId = this.getAttribute('data-id');
            const memberName = this.getAttribute('data-member');
            const memberSalary = this.getAttribute('data-salary');
            
            document.getElementById('salaryManagerId').value = memberId;
            document.getElementById('salaryMemberName').value = memberName;
            document.getElementById('currentSalary').value = memberSalary;
            
            openModal('salaryModal');
        });
    });
    
    // Open Remove Modal
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function() {
            const memberId = this.getAttribute('data-id');
            const memberName = this.getAttribute('data-member');
            
            document.getElementById('removeManagerId').value = memberId;
            document.getElementById('removeMemberName').textContent = memberName;
            
            openModal('removeModal');
        });
    });
    
    // Toggle transfer to field
    document.getElementById('transferCheckbox').addEventListener('change', function() {
        const transferField = document.getElementById('transferToField');
        if (this.checked) {
            transferField.classList.remove('hidden');
        } else {
            transferField.classList.add('hidden');
        }
    });
    


</script>
</body>
</html>
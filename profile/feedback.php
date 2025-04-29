<?php
session_start();
include "connection.php";

// Get logged-in manager's info from session
$Manager_id = $_SESSION['mag_id'];
$Company_id = $_SESSION['cmp_id'];
$Company_name = $_SESSION['cmp_name'];
$Company_descp = $_SESSION['cmp_descp'];
$Company_mail = $_SESSION['cmp_mail'];
$Company_phone = $_SESSION['cmp_phone'];
$Company_address = $_SESSION['cmp_address'];
$Company_pincode = $_SESSION['cmp_pincode'];
$ManagerProfile = $_SESSION['mag_profile'];

// Handle adding remarks and resolving feedback
$message = "";
if (isset($_POST['submit_remark'])) {
    $feedback_id = $_POST['feedback_id'];
    $feedback_remark = $_POST['feedback_remark'];
    
    // Update the feedback record with remark and set as resolved
    $update_query = "UPDATE emp_feedback 
                    SET EF_remark = '$feedback_remark', EF_resolved = 1 
                    WHERE EF_id = '$feedback_id' AND EF_mag_id = '$Manager_id'";
    
    $result = mysqli_query($con, $update_query);
    
    if ($result) {
        $message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Success!</p>
                        <p>Your response has been submitted and the feedback has been marked as resolved.</p>
                    </div>';
    } else {
        $message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Error!</p>
                        <p>Failed to submit response. Error: ' . mysqli_error($con) . '</p>
                    </div>';
    }
}

// Fetch employee feedback assigned to this manager
$feedback_query = "SELECT ef.*, e.emp_name, e.emp_profile, e.emp_mail, e.emp_phone 
                  FROM emp_feedback ef
                  JOIN employee e ON ef.EF_emp_id = e.emp_id
                  WHERE ef.EF_mag_id = '$Manager_id' 
                  ORDER BY ef.EF_resolved ASC, ef.EF_id DESC";
$feedback_result = mysqli_query($con, $feedback_query);

// Count statistics
$total_query = "SELECT COUNT(*) as total FROM emp_feedback WHERE EF_mag_id = '$Manager_id'";
$total_result = mysqli_query($con, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_feedback = $total_row['total'];

$resolved_query = "SELECT COUNT(*) as resolved FROM emp_feedback WHERE EF_mag_id = '$Manager_id' AND EF_resolved = 1";
$resolved_result = mysqli_query($con, $resolved_query);
$resolved_row = mysqli_fetch_assoc($resolved_result);
$resolved_feedback = $resolved_row['resolved'];

// Calculate pending feedback
$pending_feedback = $total_feedback - $resolved_feedback;

// Calculate resolution rate
$resolution_rate = ($total_feedback > 0) ? round(($resolved_feedback / $total_feedback) * 100) : 0;

// Get priority statistics
$priority_query = "SELECT EF_priority, COUNT(*) as count FROM emp_feedback WHERE EF_mag_id = '$Manager_id' GROUP BY EF_priority";
$priority_result = mysqli_query($con, $priority_query);
$priority_stats = array(
    'Critical' => 0,
    'High' => 0,
    'Medium' => 0,
    'Low' => 0
);

while ($row = mysqli_fetch_assoc($priority_result)) {
    $priority_stats[$row['EF_priority']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Employee Feedback</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="dashboard.css">
  <script src="dashboard.js" defer></script>
  <link rel="stylesheet" href="../src/output.css">
  
  <style>
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

    /* Active state for sidebar links */
    .sidebar-link.active {
      background-color: rgba(239, 68, 68, 0.1);
      border-left: 3px solid #ef4444;
      color: white;
    }
    
    /* Input focus styles */
    .input-focus:focus {
      border-color: #ef4444;
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
    }
    
    /* Status badge styles */
    .status-resolved {
      background-color: #10B981;
    }
    .status-pending {
      background-color: #F59E0B;
    }
    
    /* Employee profile image */
    .profile-img {
      height: 40px;
      width: 40px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #ef4444;
    }
    
    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.7);
    }
    
    .modal-content {
      margin: 10% auto;
      width: 60%;
      max-width: 600px;
      animation: modalFade 0.3s ease;
    }
    
    @keyframes modalFade {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    
    /* Tabs */
    .tab-active {
      color: white;
      border-bottom: 3px solid #ef4444;
    }
    
    /* Priority colors */
    .priority-critical { background-color: #ef4444; }
    .priority-high { background-color: #f97316; }
    .priority-medium { background-color: #f59e0b; }
    .priority-low { background-color: #3b82f6; }
    
    /* Progress bar animation */
    @keyframes progress {
      0% { width: 0; }
      100% { width: 100%; }
    }

    /* Loading animation */
    @keyframes pulse {
        0% { opacity: 0.8; }
        50% { opacity: 1; }
        100% { opacity: 0.8; }
    }
    
    .pulse-text {
        animation: pulse 2s infinite;
    }
  </style>
</head>

<body class="bg-gradient-custom text-white min-h-screen font-sans antialiased">

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
              <a href="team.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Team">
                <i class="fas fa-users mr-3"></i>
                <span>Team</span>
              </a>
            </li>
            <li>
              <a href="feedback.php" class="sidebar-link active flex items-center p-3 rounded text-white" data-title="Feedback">
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

    <!-- Main Content Area -->
    <main class="md:ml-64 lg:ml-64 flex-1 p-4 md:p-6 transition-all">
      <button class="menu-toggle md:hidden mb-4 bg-gray-800 p-2 rounded">
          <i class="fas fa-bars"></i>
      </button>

      <!-- Page Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
          <!-- Left Side: Heading -->
          <div class="flex flex-col gap-2">
              <h1 class="text-2xl md:text-3xl font-bold">Employee Feedback Management</h1>
              <p class="text-gray-400" id="currentDateTime">Loading date...</p>
              <p class="text-gray-400">
                  Review and respond to feedback from your team members
              </p>
          </div>

          <!-- Right Side: Profile -->
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
        
      <!-- Display message if any -->
      <?php echo $message; ?>
        
      <!-- Dashboard Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-gray-800 rounded-xl p-4 shadow-lg card-hover">
              <div class="flex items-center">
                  <div class="bg-blue-500 bg-opacity-25 rounded-full p-3 mr-4">
                      <i class="fas fa-comments text-blue-500 text-xl"></i>
                  </div>
                  <div>
                      <h3 class="text-gray-400 text-sm">Total Feedback</h3>
                      <p class="text-2xl font-bold"><?php echo $total_feedback; ?></p>
                  </div>
              </div>
          </div>
          
          <div class="bg-gray-800 rounded-xl p-4 shadow-lg card-hover">
              <div class="flex items-center">
                  <div class="bg-green-500 bg-opacity-25 rounded-full p-3 mr-4">
                      <i class="fas fa-check-circle text-green-500 text-xl"></i>
                  </div>
                  <div>
                      <h3 class="text-gray-400 text-sm">Resolved</h3>
                      <p class="text-2xl font-bold"><?php echo $resolved_feedback; ?></p>
                  </div>
              </div>
          </div>
          
          <div class="bg-gray-800 rounded-xl p-4 shadow-lg card-hover">
              <div class="flex items-center">
                  <div class="bg-yellow-500 bg-opacity-25 rounded-full p-3 mr-4">
                      <i class="fas fa-clock text-yellow-500 text-xl"></i>
                  </div>
                  <div>
                      <h3 class="text-gray-400 text-sm">Pending</h3>
                      <p class="text-2xl font-bold"><?php echo $pending_feedback; ?></p>
                  </div>
              </div>
          </div>
          
          <div class="bg-gray-800 rounded-xl p-4 shadow-lg card-hover">
              <div class="flex items-center">
                  <div class="bg-red-500 bg-opacity-25 rounded-full p-3 mr-4">
                      <i class="fas fa-chart-pie text-red-500 text-xl"></i>
                  </div>
                  <div>
                      <h3 class="text-gray-400 text-sm">Resolution Rate</h3>
                      <p class="text-2xl font-bold"><?php echo $resolution_rate; ?>%</p>
                  </div>
              </div>
              <div class="w-full bg-gray-700 rounded-full h-2 mt-3">
                  <div class="bg-red-500 h-2 rounded-full" style="width: <?php echo $resolution_rate; ?>%"></div>
              </div>
          </div>
      </div>
      
      <!-- Priority Distribution -->
      <div class="bg-gray-800 p-6 rounded-xl shadow-lg mb-6">
          <h3 class="text-xl font-semibold mb-4">Feedback Priority Distribution</h3>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="bg-gray-900 p-4 rounded-lg">
                  <div class="flex justify-between mb-2">
                      <span class="text-gray-400">Critical</span>
                      <span class="px-2 py-1 rounded-full text-xs bg-red-600"><?php echo $priority_stats['Critical']; ?></span>
                  </div>
                  <div class="w-full bg-gray-700 rounded-full h-2">
                      <div class="bg-red-600 h-2 rounded-full" style="width: <?php echo ($total_feedback > 0) ? ($priority_stats['Critical'] / $total_feedback) * 100 : 0; ?>%"></div>
                  </div>
              </div>
              <div class="bg-gray-900 p-4 rounded-lg">
                  <div class="flex justify-between mb-2">
                      <span class="text-gray-400">High</span>
                      <span class="px-2 py-1 rounded-full text-xs bg-orange-500"><?php echo $priority_stats['High']; ?></span>
                  </div>
                  <div class="w-full bg-gray-700 rounded-full h-2">
                      <div class="bg-orange-500 h-2 rounded-full" style="width: <?php echo ($total_feedback > 0) ? ($priority_stats['High'] / $total_feedback) * 100 : 0; ?>%"></div>
                  </div>
              </div>
              <div class="bg-gray-900 p-4 rounded-lg">
                  <div class="flex justify-between mb-2">
                      <span class="text-gray-400">Medium</span>
                      <span class="px-2 py-1 rounded-full text-xs bg-yellow-500"><?php echo $priority_stats['Medium']; ?></span>
                  </div>
                  <div class="w-full bg-gray-700 rounded-full h-2">
                      <div class="bg-yellow-500 h-2 rounded-full" style="width: <?php echo ($total_feedback > 0) ? ($priority_stats['Medium'] / $total_feedback) * 100 : 0; ?>%"></div>
                  </div>
              </div>
              <div class="bg-gray-900 p-4 rounded-lg">
                  <div class="flex justify-between mb-2">
                      <span class="text-gray-400">Low</span>
                      <span class="px-2 py-1 rounded-full text-xs bg-blue-600"><?php echo $priority_stats['Low']; ?></span>
                  </div>
                  <div class="w-full bg-gray-700 rounded-full h-2">
                      <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo ($total_feedback > 0) ? ($priority_stats['Low'] / $total_feedback) * 100 : 0; ?>%"></div>
                  </div>
              </div>
          </div>
      </div>
      
      <!-- Feedback Tabs & List -->
      <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
          <!-- Tabs -->
          <div class="flex border-b border-gray-700">
              <button class="tab-btn tab-active px-6 py-3 focus:outline-none" data-tab="all">
                  All Feedback
              </button>
              <button class="tab-btn px-6 py-3 text-white focus:outline-none" data-tab="pending">
                  Pending <span class="ml-2 px-2 py-1 text-xs bg-yellow-500 rounded-full"><?php echo $pending_feedback; ?></span>
              </button>
              <button class="tab-btn px-6 py-3 text-white focus:outline-none" data-tab="resolved">
                  Resolved <span class="ml-2 px-2 py-1 text-xs bg-green-500 rounded-full"><?php echo $resolved_feedback; ?></span>
              </button>
          </div>
          
          <!-- Feedback List -->
          <div class="p-4">
              <?php if (mysqli_num_rows($feedback_result) > 0): ?>
                  <div class="overflow-x-auto">
                      <table class="min-w-full">
                          <thead class="bg-gray-900">
                              <tr>
                                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Employee</th>
                                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Issue</th>
                                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Priority</th>
                                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Action</th>
                              </tr>
                          </thead>
                          <tbody class="divide-y divide-gray-700">
                              <?php while ($row = mysqli_fetch_assoc($feedback_result)): ?>
                                  <tr class="feedback-row <?php echo ($row['EF_resolved'] == 1) ? 'resolved' : 'pending'; ?> hover:bg-gray-750">
                                      <td class="px-4 py-3 whitespace-nowrap">
                                          <div class="flex items-center">
                                              <?php if (!empty($row['emp_profile']) && file_exists($row['emp_profile'])): ?>
                                                  <img src="<?php echo htmlspecialchars($row['emp_profile']); ?>" alt="Employee" class="profile-img mr-3" />
                                              <?php else: ?>
                                                  <img src="../images/Profile/guest.png" alt="Employee" class="profile-img mr-3" />
                                              <?php endif; ?>
                                              <div>
                                                  <div class="font-medium"><?php echo htmlspecialchars($row['emp_name']); ?></div>
                                                  <div class="text-xs text-gray-400"><?php echo htmlspecialchars($row['emp_mail']); ?></div>
                                              </div>
                                          </div>
                                      </td>
                                      <td class="px-4 py-3">
                                          <?php echo htmlspecialchars($row['EF_type']); ?>
                                      </td>
                                      <td class="px-4 py-3">
                                          <div class="max-w-xs truncate">
                                              <?php echo htmlspecialchars($row['EF_issue']); ?>
                                          </div>
                                      </td>
                                      <td class="px-4 py-3">
                                          <?php
                                              $priorityClass = "";
                                              switch ($row['EF_priority']) {
                                                  case 'Low':
                                                      $priorityClass = "priority-low";
                                                      break;
                                                  case 'Medium':
                                                      $priorityClass = "priority-medium";
                                                      break;
                                                  case 'High':
                                                      $priorityClass = "priority-high";
                                                      break;
                                                  case 'Critical':
                                                      $priorityClass = "priority-critical";
                                                      break;
                                              }
                                          ?>
                                          <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $priorityClass; ?>">
                                              <?php echo htmlspecialchars($row['EF_priority']); ?>
                                          </span>
                                      </td>
                                      <td class="px-4 py-3">
                                          <?php if ($row['EF_resolved'] == 1): ?>
                                              <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full status-resolved">
                                                  Resolved
                                              </span>
                                          <?php else: ?>
                                              <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full status-pending">
                                                  Pending
                                              </span>
                                          <?php endif; ?>
                                      </td>
                                      <td class="px-4 py-3">
                                          <button class="view-details bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm" 
                                                  data-feedback-id="<?php echo $row['EF_id']; ?>"
                                                  data-emp-name="<?php echo htmlspecialchars($row['emp_name']); ?>"
                                                  data-emp-email="<?php echo htmlspecialchars($row['emp_mail']); ?>"
                                                  data-emp-phone="<?php echo htmlspecialchars($row['emp_phone']); ?>"
                                                  data-feedback-type="<?php echo htmlspecialchars($row['EF_type']); ?>"
                                                  data-feedback-issue="<?php echo htmlspecialchars($row['EF_issue']); ?>"
                                                  data-feedback-priority="<?php echo htmlspecialchars($row['EF_priority']); ?>"
                                                  data-feedback-resolved="<?php echo $row['EF_resolved']; ?>"
                                                  data-feedback-remark="<?php echo htmlspecialchars($row['EF_remark']); ?>"
                                                  data-emp-profile="<?php echo !empty($row['emp_profile']) && file_exists($row['emp_profile']) ? htmlspecialchars($row['emp_profile']) : '../images/Profile/guest.png'; ?>">
                                              <i class="fas fa-eye mr-1"></i> View
                                          </button>
                                      </td>
                                  </tr>
                              <?php endwhile; ?>
                          </tbody>
                      </table>
                  </div>
              <?php else: ?>
                  <div class="text-center py-10">
                      <div class="mb-4 text-gray-400">
                          <i class="fas fa-comment-slash text-4xl"></i>
                      </div>
                      <h3 class="text-lg font-medium mb-2">No feedback found</h3>
                      <p class="text-gray-400">There are no employee feedback records assigned to you.</p>
                  </div>
              <?php endif; ?>
          </div>
      </div>
      
      <!-- Feedback Details Modal -->
      <div id="feedbackModal" class="modal">
          <div class="modal-content bg-gray-800 rounded-xl shadow-2xl p-0 overflow-hidden">
              <!-- Modal Header -->
              <div class="bg-gray-900 p-4 flex justify-between items-center">
                  <h3 class="text-lg font-semibold" id="modalTitle">Feedback Details</h3>
                  <button id="closeModal" class="text-gray-400 hover:text-white">
                      <i class="fas fa-times"></i>
                  </button>
              </div>
              
              <!-- Modal Body -->
              <div class="p-6">
                  <!-- Employee Info -->
                  <div class="flex items-center mb-6 bg-gray-750 p-4 rounded-lg">
                      <img id="modalEmpProfile" src="../images/Profile/guest.png" alt="Employee" class="h-16 w-16 rounded-full border-2 border-red-500 mr-4" />
                      <div>
                          <h4 id="modalEmpName" class="text-lg font-semibold">Employee Name</h4>
                          <div class="text-gray-400 flex items-center mt-1">
                              <i class="fas fa-envelope mr-2"></i>
                              <span id="modalEmpEmail">employee@example.com</span>
                          </div>
                          <div class="text-gray-400 flex items-center mt-1">
                              <i class="fas fa-phone mr-2"></i>
                              <span id="modalEmpPhone">+1234567890</span>
                          </div>
                      </div>
                  </div>
                  
                  <!-- Feedback Details -->
                <div class="bg-gray-750 p-4 rounded-lg mb-6">
                    <div class="mb-4">
                        <span class="text-gray-400">Feedback Type:</span>
                        <span id="modalFeedbackType" class="font-medium ml-2">Technical Issue</span>
                    </div>
                    <div class="mb-4">
                        <span class="text-gray-400">Priority:</span>
                        <span id="modalFeedbackPriority" class="font-medium ml-2 px-2 py-1 text-xs rounded-full">High</span>
                    </div>
                    <div class="mb-4">
                        <span class="text-gray-400">Status:</span>
                        <span id="modalFeedbackStatus" class="font-medium ml-2 px-2 py-1 text-xs rounded-full">Pending</span>
                    </div>
                    <div>
                        <h5 class="text-gray-400 mb-2">Issue Description:</h5>
                        <div id="modalFeedbackIssue" class="bg-gray-900 p-3 rounded">
                            Feedback issue details will appear here.
                        </div>
                    </div>
                </div>

                <!-- Response Form (shown only if feedback is pending) -->
                <div id="responseForm" class="border-t border-gray-700 pt-4">
                    <h4 class="text-lg font-semibold mb-4">Provide Your Response</h4>
                    <form method="POST" action="">
                        <input type="hidden" id="modalFeedbackId" name="feedback_id" value="">
                        <div class="mb-4">
                            <label for="feedback_remark" class="block text-gray-400 mb-2">Your Remarks:</label>
                            <textarea name="feedback_remark" id="feedback_remark" rows="4" class="w-full bg-gray-900 border border-gray-700 rounded p-3 text-white focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50" placeholder="Enter your response to this feedback..."></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" name="submit_remark" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                Resolve Feedback
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Previous Response (shown only if feedback is resolved) -->
                <div id="previousResponse" class="border-t border-gray-700 pt-4" style="display: none;">
                    <h4 class="text-lg font-semibold mb-4">Your Response</h4>
                    <div class="bg-gray-900 p-4 rounded">
                        <div id="modalFeedbackRemark" class="text-gray-300">
                            Previous response will appear here.
                        </div>
                        <div class="text-gray-400 text-sm mt-3">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            Marked as resolved
                        </div>
                    </div>
                </div>


                </div>
          </div>
      </div>
      
      <!-- Footer -->
      <footer class="mt-8 text-center text-sm text-gray-500 pb-4">
          <p>&copy; 2024 CryBug - Bug and Feedback Management System. All rights reserved.</p>
      </footer>
      
    </main>
  </div>

  <script>
    
    
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const feedbackRows = document.querySelectorAll('.feedback-row');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Set active tab
            tabBtns.forEach(b => {
                b.classList.remove('tab-active');
                b.classList.add('text-gray-400');
            });
            btn.classList.add('tab-active');
            btn.classList.remove('text-gray-400');
            
            // Filter rows
            const tab = btn.getAttribute('data-tab');
            feedbackRows.forEach(row => {
                if (tab === 'all' || 
                    (tab === 'pending' && row.classList.contains('pending')) || 
                    (tab === 'resolved' && row.classList.contains('resolved'))) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Modal functionality
    const modal = document.getElementById('feedbackModal');
    const closeModal = document.getElementById('closeModal');
    const viewButtons = document.querySelectorAll('.view-details');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Set modal content from data attributes
            document.getElementById('modalEmpName').textContent = button.getAttribute('data-emp-name');
            document.getElementById('modalEmpEmail').textContent = button.getAttribute('data-emp-email');
            document.getElementById('modalEmpPhone').textContent = button.getAttribute('data-emp-phone');
            document.getElementById('modalEmpProfile').src = button.getAttribute('data-emp-profile');
            document.getElementById('modalFeedbackType').textContent = button.getAttribute('data-feedback-type');
            document.getElementById('modalFeedbackIssue').textContent = button.getAttribute('data-feedback-issue');
            document.getElementById('modalFeedbackId').value = button.getAttribute('data-feedback-id');
            
            // Set priority with appropriate class
            const priority = button.getAttribute('data-feedback-priority');
            const prioritySpan = document.getElementById('modalFeedbackPriority');
            prioritySpan.textContent = priority;
            prioritySpan.className = "font-medium ml-2 px-2 py-1 text-xs rounded-full";
            
            switch (priority) {
                case 'Critical':
                    prioritySpan.classList.add('priority-critical');
                    break;
                case 'High':
                    prioritySpan.classList.add('priority-high');
                    break;
                case 'Medium':
                    prioritySpan.classList.add('priority-medium');
                    break;
                case 'Low':
                    prioritySpan.classList.add('priority-low');
                    break;
            }
            
            // Set status and show appropriate form
            const resolved = button.getAttribute('data-feedback-resolved') === '1';
            const statusSpan = document.getElementById('modalFeedbackStatus');
            statusSpan.textContent = resolved ? 'Resolved' : 'Pending';
            statusSpan.className = "font-medium ml-2 px-2 py-1 text-xs rounded-full";
            statusSpan.classList.add(resolved ? 'status-resolved' : 'status-pending');
            
            // Show/hide appropriate sections
            document.getElementById('responseForm').style.display = resolved ? 'none' : 'block';
            document.getElementById('previousResponse').style.display = resolved ? 'block' : 'none';
            
            if (resolved) {
                document.getElementById('modalFeedbackRemark').textContent = button.getAttribute('data-feedback-remark');
            }
            
            // Show modal
            modal.style.display = 'block';
        });
    });
    
    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    
  </script>
</body>
</html>
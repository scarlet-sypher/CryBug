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

$ManagerProfile = $_SESSION['mag_profile'] ;

// Insert feedback when form is submitted
$message = "";
if (isset($_POST['submit_feedback'])) {
    $feedback_type = $_POST['feedback_type'];
    $feedback_issue = $_POST['feedback_issue'];
    $feedback_priority = $_POST['feedback_priority'];
    // $feedback_remark = $_POST['feedback_remark'];
    
    // Insert feedback using normal query
    $insert_query = "INSERT INTO manager_feedback 
                    (MF_mag_id, MF_cmp_id, MF_type, MF_issue, MF_priority, MF_is_resolved) 
                    VALUES 
                    ('$Manager_id', '$Company_id', '$feedback_type', '$feedback_issue', '$feedback_priority', 0)";
    
    $result = mysqli_query($con, $insert_query);
    
    if ($result) {
        $message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Success!</p>
                        <p>Your feedback has been submitted successfully.</p>
                    </div>';
    } else {
        $message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Error!</p>
                        <p>Failed to submit feedback. Error: ' . mysqli_error($con) . '</p>
                    </div>';
    }
}

// Handle feedback deletion (without redirection)
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    
    // Only delete unresolved feedback
    $delete_query = "DELETE FROM manager_feedback WHERE MF_id = '$delete_id' AND MF_mag_id = '$Manager_id' AND MF_is_resolved = 0";
    $delete_result = mysqli_query($con, $delete_query);
    
    if ($delete_result) {
        $message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                      <p class="font-bold">Success!</p>
                      <p>Feedback successfully deleted.</p>
                    </div>';
    } else {
        $message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                      <p class="font-bold">Error!</p>
                      <p>Failed to delete feedback. Error: ' . mysqli_error($con) . '</p>
                    </div>';
    }
}

// Fetch previous feedback from this manager
$feedback_query = "SELECT * FROM manager_feedback 
                  WHERE MF_mag_id = '$Manager_id' 
                  ORDER BY MF_id DESC";
$feedback_result = mysqli_query($con, $feedback_query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Manager Feedback</title>
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

    .faq-content {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
    }

    .faq-toggle:hover {
        background-color: rgba(239, 68, 68, 0.1);
    }

    /* Add a subtle pulse animation to the FAQ header */
    @keyframes pulse {
        0% { opacity: 0.8; }
        50% { opacity: 1; }
        100% { opacity: 0.8; }
    }

    .bg-clip-text {
        animation: pulse 2s infinite;
    }

    .brr {

      border-left-width: 4px;
      border-left-color: #f97316;
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
            <a href="help.php" class="flex items-center p-3 active rounded text-gray-300 hover:text-white" data-title="Help Center">
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
              <h1 class="text-2xl md:text-3xl font-bold">Manager Feedback</h1>
              <p class="text-gray-400" id="currentDateTime">Loading date...</p>
              <p class="text-gray-400">
                  Submit and manage your feedback for <?php echo htmlspecialchars($Company_name); ?>
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

        
        <!-- Display message if any -->
        <?php echo $message; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Feedback Form -->
            <div class="col-span-1 md:col-span-1">
                <div class="bg-gray-800 p-6 rounded-xl shadow-lg card-hover mb-6">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-comment-dots text-red-500 mr-2"></i> Submit Feedback
                    </h3>
                    
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="block text-gray-400 text-sm font-medium mb-2">Feedback Type</label>
                            <select name="feedback_type" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 input-focus focus:outline-none" required>
                                <option value="Bug">Bug Report</option>
                                <option value="Feature">Feature Request</option>
                                <option value="Improvement">Improvement Suggestion</option>
                                <option value="Complaint">Complaint</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-400 text-sm font-medium mb-2">Issue Description</label>
                            <textarea name="feedback_issue" placeholder="Describe the issue in detail" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 input-focus focus:outline-none" rows="4" required></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-400 text-sm font-medium mb-2">Priority</label>
                            <select name="feedback_priority" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 input-focus focus:outline-none" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>      
                        <div class="flex justify-end">
                            <button type="submit" name="submit_feedback" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Manager Information -->
                <div class="bg-gray-800 p-6 rounded-xl shadow-lg card-hover">
                  <h3 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-address-card text-red-500 mr-2"></i> Contact Information
                  </h3>

                  <div class="flex items-start mb-5">
                      <div class="flex-shrink-0 bg-gray-700 p-2 rounded-full">
                        <i class="fas fa-building text-red-500"></i>
                      </div>
                      <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-300"><?php echo htmlspecialchars($Company_name) ; ?></p>
                        <p class="text-md"><?php echo htmlspecialchars($Company_descp) ; ?></p>
                      </div>
                    </div>

                  <div class="space-y-4">
                    <div class="flex items-start">
                      <div class="flex-shrink-0 bg-gray-700 p-2 rounded-full">
                        <i class="fas fa-phone text-red-500"></i>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Support Hotline</p>
                        <p class="text-lg"><?php echo htmlspecialchars($Company_phone) ; ?></p>
                        <p class="text-xs text-gray-400">Available 24/7</p>
                      </div>
                    </div>
                    
                    <div class="flex items-start">
                      <div class="flex-shrink-0 bg-gray-700 p-2 rounded-full">
                        <i class="fas fa-envelope text-red-500"></i>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Email Support</p>
                        <p class="text-lg"><?php echo htmlspecialchars($Company_mail) ; ?></p>
                        <p class="text-xs text-gray-400">Response within 24 hours</p>
                      </div>
                    </div>
                    
                    <div class="flex items-start">
                      <div class="flex-shrink-0 bg-gray-700 p-2 rounded-full">
                        <i class="fas fa-map-marker-alt text-red-500"></i>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Office Address</p>
                        <p class="text-lg"><?php echo htmlspecialchars($Company_name) ; ?> Inc.</p>
                        <p><?php echo htmlspecialchars($Company_address) ; ?></p>
                        <p><?php echo htmlspecialchars($Company_pincode) ; ?></p>
                      </div>
                    </div>
                    
                    <div class="flex items-start">
                      <div class="flex-shrink-0 bg-gray-700 p-2 rounded-full">
                        <i class="fas fa-clock text-red-500"></i>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Business Hours</p>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM (PST)</p>
                        <p>Saturday - Sunday: Closed</p>
                      </div>
                    </div>
                  </div>
                </div>
                </div>
            
            
            <!-- Previous Feedback -->
            <div class="col-span-1 md:col-span-2">
                <div class="bg-gray-800 p-6 rounded-xl shadow-lg card-hover">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-history text-red-500 mr-2"></i> Your Previous Feedback
                    </h3>
                    
                    <?php if (mysqli_num_rows($feedback_result) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-gray-900 rounded-lg overflow-hidden">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Issue</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Priority</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Resolution Remarks</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                <?php while ($row = mysqli_fetch_assoc($feedback_result)): ?>
                                    <tr class="hover:bg-gray-800">
                                        
                                        <td class="px-4 py-3 whitespace-nowrap"><?php echo htmlspecialchars($row['MF_type']); ?></td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm truncate max-w-xs">
                                                <?php echo htmlspecialchars($row['MF_issue']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <?php
                                                $priorityClass = "";
                                                switch ($row['MF_priority']) {
                                                    case 'Low':
                                                        $priorityClass = "bg-blue-600";
                                                        break;
                                                    case 'Medium':
                                                        $priorityClass = "bg-yellow-400";
                                                        break;
                                                    case 'High':
                                                        $priorityClass = "bg-orange-500";
                                                        break;
                                                    case 'Critical':
                                                        $priorityClass = "bg-red-600";
                                                        break;
                                                }
                                            ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $priorityClass; ?>">
                                                <?php echo htmlspecialchars($row['MF_priority']); ?>
                                            </span>
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <?php if ($row['MF_is_resolved'] == 1): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-resolved">
                                                    Resolved
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-pending">
                                                    Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-4 py-3">
                                            <?php if ($row['MF_is_resolved'] == 1): ?>
                                                <div class="text-sm text-gray-300">
                                                    <?php echo htmlspecialchars($row['MF_remark']); ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-500 italic">Pending resolution</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <?php if ($row['MF_is_resolved'] == 0): ?>
                                                <!-- Only show delete option for unresolved feedback -->
                                                <form method="POST" action="" style="display: inline;">
                                                    <input type="hidden" name="delete_id" value="<?php echo $row['MF_id']; ?>">
                                                    <button type="submit" class="text-red-500 hover:text-red-400" title="Delete Feedback" onclick="return confirm('Are you sure you want to delete this feedback?');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="bg-gray-900 p-6 rounded-lg text-center">
                            <i class="fas fa-comment-slash text-red-500 text-4xl mb-3"></i>
                            <p class="text-lg">You haven't submitted any feedback yet.</p>
                            <p class="text-gray-400 mt-2">Use the form on the left to submit your first feedback.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Feedback Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <?php
                        // Count total feedback
                        $total_query = "SELECT COUNT(*) as total FROM manager_feedback WHERE MF_mag_id = '$Manager_id'";
                        $total_result = mysqli_query($con, $total_query);
                        $total_row = mysqli_fetch_assoc($total_result);
                        $total_feedback = $total_row['total'];
                        
                        // Count resolved feedback
                        $resolved_query = "SELECT COUNT(*) as resolved FROM manager_feedback WHERE MF_mag_id = '$Manager_id' AND MF_is_resolved = 1";
                        $resolved_result = mysqli_query($con, $resolved_query);
                        $resolved_row = mysqli_fetch_assoc($resolved_result);
                        $resolved_feedback = $resolved_row['resolved'];
                        
                        // Calculate pending feedback
                        $pending_feedback = $total_feedback - $resolved_feedback;
                        
                        // Calculate resolution rate
                        $resolution_rate = ($total_feedback > 0) ? round(($resolved_feedback / $total_feedback) * 100) : 0;
                    ?>
                    
                    <div class="bg-gray-800 p-4 rounded-xl shadow-lg card-hover text-center">
                        <div class="text-4xl font-bold text-red-500"><?php echo $total_feedback; ?></div>
                        <p class="text-gray-400 mt-1">Total Feedback</p>
                    </div>
                    
                    <div class="bg-gray-800 p-4 rounded-xl shadow-lg card-hover text-center">
                        <div class="text-4xl font-bold text-green-500"><?php echo $resolved_feedback; ?></div>
                        <p class="text-gray-400 mt-1">Resolved</p>
                    </div>
                    
                    <div class="bg-gray-800 p-4 rounded-xl shadow-lg card-hover text-center">
                        <div class="text-4xl font-bold text-yellow-500"><?php echo $pending_feedback; ?></div>
                        <p class="text-gray-400 mt-1">Pending</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced FAQ Section -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-xl shadow-lg card-hover mt-6 border border-gray-700">
            <h3 class="text-xl font-semibold mb-6 flex items-center">
                <i class="fas fa-question-circle text-red-500 mr-2"></i> 
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-red-400 to-red-600">Frequently Asked Questions</span>
            </h3>
            
            <div class="space-y-4" id="faq-accordion">
                <!-- FAQ Item 1 -->
                <div class="bg-gray-800 bg-opacity-50 rounded-lg overflow-hidden border border-gray-700 hover:border-red-500 transition-all duration-300">
                    <button class="faq-toggle w-full flex justify-between items-center p-4 text-left focus:outline-none" data-target="faq-1">
                        <span class="font-medium flex items-center">
                            <i class="fas fa-clock text-red-500 mr-3 w-5"></i>
                            <span>How long does it take to get a response to my feedback?</span>
                        </span>
                        <i class="fas fa-chevron-down text-red-400 transition-transform duration-300"></i>
                    </button>
                    <div id="faq-1" class="faq-content hidden px-6 pb-5 pt-2 border-t border-gray-700">
                        <p class="text-gray-300 leading-relaxed">
                            Our team typically reviews and responds to all feedback within <span class="text-red-400 font-medium">48-72 business hours</span>. 
                            Critical priority feedback is usually addressed within <span class="text-red-400 font-medium">24 hours</span>. 
                            You can always check the status of your feedback in the "Previous Feedback" table.
                        </p>
                        <div class="flex items-center mt-3 text-sm text-gray-400">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            <span>Pro tip: Use appropriate priority levels to ensure timely responses.</span>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="bg-gray-800 bg-opacity-50 rounded-lg overflow-hidden border border-gray-700 hover:border-red-500 transition-all duration-300">
                    <button class="faq-toggle w-full flex justify-between items-center p-4 text-left focus:outline-none" data-target="faq-2">
                        <span class="font-medium flex items-center">
                            <i class="fas fa-edit text-red-500 mr-3 w-5"></i>
                            <span>Can I edit my feedback after submitting it?</span>
                        </span>
                        <i class="fas fa-chevron-down text-red-400 transition-transform duration-300"></i>
                    </button>
                    <div id="faq-2" class="faq-content hidden px-6 pb-5 pt-2 border-t border-gray-700">
                        <p class="text-gray-300 leading-relaxed">
                            Currently, you cannot edit feedback after it's been submitted. However, you can <span class="text-red-400 font-medium">delete unresolved feedback</span> and submit a new one with the updated information. Once feedback has been marked as resolved, it cannot be deleted or modified.
                        </p>
                        <div class="flex justify-between items-center mt-4 bg-gray-900 p-3 rounded-lg text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-trash-alt text-red-500 mr-2"></i>
                                <span>Unresolved</span>
                            </div>
                            <span class="text-green-500">Can delete and resubmit</span>
                        </div>
                        <div class="flex justify-between items-center mt-2 bg-gray-900 p-3 rounded-lg text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-lock text-gray-500 mr-2"></i>
                                <span>Resolved</span>
                            </div>
                            <span class="text-gray-500">Cannot be modified</span>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="bg-gray-800 bg-opacity-50 rounded-lg overflow-hidden border border-gray-700 hover:border-red-500 transition-all duration-300">
                    <button class="faq-toggle w-full flex justify-between items-center p-4 text-left focus:outline-none" data-target="faq-3">
                        <span class="font-medium flex items-center">
                            <i class="fas fa-bolt text-red-500 mr-3 w-5"></i>
                            <span>What should I include in my feedback for fastest resolution?</span>
                        </span>
                        <i class="fas fa-chevron-down text-red-400 transition-transform duration-300"></i>
                    </button>
                    <div id="faq-3" class="faq-content hidden px-6 pb-5 pt-2 border-t border-gray-700">
                        <p class="text-gray-300 leading-relaxed mb-3">For the quickest resolution, please include:</p>
                        <div class="space-y-2">
                            <div class="flex items-start bg-gray-900 p-3 rounded-lg">
                                <div class="mr-3 mt-1 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs">1</div>
                                <div>
                                    <p class="font-medium text-white">Specific details about the issue or suggestion</p>
                                    <p class="text-sm text-gray-400">Be clear and concise about what you're experiencing</p>
                                </div>
                            </div>
                            <div class="flex items-start bg-gray-900 p-3 rounded-lg">
                                <div class="mr-3 mt-1 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs">2</div>
                                <div>
                                    <p class="font-medium text-white">Steps to reproduce the problem (for bugs)</p>
                                    <p class="text-sm text-gray-400">Provide a clear sequence of actions that cause the issue</p>
                                </div>
                            </div>
                            <div class="flex items-start bg-gray-900 p-3 rounded-lg">
                                <div class="mr-3 mt-1 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs">3</div>
                                <div>
                                    <p class="font-medium text-white">Error messages & visual examples</p>
                                    <p class="text-sm text-gray-400">Include any error codes or messages you encountered</p>
                                </div>
                            </div>
                            <div class="flex items-start bg-gray-900 p-3 rounded-lg">
                                <div class="mr-3 mt-1 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs">4</div>
                                <div>
                                    <p class="font-medium text-white">Appropriate priority level</p>
                                    <p class="text-sm text-gray-400">Select the right priority based on impact to your work</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="bg-gray-800 bg-opacity-50 rounded-lg overflow-hidden border border-gray-700 hover:border-red-500 transition-all duration-300">
                    <button class="faq-toggle w-full flex justify-between items-center p-4 text-left focus:outline-none" data-target="faq-4">
                        <span class="font-medium flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-3 w-5"></i>
                            <span>How do I determine the right priority level for my feedback?</span>
                        </span>
                        <i class="fas fa-chevron-down text-red-400 transition-transform duration-300"></i>
                    </button>
                    <div id="faq-4" class="faq-content hidden px-6 pb-5 pt-2 border-t border-gray-700">
                        <p class="text-gray-300 leading-relaxed mb-4">Here's a guide to selecting the appropriate priority:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-gray-900 rounded-lg p-3 border-l-4 border-red-600">
                                <div class="flex items-center mb-2">
                                    <span class="px-2 py-1 rounded-full bg-red-600 text-xs font-semibold">Critical</span>
                                </div>
                                <ul class="text-sm space-y-1 text-gray-300">
                                    <li>• System is completely unusable</li>
                                    <li>• Major functionality is blocked</li>
                                    <li>• Data loss occurred</li>
                                    <li>• Security vulnerability</li>
                                </ul>
                            </div>
                            <div class="bg-gray-900 rounded-lg p-3  border-l-4 border-orange-500 brr">
                                <div class="flex items-center mb-2">
                                    <span class="px-2 py-1 rounded-full bg-orange-500 text-xs font-semibold">High</span>
                                </div>
                                <ul class="text-sm space-y-1 text-gray-300">
                                    <li>• Significant impact to functionality</li>
                                    <li>• Workarounds are difficult</li>
                                    <li>• Affects multiple users</li>
                                    <li>• Impedes critical work</li>
                                </ul>
                            </div>
                            <div class="bg-gray-900 rounded-lg p-3 border-l-4 border-yellow-400">
                                <div class="flex items-center mb-2">
                                    <span class="px-2 py-1 rounded-full bg-yellow-400 text-xs font-semibold">Medium</span>
                                </div>
                                <ul class="text-sm space-y-1 text-gray-300">
                                    <li>• Moderate impact to work</li>
                                    <li>• Workarounds are available</li>
                                    <li>• Non-critical functionality affected</li>
                                    <li>• Important feature requests</li>
                                </ul>
                            </div>
                            <div class="bg-gray-900 rounded-lg p-3 border-l-4 border-blue-600">
                                <div class="flex items-center mb-2">
                                    <span class="px-2 py-1 rounded-full bg-blue-600 text-xs font-semibold">Low</span>
                                </div>
                                <ul class="text-sm space-y-1 text-gray-300">
                                    <li>• Minor issues or inconveniences</li>
                                    <li>• Cosmetic improvements</li>
                                    <li>• General suggestions</li>
                                    <li>• Documentation issues</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 5 -->
                <div class="bg-gray-800 bg-opacity-50 rounded-lg overflow-hidden border border-gray-700 hover:border-red-500 transition-all duration-300">
                    <button class="faq-toggle w-full flex justify-between items-center p-4 text-left focus:outline-none" data-target="faq-5">
                        <span class="font-medium flex items-center">
                            <i class="fas fa-reply text-red-500 mr-3 w-5"></i>
                            <span>Can I follow up on my submitted feedback?</span>
                        </span>
                        <i class="fas fa-chevron-down text-red-400 transition-transform duration-300"></i>
                    </button>
                    <div id="faq-5" class="faq-content hidden px-6 pb-5 pt-2 border-t border-gray-700">
                        <p class="text-gray-300 leading-relaxed">
                            Currently, there isn't a direct follow-up feature in the feedback system. For updates on existing feedback, please contact our support team via email or phone with your feedback ID.
                        </p>
                        <div class="flex items-center justify-between mt-4 bg-gray-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-blue-600 p-2 rounded-full text-white">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium">Email Support</p>
                                    <p class="text-sm text-gray-400">Include your feedback ID in the subject</p>
                                </div>
                            </div>
                            <span class="text-blue-400"><?php echo htmlspecialchars($Company_mail); ?></span>
                        </div>
                        <div class="flex items-center justify-between mt-2 bg-gray-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-green-600 p-2 rounded-full text-white">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium">Phone Support</p>
                                    <p class="text-sm text-gray-400">Available 24/7</p>
                                </div>
                            </div>
                            <span class="text-green-400"><?php echo htmlspecialchars($Company_phone); ?></span>
                        </div>
                        <div class="flex items-center mt-4 text-sm">
                            <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                            <span class="text-gray-400">We're working on implementing a comment feature in future updates.</span>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 6 -->
                <div class="bg-gray-800 bg-opacity-50 rounded-lg overflow-hidden border border-gray-700 hover:border-red-500 transition-all duration-300">
                    <button class="faq-toggle w-full flex justify-between items-center p-4 text-left focus:outline-none" data-target="faq-6">
                        <span class="font-medium flex items-center">
                            <i class="fas fa-shield-alt text-red-500 mr-3 w-5"></i>
                            <span>Is my feedback shared with other managers in the system?</span>
                        </span>
                        <i class="fas fa-chevron-down text-red-400 transition-transform duration-300"></i>
                    </button>
                    <div id="faq-6" class="faq-content hidden px-6 pb-5 pt-2 border-t border-gray-700">
                        <div class="flex mb-4">
                            <div class="flex-shrink-0 bg-green-600 rounded-full h-8 w-8 flex items-center justify-center mr-3">
                                <i class="fas fa-lock text-white"></i>
                            </div>
                            <div>
                                <p class="text-gray-300 leading-relaxed">
                                    No, your feedback is <span class="text-green-400 font-medium">private and confidential</span>. It's only visible to you and the CryBug support team. We respect your privacy and the confidentiality of the issues you report.
                                </p>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="flex-shrink-0 bg-blue-600 rounded-full h-8 w-8 flex items-center justify-center mr-3">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div>
                                <p class="text-gray-300 leading-relaxed">
                                    General insights and trends from feedback may be used to improve the system for all users, but your specific details remain confidential.
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 bg-gray-900 p-4 rounded-lg border-l-4 border-yellow-500 brr">
                            <div class="flex items-center">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                <p class="font-medium">Privacy First</p>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">
                                Feel free to provide candid feedback without concern—our privacy-first approach ensures your comments remain confidential.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


  <script>

      document.addEventListener('DOMContentLoaded', function() {
          // Get all FAQ toggle buttons
          const faqToggles = document.querySelectorAll('.faq-toggle');
          
          // Add click event listener to each toggle button
          faqToggles.forEach(toggle => {
              toggle.addEventListener('click', function() {
                  // Get the target content element
                  const targetId = this.getAttribute('data-target');
                  const targetContent = document.getElementById(targetId);
                  
                  // Toggle the content visibility with a smooth animation
                  if (targetContent.classList.contains('hidden')) {
                      // Open this FAQ item
                      targetContent.classList.remove('hidden');
                      targetContent.style.maxHeight = '0';
                      setTimeout(() => {
                          targetContent.style.maxHeight = targetContent.scrollHeight + 'px';
                      }, 10);
                      
                      // Rotate the icon
                      const icon = this.querySelector('.fa-chevron-down');
                      icon.style.transform = 'rotate(180deg)';
                      
                      // Highlight the active FAQ item
                      this.closest('.bg-gray-800').classList.add('ring-2', 'ring-red-500', 'ring-opacity-50');
                  } else {
                      // Close this FAQ item
                      targetContent.style.maxHeight = '0';
                      setTimeout(() => {
                          targetContent.classList.add('hidden');
                      }, 300);
                      
                      // Reset the icon
                      const icon = this.querySelector('.fa-chevron-down');
                      icon.style.transform = 'rotate(0deg)';
                      
                      // Remove highlight
                      this.closest('.bg-gray-800').classList.remove('ring-2', 'ring-red-500', 'ring-opacity-50');
                  }
              });
          });
      });
  </script>


</body>
</html>
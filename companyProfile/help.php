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

// Handle feedback form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_feedback'])) {
    $issueType = $_POST['issue_type'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Insert feedback into database
    $sql = "INSERT INTO admin_feedback (a_cmp_id, a_message, a_issue_type, a_is_resolved, a_email) 
            VALUES ('$companyId', '$message', '$issueType', 0, '$companyEmail')";
    
    if ($con->query($sql) === TRUE) {
        $feedbackSuccess = "Feedback submitted successfully!";
    } else {
        $feedbackError = "Error: " . $sql . "<br>" . $con->error;
    }
}

// Fetch existing feedback for this company
$feedbackQuery = "SELECT * FROM admin_feedback WHERE a_cmp_id = '$companyId' ORDER BY a_id DESC";
$feedbackResult = $con->query($feedbackQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CryBug Help Center</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="dashboard.js" defer></script>
  <link rel="stylesheet" href="../src/output.css">
  <style>
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
    
    .card-hover {
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
    }
    
    .faq-item {
      border-bottom: 1px solid rgba(75, 85, 99, 0.3);
    }
    
    .faq-question {
      transition: all 0.3s ease;
    }
    
    /* Fix for FAQ answers */
    .faq-answer {
      max-height: 0;
      overflow: hidden;
      opacity: 0;
      transition: all 0.5s ease;
    }
    
    .faq-item.active .faq-answer {
      max-height: 500px;
      opacity: 1;
    }
    
    .faq-item.active .faq-toggle i {
      transform: rotate(180deg);
    }
    
    .search-input:focus {
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
    }
    
    .category-card {
      background-color: #1e293b;
      border-radius: 0.5rem;
      transition: all 0.3s ease;
    }
    
    .category-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
    }
    
    .action-button {
      transition: all 0.3s ease;
    }
    
    .action-button:hover {
      transform: translateY(-2px);
    }
    
    /* Learn more content styles */
    .learn-more-content {
      max-height: 0;
      overflow: hidden;
      opacity: 0;
      transition: all 0.5s ease;
    }
    
    .learn-more-content.active {
      max-height: 1000px;
      opacity: 1;
      margin-top: 1rem;
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
    
    .bg-gradient-custom {
      background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }
    
    
    
    /* Status badge styles */
    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 500;
    }
    
    .status-resolved {
      /* background-color: rgba(16, 185, 129, 0.2); */
      color: #10b981;
      
    }
    
    .status-unresolved {
      /* background-color: rgba(239, 68, 68, 0.2); */
      color: #ef4444;
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
            <a href="team.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Team">
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
          <a href="help.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Help Center">
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
      <button class="menu-toggle md:hidden mb-4 bg-gray-800 p-2 rounded">
        <i class="fas fa-bars"></i>
      </button>
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold">Help Center</h1>
          <p class="text-gray-400">Find answers, guides and support for CryBug</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
          <button class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded flex items-center" id="contactSupportBtn">
            <i class="fas fa-headset mr-2"></i> Contact Support
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
            <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg p-2 hidden z-50" id="profileDropdown">
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
      
      <!-- Your Feedback Section -->
      <div class="bg-gray-800 rounded-xl p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Your Feedback</h2>
        
        <?php if(isset($feedbackSuccess)): ?>
        <div class="bg-green-500 bg-opacity-20 border border-green-500 text-green-400 px-4 py-3 rounded mb-4">
          <?php echo $feedbackSuccess; ?>
        </div>
        <?php endif; ?>
        
        <?php if(isset($feedbackError)): ?>
        <div class="bg-red-500 bg-opacity-20 border border-red-500 text-red-400 px-4 py-3 rounded mb-4">
          <?php echo $feedbackError; ?>
        </div>
        <?php endif; ?>
        
        <?php if($feedbackResult && $feedbackResult->num_rows > 0): ?>
        <div class="overflow-x-auto">
          <table class="w-full table-auto">
            <thead>
              <tr class="text-left bg-gray-700 bg-opacity-50">
                <th class="px-4 py-2 rounded-tl-lg">Issue Type</th>
                <th class="px-4 py-2">Message</th>
                <th class="px-4 py-2 rounded-tr-lg">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while($feedback = $feedbackResult->fetch_assoc()): ?>
              <tr class="border-b border-gray-700">
                <td class="px-4 py-3"><?php echo htmlspecialchars($feedback['a_issue_type']); ?></td>
                <td class="px-4 py-3"><?php echo htmlspecialchars($feedback['a_message']); ?></td>
                <td class="px-4 py-3">
                  <?php if($feedback['a_is_resolved'] == 1): ?>
                    <span class="status-badge status-resolved">Resolved</span>
                  <?php else: ?>
                    <span class="status-badge status-unresolved">Unresolved</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <p class="text-gray-400">You haven't submitted any feedback yet.</p>
        <?php endif; ?>
      </div>

      <!-- Help Categories -->
      <h2 class="text-xl font-semibold mb-4">Help Categories</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Getting Started -->
        <div class="category-card p-5 cursor-pointer">
          <div class="mb-4 rounded-lg bg-indigo-500 bg-opacity-20 p-3 inline-block">
            <i class="fas fa-rocket text-indigo-400 text-xl"></i>
          </div>
          <h3 class="font-bold text-lg mb-2">Getting Started</h3>
          <p class="text-gray-400 mb-4">Setup guides, onboarding, and basic features</p>
          <a href="#" class="learn-more-link text-indigo-400 hover:text-indigo-300 flex items-center text-sm">
            Learn more <i class="fas fa-arrow-right ml-2"></i>
          </a>
          <div class="learn-more-content">
            <ul class="mt-4 text-gray-400 space-y-2">
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Account setup and configuration</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Creating your first project</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> User interface tour</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Essential settings to review</li>
            </ul>
            <a href="#" class="mt-4 inline-block text-indigo-400 hover:text-indigo-300 text-sm">View full documentation <i class="fas fa-external-link-alt ml-1"></i></a>
          </div>
        </div>
        
        <!-- Bug Tracking -->
        <div class="category-card p-5 cursor-pointer">
          <div class="mb-4 rounded-lg bg-red-500 bg-opacity-20 p-3 inline-block">
            <i class="fas fa-bug text-red-400 text-xl"></i>
          </div>
          <h3 class="font-bold text-lg mb-2">Bug Tracking</h3>
          <p class="text-gray-400 mb-4">Creating, managing, and resolving bugs</p>
          <a href="#" class="learn-more-link text-indigo-400 hover:text-indigo-300 flex items-center text-sm">
            Learn more <i class="fas fa-arrow-right ml-2"></i>
          </a>
          <div class="learn-more-content">
            <ul class="mt-4 text-gray-400 space-y-2">
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Creating detailed bug reports</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Assigning priority and severity</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Working with bug lifecycles</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Resolution workflows</li>
            </ul>
            <a href="#" class="mt-4 inline-block text-indigo-400 hover:text-indigo-300 text-sm">View full documentation <i class="fas fa-external-link-alt ml-1"></i></a>
          </div>
        </div>
        
        <!-- Team Management -->
        <div class="category-card p-5 cursor-pointer">
          <div class="mb-4 rounded-lg bg-blue-500 bg-opacity-20 p-3 inline-block">
            <i class="fas fa-users text-blue-400 text-xl"></i>
          </div>
          <h3 class="font-bold text-lg mb-2">Team Management</h3>
          <p class="text-gray-400 mb-4">Adding team members and managing roles</p>
          <a href="#" class="learn-more-link text-indigo-400 hover:text-indigo-300 flex items-center text-sm">
            Learn more <i class="fas fa-arrow-right ml-2"></i>
          </a>
          <div class="learn-more-content">
            <ul class="mt-4 text-gray-400 space-y-2">
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Inviting team members</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Setting up roles and permissions</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Team communication tools</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Activity tracking and workload management</li>
            </ul>
            <a href="#" class="mt-4 inline-block text-indigo-400 hover:text-indigo-300 text-sm">View full documentation <i class="fas fa-external-link-alt ml-1"></i></a>
          </div>
        </div>
        
        <!-- Analytics & Reports -->
        <div class="category-card p-5 cursor-pointer">
          <div class="mb-4 rounded-lg bg-green-500 bg-opacity-20 p-3 inline-block">
            <i class="fas fa-chart-bar text-green-400 text-xl"></i>
          </div>
          <h3 class="font-bold text-lg mb-2">Analytics & Reports</h3>
          <p class="text-gray-400 mb-4">Understanding data and generating reports</p>
          <a href="#" class="learn-more-link text-indigo-400 hover:text-indigo-300 flex items-center text-sm">
            Learn more <i class="fas fa-arrow-right ml-2"></i>
          </a>
          <div class="learn-more-content">
            <ul class="mt-4 text-gray-400 space-y-2">
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Building custom reports</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Analyzing bug trends</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Team performance metrics</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Automated reporting schedules</li>
            </ul>
            <a href="#" class="mt-4 inline-block text-indigo-400 hover:text-indigo-300 text-sm">View full documentation <i class="fas fa-external-link-alt ml-1"></i></a>
          </div>
        </div>
        
        <!-- Account & Billing -->
        <div class="category-card p-5 cursor-pointer">
          <div class="mb-4 rounded-lg bg-yellow-500 bg-opacity-20 p-3 inline-block">
            <i class="fas fa-credit-card text-yellow-400 text-xl"></i>
          </div>
          <h3 class="font-bold text-lg mb-2">Account & Billing</h3>
          <p class="text-gray-400 mb-4">Subscription management and payments</p>
          <a href="#" class="learn-more-link text-indigo-400 hover:text-indigo-300 flex items-center text-sm">
            Learn more <i class="fas fa-arrow-right ml-2"></i>
          </a>
          <div class="learn-more-content">
            <ul class="mt-4 text-gray-400 space-y-2">
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Subscription plans and features</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Billing cycle management</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Payment methods and invoices</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> User seat management</li>
            </ul>
            <a href="#" class="mt-4 inline-block text-indigo-400 hover:text-indigo-300 text-sm">View full documentation <i class="fas fa-external-link-alt ml-1"></i></a>
          </div>
        </div>
        
        <!-- API & Integrations -->
        <div class="category-card p-5 cursor-pointer">
          <div class="mb-4 rounded-lg bg-purple-500 bg-opacity-20 p-3 inline-block">
            <i class="fas fa-plug text-purple-400 text-xl"></i>
          </div>
          <h3 class="font-bold text-lg mb-2">API & Integrations</h3>
          <p class="text-gray-400 mb-4">Connect with other tools and services</p>
          <a href="#" class="learn-more-link text-indigo-400 hover:text-indigo-300 flex items-center text-sm">
            Learn more <i class="fas fa-arrow-right ml-2"></i>
          </a>
          <div class="learn-more-content">
            <ul class="mt-4 text-gray-400 space-y-2">
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> API documentation and usage</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Available third-party integrations</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Setting up webhooks</li>
              <li><i class="fas fa-check-circle text-indigo-400 mr-2"></i> Custom integration development</li>
            </ul>
            <a href="#" class="mt-4 inline-block text-indigo-400 hover:text-indigo-300 text-sm">View full documentation <i class="fas fa-external-link-alt ml-1"></i></a>
          </div>
        </div>
      </div>
      
      <!-- Video Tutorials -->
      <div class="bg-gray-800 rounded-xl p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Video Tutorials</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Video 1 -->
          <div class="card-hover">
            <div class="relative mb-3 rounded overflow-hidden">
              <img src="../images/thumbnail/getting-started.png" alt="Getting Started" class="w-full">
              <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center">
                  <i class="fas fa-play text-white"></i>
                </div>
              </div>
              <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-xs text-white px-2 py-1 rounded">
                4:32
              </div>
            </div>
            <h3 class="font-medium">Getting Started with CryBug</h3>
            <p class="text-sm text-gray-400">Learn how to set up your account and create your first project</p>
          </div>
          
          <!-- Video 2 -->
          <div class="card-hover">
            <div class="relative mb-3 rounded overflow-hidden">
              <img src="../images/thumbnail/advance.png" alt="Advanced Bug Tracking" class="w-full">
              <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center">
                  <i class="fas fa-play text-white"></i>
                </div>
              </div>
              <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-xs text-white px-2 py-1 rounded">
                7:15
              </div>
            </div>
            <h3 class="font-medium">Advanced Bug Tracking</h3>
            <p class="text-sm text-gray-400">Deep dive into bug management and prioritization</p>
          </div>
          
          <!-- Video 3 -->
          <div class="card-hover">
            <div class="relative mb-3 rounded overflow-hidden">
              <img src="../images/thumbnail/report.png" alt="Reports and Analytics" class="w-full">
              <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center">
                  <i class="fas fa-play text-white"></i>
                </div>
              </div>
              <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-xs text-white px-2 py-1 rounded">
                5:48
              </div>
            </div>
            <h3 class="font-medium">Reports and Analytics</h3>
            <p class="text-sm text-gray-400">Generate insightful reports from your project data</p>
          </div>
        </div>
      </div>
      
      <!-- Feedback Modal -->
      <div id="feedbackModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Submit Feedback</h2>
            <button id="closeFeedbackModal" class="text-gray-400 hover:text-white">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <form action="" method="POST">
            <div class="mb-4">
              <label class="block text-gray-300 mb-2">Company Email</label>
              <input type="email" name="email" value="<?php echo htmlspecialchars($companyEmail); ?>" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white" readonly>
            </div>
            
            <div class="mb-4">
              <label class="block text-gray-300 mb-2">Issue Type</label>
              <select name="issue_type" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
                <option value="Bug Report">Bug Report</option>
                <option value="Feature Request">Feature Request</option>
                <option value="Account Issue">Account Issue</option>
                <option value="Billing Question">Billing Question</option>
                <option value="General Inquiry">General Inquiry</option>
              </select>
            </div>
            
            <div class="mb-6">
              <label class="block text-gray-300 mb-2">Message</label>
              <textarea name="message" rows="4" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white" placeholder="Describe your issue or feedback..."></textarea>
            </div>
            
            <input type="hidden" name="company_id" value="<?php echo htmlspecialchars($companyId); ?>">
            
            <button type="submit" name="submit_feedback" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded transition duration-300">
              Submit Feedback
            </button>
          </form>
        </div>
      </div>
      
      <!-- Frequently Asked Questions -->
      <h2 class="text-xl font-semibold mb-6">Frequently Asked Questions</h2>
      <div class="bg-gray-800 rounded-xl p-6 mb-8">
        <div class="space-y-4" id="faqContainer">
          <!-- FAQ Item 1 -->
          <div class="faq-item">
            <div class="faq-question flex justify-between items-center py-3 cursor-pointer">
              <h3 class="font-medium">How do I create a new project?</h3>
              <button class="faq-toggle text-gray-400">
                <i class="fas fa-chevron-down transition-transform duration-300"></i>
              </button>
            </div>
            <div class="faq-answer px-4 pb-4 text-gray-400">
              <p>To create a new project:</p>
              <ol class="list-decimal list-inside ml-4 space-y-2 mt-2">
                <li>Navigate to the Dashboard from the sidebar</li>
                <li>Click on the "New Project" button in the top right corner</li>
                <li>Fill out the project details form</li>
                <li>Assign team members and set permissions</li>
                <li>Click "Create Project" to finish</li>
              </ol>
              <p class="mt-2">You can then customize project settings by clicking on the project name and selecting "Settings" from the dropdown menu.</p>
            </div>
          </div>
          
          <!-- FAQ Item 2 -->
          <div class="faq-item">
            <div class="faq-question flex justify-between items-center py-3 cursor-pointer">
              <h3 class="font-medium">How do I invite team members?</h3>
              <button class="faq-toggle text-gray-400">
                <i class="fas fa-chevron-down transition-transform duration-300"></i>
              </button>
            </div>
            <div class="faq-answer px-4 pb-4 text-gray-400">
              <p>Inviting team members is simple:</p>
              <ol class="list-decimal list-inside ml-4 space-y-2 mt-2">
                <li>Go to the "Team" section from the main sidebar</li>
                <li>Click on "Invite Member" button</li>
                <li>Enter the email address and select their role</li>
                <li>Customize access permissions if needed</li>
                <li>Click "Send Invitation"</li>
              </ol>
              <p class="mt-2">Team members will receive an email with instructions to join your CryBug workspace.</p>
            </div>
          </div>
          
          <!-- FAQ Item 3 -->
          <div class="faq-item">
            <div class="faq-question flex justify-between items-center py-3 cursor-pointer">
              <h3 class="font-medium">How do I generate reports?</h3>
              <button class="faq-toggle text-gray-400">
                <i class="fas fa-chevron-down transition-transform duration-300"></i>
              </button>
            </div>
            <div class="faq-answer px-4 pb-4 text-gray-400">
              <p>To generate reports:</p>
              <ol class="list-decimal list-inside ml-4 space-y-2 mt-2">
                <li>Navigate to the "Analytics" section</li>
                <li>Click on "Create Report" in the top right</li>
                <li>Select report type and time period</li>
                <li>Choose which metrics to include</li>
                <li>Click "Generate Report"</li>
              </ol>
              <p class="mt-2">You can save report templates for future use and schedule automated reports to be sent to team members on a regular basis.</p>
            </div>
          </div>
          
          <!-- FAQ Item 4 -->
          <div class="faq-item">
            <div class="faq-question flex justify-between items-center py-3 cursor-pointer">
              <h3 class="font-medium">How do I track time spent on bugs?</h3>
              <button class="faq-toggle text-gray-400">
                <i class="fas fa-chevron-down transition-transform duration-300"></i>
              </button>
            </div>
            <div class="faq-answer px-4 pb-4 text-gray-400">
              <p>Time tracking for bugs can be done as follows:</p>
              <ol class="list-decimal list-inside ml-4 space-y-2 mt-2">
                <li>Open the bug details page</li>
                <li>Click on the "Time Tracking" section</li>
                <li>Click "Start Timer" or manually add time entries</li>
                <li>Add notes to each time entry if needed</li>
                <li>Save your changes</li>
              </ol>
              <p class="mt-2">Time tracking data will be included in analytics and reports, helping you understand resource allocation and project timelines.</p>
            </div>
          </div>
          
          <!-- FAQ Item 5 -->
          <div class="faq-item">
            <div class="faq-question flex justify-between items-center py-3 cursor-pointer">
              <h3 class="font-medium">How do I integrate with other tools?</h3>
              <button class="faq-toggle text-gray-400">
                <i class="fas fa-chevron-down transition-transform duration-300"></i>
              </button>
            </div>
            <div class="faq-answer px-4 pb-4 text-gray-400">
              <p>CryBug offers several integration options:</p>
              <ol class="list-decimal list-inside ml-4 space-y-2 mt-2">
                <li>Go to "Settings" in the sidebar</li>
                <li>Select the "Integrations" tab</li>
                <li>Browse available integrations or click "Add New"</li>
                <li>Follow the specific setup instructions for your chosen tool</li>
                <li>Configure sync options and permissions</li>
              </ol>
              <p class="mt-2">We offer direct integrations with popular tools like Slack, GitHub, Jira, and more. For custom integrations, check our API documentation.</p>
            </div>
          </div>
          
          <!-- FAQ Item 6 -->
          <div class="faq-item">
            <div class="faq-question flex justify-between items-center py-3 cursor-pointer">
              <h3 class="font-medium">How do I upgrade my subscription?</h3>
              <button class="faq-toggle text-gray-400">
                <i class="fas fa-chevron-down transition-transform duration-300"></i>
              </button>
            </div>
            <div class="faq-answer px-4 pb-4 text-gray-400">
              <p>To upgrade your subscription:</p>
              <ol class="list-decimal list-inside ml-4 space-y-2 mt-2">
                <li>Navigate to "Settings" in the sidebar</li>
                <li>Select "Account & Billing"</li>
                <li>Click on "Subscription" tab</li>
                <li>Review available plans and select "Upgrade"</li>
                <li>Complete payment information and confirm</li>
              </ol>
              <p class="mt-2">Your new subscription benefits will be available immediately. You'll be charged the prorated difference for the remainder of your billing cycle.</p>
            </div>
          </div>
        </div>
          
          <!-- FAQ Items 2-6 remain unchanged -->
        </div>
      </div>
      
        <!-- Footer -->
        <footer class="mt-12 py-6 border-t border-gray-700">
            <div class="flex flex-col md:flex-row justify-between items-center">
              <div class="flex items-center mb-4 md:mb-0">
                <i class="fas fa-bug text-indigo-500 text-xl mr-2"></i>
                <span class="text-lg font-bold text-indigo-500">CryBug</span>
              </div>
              <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white">
                  <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white">
                  <i class="fab fa-github"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white">
                  <i class="fab fa-linkedin"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white">
                  <i class="fab fa-youtube"></i>
                </a>
              </div>
            </div>
            <div class="mt-4 text-center text-sm text-gray-500">
              <p>© 2025 CryBug. All rights reserved.</p>
              <div class="mt-2 space-x-4">
                <a href="#" class="text-gray-500 hover:text-gray-300">Privacy Policy</a>
                <a href="#" class="text-gray-500 hover:text-gray-300">Terms of Service</a>
                <a href="#" class="text-gray-500 hover:text-gray-300">Cookie Policy</a>
              </div>
            </div>
          </footer>
        </main>
      </div>
    </main>
  </div>
  
  <!-- Add the JavaScript for the feedback form -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const feedbackModal = document.getElementById('feedbackModal');
      const closeFeedbackModal = document.getElementById('closeFeedbackModal');
      const contactSupportBtn = document.getElementById('contactSupportBtn');
      
      // Show feedback modal on page load
      // Uncomment the line below to make the modal appear on page load
      feedbackModal.classList.remove('hidden');
      
      // Show feedback modal when "Contact Support" button is clicked
      contactSupportBtn.addEventListener('click', function() {
        feedbackModal.classList.remove('hidden');
      });
      
      // Close feedback modal
      closeFeedbackModal.addEventListener('click', function() {
        feedbackModal.classList.add('hidden');
      });
      
      // FAQ Toggle Logic
      const faqItems = document.querySelectorAll('.faq-item');
      faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
          item.classList.toggle('active');
        });
      });
      
      // Learn More Toggle Logic
      const learnMoreLinks = document.querySelectorAll('.learn-more-link');
      learnMoreLinks.forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          const content = link.nextElementSibling;
          content.classList.toggle('active');
        });
      });
      
      // Mobile menu
      const menuToggle = document.querySelector('.menu-toggle');
      const sidebar = document.getElementById('sidebar');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      const closeSidebar = document.getElementById('closeSidebar');
      
      menuToggle.addEventListener('click', () => {
        sidebar.classList.add('active');
        sidebarOverlay.classList.add('active');
      });
      
      const closeSidebarFunc = () => {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
      };
      
      closeSidebar.addEventListener('click', closeSidebarFunc);
      sidebarOverlay.addEventListener('click', closeSidebarFunc);
      
      // Profile dropdown
      const profileDropdownBtn = document.getElementById('profileDropdownBtn');
      const profileDropdown = document.getElementById('profileDropdown');
      
      profileDropdownBtn.addEventListener('click', () => {
        profileDropdown.classList.toggle('hidden');
      });
      
      document.addEventListener('click', (e) => {
        if (!profileDropdownBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
          profileDropdown.classList.add('hidden');
        }
      });
    });
  </script>
</body>
</html>
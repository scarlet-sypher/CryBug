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
$companyName = $_SESSION['cmp_name'] ?? 'Company';

// Initialize variables
$successMessage = '';
$errorMessage = '';

// Process feedback update (resolve feedback)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_feedback'])) {
    $feedbackId = $_POST['feedback_id'] ?? '';
    $remark = trim(htmlspecialchars($_POST['feedback_remark'] ?? ''));
    
    // Validate input
    $errors = [];
    
    if (empty($feedbackId)) {
        $errors[] = "Invalid feedback ID";
    }
    
    if (empty($remark)) {
        $errors[] = "Remark is required";
    } elseif (strlen($remark) > 1000) {
        $errors[] = "Remark cannot exceed 1000 characters";
    }
    
    if (empty($errors)) {
        // Update feedback in the database
        $safeRemark = $con->real_escape_string($remark);
        $updateQuery = "UPDATE manager_feedback 
                        SET MF_remark = '$safeRemark', 
                            MF_is_resolved = 1 
                        WHERE MF_id = '$feedbackId' AND MF_cmp_id = '$companyId'";
        
        if ($con->query($updateQuery) === TRUE) {
            $successMessage = "Feedback resolved successfully!";
        } else {
            $errorMessage = "Error updating feedback: " . $con->error;
        }
    } else {
        $errorMessage = implode("<br>", $errors);
    }
}

// Get all feedback for this company
$feedbackQuery = "SELECT mf.*, m.mag_profile, m.mag_name 
                  FROM manager_feedback mf
                  LEFT JOIN manager m ON mf.MF_mag_id = m.mag_id
                  WHERE mf.MF_cmp_id = '$companyId'
                  ORDER BY mf.MF_is_resolved ASC, mf.MF_id DESC";
$feedbackResult = $con->query($feedbackQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management | CryBug</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="../src/output.css">
    <script src="dashboard.js" defer></script>
</head>
<body class="bg-gradient-custom text-white min-h-screen font-sans bg-black antialiased" id="home">

  <div class="overlay" id="sidebarOverlay"></div>
  
  <div class="flex flex-col md:flex-row">
    
    <!-- Sidebar (Same as in other pages) -->
    <aside class="sidebar w-64 bg-gray-900 p-4 md:fixed md:h-screen transition-all">
      <!-- Sidebar content unchanged -->
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
            <a href="dashboard.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Dashboard">
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
              <span>Add Holiday</span>
            </a>
          </li>
          <li>
            <a href="feedback.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Feedback">
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
          <h1 class="text-2xl md:text-3xl font-bold">Feedback Management</h1>
          <p class="text-gray-400">View and respond to manager feedback</p>
        </div>

        <div class="relative">
          <button id="profileDropdownBtn" class="flex items-center">
            <?php if(!empty($_SESSION['cmp_logo']) && file_exists($_SESSION['cmp_logo'])): ?>
              <img src="<?php echo htmlspecialchars($_SESSION['cmp_logo']); ?>" alt="Profile" class="h-10 w-10 object-cover rounded-full border-2 border-indigo-500" />
            <?php else: ?>
              <div class="h-10 w-10 rounded-full border-2 border-indigo-500 flex items-center justify-center bg-gray-700">
                <i class="fas fa-building"></i>
              </div>
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
      
      <!-- Feedback Stats Section -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php
        // Get feedback stats
        $totalFeedbackQuery = "SELECT COUNT(*) as total FROM manager_feedback WHERE MF_cmp_id = '$companyId'";
        $resolvedFeedbackQuery = "SELECT COUNT(*) as resolved FROM manager_feedback WHERE MF_cmp_id = '$companyId' AND MF_is_resolved = 1";
        $pendingFeedbackQuery = "SELECT COUNT(*) as pending FROM manager_feedback WHERE MF_cmp_id = '$companyId' AND MF_is_resolved = 0";
        
        $totalResult = $con->query($totalFeedbackQuery);
        $resolvedResult = $con->query($resolvedFeedbackQuery);
        $pendingResult = $con->query($pendingFeedbackQuery);
        
        $totalFeedback = $totalResult->fetch_assoc()['total'] ?? 0;
        $resolvedFeedback = $resolvedResult->fetch_assoc()['resolved'] ?? 0;
        $pendingFeedback = $pendingResult->fetch_assoc()['pending'] ?? 0;
        
        // Calculate resolution percentage
        $resolutionPercentage = ($totalFeedback > 0) ? round(($resolvedFeedback / $totalFeedback) * 100) : 0;
        ?>
        
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Total Feedback</p>
              <h3 class="text-2xl font-bold"><?php echo $totalFeedback; ?></h3>
            </div>
            <div class="p-3 bg-indigo-500 bg-opacity-20 rounded-lg">
              <i class="fas fa-comments text-xl text-indigo-400"></i>
            </div>
          </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-xl shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Resolved</p>
              <h3 class="text-2xl font-bold"><?php echo $resolvedFeedback; ?></h3>
            </div>
            <div class="p-3 bg-green-300 bg-opacity-20 rounded-lg">
              <i class="fas fa-check-circle text-xl text-lime-400"></i>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Pending</p>
              <h3 class="text-2xl font-bold"><?php echo $pendingFeedback; ?></h3>
            </div>
            <div class="p-3 bg-yellow-500 bg-opacity-20 rounded-lg">
              <i class="fas fa-clock text-xl text-yellow-400"></i>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Success/Error Messages -->
      <?php if(!empty($successMessage)): ?>
        <div class="bg-green-500 bg-opacity-20 text-green-400 p-3 rounded-lg mb-4">
          <i class="fas fa-check-circle mr-2"></i> <?php echo $successMessage; ?>
        </div>
      <?php endif; ?>
      
      <?php if(!empty($errorMessage)): ?>
        <div class="bg-red-500 bg-opacity-20 text-red-400 p-3 rounded-lg mb-4">
          <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $errorMessage; ?>
        </div>
      <?php endif; ?>
      
      <!-- Feedback List Section -->
      <!-- Feedback List Section -->
    <section class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold"><i class="fas fa-comments text-indigo-400 mr-2"></i>Manager Feedback</h2>
        
        <div class="flex space-x-2">
        <button id="filterAll" class="px-3 py-1 rounded bg-gray-700 hover:bg-gray-600 text-sm active">All</button>
        <button id="filterPending" class="px-3 py-1 rounded bg-gray-700 hover:bg-gray-600 text-sm">Pending</button>
        <button id="filterResolved" class="px-3 py-1 rounded bg-gray-700 hover:bg-gray-600 text-sm">Resolved</button>
        </div>
    </div>
    
    <?php if($feedbackResult && $feedbackResult->num_rows > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php while($feedback = $feedbackResult->fetch_assoc()): 
        $isResolved = $feedback['MF_is_resolved'] == 1;
        $priorityClass = '';
        
        switch(strtolower($feedback['MF_priority'])) {
            case 'critical':
            $priorityClass = 'bg-red-500 bg-opacity-20 text-red-400';
            break;
        case 'high':
            $priorityClass = 'bg-red-500 bg-opacity-20 text-red-400';
            break;
            case 'medium':
            $priorityClass = 'bg-yellow-500 bg-opacity-20 text-yellow-400';
            break;
            case 'low':
            $priorityClass = 'bg-blue-500 bg-opacity-20 text-blue-400';
            break;
            default:
            $priorityClass = 'bg-gray-500 bg-opacity-20 text-gray-400';
        }
        ?>
        
        <div class="feedback-item <?php echo $isResolved ? 'resolved' : 'pending'; ?> bg-gray-900 rounded-lg overflow-hidden h-full">
        <div class="p-4 border-l-4 h-full flex flex-col <?php echo $isResolved ? 'border-green-400' : 'border-yellow-400'; ?>">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center">
                <?php if(!empty($feedback['mag_profile'])): ?>
                <img src="<?php echo htmlspecialchars($feedback['mag_profile']); ?>" alt="Manager" class="h-10 w-10 object-cover rounded-full border-2 border-indigo-500 mr-3" />
                <?php else: ?>
                <div class="h-10 w-10 rounded-full border-2 border-indigo-500 flex items-center justify-center bg-gray-700 mr-3">
                    <i class="fas fa-user"></i>
                </div>
                <?php endif; ?>
                
                <div>
                <h3 class="font-medium"><?php echo htmlspecialchars($feedback['mag_name'] ?? 'Unknown Manager'); ?></h3>
                <span class="text-xs text-gray-400">Manager ID: <?php echo htmlspecialchars($feedback['MF_mag_id']); ?></span>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 rounded text-xs <?php echo $priorityClass; ?>">
                <?php echo htmlspecialchars($feedback['MF_priority']); ?> Priority
                </span>
                
                <span class="px-2 py-1 rounded text-xs <?php echo $isResolved ? 'bg-green-300 bg-opacity-20 text-green-400' : 'bg-yellow-500 bg-opacity-20 text-yellow-400'; ?>">
                <?php echo $isResolved ? 'Resolved' : 'Pending'; ?>
                </span>
            </div>
            </div>
            
            <div class="mt-4 flex-grow">
            <h4 class="text-lg font-medium">Issue:</h4>
            <p class="text-gray-300 bg-gray-800 p-3 rounded mt-2">
                <?php echo nl2br(htmlspecialchars($feedback['MF_issue'])); ?>
            </p>
            </div>
            
            <?php if($isResolved): ?>
            <div class="mt-4">
                <h4 class="text-lg font-medium">Resolution:</h4>
                <p class="text-gray-300 bg-gray-800 p-3 rounded mt-2">
                <?php echo nl2br(htmlspecialchars($feedback['MF_remark'])); ?>
                </p>
            </div>
            <?php else: ?>
            <div class="mt-4">
                <button class="respond-btn bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded flex items-center" data-id="<?php echo $feedback['MF_id']; ?>">
                <i class="fas fa-reply mr-2"></i> Respond
                </button>
                
                <div class="response-form hidden mt-4" id="form-<?php echo $feedback['MF_id']; ?>">
                <form method="POST" action="">
                    <input type="hidden" name="feedback_id" value="<?php echo $feedback['MF_id']; ?>">
                    
                    <div class="mb-3">
                    <label for="feedback_remark" class="block text-sm text-gray-400 mb-1">Your Response</label>
                    <textarea id="feedback_remark" name="feedback_remark" rows="4" required
                            class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white"></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                    <button type="button" class="cancel-btn bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded mr-2">
                        Cancel
                    </button>
                    <button type="submit" name="update_feedback" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center">
                        <i class="fas fa-check mr-2"></i> Resolve Feedback
                    </button>
                    </div>
                </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
        <div class="text-center py-8 text-gray-400">
        <i class="fas fa-inbox text-4xl mb-4"></i>
        <p>No feedback found</p>
        </div>
    <?php endif; ?>
    </section>
      
      <footer class="bg-gray-900 p-4 rounded-lg text-center text-gray-400 text-sm">
        <p>&copy; 2025 CryBug Bug Tracking System. All rights reserved.</p>
      </footer>
    </main>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        
      const filterAll = document.getElementById('filterAll');
      const filterPending = document.getElementById('filterPending');
      const filterResolved = document.getElementById('filterResolved');
      const feedbackItems = document.querySelectorAll('.feedback-item');
      
      filterAll.addEventListener('click', function() {
        setActiveFilter(this);
        feedbackItems.forEach(item => {
          item.style.display = 'block';
        });
      });
      
      filterPending.addEventListener('click', function() {
        setActiveFilter(this);
        feedbackItems.forEach(item => {
          if (item.classList.contains('pending')) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      });
      
      filterResolved.addEventListener('click', function() {
        setActiveFilter(this);
        feedbackItems.forEach(item => {
          if (item.classList.contains('resolved')) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      });
      
      function setActiveFilter(element) {
        [filterAll, filterPending, filterResolved].forEach(btn => {
          btn.classList.remove('bg-indigo-600');
          btn.classList.add('bg-gray-700');
        });
        element.classList.remove('bg-gray-700');
        element.classList.add('bg-indigo-600');
      }
      
      // Auto-hide success message
      const successMessage = document.querySelector('.bg-green-500');
      if (successMessage) {
        setTimeout(function() {
          successMessage.style.opacity = '0';
          setTimeout(function() {
            successMessage.style.display = 'none';
          }, 500);
        }, 3000);
      }
    });

    const respondButtons = document.querySelectorAll('.respond-btn');
    respondButtons.forEach(button => {
      button.addEventListener('click', function() {
        const feedbackId = this.getAttribute('data-id');
        const form = document.getElementById('form-' + feedbackId);
        form.classList.toggle('hidden');
      });
    });

    // Handle cancel buttons in response forms
    const cancelButtons = document.querySelectorAll('.cancel-btn');
    cancelButtons.forEach(button => {
      button.addEventListener('click', function() {
        const form = this.closest('.response-form');
        form.classList.add('hidden');
      });
    });
  </script>
</body>
</html>
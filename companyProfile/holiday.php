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
$formData = [
    'holiday_name' => '',
    'holiday_date' => '',
    'holiday_total_days' => 1,
    'holiday_day_type' => 'Full'
];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form data
    $errors = [];
    
    // Validate Holiday Name
    if (empty($_POST['holiday_name'])) {
        $errors[] = "Holiday name is required";
    } else {
        $formData['holiday_name'] = trim(htmlspecialchars($_POST['holiday_name']));
        // Check length
        if (strlen($formData['holiday_name']) > 100) {
            $errors[] = "Holiday name cannot exceed 100 characters";
        }
    }
    
    // Validate Holiday Date
    if (empty($_POST['holiday_date'])) {
        $errors[] = "Holiday date is required";
    } else {
        $formData['holiday_date'] = trim($_POST['holiday_date']);
        // Validate date format
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $formData['holiday_date'])) {
            $errors[] = "Invalid date format";
        } else {
            // Validate date is valid and not in the past
            $inputDate = new DateTime($formData['holiday_date']);
            $today = new DateTime(date('Y-m-d'));
            
            if ($inputDate < $today) {
                $errors[] = "Holiday date cannot be in the past";
            }
        }
    }
    
    // Validate Holiday Total Days
    if (!isset($_POST['holiday_total_days'])) {
        $errors[] = "Total days is required";
    } else {
        $totalDays = filter_var($_POST['holiday_total_days'], FILTER_VALIDATE_INT);
        if ($totalDays === false || $totalDays < 1) {
            $errors[] = "Total days must be a positive integer";
        } else {
            $formData['holiday_total_days'] = $totalDays;
        }
    }
    
    // Validate Holiday Day Type
    if (empty($_POST['holiday_day_type'])) {
        $errors[] = "Day type is required";
    } else {
        $dayType = trim($_POST['holiday_day_type']);
        if ($dayType !== 'Full' && $dayType !== 'Half') {
            $errors[] = "Invalid day type";
        } else {
            $formData['holiday_day_type'] = $dayType;
        }
    }
    
    // Process form if no errors
    if (empty($errors)) {
        // Sanitize data for database insertion
        $holidayName = $con->real_escape_string($formData['holiday_name']);
        $holidayDate = $con->real_escape_string($formData['holiday_date']);
        $holidayTotalDays = $formData['holiday_total_days'];
        $holidayDayType = $con->real_escape_string($formData['holiday_day_type']);
        
        // Insert into holiday table
        $insertQuery = "INSERT INTO holiday (holiday_name, holiday_date, holiday_total_days, holiday_cmp_id, holiday_day_type) 
                        VALUES ('$holidayName', '$holidayDate', $holidayTotalDays, '$companyId', '$holidayDayType')";
        
        if ($con->query($insertQuery) === TRUE) {
            $successMessage = "Holiday added successfully!";
            // Clear form data after successful submission
            $formData = [
                'holiday_name' => '',
                'holiday_date' => '',
                'holiday_total_days' => 1,
                'holiday_day_type' => 'Full'
            ];
        } else {
            $errorMessage = "Error: " . $con->error;
        }
    } else {
        // Combine all errors into one message
        $errorMessage = implode("<br>", $errors);
    }
}

// Get existing holidays for this company
$holidaysQuery = "SELECT * FROM holiday WHERE holiday_cmp_id = '$companyId' ORDER BY holiday_date DESC";
$holidaysResult = $con->query($holidaysQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Holiday | CryBug</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <script src="dashboard.js" defer></script>
</head>
<body class="bg-gradient-custom text-white min-h-screen font-sans bg-black antialiased" id="home">

  <div class="overlay" id="sidebarOverlay"></div>
  
  <div class="flex flex-col md:flex-row">
    
    <!-- Sidebar (Same as in dashboard.php) -->
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
            <a href="holiday.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Clients">
              <i class="fas fa-calendar-alt mr-3"></i>
              <span>Add Holiday</span>
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
          <h1 class="text-2xl md:text-3xl font-bold">Add Holiday</h1>
          <p class="text-gray-400">Manage company holidays and time-off days</p>
        </div>

        <div class="relative">
      <button id="profileDropdownBtn" class="flex items-center">
        <?php if(!empty($_SESSION['cmp_logo']) && file_exists($_SESSION['cmp_logo'])): ?>
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
      
      <!-- Holiday Form Section -->
      <section class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8">
        <h2 class="text-xl font-bold mb-6"><i class="fas fa-calendar-alt text-indigo-400 mr-2"></i>Add New Holiday</h2>
        
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
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="holiday_name" class="block text-sm text-gray-400 mb-1">Holiday Name</label>
              <input type="text" id="holiday_name" name="holiday_name" required
                     class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white"
                     value="<?php echo htmlspecialchars($formData['holiday_name']); ?>">
            </div>
            
            <div>
              <label for="holiday_date" class="block text-sm text-gray-400 mb-1">Holiday Date</label>
              <input type="date" id="holiday_date" name="holiday_date" required
                     class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white"
                     value="<?php echo htmlspecialchars($formData['holiday_date']); ?>">
              <p class="text-xs text-gray-500 mt-1">Date must be today or in the future</p>
            </div>
            
            <div>
              <label for="holiday_total_days" class="block text-sm text-gray-400 mb-1">Total Days</label>
              <input type="number" id="holiday_total_days" name="holiday_total_days" min="1" required
                     class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white"
                     value="<?php echo htmlspecialchars($formData['holiday_total_days']); ?>">
            </div>
            
            <div>
              <label for="holiday_day_type" class="block text-sm text-gray-400 mb-1">Day Type</label>
              <select id="holiday_day_type" name="holiday_day_type" required
                     class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white">
                <option value="Full" <?php echo ($formData['holiday_day_type'] == 'Full') ? 'selected' : ''; ?>>Full Day</option>
                <option value="Half" <?php echo ($formData['holiday_day_type'] == 'Half') ? 'selected' : ''; ?>>Half Day</option>
              </select>
            </div>
            
            <div>
              <label for="holiday_cmp_id" class="block text-sm text-gray-400 mb-1">Company ID</label>
              <div class="flex">
                <input type="text" id="holiday_cmp_id" name="holiday_cmp_id" readonly
                       class="w-full bg-gray-700 border border-gray-700 rounded p-2 text-gray-300 cursor-not-allowed"
                       value="<?php echo htmlspecialchars($companyId); ?>">
                <div class="bg-gray-700 p-2 rounded-r border-r border-t border-b border-gray-700 text-gray-400">
                  <i class="fas fa-building"></i>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-1">Company: <?php echo htmlspecialchars($companyName); ?></p>
            </div>
          </div>
          
          <div class="pt-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded flex items-center">
              <i class="fas fa-plus-circle mr-2"></i> Add Holiday
            </button>
          </div>
        </form>
      </section>
      
      <!-- Existing Holidays Section - unchanged -->
      <section class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8">
        <h2 class="text-xl font-bold mb-6"><i class="fas fa-list text-indigo-400 mr-2"></i>Company Holidays</h2>
        
        <div class="overflow-x-auto">
          <table class="min-w-full bg-gray-900 rounded-lg overflow-hidden">
            <thead>
              <tr class="bg-gray-800 text-left">
                <th class="px-4 py-3 text-sm font-medium text-gray-300">ID</th>
                <th class="px-4 py-3 text-sm font-medium text-gray-300">Holiday Name</th>
                <th class="px-4 py-3 text-sm font-medium text-gray-300">Date</th>
                <th class="px-4 py-3 text-sm font-medium text-gray-300">Total Days</th>
                <th class="px-4 py-3 text-sm font-medium text-gray-300">Day Type</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($holidaysResult && $holidaysResult->num_rows > 0) {
                  while ($holiday = $holidaysResult->fetch_assoc()) {
                      $holidayDate = new DateTime($holiday['holiday_date']);
                      $formattedDate = $holidayDate->format('M j, Y');
                      
                      // Set row color based on date (past or future)
                      $currentDate = new DateTime();
                      $isPast = $holidayDate < $currentDate;
                      $rowClass = $isPast ? 'opacity-60' : '';
                      
                      // Set badge color based on day type
                      $badgeClass = $holiday['holiday_day_type'] === 'Full' ? 'bg-indigo-500' : 'bg-purple-500';
              ?>
                      <tr class="border-t border-gray-800 hover:bg-gray-800 <?php echo $rowClass; ?>">
                        <td class="px-4 py-3 text-sm"><?php echo htmlspecialchars($holiday['holiday_id']); ?></td>
                        <td class="px-4 py-3 text-sm font-medium"><?php echo htmlspecialchars($holiday['holiday_name']); ?></td>
                        <td class="px-4 py-3 text-sm"><?php echo $formattedDate; ?></td>
                        <td class="px-4 py-3 text-sm"><?php echo htmlspecialchars($holiday['holiday_total_days']); ?></td>
                        <td class="px-4 py-3 text-sm">
                          <span class="px-2 py-1 rounded-full text-xs <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($holiday['holiday_day_type']); ?>
                          </span>
                        </td>
                      </tr>
              <?php
                  }
              } else {
              ?>
                  <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-400">No holidays found</td>
                  </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
      </section>
      
      <footer class="bg-gray-900 p-4 rounded-lg text-center text-gray-400 text-sm">
        <p>&copy; 2025 CryBug Bug Tracking System. All rights reserved.</p>
      </footer>
    </main>
  </div>
  
  <script>
    
    
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
  </script>
</body>
</html>
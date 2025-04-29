<?php
// Start session
session_start();

// Check if user is logged in (assuming login stores emp_id in session)
if (!isset($_SESSION['emp_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../employee/employee-Login.php");
    exit();
}

// Get the logged in employee ID
$emp_id = $_SESSION['emp_id'];
$empProfile = $_SESSION['emp_profile'] ?? '../images/Profile/guest.png';

include "connection.php";

// Initialize message variables
$success = false;
$error = null;
$message = "";

// Handle form submission for updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['update_type'];
    $item_id = $_POST['item_id'];
    $status = $_POST['new_status'];
    $progress = $_POST['new_progress'];
    $note = $_POST['update_note'];
    $current_date = date('Y-m-d H:i:s');
    
    if ($type == 'project') {
        // Check if this employee is assigned to this project
        $check_query = "SELECT * FROM project WHERE project_id = '$item_id' AND project_alloc_emp = '$emp_id'";
        $check_result = mysqli_query($con, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Update project progress and status
            $update_query = "UPDATE project SET 
                project_status = '$status',
                project_progress = '$progress' 
                WHERE project_id = '$item_id'";
            
            if (mysqli_query($con, $update_query)) {
                // Insert into project_updates table (if you have one)
                $success = true;
                $message = "Project progress updated successfully!";
            } else {
                $error = "Error updating project: " . mysqli_error($con);
            }
        } else {
            $error = "You are not authorized to update this project.";
        }
    } elseif ($type == 'bug') {
        // Check if this employee is assigned to this bug
        $check_query = "SELECT * FROM bug WHERE bug_id = '$item_id' AND bug_assigned_to = '$emp_id'";
        $check_result = mysqli_query($con, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            // For bugs, consider 100% progress and "Fixed" status as resolved
            $resolved_date = "";
            if ($progress == 100 && $status == 'Fixed') {
                $resolved_date = ", bug_resolved_date = '$current_date'";
            }
            
            // Update bug status and progress
            $update_query = "UPDATE bug SET 
                bug_status = '$status',
                bug_progress = '$progress'
                $resolved_date 
                WHERE bug_id = '$item_id'";
            
            if (mysqli_query($con, $update_query)) {
                $success = true;
                $message = "Bug status and progress updated successfully!";
            } else {
                $error = "Error updating bug: " . mysqli_error($con);
            }
        } else {
            $error = "You are not authorized to update this bug.";
        }
    }
}

// Get projects assigned to the logged-in employee
$projects_query = "SELECT * FROM project WHERE project_alloc_emp = '$emp_id'";
$projects_result = mysqli_query($con, $projects_query);

// Fix the bugs query to properly get bugs assigned to this employee
$bugs_query = "SELECT b.* FROM bug b WHERE b.bug_assigned_to = '$emp_id'";
$bugs_result = mysqli_query($con, $bugs_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Update Progress</title>
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
            <a href="project.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Projects">
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
            <a href="update.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Reports">
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
          <h1 class="text-2xl md:text-3xl font-bold">Update Progress</h1>
          <p class="text-gray-400" id="currentDateTime">Loading date...</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-4">

          <div class="relative">
            <button id="profileDropdownBtn" class="flex items-center">
            <?php if(!empty($empProfile) && file_exists($empProfile)): ?>
              <img src="<?php echo htmlspecialchars($empProfile) ; ?>" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
            <?php else : ; ?>
              <img src="../images/Profile/guest.png" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
            <?php endif ; ?>
              <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
            </button>
            <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg p-2 hidden" id="profileDropdown">
              <a href="dashboard.php" class="block p-2 hover:bg-gray-700 rounded text-sm">
                <i class="fas fa-user mr-2"></i> My Profile
              </a>
              <a href="logout.php" class="block p-2 hover:bg-gray-700 rounded text-sm text-green-400">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <?php if($success): ?>
      <div class="bg-green-600 text-white p-4 mb-6 rounded-md">
        <?php echo $message; ?>
      </div>
      <?php endif; ?>
      
      <?php if($error): ?>
      <div class="bg-red-600 text-white p-4 mb-6 rounded-md">
        <?php echo $error; ?>
      </div>
      <?php endif; ?>
      
      <!-- Project Progress Section -->
      <div class="bg-gray-800 rounded-xl p-6 mb-8 card-hover transition-all duration-300">
        <h2 class="text-xl font-bold mb-4 flex items-center">
          <i class="fas fa-project-diagram text-green-500 mr-2"></i>
          My Projects
        </h2>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-700">
            <thead>
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Project ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Project</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Progress</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
              <?php 
              if(mysqli_num_rows($projects_result) > 0) {
                while($row = mysqli_fetch_assoc($projects_result)) {
                  $status_class = '';
                  switch($row['project_status']) {
                    case 'In Progress': $status_class = 'bg-yellow-500'; break;
                    case 'On Hold': $status_class = 'bg-blue-500'; break;
                    case 'Completed': $status_class = 'bg-green-500'; break;
                    default: $status_class = 'bg-red-500';
                  }
              ?>
              <tr>

                <td>
                  <div class="font-medium"><?php echo htmlspecialchars($row['project_id']); ?></div>
                </td>
                <td class="px-4 py-4">
                  <div class="font-medium"><?php echo htmlspecialchars($row['project_name']); ?></div>
                  <div class="text-xs text-gray-400"><?php echo htmlspecialchars($row['project_descp']); ?></div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <span class="<?php echo $status_class; ?> text-xs px-2 py-1 rounded-full"><?php echo htmlspecialchars($row['project_status']); ?></span>
                  </div>
                </td>
                <td class="px-4 py-4">
                  <div class="flex items-center">
                    <div class="w-full bg-gray-700 rounded-full h-2 mr-2">
                      <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $row['project_progress']; ?>%"></div>
                    </div>
                    <span class="text-sm"><?php echo $row['project_progress']; ?>%</span>
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap flex justify-center items-center ">
                  <button class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-sm font-medium mr-2" 
                    data-project="<?php echo htmlspecialchars($row['project_name']); ?>" 
                    data-id="<?php echo $row['project_id']; ?>"
                    data-progress="<?php echo $row['project_progress']; ?>" 
                    data-status="<?php echo htmlspecialchars($row['project_status']); ?>" 
                    onclick="openUpdateModal(this)">Update</button>
                </td>
              </tr>
              <?php
                }
              } else {
              ?>
              <tr>
                <td colspan="4" class="px-4 py-4 text-center">No projects assigned to you.</td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Bug Progress Section -->
      <div class="bg-gray-800 rounded-xl p-6 mb-8 card-hover transition-all duration-300">
      <h2 class="text-xl font-bold mb-4 flex items-center">
        <i class="fas fa-bug text-red-500 mr-2"></i>
        My Bugs
      </h2>
      
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
          <thead>
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Bug ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Progress</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-700 ">
            <?php 
            if(mysqli_num_rows($bugs_result) > 0) {
              while($row = mysqli_fetch_assoc($bugs_result)) {
                $status_class = '';
                switch($row['bug_status']) {
                  case 'In Progress': $status_class = 'bg-yellow-500'; break;
                  case 'Fixed': $status_class = 'bg-green-500'; break;
                  default: $status_class = 'bg-red-500';
                }
            ?>
            <tr>
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="font-medium"><?php echo htmlspecialchars($row['bug_id']); ?></div>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm"><?php echo htmlspecialchars($row['bug_title']); ?></div>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <span class="<?php echo $status_class; ?> text-xs px-2 py-1 rounded-full"><?php echo htmlspecialchars($row['bug_status']); ?></span>
              </td>
              <td class="px-4 py-4">
                <div class="flex items-center">
                  <div class="w-full bg-gray-700 rounded-full h-2 mr-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $row['bug_progress']; ?>%"></div>
                  </div>
                  <span class="text-sm"><?php echo $row['bug_progress']; ?>%</span>
                </div>
              </td>
              <td class="px-4 py-4 whitespace-nowrap flex justify-center items-center">
                <button class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-sm font-medium mr-2" 
                  data-bug="<?php echo htmlspecialchars($row['bug_id']); ?>" 
                  data-title="<?php echo htmlspecialchars($row['bug_title']); ?>" 
                  data-status="<?php echo htmlspecialchars($row['bug_status']); ?>" 
                  data-progress="<?php echo $row['bug_progress']; ?>"
                  onclick="openUpdateModal(this, 'bug')">Update</button>
              </td>
            </tr>
            <?php
              }
            } else {
            ?>
            <tr>
              <td colspan="5" class="px-4 py-4 text-center">No bugs assigned to you.</td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
      
      <!-- Update Modal -->
      <div id="updateModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50">
        <div class="flex items-center justify-center h-full w-full">
          <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-xl font-bold" id="modalTitle">Update Progress</h3>
              <button onclick="closeModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
              </button>
            </div>
            
            <form method="POST" action="">
              <input type="hidden" id="update_type" name="update_type" value="">
              <input type="hidden" id="item_id" name="item_id" value="">
              
              <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-1">Item</label>
                <p id="itemName" class="text-white font-medium"></p>
              </div>
              
              <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-1">Current Status</label>
                <p id="currentStatus" class="text-white font-medium"></p>
              </div>
              
              <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-1">Current Progress</label>
                <div class="flex items-center">
                  <div class="w-full bg-gray-700 rounded-full h-2 mr-2">
                    <div id="currentProgressBar" class="bg-green-500 h-2 rounded-full"></div>
                  </div>
                  <span id="currentProgressText" class="text-sm">0%</span>
                </div>
              </div>
              
              <div class="mb-4">
                <label for="new_status" class="block text-sm text-gray-400 mb-1">Update Status</label>
                <select id="new_status" name="new_status" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full">
                  <option value="Not Started">Not Started</option>
                  <option value="In Progress">In Progress</option>
                  <option value="On Hold">On Hold</option>
                  <option value="Completed">Completed</option>
                  <option value="Fixed">Fixed</option>
                  <option value="Open">Open</option>
                </select>
              </div>
              
              <div class="mb-4">
                <label for="new_progress" class="block text-sm text-gray-400 mb-1">Update Progress (%)</label>
                <input type="range" id="new_progress" name="new_progress" min="0" max="100" class="w-full">
                <div class="flex justify-between">
                  <span class="text-xs text-gray-400">0%</span>
                  <span id="progressValue" class="text-xs text-white font-medium">0%</span>
                  <span class="text-xs text-gray-400">100%</span>
                </div>
              </div>
              
              <div class="mb-4">
                <label for="update_note" class="block text-sm text-gray-400 mb-1">Update Note</label>
                <textarea id="update_note" name="update_note" rows="3" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" placeholder="Add details about this update..."></textarea>
              </div>
              
              <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Save Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <!-- JavaScript for modal functionality -->
      <script>
      function openUpdateModal(element, type = 'project') {
        const modal = document.getElementById('updateModal');
        const itemName = document.getElementById('itemName');
        const currentStatus = document.getElementById('currentStatus');
        const currentProgressBar = document.getElementById('currentProgressBar');
        const currentProgressText = document.getElementById('currentProgressText');
        const newProgress = document.getElementById('new_progress');
        const progressValue = document.getElementById('progressValue');
        const updateType = document.getElementById('update_type');
        const itemId = document.getElementById('item_id');
        
        // Set modal title
        document.getElementById('modalTitle').textContent = type === 'project' ? 'Update Project Progress' : 'Update Bug Status';
        
        // Set form values
        updateType.value = type;
        
        // Set item name and ID based on type
        if (type === 'project') {
          itemName.textContent = element.dataset.project;
          itemId.value = element.dataset.id;
        } else {
          itemName.textContent = `${element.dataset.bug}: ${element.dataset.title}`;
          itemId.value = element.dataset.bug;
        }
        
        // Set current status
        currentStatus.textContent = element.dataset.status;
        
        // Set current progress
        const progress = parseInt(element.dataset.progress);
        currentProgressBar.style.width = `${progress}%`;
        currentProgressText.textContent = `${progress}%`;
        
        // Set initial value for range input
        newProgress.value = progress;
        progressValue.textContent = `${progress}%`;
        
        // Set initial selected status
        const statusSelect = document.getElementById('new_status');
        for (let i = 0; i < statusSelect.options.length; i++) {
          if (statusSelect.options[i].value === element.dataset.status) {
            statusSelect.selectedIndex = i;
            break;
          }
        }
        
        // Update progress text when slider changes
        newProgress.addEventListener('input', function() {
          progressValue.textContent = `${this.value}%`;
        });
        
        // Show modal by removing hidden class
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function closeModal() {
        const modal = document.getElementById('updateModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
      }
      </script>
    </main>
  </div>
</body>
</html>
<?php
// Start session
session_start();
$empNanme = $_SESSION['emp_name'] ?? 'Company';
$empProfile = $_SESSION['emp_profile'] ?? '../images/Profile/guest.png';
$empEmail = $_SESSION['emp_email']  ?? 'company@example.com';
$empID = $_SESSION['emp_id'] ?? 'No ID avaliable';
$empRole = $_SESSION['emp_role'] ?? 'No role' ;
$empPhone = $_SESSION['emp_phone']  ?? 'phone number' ;
$empDept = $_SESSION['emp_dept'] ;
$empExp = $_SESSION['emp_exp']  ; 
$empDev = $_SESSION['dev'] ; 
$empAuto = $_SESSION['auto'] ; 
$empDesign = $_SESSION['design']  ; 
$empVerbal = $_SESSION['verbal'] ;

// Check if user is logged in
if (!isset($_SESSION['emp_id'])) {
    header("Location: ../employee/employee-Login.php");
    exit();
}

include "connection.php" ;
// Get employee details
$emp_id = $_SESSION['emp_id'];
$query = "SELECT * FROM employee WHERE emp_id = '$emp_id'";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$employee = mysqli_fetch_assoc($result);

// Set active tab based on form submission
$activeTab = 'profile';
if (isset($_POST['update_password'])) {
    $activeTab = 'account';
}

// Handle profile update
$message = "";
if (isset($_POST['save_profile'])) {
    $emp_name = mysqli_real_escape_string($con, $_POST['fullName']);
    $emp_mail = mysqli_real_escape_string($con, $_POST['email']);
    $emp_phone = mysqli_real_escape_string($con, $_POST['phoneNumber']);
    $emp_role = mysqli_real_escape_string($con, $_POST['jobTitle']);
    $emp_dept = mysqli_real_escape_string($con, $_POST['company']);
    $emp_exp = mysqli_real_escape_string($con, $_POST['location']);
    $github = mysqli_real_escape_string($con, $_POST['github']);
    $linkedin = mysqli_real_escape_string($con, $_POST['linkedin']);
    $x = mysqli_real_escape_string($con, $_POST['twitter']);
    $password = mysqli_real_escape_string($con, $_POST['confirm_password']);
    
    // Verify password
    $verify_query = "SELECT emp_password FROM employee WHERE emp_id = '$emp_id' AND emp_password = '$password'";
    $verify_result = mysqli_query($con, $verify_query);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Process profile picture upload if a file is selected
        $profile_pic_path = $employee['emp_profile']; // Keep existing path by default
        
        if(isset($_FILES['profilePicture']) && $_FILES['profilePicture']['size'] > 0) {
            $target_dir = "../uploads/employee_images/";
            
            // Create directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["profilePicture"]["name"], PATHINFO_EXTENSION);
            $new_filename = $emp_id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Check if image file is an actual image
            $check = getimagesize($_FILES["profilePicture"]["tmp_name"]);
            if($check !== false) {
                // Upload file
                if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $target_file)) {
                    $profile_pic_path = $target_file;
                } else {
                    $message = "<div class='bg-red-500 text-white p-3 rounded mb-4'>Sorry, there was an error uploading your file.</div>";
                    // Continue with other updates even if image upload fails
                }
            } else {
                $message = "<div class='bg-red-500 text-white p-3 rounded mb-4'>File is not an image.</div>";
                // Continue with other updates even if image validation fails
            }
        }
        
        // Update profile including profile picture path
        $update_query = "UPDATE employee SET 
                        emp_name='$emp_name', 
                        emp_mail='$emp_mail', 
                        emp_phone='$emp_phone', 
                        emp_role='$emp_role', 
                        emp_dept='$emp_dept', 
                        emp_exp='$emp_exp',
                        github='$github',
                        linkedin='$linkedin',
                        x='$x',
                        emp_profile='$profile_pic_path'
                        WHERE emp_id='$emp_id'";
        
        if (mysqli_query($con, $update_query)) {
            $message = "<div class='bg-green-500 text-white p-3 rounded mb-4'>Profile updated successfully!</div>";
            
            // Refresh employee data
            $result = mysqli_query($con, $query);
            $employee = mysqli_fetch_assoc($result);
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded mb-4'>Error updating profile: " . mysqli_error($con) . "</div>";
        }
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded mb-4'>Incorrect password. Changes not saved.</div>";
    }
}

// Handle password change
$password_message = "";
if (isset($_POST['update_password'])) {
    $current_password = mysqli_real_escape_string($con, $_POST['currentPassword']);
    $new_password = mysqli_real_escape_string($con, $_POST['newPassword']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirmPassword']);
    
    // Verify current password
    $verify_query = "SELECT emp_password FROM employee WHERE emp_id = '$emp_id' AND emp_password = '$current_password'";
    $verify_result = mysqli_query($con, $verify_query);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Check password length only (simplified validation)
            if (strlen($new_password) >= 6) {
                // Update password
                $update_query = "UPDATE employee SET emp_password='$new_password' WHERE emp_id='$emp_id'";
                
                if (mysqli_query($con, $update_query)) {
                    $password_message = "<div class='bg-green-500 text-white p-3 rounded mb-4'>Password updated successfully!</div>";
                } else {
                    $password_message = "<div class='bg-red-500 text-white p-3 rounded mb-4'>Error updating password: " . mysqli_error($con) . "</div>";
                }
            } else {
                $password_message = "<div class='bg-red-500 text-white p-3 rounded mb-4'>Password must be at least 6 characters.</div>";
            }
        } else {
            $password_message = "<div class='bg-red-500 text-white p-3 rounded mb-4'>New passwords do not match.</div>";
        }
    } else {
        $password_message = "<div class='bg-red-500 text-white p-3 rounded mb-4'>Current password is incorrect.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Settings</title>
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
            <a href="update.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Reports">
              <i class="fas fa-tasks mr-3"></i>
              <span>Update Progress</span>
            </a>
          </li>
          <li>
            <a href="setting.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Settings">
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
          <h1 class="text-2xl md:text-3xl font-bold">Settings</h1>
          <p class="text-gray-400" id="currentDateTime">April 21, 2025</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
           
          <div class="relative">
            <button id="profileDropdownBtn" class="flex items-center">
              <img src="<?php echo htmlspecialchars($empProfile); ?>" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
              <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
            </button>
            <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg p-2 hidden" id="profileDropdown">
              <a href="#" class="block p-2 hover:bg-gray-700 rounded text-sm">
                <i class="fas fa-user mr-2"></i> My Profile
              </a>
              <a href="logout.php" class="block p-2 hover:bg-gray-700 rounded text-sm text-green-400">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Settings Tabs -->
      <div class="bg-gray-800 rounded-xl p-6 mb-8">
        <div class="border-b border-gray-700 pb-4 mb-6">
          <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="settingsTabs" role="tablist">
            <li class="mr-2" role="presentation">
              <button class="inline-block p-4 border-b-2 <?php echo $activeTab === 'profile' ? 'border-green-500 text-green-500' : 'border-transparent hover:text-gray-300 hover:border-gray-300'; ?> rounded-t-lg" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="<?php echo $activeTab === 'profile' ? 'true' : 'false'; ?>">Profile Settings</button>
            </li>
            <li class="mr-2" role="presentation">
              <button class="inline-block p-4 border-b-2 <?php echo $activeTab === 'account' ? 'border-green-500 text-green-500' : 'border-transparent hover:text-gray-300 hover:border-gray-300'; ?> rounded-t-lg" id="account-tab" data-tabs-target="#account" type="button" role="tab" aria-controls="account" aria-selected="<?php echo $activeTab === 'account' ? 'true' : 'false'; ?>">Account Security</button>
            </li>
          </ul>
        </div>
        
        <div id="settingsTabContent">
          <!-- Profile Settings Tab -->
          <div class="<?php echo $activeTab === 'profile' ? 'block' : 'hidden'; ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <?php echo $message; ?>
            <form method="POST" action="" enctype="multipart/form-data">
              <div class="flex flex-col md:flex-row gap-8">
                <!-- Profile Picture Section -->
                <div class="md:w-1/3">
                  <div class="flex flex-col items-center justify-center p-6 bg-gray-700 rounded-lg">
                    <div class="mb-4 relative">
                      <div class="w-32 h-32 rounded-full bg-gray-600 overflow-hidden flex items-center justify-center relative">
                        <?php if(!empty($employee['emp_profile']) && file_exists($employee['emp_profile'])): ?>
                          <img src="<?php echo htmlspecialchars($employee['emp_profile']); ?>" alt="Profile Picture" class="w-full h-full object-cover" id="previewProfilePic">
                        <?php else: ?>
                          <img src="<?php echo htmlspecialchars($empProfile); ?>" alt="Profile Picture" class="w-full h-full object-cover" id="previewProfilePic">
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 hover:opacity-100 transition-all duration-200">
                          <label for="profilePicture" class="cursor-pointer text-white text-center p-2 w-full h-full flex flex-col items-center justify-center">
                            <i class="fas fa-camera text-xl mb-1"></i>
                            <p class="text-xs">Change Photo</p>
                          </label>
                        </div>
                      </div>
                      <input type="file" id="profilePicture" name="profilePicture" class="hidden" accept="image/*">
                    </div>
                    <h3 class="text-lg font-medium"><?php echo $employee['emp_name']; ?></h3>
                    <p class="text-sm text-gray-400"><?php echo $employee['emp_role']; ?></p>
                    <p class="text-sm mt-2 text-gray-400">Employee ID: <?php echo $employee['emp_id']; ?></p>
                  </div>
                </div>
                
                <!-- Profile Information Section -->
                <div class="md:w-2/3">
                  <h3 class="text-lg font-medium mb-4">Personal Information</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label for="fullName" class="block text-sm text-gray-400 mb-1">Full Name</label>
                      <input type="text" id="fullName" name="fullName" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $employee['emp_name']; ?>">
                    </div>
                    
                    <div>
                      <label for="jobTitle" class="block text-sm text-gray-400 mb-1">Job Title</label>
                      <input type="text" id="jobTitle" name="jobTitle" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $employee['emp_role']; ?>">
                    </div>
                    
                    <div>
                      <label for="email" class="block text-sm text-gray-400 mb-1">Email Address</label>
                      <input type="email" id="email" name="email" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $employee['emp_mail']; ?>">
                    </div>
                    
                    <div>
                      <label for="phoneNumber" class="block text-sm text-gray-400 mb-1">Phone Number</label>
                      <input type="tel" id="phoneNumber" name="phoneNumber" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $employee['emp_phone']; ?>">
                    </div>
                    
                    <div>
                      <label for="company" class="block text-sm text-gray-400 mb-1">Department</label>
                      <input type="text" id="company" name="company" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $employee['emp_dept']; ?>">
                    </div>
                    
                    <div>
                      <label for="location" class="block text-sm text-gray-400 mb-1">Experience</label>
                      <input type="text" id="location" name="location" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $employee['emp_exp']; ?>">
                    </div>
                  </div>
                  
                  <h3 class="text-lg font-medium mt-8 mb-4">Social Media Links</h3>
                  
                  <div class="grid grid-cols-1 gap-4">
                    <div class="flex items-center">
                      <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 mr-3">
                        <i class="fab fa-linkedin-in"></i>
                      </div>
                      <input type="url" name="linkedin" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 flex-grow" placeholder="LinkedIn URL" value="<?php echo isset($employee['linkedin']) ? $employee['linkedin'] : ''; ?>">
                    </div>
                    
                    <div class="flex items-center">
                      <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-600 mr-3">
                        <i class="fab fa-github"></i>
                      </div>
                      <input type="url" name="github" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 flex-grow" placeholder="GitHub URL" value="<?php echo isset($employee['github']) ? $employee['github'] : ''; ?>">
                    </div>
                    
                    <div class="flex items-center">
                      <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-400 mr-3">
                        <i class="fab fa-twitter"></i>
                      </div>
                      <input type="url" name="twitter" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 flex-grow" placeholder="Twitter URL" value="<?php echo isset($employee['x']) ? $employee['x'] : ''; ?>">
                    </div>
                  </div>
                  
                  <div class="mt-6 pb-6 border-b border-gray-700">
                    <h3 class="text-lg font-medium mb-4">Password Verification</h3>
                    <div class="mb-4">
                      <label for="confirm_password" class="block text-sm text-gray-400 mb-1">Enter Current Password to Save Changes</label>
                      <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full pr-10" required>
                        <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center" data-target="confirm_password">
                          <i class="fas fa-eye text-gray-400"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-6 flex justify-end">
                    <button type="button" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded mr-3">Cancel</button>
                    <button type="submit" name="save_profile" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Save Changes</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
          
          <!-- Account Security Tab -->
          <div class="<?php echo $activeTab === 'account' ? 'block' : 'hidden'; ?>" id="account" role="tabpanel" aria-labelledby="account-tab">
            <div class="max-w-2xl mx-auto">
              <!-- Change Password Section -->
              <?php echo $password_message; ?>
              <form method="POST" action="">
                <div class="mb-8 p-6 bg-gray-700 rounded-lg">
                  <h3 class="text-lg font-medium mb-4">Change Password</h3>
                  
                  <div class="space-y-4">
                    <div>
                      <label for="currentPassword" class="block text-sm text-gray-400 mb-1">Current Password</label>
                      <div class="relative">
                        <input type="password" id="currentPassword" name="currentPassword" class="bg-gray-800 text-white border border-gray-600 rounded px-3 py-2 w-full pr-10" required>
                        <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center" data-target="currentPassword">
                          <i class="fas fa-eye text-gray-400"></i>
                        </button>
                      </div>
                    </div>
                    
                    <div>
                      <label for="newPassword" class="block text-sm text-gray-400 mb-1">New Password</label>
                      <div class="relative">
                        <input type="password" id="newPassword" name="newPassword" class="bg-gray-800 text-white border border-gray-600 rounded px-3 py-2 w-full pr-10" required>
                        <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center" data-target="newPassword">
                          <i class="fas fa-eye text-gray-400"></i>
                        </button>
                      </div>
                      <p class="text-xs text-gray-400 mt-1">Password must be at least 6 characters</p>
                    </div>
                    
                    <div>
                      <label for="confirmPassword" class="block text-sm text-gray-400 mb-1">Confirm New Password</label>
                      <div class="relative">
                        <input type="password" id="confirmPassword" name="confirmPassword" class="bg-gray-800 text-white border border-gray-600 rounded px-3 py-2 w-full pr-10" required>
                        <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center" data-target="confirmPassword">
                          <i class="fas fa-eye text-gray-400"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <button type="submit" name="update_password" class="mt-6 bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Update Password</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
  // Get tab elements
  const profileTab = document.getElementById("profile-tab");
  const accountTab = document.getElementById("account-tab");
  
  // Get content elements
  const profileContent = document.getElementById("profile");
  const accountContent = document.getElementById("account");
  
  // Function to set active tab
  function setActiveTab(tab, content) {
    // Reset all tabs and content
    profileTab.classList.remove("border-green-500", "text-green-500");
    profileTab.classList.add("border-transparent", "hover:text-gray-300", "hover:border-gray-300");
    accountTab.classList.remove("border-green-500", "text-green-500");
    accountTab.classList.add("border-transparent", "hover:text-gray-300", "hover:border-gray-300");
    
    profileContent.classList.add("hidden");
    accountContent.classList.add("hidden");
    
    // Set active tab and content
    tab.classList.remove("border-transparent", "hover:text-gray-300", "hover:border-gray-300");
    tab.classList.add("border-green-500", "text-green-500");
    content.classList.remove("hidden");
  }
  
  // Event listeners for tab clicks
  profileTab.addEventListener("click", function() {
    setActiveTab(profileTab, profileContent);
  });
  
  accountTab.addEventListener("click", function() {
    setActiveTab(accountTab, accountContent);
  });
  
  // Check form submission state
  // This will handle the case when "update_password" is submitted
  const formElements = document.forms;
  for (let i = 0; i < formElements.length; i++) {
    formElements[i].addEventListener("submit", function(event) {
      // If this is the password update form
      if (this.querySelector('button[name="update_password"]')) {
        // No need to prevent default as we want the form to submit
        // Just make sure we're saving the state that we want to stay on account tab
        localStorage.setItem("activeSettingsTab", "account");
      }
    });
  }
  
  // Check if we need to show account tab based on URL or local storage
  if (localStorage.getItem("activeSettingsTab") === "account" || 
      window.location.search.includes("update_password")) {
    setActiveTab(accountTab, accountContent);
    // Clear the storage after using it
    localStorage.removeItem("activeSettingsTab");
  }
});


document.addEventListener("DOMContentLoaded", function() {
  // Get all password toggle buttons
  const toggleButtons = document.querySelectorAll('.toggle-password');
  
  // Add click event to each button
  toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Get the target input field
      const targetId = this.getAttribute('data-target');
      const passwordInput = document.getElementById(targetId);
      
      // Toggle password visibility
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        this.innerHTML = '<i class="fas fa-eye-slash text-gray-400"></i>';
      } else {
        passwordInput.type = 'password';
        this.innerHTML = '<i class="fas fa-eye text-gray-400"></i>';
      }
    });
  });
  
  // Image preview functionality
  const profilePictureInput = document.getElementById('profilePicture');
  const previewProfilePic = document.getElementById('previewProfilePic');
  
  if (profilePictureInput && previewProfilePic) {
    profilePictureInput.addEventListener('change', function() {
      // Check if a file is selected
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        
        // When file is loaded, set the preview image source
        reader.onload = function(e) {
          previewProfilePic.src = e.target.result;
        };
        
        // Read the selected file as a data URL
        reader.readAsDataURL(this.files[0]);
      }
    });
  }
  
});

</script>

</body>
</html>

  

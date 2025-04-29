<?php
// Start session

session_start();

// Check if user is logged in
if (!isset($_SESSION['mag_id'])) {
    header("Location: login.php");
    exit();
}

include "connection.php";

// Get manager details
$mag_id = $_SESSION['mag_id'];
$query = "SELECT * FROM manager WHERE mag_id = '$mag_id'";
$result = mysqli_query($con, $query);

$ManagerProfile = $_SESSION['mag_profile'] ;

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$manager = mysqli_fetch_assoc($result);

// Set default values from session or database
$magName = $manager['mag_name'] ?? 'Manager';
$magProfile = $manager['mag_profile'] ?? '../images/Profile/guest.png';
$magEmail = $manager['mag_email'] ?? 'manager@example.com';
$magID = $manager['mag_id'] ?? 'No ID available';
$magRole = $manager['mag_role'] ?? 'No role';
$magPhone = $manager['mag_phone'] ?? 'phone number';
$githubLink = $manager['github'] ?? '';
$linkedinLink = $manager['linkedin'] ?? '';
$xLink = $manager['x'] ?? '';

// Set active tab based on form submission
$activeTab = 'profile';
if (isset($_POST['update_password'])) {
    $activeTab = 'account';
}

// Handle profile update
$message = "";
if (isset($_POST['save_profile'])) {
    $mag_name = mysqli_real_escape_string($con, $_POST['fullName'] ?? $magName);
    $mag_email = mysqli_real_escape_string($con, $_POST['email'] ?? $magEmail);
    $mag_phone = mysqli_real_escape_string($con, $_POST['phoneNumber'] ?? $magPhone);
    $mag_role = mysqli_real_escape_string($con, $_POST['jobTitle'] ?? $magRole);
    $github = mysqli_real_escape_string($con, $_POST['github'] ?? $githubLink);
    $linkedin = mysqli_real_escape_string($con, $_POST['linkedin'] ?? $linkedinLink);
    $x = mysqli_real_escape_string($con, $_POST['x'] ?? $xLink);
    $password = mysqli_real_escape_string($con, $_POST['confirm_password']);
    
    // Verify password
    $verify_query = "SELECT mag_password FROM manager WHERE mag_id = '$mag_id' AND mag_password = '$password'";
    $verify_result = mysqli_query($con, $verify_query);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Process profile picture upload if a file is selected
        $target_path = $magProfile; // Keep the existing path by default
        
        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
            $pic_tmp_name = $_FILES['profilePicture']['tmp_name'];
            $pic_name = basename($_FILES['profilePicture']['name']);
            $upload_dir = "../uploads/manager_images/";

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $target_path = $upload_dir . $pic_name;
            move_uploaded_file($pic_tmp_name, $target_path);
        }
        
        // Update profile including profile picture path
        $update_query = "UPDATE manager SET 
                        mag_name='$mag_name', 
                        mag_email='$mag_email', 
                        mag_phone='$mag_phone', 
                        mag_role='$mag_role', 
                        github='$github',
                        linkedin='$linkedin',
                        x='$x',
                        mag_profile='$target_path'
                        WHERE mag_id='$mag_id'";
        
        if (mysqli_query($con, $update_query)) {
            $message = "<div class='bg-green-500 text-white p-3 rounded mb-4'>Profile updated successfully!</div>";
            
            // Refresh manager data
            $result = mysqli_query($con, $query);
            $manager = mysqli_fetch_assoc($result);
            
            // Update session variables
            $_SESSION['mag_name'] = $mag_name;
            $_SESSION['mag_email'] = $mag_email;
            $_SESSION['mag_profile'] = $target_path;
            $_SESSION['mag_role'] = $mag_role;
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
    $verify_query = "SELECT mag_password FROM manager WHERE mag_id = '$mag_id' AND mag_password = '$current_password'";
    $verify_result = mysqli_query($con, $verify_query);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Check password length only (simplified validation)
            if (strlen($new_password) >= 6) {
                // Update password
                $update_query = "UPDATE manager SET mag_password='$new_password' WHERE mag_id='$mag_id'";
                
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
  <link rel="stylesheet" href="dashboard.css"> 
  <link rel="stylesheet" href="../src/output.css">
  <script src="dashboard.js" defer></script>
  
  <style>
    /* Gradient background */
   
    
    /* Input focus styles */
    .input-focus:focus {
      border-color: #ef4444;
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
    }

    .image-preview {
      width: 100%;
      height: 0;
      overflow: hidden;
      transition: height 0.3s ease;
      display: flex;
      justify-content: center;
      margin-top: 10px;
    }

    .image-preview img {
      max-height: 100px;
      border-radius: 8px;
      border: 2px solid #ef4444;
      object-fit: cover;
    }

    /* Mobile sidebar */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        position: fixed;
        z-index: 50;
        height: 100vh;
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 40;
      }
      
      .overlay.active {
        display: block;
      }
    }

    /* Toggle Switch */
    input:checked ~ .dot {
      transform: translateX(100%);
    }

    input:checked ~ .block {
      background-color: #ef4444;
    }

    .dot {
      transition: all 0.3s ease-in-out;
    }
  </style>
</head>

<body class="bg-gradient-custom text-white min-h-screen font-sans antialiased">
  <!-- Overlay for mobile sidebar -->
  <div class="overlay" id="sidebarOverlay"></div>
  
  <div class="flex flex-col md:flex-row">
    <!-- Sidebar -->
    <aside class="sidebar w-64 bg-gray-900 p-4 md:fixed md:h-screen">
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
            <a href="../index.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
              <i class="fas fa-home mr-3"></i>
              <span>Home</span>
            </a>
          </li>
          <li>
            <a href="dashboard.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
              <i class="fas fa-tachometer-alt mr-3"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="project.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
              <i class="fas fa-project-diagram mr-3"></i>
              <span>Projects</span>
            </a>
          </li>
          <li>
            <a href="bug.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
              <i class="fas fa-bug mr-3"></i>
              <span>Bugs</span>
            </a>
          </li>
          <li>
            <a href="team.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
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
            <a href="setting.php" class="sidebar-link active flex items-center p-3 rounded text-gray-300 hover:text-white">
              <i class="fas fa-cog mr-3"></i>
              <span>Settings</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <div class="mt-auto pt-8">
        <div class="border-t border-gray-700 pt-4">
          <a href="help.php" class="flex items-center p-3 rounded text-gray-300 hover:text-white">
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
    <main class="w-full md:ml-64 flex-1 p-4 md:p-6">
      <!-- Mobile menu toggle -->
      <button class="menu-toggle md:hidden mb-4 bg-gray-800 p-2 rounded">
        <i class="fas fa-bars"></i>
      </button>
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div class="flex flex-col gap-2">
          <h1 class="text-2xl md:text-3xl font-bold">Settings</h1>
          <p class="text-gray-400" id="currentDateTime"><?php echo date('F j, Y'); ?></p>
          <p class="text-gray-400">Manage your profile and account preferences</p>
        </div>

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
      
      <!-- Settings Content -->
      <div class="bg-gray-800 rounded-xl p-6 mb-8">
        <div class="border-b border-gray-700 pb-4 mb-6">
          <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="settingsTabs" role="tablist">
            <li class="mr-2" role="presentation">
              <button class="inline-block p-4 border-b-2 <?php echo $activeTab === 'profile' ? 'border-red-500 text-red-500' : 'border-transparent hover:text-gray-300 hover:border-gray-300'; ?> rounded-t-lg" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="<?php echo $activeTab === 'profile' ? 'true' : 'false'; ?>">Profile Settings</button>
            </li>
            <li class="mr-2" role="presentation">
              <button class="inline-block p-4 border-b-2 <?php echo $activeTab === 'account' ? 'border-red-500 text-red-500' : 'border-transparent hover:text-gray-300 hover:border-gray-300'; ?> rounded-t-lg" id="account-tab" data-tabs-target="#account" type="button" role="tab" aria-controls="account" aria-selected="<?php echo $activeTab === 'account' ? 'true' : 'false'; ?>">Account Security</button>
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
                        <?php if(!empty($manager['mag_profile']) && file_exists($manager['mag_profile'])): ?>
                          <img src="<?php echo htmlspecialchars($manager['mag_profile']); ?>" alt="Profile Picture" class="w-full h-full object-cover" id="previewProfilePic">
                        <?php else: ?>
                          <img src="<?php echo htmlspecialchars($magProfile); ?>" alt="Profile Picture" class="w-full h-full object-cover" id="previewProfilePic">
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
                    <h3 class="text-lg font-medium"><?php echo $manager['mag_name']; ?></h3>
                    <p class="text-sm text-gray-400"><?php echo $manager['mag_role']; ?></p>
                    <p class="text-sm mt-2 text-gray-400">Manager ID: <?php echo $manager['mag_id']; ?></p>
                  </div>
                </div>
                
                <!-- Profile Information Section -->
                <div class="md:w-2/3">
                  <h3 class="text-lg font-medium mb-4">Personal Information</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label for="fullName" class="block text-sm text-gray-400 mb-1">Full Name</label>
                      <input type="text" id="fullName" name="fullName" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $manager['mag_name']; ?>">
                    </div>
                    
                    <div>
                      <label for="jobTitle" class="block text-sm text-gray-400 mb-1">Role</label>
                      <input type="text" id="jobTitle" name="jobTitle" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $manager['mag_role']; ?>">
                    </div>
                    
                    <div>
                      <label for="email" class="block text-sm text-gray-400 mb-1">Email Address</label>
                      <input type="email" id="email" name="email" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $manager['mag_email']; ?>">
                    </div>
                    
                    <div>
                      <label for="phoneNumber" class="block text-sm text-gray-400 mb-1">Phone Number</label>
                      <input type="tel" id="phoneNumber" name="phoneNumber" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 w-full" value="<?php echo $manager['mag_phone']; ?>">
                    </div>
                  </div>
                  
                  <h3 class="text-lg font-medium mt-8 mb-4">Social Media Links</h3>
                  
                  <div class="grid grid-cols-1 gap-4">
                    <div class="flex items-center">
                      <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 mr-3">
                        <i class="fab fa-linkedin-in"></i>
                      </div>
                      <input type="url" name="linkedin" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 flex-grow" placeholder="LinkedIn URL" value="<?php echo isset($manager['linkedin']) ? $manager['linkedin'] : ''; ?>">
                    </div>
                    
                    <div class="flex items-center">
                      <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-600 mr-3">
                        <i class="fab fa-github"></i>
                      </div>
                      <input type="url" name="github" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 flex-grow" placeholder="GitHub URL" value="<?php echo isset($manager['github']) ? $manager['github'] : ''; ?>">
                    </div>
                    
                    <div class="flex items-center">
                      <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-400 mr-3">
                        <i class="fab fa-twitter"></i>
                      </div>
                      <input type="url" name="x" class="bg-gray-700 text-white border border-gray-600 rounded px-3 py-2 flex-grow" placeholder="Twitter URL" value="<?php echo isset($manager['x']) ? $manager['x'] : ''; ?>">
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
                    <button type="submit" name="save_profile" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Save Changes</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
            
            <!-- Account Security Tab -->
            <div class="<?php echo $activeTab === 'account' ? 'block' : 'hidden'; ?>" id="account" role="tabpanel" aria-labelledby="account-tab">
              <!-- Change Password Section -->
              <div class="max-w-2xl mx-auto">
                <?php echo $password_message; ?>
                <div class="mb-8 p-6 bg-gray-700 rounded-lg">
                  <h3 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-lock text-red-500 mr-2"></i> Change Password
                  </h3>
                  <form id="passwordForm" method="post">
                    <div class="space-y-4">
                      <div>
                        <label class="block text-gray-400 text-sm font-medium mb-2">Current Password</label>
                        <div class="relative">
                          <input type="password" id="currentPassword" name="currentPassword" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 input-focus focus:outline-none pr-10" required>
                          <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center" data-target="currentPassword">
                            <i class="fas fa-eye text-gray-400"></i>
                          </button>
                        </div>
                      </div>
                      
                      <div>
                        <label class="block text-gray-400 text-sm font-medium mb-2">New Password</label>
                        <div class="relative">
                          <input type="password" id="newPassword" name="newPassword" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 input-focus focus:outline-none pr-10" required>
                          <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center" data-target="newPassword">
                            <i class="fas fa-eye text-gray-400"></i>
                          </button>
                        </div>
                        
                      </div>
                      
                      <div>
                        <label class="block text-gray-400 text-sm font-medium mb-2">Confirm New Password</label>
                        <div class="relative">
                          <input type="password" id="confirmPassword" name="confirmPassword" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-2 input-focus focus:outline-none pr-10" required>
                          <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center" data-target="confirmPassword">
                            <i class="fas fa-eye text-gray-400"></i>
                          </button>
                        </div>
                        <p id="password-match" class="text-xs text-red-500 mt-1 hidden">Passwords do not match</p>
                      </div>
                      
                      <div class="flex justify-end mt-6">
                        <button type="submit" name="update_password" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">
                          <i class="fas fa-key mr-2"></i> Update Password
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  
  <script>
    // Tab handling
    const tabButtons = document.querySelectorAll('[role="tab"]');
    const tabPanels = document.querySelectorAll('[role="tabpanel"]');
    
    tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        // Deactivate all tabs
        tabButtons.forEach(btn => {
          btn.classList.remove('border-red-500', 'text-red-500');
          btn.classList.add('border-transparent');
          btn.setAttribute('aria-selected', 'false');
        });
        
        // Hide all panels
        tabPanels.forEach(panel => {
          panel.classList.add('hidden');
        });
        
        // Activate clicked tab
        button.classList.remove('border-transparent');
        button.classList.add('border-red-500', 'text-red-500');
        button.setAttribute('aria-selected', 'true');
        
        // Show corresponding panel
        const panelId = button.getAttribute('data-tabs-target').substring(1);
        document.getElementById(panelId).classList.remove('hidden');
      });
    });
    
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
      button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const passwordInput = document.getElementById(targetId);
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          passwordInput.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      });
    });
    
   
    // Profile picture preview
    const profilePicture = document.getElementById('profilePicture');
    const previewProfilePic = document.getElementById('previewProfilePic');
    
    if (profilePicture && previewProfilePic) {
      profilePicture.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewProfilePic.src = e.target.result;
          }
          reader.readAsDataURL(this.files[0]);
        }
      });
    }

  
    
    // Current date time display
</script>
  </body>
  </html>

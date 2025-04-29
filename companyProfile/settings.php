<?php
// Start the session
session_start();
// Check if user is logged in
if(!isset($_SESSION['cmp_id'])) {
    header("Location: ../companies/company-Login.php");
    exit();
}

include "connection.php";
// Get company data
$user_id = $_SESSION['cmp_id'];
$sql = "SELECT * FROM company WHERE cmp_id = '$user_id'";
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) > 0) {
   $company = mysqli_fetch_assoc($result);
} else {
   // If no company data, redirect to setup page
   // header("Location: company_setup.php");
   // exit();
}

// Process form submission
$success_message = "";
$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if(isset($_POST['update_company'])) {
    // Get form data - use existing values from $company if not in POST
    $cmp_name = isset($_POST['cmp_name']) && $_POST['cmp_name'] !== '' ? 
        mysqli_real_escape_string($con, $_POST['cmp_name']) : $company['cmp_name'];
    
    $cmp_descp = isset($_POST['cmp_descp']) && $_POST['cmp_descp'] !== '' ? 
        mysqli_real_escape_string($con, $_POST['cmp_descp']) : $company['cmp_descp'];
    
    $cmp_pincode = isset($_POST['cmp_pincode']) && $_POST['cmp_pincode'] !== '' ? 
        mysqli_real_escape_string($con, $_POST['cmp_pincode']) : $company['cmp_pincode'];
    
    $cmp_mail = isset($_POST['cmp_mail']) && $_POST['cmp_mail'] !== '' ? 
        mysqli_real_escape_string($con, $_POST['cmp_mail']) : $company['cmp_mail'];
    
    $cmp_phone = isset($_POST['cmp_phone']) && $_POST['cmp_phone'] !== '' ? 
        mysqli_real_escape_string($con, $_POST['cmp_phone']) : $company['cmp_phone'];
    
    $cmp_address = isset($_POST['cmp_address']) && $_POST['cmp_address'] !== '' ? 
        mysqli_real_escape_string($con, $_POST['cmp_address']) : $company['cmp_address'];
    
    // Handle social media fields the same way
    $github = isset($_POST['github']) && $_POST['github'] !== '' ? 
        mysqli_real_escape_string($con, $_POST['github']) : (isset($company['github']) ? $company['github'] : '');
    
    $linkedin = isset($_POST['linkedin']) && $_POST['linkedin'] !== '' ? 
        mysqli_real_escape_string($con, $_POST['linkedin']) : (isset($company['linkedin']) ? $company['linkedin'] : '');
    
    $x = isset($_POST['x']) && $_POST['x'] !== '' ? 
        mysqli_real_escape_string($con, $_POST['x']) : (isset($company['x']) ? $company['x'] : '');
    
    $confirm_password = isset($_POST['confirm_password']) ? mysqli_real_escape_string($con, $_POST['confirm_password']) : '';
    
    // Initialize logo_path variable before any conditions
    $logo_path = isset($company['cmp_logo']) ? $company['cmp_logo'] : '';
    
    // Verify password before making changes
    $verify_query = "SELECT cmp_password FROM company WHERE cmp_id = '$user_id'";
    $verify_result = mysqli_query($con, $verify_query);
    
    if ($verify_result && mysqli_num_rows($verify_result) > 0) {
        $password_row = mysqli_fetch_assoc($verify_result);
        $stored_password = $password_row['cmp_password'];
        
        // Direct plain text comparison
        if ($confirm_password === $stored_password) {
            // Password verified, proceed with update
            
            // Handle logo upload if provided
            if(isset($_FILES['cmp_logo']) && $_FILES['cmp_logo']['error'] == 0) {
                $upload_dir = "../uploads/company_images/";
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $pic_name = basename($_FILES['cmp_logo']['name']);
                $target_path = $upload_dir . $pic_name;
                
                if (move_uploaded_file($_FILES['cmp_logo']['tmp_name'], $target_path)) {
                    $logo_path = $target_path;
                } else {
                    $error_message = "Sorry, there was an error uploading your file.";
                    // Continue with other updates even if image upload fails
                }
            }
            
            // First check if these columns exist in the database
            $check_columns_query = "SHOW COLUMNS FROM company LIKE 'github'";
            $github_exists = mysqli_query($con, $check_columns_query);
            $has_github = mysqli_num_rows($github_exists) > 0;
            
            $check_columns_query = "SHOW COLUMNS FROM company LIKE 'linkedin'";
            $linkedin_exists = mysqli_query($con, $check_columns_query);
            $has_linkedin = mysqli_num_rows($linkedin_exists) > 0;
            
            $check_columns_query = "SHOW COLUMNS FROM company LIKE 'x'";
            $x_exists = mysqli_query($con, $check_columns_query);
            $has_x = mysqli_num_rows($x_exists) > 0;
            
            // Build the SQL query dynamically based on existing columns
            $update_sql = "UPDATE company SET
                cmp_name = '$cmp_name',
                cmp_descp = '$cmp_descp',
                cmp_pincode = '$cmp_pincode',
                cmp_mail = '$cmp_mail',
                cmp_phone = '$cmp_phone',
                cmp_address = '$cmp_address',
                cmp_logo = '$logo_path'";
            
            // Only add social media fields if they exist in the database
            if ($has_github) {
                $update_sql .= ", github = '$github'";
            }
            if ($has_linkedin) {
                $update_sql .= ", linkedin = '$linkedin'";
            }
            if ($has_x) {
                $update_sql .= ", x = '$x'";
            }
            
            // Complete the query
            $update_sql .= " WHERE cmp_id = '$user_id'";
            
            if(mysqli_query($con, $update_sql)) {
                $success_message = "Company profile updated successfully!";
                // Refresh company data
                $result = mysqli_query($con, $sql);
                $company = mysqli_fetch_assoc($result);
            } else {
                $error_message = "Error updating profile: " . mysqli_error($con);
            }
        } else {
            $error_message = "Incorrect password. Changes not saved.";
        }
    } else {
        $error_message = "Error verifying password.";
    }
  } 
  else if(isset($_POST['update_password'])) {
    $current_password = mysqli_real_escape_string($con, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    $confirm_new_password = mysqli_real_escape_string($con, $_POST['confirm_new_password']);
    
    // First, verify current password
    $verify_query = "SELECT cmp_password FROM company WHERE cmp_id = '$user_id'";
    $verify_result = mysqli_query($con, $verify_query);
    
    if ($verify_result && mysqli_num_rows($verify_result) > 0) {
        $password_row = mysqli_fetch_assoc($verify_result);
        $stored_password = $password_row['cmp_password'];
        
        // Plain text comparison
        if ($current_password === $stored_password) {
            if($new_password === $confirm_new_password) {
                // Store the new password as plain text
                $update_pwd_sql = "UPDATE company SET cmp_password = '$new_password' WHERE cmp_id = '$user_id'";
                
                if(mysqli_query($con, $update_pwd_sql)) {
                    $success_message = "Password updated successfully!";
                } else {
                    $error_message = "Error updating password: " . mysqli_error($con);
                }
            } else {
                $error_message = "New passwords do not match!";
            }
        } else {
            $error_message = "Current password is incorrect.";
        }
    } else {
        $error_message = "Error retrieving password information.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Company Settings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="dashboard.php" defer></script>
</head>
<body class="bg-gradient-custom text-white min-h-screen font-sans bg-black antialiased">

  <div class="overlay" id="sidebarOverlay"></div>
  
  <div class="flex flex-col md:flex-row">
    
    <!-- Sidebar -->
    <aside class="sidebar w-64 bg-gray-900 p-4 md:fixed md:h-screen transition-all">
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
            <a href="team.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
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
            <a href="analysis.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white">
              <i class="fas fa-chart-bar mr-3"></i>
              <span>Analytics</span>
            </a>
          </li>
          <li>
            <a href="settings.php" class="sidebar-link active flex items-center p-3 rounded text-white bg-gray-800">
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
          <a href="logout.php" class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 p-2 rounded flex items-center justify-center">
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
      
      <!-- Alerts -->
      <?php if(!empty($success_message)): ?>
        <div class="bg-green-800 text-white p-4 rounded mb-6 flex justify-between items-center" id="success-alert">
          <div><i class="fas fa-check-circle mr-2"></i> <?php echo $success_message; ?></div>
          <button onclick="document.getElementById('success-alert').style.display='none'" class="text-white">
            <i class="fas fa-times"></i>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if(!empty($error_message)): ?>
        <div class="bg-red-800 text-white p-4 rounded mb-6 flex justify-between items-center" id="error-alert">
          <div><i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error_message; ?></div>
          <button onclick="document.getElementById('error-alert').style.display='none'" class="text-white">
            <i class="fas fa-times"></i>
          </button>
        </div>
      <?php endif; ?>

      <!-- Settings Section -->
      <div class="bg-gray-800 rounded-xl p-6 mb-8 card-hover">
        <div class="flex justify-between items-center mb-6">
          <h3 class="font-bold text-lg">Company Settings</h3>

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
        
        <!-- Settings Tabs -->
        <div class="border-b border-gray-700 mb-6">
          <div class="flex gap-5">
            <button class="tab-button active" data-settings-tab="companyInfoTab">
              <i class="fas fa-building mr-2"></i> Company Info
            </button>
            <button class="tab-button" data-settings-tab="securityTab">
              <i class="fas fa-lock mr-2"></i> Security
            </button>
            <button class="tab-button" data-settings-tab="socialMediaTab">
              <i class="fas fa-share-alt mr-2"></i> Social Media
            </button>
          </div>
        </div>
        
        <!-- Company Info Tab -->
        <div id="companyInfoTab" class="settings-tab-content active">
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Company Logo Upload -->
              <div class="col-span-2">
                <label class="block text-gray-300 text-sm font-medium mb-2">Company Logo</label>
                <div class="flex items-center space-x-4">
                  <div class="bg-gray-900 rounded-lg p-4 flex items-center justify-center w-24 h-24">
                    <img src="<?php echo !empty($company['cmp_logo']) ? $company['cmp_logo'] : '../images/Profile/guest.png'; ?>" alt="Company Logo" class="max-w-full max-h-full" id="previewLogo">
                  </div>
                  <div class="flex-1">
                    <div class="bg-gray-900 border border-gray-700 rounded-lg p-4 text-center cursor-pointer hover:bg-gray-800 transition-all" id="logoUploadArea">
                      <i class="fas fa-cloud-upload-alt text-indigo-500 text-xl mb-2"></i>
                      <p class="text-sm text-gray-400">Drag & drop your logo or</p>
                      <button type="button" class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Browse Files</button>
                      <input type="file" name="cmp_logo" accept="image/*" class="hidden" id="logoUpload">
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Recommended: 512x512px, PNG or SVG</p>
                  </div>
                </div>
              </div>
              
              <!-- Company Details -->
              <div>
                <label class="block text-gray-300 text-sm font-medium mb-2">Company Name</label>
                <input type="text" name="cmp_name" value="<?php echo htmlspecialchars($company['cmp_name']); ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 focus:outline-none focus:border-indigo-500" required>
              </div>
              
              <div>
                <label class="block text-gray-300 text-sm font-medium mb-2">Company Description</label>
                <input type="text" name="cmp_descp" value="<?php echo htmlspecialchars($company['cmp_descp']); ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 focus:outline-none focus:border-indigo-500" required>
              </div>
              
              <div>
                <label class="block text-gray-300 text-sm font-medium mb-2">Email Address</label>
                <input type="email" name="cmp_mail" value="<?php echo htmlspecialchars($company['cmp_mail']); ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 focus:outline-none focus:border-indigo-500" required>
              </div>
              
              <div>
                <label class="block text-gray-300 text-sm font-medium mb-2">Phone</label>
                <input type="tel" name="cmp_phone" value="<?php echo htmlspecialchars($company['cmp_phone']); ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 focus:outline-none focus:border-indigo-500" required>
              </div>
              
              <div>
                <label class="block text-gray-300 text-sm font-medium mb-2">Pincode</label>
                <input type="text" name="cmp_pincode" value="<?php echo htmlspecialchars($company['cmp_pincode']); ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 focus:outline-none focus:border-indigo-500" required>
              </div>
              
              <div class="col-span-2">
                <label class="block text-gray-300 text-sm font-medium mb-2">Address</label>
                <textarea name="cmp_address" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 focus:outline-none focus:border-indigo-500" rows="2" required><?php echo htmlspecialchars($company['cmp_address']); ?></textarea>
              </div>
            </div>
            
            <!-- Password Confirmation Section -->
            <div class="mt-8 border-t border-gray-700 pt-6">
              <h4 class="text-indigo-400 font-medium mb-4">Confirm Changes</h4>
              <div class="max-w-md">
                <div class="mb-4">
                  <label class="block text-gray-300 text-sm font-medium mb-2">Enter Your Password to Save Changes</label>
                  <div class="relative">
                    <input type="password" name="confirm_password" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 pr-10 focus:outline-none focus:border-indigo-500" placeholder="Enter your current password" required>
                    <button type="button" class="toggle-password absolute right-3 top-3 text-gray-400">
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end gap-4 mt-6">
              <button type="reset" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded font-medium">Reset</button>
              <button type="submit" name="update_company" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded font-medium">Save Changes</button>
            </div>
          </form>
        </div>
        
        <!-- Security Tab (Password Change) -->
        <div id="securityTab" class="settings-tab-content hidden">
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="max-w-md mx-auto">
              <div class="mb-6">
                <label class="block text-gray-300 text-sm font-medium mb-2">Current Password</label>
                <div class="relative">
                  <input type="password" name="current_password" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 pr-10 focus:outline-none focus:border-indigo-500" placeholder="Enter current password" required>
                  <button type="button" class="toggle-password absolute right-3 top-3 text-gray-400">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
              
              <div class="mb-6">
                <label class="block text-gray-300 text-sm font-medium mb-2">New Password</label>
                <div class="relative">
                  <input type="password" name="new_password" id="newPassword" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 pr-10 focus:outline-none focus:border-indigo-500" placeholder="Enter new password" required>
                  <button type="button" class="toggle-password absolute right-3 top-3 text-gray-400">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                
              </div>
              
              <div class="mb-6">
                <label class="block text-gray-300 text-sm font-medium mb-2">Confirm New Password</label>
                <div class="relative">
                  <input type="password" name="confirm_new_password" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 pr-10 focus:outline-none focus:border-indigo-500" placeholder="Confirm new password" required>
                  <button type="button" class="toggle-password absolute right-3 top-3 text-gray-400">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
              
              <!-- Action Buttons -->
              <div class="flex justify-end gap-4 mt-6">
                <button type="reset" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded font-medium">Cancel</button>
                <button type="submit" name="update_password" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded font-medium">Change Password</button>
              </div>
            </div>
          </form>
        </div>
        
        <!-- Social Media Tab -->
        <div id="socialMediaTab" class="settings-tab-content hidden">
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="max-w-lg mx-auto">
              <div class="mb-6">
                <label class="block text-gray-300 text-sm font-medium mb-2">
                  <i class="fab fa-github text-gray-400 mr-2"></i> GitHub Profile URL
                </label>
                <input type="url" name="github" value="<?php echo htmlspecialchars($company['github'] ?? ''); ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 focus:outline-none focus:border-indigo-500" placeholder="https://github.com/yourusername">
              </div>
              
              <div class="mb-6">
                <label class="block text-gray-300 text-sm font-medium mb-2">
                  <i class="fab fa-linkedin text-gray-400 mr-2"></i> LinkedIn Profile URL
                </label>
                <input type="url" name="linkedin" value="<?php echo htmlspecialchars($company['linkedin'] ?? ''); ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 focus:outline-none focus:border-indigo-500" placeholder="https://linkedin.com/in/yourprofile">
              </div>
              
              <div class="mb-6">
                <label class="block text-gray-300 text-sm font-medium mb-2">
                  <i class="fab fa-twitter text-gray-400 mr-2"></i> X (Twitter) Profile URL
                </label>
                <input type="url" name="x" value="<?php echo htmlspecialchars($company['x'] ?? ''); ?>" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 focus:outline-none focus:border-indigo-500" placeholder="https://x.com/yourhandle">
              </div>
              
              <!-- Password Confirmation Section -->
              <div class="mt-8 border-t border-gray-700 pt-6">
                <h4 class="text-indigo-400 font-medium mb-4">Confirm Changes</h4>
                <div class="mb-4">
                  <label class="block text-gray-300 text-sm font-medium mb-2">Enter Your Password to Save Changes</label>
                  <div class="relative">
                    <input type="password" name="confirm_password" class="w-full bg-gray-900 text-white border border-gray-700 rounded p-3 pr-10 focus:outline-none focus:border-indigo-500" placeholder="Enter your current password" required>
                    <button type="button" class="toggle-password absolute right-3 top-3 text-gray-400">
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- Action Buttons -->
              <div class="flex justify-end gap-4 mt-6">
                <button type="reset" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded font-medium">Reset</button>
                <button type="submit" name="update_company" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded font-medium">Save Changes</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Logo upload preview functionality
    logoUploadArea.addEventListener('drop', (e) => {
  e.preventDefault();
  logoUploadArea.classList.remove('border-indigo-500');
  
  if (e.dataTransfer.files.length) {
    handleLogoFile(e.dataTransfer.files[0]);
  }
});

logoUpload.addEventListener('change', (e) => {
  if (e.target.files.length) {
    handleLogoFile(e.target.files[0]);
  }
});

function handleLogoFile(file) {
  if (file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = (e) => {
      previewLogo.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

// Toggle password visibility
const togglePasswordButtons = document.querySelectorAll('.toggle-password');
togglePasswordButtons.forEach(button => {
  button.addEventListener('click', () => {
    const passwordField = button.parentElement.querySelector('input');
    const icon = button.querySelector('i');
    
    if (passwordField.type === 'password') {
      passwordField.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordField.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });
});

// Settings tabs functionality
const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.settings-tab-content');

tabButtons.forEach(button => {
  button.addEventListener('click', () => {
    // Remove active class from all buttons and content
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.add('hidden'));
    
    // Add active class to clicked button and show corresponding content
    button.classList.add('active');
    const tabId = button.getAttribute('data-settings-tab');
    document.getElementById(tabId).classList.remove('hidden');
    document.getElementById(tabId).classList.add('active');
  });
});

// Profile dropdown toggle
const profileDropdownBtn = document.getElementById('profileDropdownBtn');
const profileDropdown = document.getElementById('profileDropdown');

profileDropdownBtn.addEventListener('click', () => {
  profileDropdown.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
  if (!profileDropdownBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
    profileDropdown.classList.add('hidden');
  }
});

// Mobile sidebar functionality
const menuToggle = document.querySelector('.menu-toggle');
const sidebar = document.querySelector('.sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const closeSidebar = document.querySelector('.close-sidebar');

menuToggle.addEventListener('click', () => {
  sidebar.classList.toggle('active');
  sidebarOverlay.classList.toggle('active');
  document.body.classList.toggle('sidebar-open');
});

closeSidebar.addEventListener('click', () => {
  sidebar.classList.remove('active');
  sidebarOverlay.classList.remove('active');
  document.body.classList.remove('sidebar-open');
});

sidebarOverlay.addEventListener('click', () => {
  sidebar.classList.remove('active');
  sidebarOverlay.classList.remove('active');
  document.body.classList.remove('sidebar-open');
});

// Auto close alerts after 5 seconds
const alerts = document.querySelectorAll('#success-alert, #error-alert');
alerts.forEach(alert => {
  setTimeout(() => {
    if (alert) {
      alert.style.display = 'none';
    }
  }, 5000);
});


// Add this to your existing script
document.querySelector('button[type="button"]').addEventListener('click', () => {
  document.getElementById('logoUpload').click();
});

// Also make sure your drag and drop handlers are properly set up
logoUploadArea.addEventListener('dragover', (e) => {
  e.preventDefault();
  logoUploadArea.classList.add('border-indigo-500');
});

logoUploadArea.addEventListener('dragleave', (e) => {
  e.preventDefault();
  logoUploadArea.classList.remove('border-indigo-500');
});

</script>
</body>
</html>
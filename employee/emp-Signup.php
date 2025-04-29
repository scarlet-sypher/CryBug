<?php

session_start() ;

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "connection.php";

$registration_success = false;
$emp_id = "";

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (empty($_POST['employeeID'])) {
        $prefix = 'CRYEMP';
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $timestamp = substr(time(), -4);
        $emp_id = $prefix . $randomDigits . $timestamp;
    } else {
        $emp_id = $_POST['employeeID'];
    }

    // Get form data and sanitize inputs
    $emp_name = mysqli_real_escape_string($con, $_POST['employeeName']);
    $emp_mail = mysqli_real_escape_string($con, $_POST['email']);
    $emp_phone = mysqli_real_escape_string($con, $_POST['phone']);
    $emp_password = mysqli_real_escape_string($con, $_POST['password']);
    $emp_gender = mysqli_real_escape_string($con, $_POST['gender']);
    $emp_role = mysqli_real_escape_string($con, $_POST['role']);
    $emp_dept = mysqli_real_escape_string($con, $_POST['department']);
    $emp_exp = mysqli_real_escape_string($con, $_POST['experience']);
    
    // Get mag_id (optional)
    $mag_id = !empty($_POST['mag_id']) ? mysqli_real_escape_string($con, $_POST['mag_id']) : null;
    
    // Get skill percentage values
    $webD = isset($_POST['webD']) ? intval($_POST['webD']) : 0;
    $auto = isset($_POST['auto']) ? intval($_POST['auto']) : 0;
    $design = isset($_POST['design']) ? intval($_POST['design']) : 0;
    $verbal = isset($_POST['verbal']) ? intval($_POST['verbal']) : 0;
    
    $emp_profile = "";
    $target_path = "";
    if (isset($_FILES['employeeProfile']) && $_FILES['employeeProfile']['error'] == 0) {
        $profile_tmp_name = $_FILES['employeeProfile']['tmp_name'];
        $profile_name = basename($_FILES['employeeProfile']['name']);
        $upload_dir = "../uploads/employee_images/";

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $target_path = $upload_dir . $profile_name;
        move_uploaded_file($profile_tmp_name, $target_path);
    }

    $sql = "INSERT INTO employee (
              emp_id, emp_name, emp_mail, emp_phone,
              emp_password, emp_gender, emp_profile, 
              emp_role, emp_dept, emp_exp, webD, auto, design, verbal, mag_id
            ) VALUES (
              '$emp_id', '$emp_name', '$emp_mail', '$emp_phone',
              '$emp_password', '$emp_gender', '$target_path', 
              '$emp_role', '$emp_dept', '$emp_exp', $webD, $auto, $design, $verbal, " . 
              ($mag_id ? "'$mag_id'" : "NULL") . "
            )";

    if (mysqli_query($con, $sql)) {
        $registration_success = true;

        $q = "INSERT INTO leaveapp (leave_id) VALUES ('$emp_id');";

        mysqli_query($con,$q) ;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CorpConnect | Employee Sign Up</title>
  <link rel="stylesheet" href="emp-Signup.css" />
  <link rel="stylesheet" href="../src/output.css">
  <script defer src="emp-Signup.js"></script>
  <style>
    /* Range slider specific styles */
    .range-slider-container {
      margin-bottom: 1.5rem;
    }
    
    .range-slider {
      -webkit-appearance: none;
      width: 100%;
      height: 8px;
      border-radius: 5px;  
      background: linear-gradient(to right, #4f46e5, #8b5cf6);
      outline: none;
      margin: 10px 0;
    }
    
    .range-slider::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 20px;
      height: 20px;
      border-radius: 50%; 
      background: #fff;
      cursor: pointer;
      box-shadow: 0 0 5px rgba(99, 102, 241, 0.5);
      transition: all 0.2s ease;
    }
    
    .range-slider::-moz-range-thumb {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: #fff;
      cursor: pointer;
      box-shadow: 0 0 5px rgba(99, 102, 241, 0.5);
      transition: all 0.2s ease;
    }
    
    .range-slider::-webkit-slider-thumb:hover {
      transform: scale(1.1);
      box-shadow: 0 0 8px rgba(99, 102, 241, 0.8);
    }
    
    .range-slider::-moz-range-thumb:hover {
      transform: scale(1.1);
      box-shadow: 0 0 8px rgba(99, 102, 241, 0.8);
    }
    
    .slider-value {
      display: inline-block;
      position: relative;
      width: 60px;
      color: white;
      line-height: 20px;
      text-align: center;
      border-radius: 3px;
      background: #4f46e5;
      padding: 5px 10px;
      margin-left: 10px;
    }
    
    .slider-label {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .skills-section {
      background: rgba(30, 41, 59, 0.5);
      border-radius: 10px;
      padding: 20px;
      margin-top: 20px;
      border: 1px solid rgba(99, 102, 241, 0.2);
    }
    
    .skills-header {
      font-size: 1.2rem;
      margin-bottom: 15px;
      color: #e2e8f0;
      border-bottom: 1px solid rgba(99, 102, 241, 0.3);
      padding-bottom: 10px;
    }
  </style>
</head>

<body class="m-0 p-0 font-sans min-h-screen flex items-center justify-center bg-gradient-dark text-white">
  <main class="w-full max-w-5xl px-8 py-8">
    <!-- Navigation buttons container -->
    
    <!-- Employee Sign Up Form -->
    <div id="signupCard" class="bg-slate-900/95 rounded-2xl p-8 shadow-xl shadow-indigo-500/30 backdrop-blur-lg border border-indigo-400 border-opacity-20 animate-card-entrance" <?php if ($registration_success) echo 'style="display: none;"'; ?>>

      <div class="fixed top-6 left-0 right-0 w-full flex justify-between px-6 md:px-12 z-50 max-w-7xl mx-auto">
        <!-- Home Button (left) -->
        <a href="../index.php" class="nav-button flex items-center bg-gradient-to-r from-teal-500 via-blue-500 to-indigo-500 text-white py-2 px-4 md:py-3 md:px-6 rounded-xl shadow-lg transition-all duration-300 hover:from-teal-600 hover:via-blue-600 hover:to-indigo-600 hover:scale-105 hover:shadow-xl group">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 mr-2 icon-float group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span class="font-semibold text-sm md:text-base">Home</span>
        </a>
      
        <!-- Previous Page Button (right) -->
        <a href="javascript:history.back()" class="nav-button flex items-center bg-gradient-to-r from-purple-500 via-fuchsia-500 to-pink-500 text-white py-2 px-4 md:py-3 md:px-6 rounded-xl shadow-lg transition-all duration-300 hover:from-purple-600 hover:via-fuchsia-600 hover:to-pink-600 hover:scale-105 hover:shadow-xl group">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 mr-2 icon-float group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span class="font-semibold text-sm md:text-base">Previous</span>
        </a>
      </div>
      
      <div class="text-center mb-8 relative">
        <!-- Logo + Pulse Ring -->
        <div class="flex justify-center items-center mb-4 relative">
          <div class="logo-pulse"></div> <!-- Pulse ring using indigo-themed animation -->
          <div class="w-24 h-24 overflow-hidden rounded-xl relative z-10">
            <img src="../images/Logo/logo.png" alt="CorpConnect Logo"
                 class="object-contain w-full h-full" />
          </div>
        </div>
      
        <!-- Heading with updated gradient text -->
        <h1 class="text-4xl leading-tight font-bold sm:leading-snug text-transparent bg-clip-text text-gradient mb-2">
          Register as Employee
        </h1>
      
        <!-- Pulse Bar Animation -->
        <div class="flex justify-center gap-2.5 my-2.5">
          <span class="block h-1 w-10 rounded-sm bg-indigo-400 animate-pulse-delay-1"></span>
          <span class="block h-1 w-10 rounded-sm bg-purple-500 animate-pulse-delay-2"></span>
          <span class="block h-1 w-10 rounded-sm bg-violet-500 animate-pulse-delay-3"></span>
        </div>
      
        <!-- Description -->
        <p class="text-slate-400 text-base">
          Join your organization on the CryBug platform.
        </p>
      </div>
      
      <form id="employeeSignupForm" action="" method="post" enctype="multipart/form-data" class="flex flex-wrap gap-6 ">
        <div class="flex-1 min-w-[300px] flex flex-col gap-6">
          <div class="flex flex-col relative mb-3">
            <label for="employeeName" class="text-sm mb-1.5 text-slate-200">Full Name</label>
            <input type="text" id="employeeName" name="employeeName" placeholder="Enter your full name" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="employeeName-error" class="error-message">Please enter your full name</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="email" class="text-sm mb-1.5 text-slate-200">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="email-error" class="error-message">Please enter a valid email address</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="phone" class="text-sm mb-1.5 text-slate-200">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your 10-digit phone number" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="phone-error" class="error-message">Please enter a valid 10-digit phone number</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="gender" class="text-sm mb-1.5 text-slate-200">Gender</label>
            <select id="gender" name="gender" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
              <option value="">Select Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
            <p id="gender-error" class="error-message">Please select your gender</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="department" class="text-sm mb-1.5 text-slate-200">Department</label>
            <input type="text" id="department" name="department" placeholder="Enter your department" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="department-error" class="error-message">Please enter your department</p>
          </div>
          
          <!-- New Manager ID field on left column at the end -->
          <div class="flex flex-col relative mb-3">
            <label for="mag_id" class="text-sm mb-1.5 text-slate-200">Manager ID (Optional)</label>
            <input type="text" id="mag_id" name="mag_id" placeholder="Enter your manager's ID if applicable" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
          </div>
        </div>
        
        <!-- Right Column -->
        <div class="flex-1 min-w-[300px] flex flex-col gap-6">
          <div class="flex flex-col relative mb-3">
            <label for="role" class="text-sm mb-1.5 text-slate-200">Role</label>
            <input type="text" id="role" name="role" placeholder="Enter your role" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="role-error" class="error-message">Please enter your role</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="experience" class="text-sm mb-1.5 text-slate-200">Experience (years)</label>
            <input type="text" id="experience" name="experience" placeholder="Enter years of experience" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="experience-error" class="error-message">Please enter valid experience</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="password" class="text-sm mb-1.5 text-slate-200">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <span class="password-toggle absolute right-2.5 top-[37px] cursor-pointer text-slate-400" id="passwordToggle">👁️</span>
            <p id="password-error" class="error-message">Password must be at least 6 characters</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="confirm-password" class="text-sm mb-1.5 text-slate-200">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <span class="password-toggle absolute right-2.5 top-[37px] cursor-pointer text-slate-400" id="confirmPasswordToggle">👁️</span>
            <p id="confirm-password-error" class="error-message">Passwords do not match</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="employeeProfile" class="text-sm mb-1.5 text-slate-200">Upload Profile Picture</label>
            <div class="relative w-full mb-2.5">
              <input type="file" id="employeeProfile" name="employeeProfile" accept="image/*" class="absolute top-0 left-0 w-[0.1px] h-[0.1px] opacity-0 overflow-hidden z-[-1]">
              <div class="flex items-center w-full">
                <span id="file-chosen" class="mr-2.5 flex-grow overflow-hidden text-ellipsis whitespace-nowrap p-2 bg-slate-800 rounded-md border border-slate-700 text-slate-400">No file chosen</span>
                <button type="button" class="file-upload-btn bg-gradient-to-r from-indigo-500 to-violet-400 text-white py-2 px-3 rounded-md cursor-pointer border-none text-sm whitespace-nowrap transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/40">Choose File</button>
              </div>
              <div id="image-preview" class="image-preview"></div>
            </div>
            <p id="employeeProfile-error" class="error-message">Please select a profile picture</p>
          </div>
        </div>
        
        <div class="w-full skills-section">
          <h3 class="skills-header flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Knowledge & Skills Assessment
          </h3>
          
          <!-- Web Development Skills -->
          <div class="range-slider-container">
            <div class="slider-label">
              <label for="webD" class="text-sm text-slate-200">Web Development Skills</label>
              <span id="webD-value" class="slider-value">50%</span>
            </div>
            <input type="range" id="webD" name="webD" min="0" max="100" value="50" class="range-slider" oninput="updateSliderValue('webD')">
          </div>
          
          <!-- Automation Skills -->
          <div class="range-slider-container">
            <div class="slider-label">
              <label for="auto" class="text-sm text-slate-200">Automation Skills</label>
              <span id="auto-value" class="slider-value">50%</span>
            </div>
            <input type="range" id="auto" name="auto" min="0" max="100" value="50" class="range-slider" oninput="updateSliderValue('auto')">
          </div>
          
          <!-- Design Skills -->
          <div class="range-slider-container">
            <div class="slider-label">
              <label for="design" class="text-sm text-slate-200">Design Skills</label>
              <span id="design-value" class="slider-value">50%</span>
            </div>
            <input type="range" id="design" name="design" min="0" max="100" value="50" class="range-slider" oninput="updateSliderValue('design')">
          </div>
          
          <!-- Soft Skills (Verbal) -->
          <div class="range-slider-container">
            <div class="slider-label">
              <label for="verbal" class="text-sm text-slate-200">Soft Skills</label>
              <span id="verbal-value" class="slider-value">50%</span>
            </div>
            <input type="range" id="verbal" name="verbal" min="0" max="100" value="50" class="range-slider" oninput="updateSliderValue('verbal')">
          </div>
        </div>

        <!-- Employee ID Section (Positioned on the right) -->
        <div class="w-full flex justify-end mt-6 mb-6">
          <div class="w-full md:w-1/3 ml-auto">
            <label for="employeeID" class="block text-sm text-slate-200 mb-2">Your Employee ID</label>
            <input type="text" id="employeeID" name="employeeID" readonly placeholder="Generated after registration"
              class="w-full p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base">
          </div>
        </div>

        <!-- Full Width Button Row -->
        <div class="w-full mt-4">
          <button type="submit" id="submitButton" name="sub" value='1' class="w-full bg-gradient-to-r from-indigo-700 to-violet-700 py-3 border-none rounded-xl text-base font-bold text-white cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-indigo-700/40 hover:animate-button-pulse">
            Register as Employee
          </button>
          <p class="text-center text-sm mt-6 text-slate-400">
            Already registered as an employee?
            <a href="emp-Login.php" class="text-indigo-400 no-underline transition-all duration-300 hover:text-indigo-300 hover:underline">Login</a>
          </p>
        </div>
      </form>
    </div>
    
    <!-- Success Message (Initially Hidden) -->
    <div id="successCard" class="bg-slate-900/95 rounded-2xl p-8 shadow-xl shadow-indigo-500/30 backdrop-blur-lg border border-indigo-500/10 <?php echo $registration_success ? 'flex' : 'hidden'; ?> flex-col items-center justify-center text-center h-full">
      <div class="text-5xl text-green-500 mb-4 animate-success-entrance animate-float">✓</div>
      <h2 class="text-2xl mb-4 text-slate-100">Employee Registration Successful!</h2>
      <p>Your account has been registered successfully.</p>
      <div class="bg-indigo-500/10 py-4 px-8 rounded-lg border border-indigo-500/30 text-xl text-indigo-500 font-bold my-4 animate-pulse" id="successEmployeeId"><?php echo $emp_id; ?></div>
      <p>Please take a screenshot or note down your Employee ID.</p>
      <p>You'll need it to access your employee dashboard.</p>
      <div class="mt-5 flex justify-center">
        <button id="doneButton" class="bg-gradient-to-r from-indigo-500 to-violet-500 text-white border-none py-3 px-8 rounded-lg text-base font-bold cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-indigo-500/40">Done</button>
      </div>
      <p class="text-sm text-slate-400 mt-4" id="countdown">Redirecting to login page in <span id="secondsLeft">30</span> seconds...</p>
    </div>
  </main>


  <?php if ($registration_success): ?>
  <script>
    let secondsLeft = 30;
    const secondsLeftSpan = document.getElementById('secondsLeft');
    const intervalId = setInterval(function() {
      secondsLeft--;
      secondsLeftSpan.textContent = secondsLeft;
      
      if (secondsLeft <= 0) {
        clearInterval(intervalId);
        window.location.href = 'emp-Login.php';
      }
    }, 1000);
    
    document.getElementById('successEmployeeId').textContent = '<?php echo $emp_id; ?>';
    
    document.getElementById('doneButton').addEventListener('click', function() {
      clearInterval(intervalId);
      window.location.href = 'emp-Login.php';
    });
  </script>
  <?php endif; ?>
  
  <script>
    // Add this function if it doesn't already exist
    function updateSliderValue(sliderId) {
      const slider = document.getElementById(sliderId);
      const valueDisplay = document.getElementById(sliderId + '-value');
      valueDisplay.textContent = slider.value + '%';
    }
  </script>
</body>
</html>
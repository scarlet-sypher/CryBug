<?php

session_start() ;

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "connection.php";

$registration_success = false;
$manager_id = "";

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
  if (empty($_POST['userID'])) {

    $prefix = 'CRYMGR';
    $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $timestamp = substr(time(), -4);
    $manager_id = $prefix . $randomDigits . $timestamp;


  }

  else $manager_id = $_POST['userID'] ;
    // Generate a unique manager ID
   
    
    // Get form data and sanitize inputs
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $cmp_id = mysqli_real_escape_string($con, $_POST['cmp_id']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $role = mysqli_real_escape_string($con, $_POST['role']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']); // Added gender field
    
    // Handle profile picture upload
    $profile_pic = "";
    $target_path = "";
    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
        $pic_tmp_name = $_FILES['profilePic']['tmp_name'];
        $pic_name = basename($_FILES['profilePic']['name']);
        $upload_dir = "../uploads/manager_images/";

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $target_path = $upload_dir . $pic_name;
        move_uploaded_file($pic_tmp_name, $target_path);
    }

    // Insert data into the manager table
    $sql = "INSERT INTO manager (
              mag_id, mag_cmp_id, mag_name, mag_email, 
              mag_phone, mag_password, mag_role, mag_profile, mag_gender
            ) VALUES (
              '$manager_id', '$cmp_id', '$fullname', '$email',
              '$phone', '$password', '$role', '$target_path', '$gender'
            )";

    if (mysqli_query($con, $sql)) {
        $registration_success = true;
        $q = "INSERT INTO leaveapp (leave_id) VALUES ('$manager_id');";

        mysqli_query($con,$q) ;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Manager Sign Up</title>
  <link rel="stylesheet" href="leader-Signup.css" />
  <link rel="stylesheet" href="../src/output.css">
  <script defer src="leader-Signup.js"></script>
</head>

<body class="m-0 p-0 font-sans min-h-screen flex items-center justify-center bg-gradient-dark text-white">
  <main class="w-full max-w-5xl px-8 py-8">
    
    <!-- Sign Up Form -->
    <div id="signupCard" class="bg-slate-900/95 rounded-2xl p-8 shadow-xl shadow-cyan-500/30 backdrop-blur-lg border border-cyan-400 border-opacity-20 animate-card-entrance" <?php if ($registration_success) echo 'style="display: none;"'; ?>>

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
        <div class="flex justify-center items-center mb-4 relative">
          <div class="logo-pulse"></div>
          <div class="w-24 h-24 overflow-hidden rounded-xl relative z-10">
            <img src="../images/Logo/logo.png" alt="CryBug Logo"
                 class="object-contain w-full h-full" />
          </div>
        </div>
        <h1 class="text-4xl leading-tight font-bold sm:leading-snug text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-teal-500 mb-2">Create Your CryBug Account</h1>
        <div class="flex justify-center gap-2.5 my-2.5">
          <span class="block h-1 w-10 rounded-sm bg-cyan-400 animate-pulse-delay-1"></span>
          <span class="block h-1 w-10 rounded-sm bg-blue-500 animate-pulse-delay-2"></span>
          <span class="block h-1 w-10 rounded-sm bg-teal-500 animate-pulse-delay-3"></span>
        </div>
        <p class="text-slate-400 text-base">Streamline bug tracking and team collaboration with CryBug.</p>
      </div>
      
      <form id="signupForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="flex flex-wrap gap-6" enctype="multipart/form-data">
        <!-- Left Column -->
        <div class="flex-1 min-w-[300px] flex flex-col gap-6">

          <div class="flex flex-col relative mb-3">
            <label for="fullname" class="text-sm mb-1.5 text-slate-200">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-cyan-400 focus:shadow-input-focus" required>
            <p id="fullname-error" class="error-message">Please enter your full name</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="email" class="text-sm mb-1.5 text-slate-200">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-cyan-400 focus:shadow-input-focus" required>
            <p id="email-error" class="error-message">Please enter a valid email address</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="phone" class="text-sm mb-1.5 text-slate-200">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your 10-digit phone number" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-cyan-400 focus:shadow-input-focus" required>
            <p id="phone-error" class="error-message">Please enter a valid 10-digit phone number</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="cmp_id" class="text-sm mb-1.5 text-slate-200">Company ID</label>
            <input type="text" id="cmp_id" name="cmp_id" placeholder="Enter your company id" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-cyan-400 focus:shadow-input-focus" required>
            <p id="cmp_id-error" class="error-message">Please enter your Company ID</p>
          </div>
          
          <!-- Added Gender field -->
          <div class="flex flex-col relative mb-3">
            <label for="gender" class="text-sm mb-1.5 text-slate-200">Gender</label>
            <select id="gender" name="gender" class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-cyan-400 focus:shadow-input-focus" required>
              <option value="">-- Select Gender --</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
              <option value="Prefer not to say">Prefer not to say</option>
            </select>
            <p id="gender-error" class="error-message">Please select your gender</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="profilePic" class="text-sm mb-1.5 text-slate-200">Upload Profile Picture</label>
            <div class="relative w-full mb-2.5">
              <input type="file" id="profilePic" name="profilePic" accept="image/*" class="absolute top-0 left-0 w-[0.1px] h-[0.1px] opacity-0 overflow-hidden z-[-1]">
              <div class="flex items-center w-full">
                <span id="file-chosen" class="mr-2.5 flex-grow overflow-hidden text-ellipsis whitespace-nowrap p-2 bg-slate-800 rounded-md border border-slate-700 text-slate-400">No file chosen</span>
                <button type="button" class="file-upload-btn bg-gradient-to-r from-cyan-500 to-teal-400 text-white py-2 px-3 rounded-md cursor-pointer border-none text-sm whitespace-nowrap transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-cyan-500/40">Choose File</button>
              </div>
              <div id="image-preview" class="image-preview"></div>
            </div>
            <p id="profilePic-error" class="error-message">Please select a profile picture</p>
          </div>
        </div>
        
        <!-- Right Column -->
        <div class="flex-1 min-w-[300px] flex flex-col gap-6">
          <div class="flex flex-col relative mb-3">
            <label for="password" class="text-sm mb-1.5 text-slate-200">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-cyan-400 focus:shadow-input-focus" required>
            <span class="password-toggle absolute right-2.5 top-[37px] cursor-pointer text-slate-400" id="passwordToggle">👁️</span>
            <p id="password-error" class="error-message">Password must be at least 6 characters</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="confirm-password" class="text-sm mb-1.5 text-slate-200">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-cyan-400 focus:shadow-input-focus" required>
            <span class="password-toggle absolute right-2.5 top-[37px] cursor-pointer text-slate-400" id="confirmPasswordToggle">👁️</span>
            <p id="confirm-password-error" class="error-message">Passwords do not match</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="role" class="text-sm mb-1.5 text-slate-200">Select Role</label>
            <select id="role" name="role" class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-cyan-400 focus:shadow-input-focus" required>
              <option value="">-- Select Role --</option>
              <option value="Developer">Developer</option>
              <option value="Tester">Tester</option>
              <option value="Manager">Manager</option>
              <option value="Admin">Admin</option>
              <option value="Project Owner">Project Owner</option>
            </select>
            <p id="role-error" class="error-message">Please select a role</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="userID" class="text-sm mb-1.5 text-slate-200">Your Unique ID</label>
            <input type="text" id="userID" name="userID" readonly placeholder="Generated after signup" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base">
          </div>
          
          <div class="flex flex-col relative mb-3">
            <a href="#" id="forgotPasswordLink" class="text-cyan-400 text-sm mb-2 inline-block no-underline transition-all duration-300 hover:text-cyan-500 hover:underline">Forgot Password? Reset here</a>
          </div>
        </div>
        
        <!-- Full Width Button Row -->
        <div class="w-full mt-4">
          <button type="submit" id="submitButton" name="submit-btn" class="w-full bg-gradient-to-r from-cyan-700 to-teal-700 py-3 border-none rounded-xl text-base font-bold text-white cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-cyan-700/40 hover:animate-button-pulse">
            Sign Up
          </button>
          <p class="text-center text-sm mt-6 text-slate-400">
            Already have an account?
            <a href="manager-Login.php" class="text-cyan-400 no-underline transition-all duration-300 hover:text-cyan-300 hover:underline">Login</a>
          </p>
        </div>
      </form>
    </div>
    
    <!-- Success Message (Initially Hidden) -->
    <div id="successCard" class="bg-slate-900/95 rounded-2xl p-8 shadow-xl shadow-cyan-500/30 backdrop-blur-lg border border-cyan-500/10 <?php echo $registration_success ? 'flex' : 'hidden'; ?> flex-col items-center justify-center text-center h-full">
      <div class="text-5xl text-teal-500 mb-4 animate-success-entrance animate-float">✓</div>
      <h2 class="text-2xl mb-4 text-slate-100">Registration Successful!</h2>
      <p>Your account has been created successfully.</p>
      <div class="bg-teal-500/10 py-4 px-8 rounded-lg border border-teal-500/30 text-xl text-teal-500 font-bold my-4 animate-pulse" id="successUserId"><?php echo $manager_id; ?></div>
      <p>Please take a screenshot or note down your ID.</p>
      <p>You'll need it to access your account.</p>
      <div class="mt-5 flex justify-center">
        <button id="doneButton" class="bg-gradient-to-r from-cyan-500 to-teal-500 text-white border-none py-3 px-8 rounded-lg text-base font-bold cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-cyan-500/40">Done</button>
      </div>
      <p class="text-sm text-slate-400 mt-4" id="countdown">Redirecting to login page in <span id="secondsLeft">30</span> seconds...</p>
    </div>
    
    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="modal">
      <div class="bg-slate-900/95 mx-auto mt-[10%] p-8 border border-cyan-500/20 w-[80%] max-w-lg rounded-2xl shadow-xl shadow-cyan-500/30 animate-modal-fade-in">
        <span class="text-slate-400 float-right text-2xl font-bold transition-all duration-300 hover:text-slate-200 cursor-pointer" id="closeModal">&times;</span>
        <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-teal-500 text-2xl mt-0">Reset Your Password</h2>
        <p class="text-slate-400 text-base">Enter your email to receive a password reset link.</p>
        
        <form id="forgotPasswordForm">
          <div class="flex flex-col relative mb-3">
            <label for="reset-email" class="text-sm mb-1.5 text-slate-200">Email Address</label>
            <input type="email" id="reset-email" name="reset-email" placeholder="Enter your registered email" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-cyan-400 focus:shadow-input-focus">
            <p id="reset-email-error" class="error-message">Please enter a valid email address</p>
          </div>
          
          <div class="flex flex-col relative mt-4">
            <button type="submit" class="w-full bg-gradient-to-r from-cyan-700 to-teal-700 py-3 border-none rounded-xl text-base font-bold text-white cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-cyan-700/40" id="resetPasswordBtn">
              Send Reset Link
            </button>
          </div>
        </form>
        
        <div id="resetConfirmation" class="mt-4 text-center hidden">
          <div class="text-teal-500 text-sm">Password reset link sent to your email!</div>
        </div>
      </div>
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
        window.location.href = 'manager-Login.php';
      }
    }, 1000);
    
    document.getElementById('successUserId').textContent = '<?php echo $manager_id; ?>';
    
    document.getElementById('doneButton').addEventListener('click', function() {
      clearInterval(intervalId);
      window.location.href = 'manager-Login.php';
    });
  </script>
  <?php endif; ?>

</body>
</html>
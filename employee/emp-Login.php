<?php 



  session_start() ;
  include "connection.php";


  /** 
   * ! For universal session 
   */

   if (isset($_SESSION['user_role'])) {
    // Redirect to appropriate dashboard based on role
    switch ($_SESSION['user_role']) {
        case 'employee':
            header("Location: ../employeeProfile/dashboard.php");
            break;
        case 'manager':
            header("Location: ../profile/dashboard.php");
            break;
        case 'company':
            header("Location: ../companyProfile/dashboard.php");
            break;
    }
    exit();
}


  $loginError = "";
  $loginSuccess = "";

  if (isset($_POST['submit-btn'])) {
    $id = $_POST['identifier'];
    $password = $_POST['password'];

    // Check if fields are empty
    if(empty($id) || empty($password)) {
      $loginError = "Please fill in both fields.";
    } else {
      // Query to check credentials
      $query = "SELECT * FROM employee WHERE emp_id='$id' AND emp_password='$password'";
      $data = mysqli_query($con, $query);
      
      // Check if query executed successfully
      if($data) {
        $num = mysqli_num_rows($data);
        
        if ($num > 0) {
          // Login successful



          $row = mysqli_fetch_assoc($data);
          $_SESSION['emp_id'] = $row['emp_id'];
          $_SESSION['emp_name'] = $row['emp_name'];
          $_SESSION['emp_profile'] = $row['emp_profile'];
          $_SESSION['emp_mail'] = $row['emp_mail'];
          $_SESSION['emp_role'] = $row['emp_role'] ;
          $_SESSION['emp_phone'] = $row['emp_phone'] ;
          $_SESSION['emp_dept'] = $row['emp_dept'] ;
          $_SESSION['emp_exp'] = $row['emp_exp'] ;
          $_SESSION['mag_id'] = $row['mag_id'] ;

          $_SESSION['dev'] = $row['webD'] ; 
          $_SESSION['auto'] = $row['auto'] ; 
          $_SESSION['design'] = $row['design'] ; 
          $_SESSION['verbal'] = $row['verbal'] ; 

          $_SESSION['logged_in'] = true ;


          $_SESSION['user_role'] = 'employee';
          $_SESSION['user_id'] = $id;
          $_SESSION['user_name'] = $row['emp_name'];

          $_SESSION['github'] = $row['github'] ;
          $_SESSION['linkedin'] = $row['linkedin'] ;
          $_SESSION['x'] = $row['x'] ;


          $id = $_SESSION['mag_id'] ;

          $sql = "SELECT * FROM manager WHERE mag_id = '" . $_SESSION['mag_id'] . "'";
          $result = mysqli_query($con, $sql);

          if (mysqli_num_rows($result) > 0) {

              $row = mysqli_fetch_assoc($result);

              $_SESSION['mag_name'] = $row['mag_name'];
              $_SESSION['mag_profile'] = $row['mag_profile'] ;
              $_SESSION['mag_role'] = $row['mag_role'] ;
              $_SESSION['mag_email'] = $row['mag_email'] ;
              $_SESSION['mag_phone'] = $row['mag_phone'] ;
              $_SESSION['mag_cmp_id'] = $row['mag_cmp_id'] ;
              $_SESSION['mag_exp'] = $row['mag_exp'] ;


              $cp = "SELECT * FROM company WHERE cmp_id = '" . $_SESSION['mag_cmp_id'] . "'";
              $r = mysqli_query($con, $cp);


              if (mysqli_num_rows($r) > 0) {

                $col = mysqli_fetch_assoc($r);

                $_SESSION['cmp_id'] = $col['cmp_id'] ;
                $_SESSION['cmp_name'] = $col['cmp_name'] ;
                $_SESSION['cmp_descp'] = $col['cmp_descp'] ;
                $_SESSION['cmp_mail'] = $col['cmp_mail'] ;
                $_SESSION['cmp_phone'] = $col['cmp_phone'] ;
                $_SESSION['cmp_address'] = $col['cmp_address'] ;

              }

          } else {
            $_SESSION['cmp_name'] = "Unknown Company";
            $_SESSION['mag_name'] = "NA";
            $_SESSION['mag_profile'] = "../images/Profile/guest.png" ;
            $_SESSION['mag_role'] = "NA" ;
            $_SESSION['mag_email'] = "NA" ;
            $_SESSION['mag_phone'] = "NA" ;
          }


          
          $loginSuccess = "Login successful! Redirecting to workspace...";
          echo "<script>
            setTimeout(() => {
              window.location.href = '../employeeProfile/dashboard.php';
            }, 2000);
          </script>";
        } else {
          // Login failed
          $loginError = "Login failed. Invalid credentials.";
        }
      } else {
        // Query execution failed
        $loginError = "Database error. Please try again later.";
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Employee Login - CryBug</title>
  <link rel="stylesheet" href="emp-Login.css" />
  <link rel="stylesheet" href="../src/output.css">
  <script src="emp-Login.js" defer></script>

</head>
<body class="bg-gradient-dark min-h-screen font-sans text-white">

  <div class="flex flex-col items-center justify-center min-h-screen p-4 relative">
    <!-- Login Card -->
    <div class="bg-gray-900 rounded-2xl p-8 max-w-md w-full shadow-lg login-card border border-pink-400 border-opacity-20 mx-auto backdrop-blur-lg shadow-pink-400/30 animate-card-entrance">
      <div class="fixed top-6 left-0 right-0 w-full flex justify-between px-6 md:px-12 z-50 max-w-7xl mx-auto">
        <!-- Home Button (left) -->
        <a href="../index.php" class="nav-button flex items-center pr-2 justify-center bg-gradient-to-r from-teal-500 via-blue-500 to-indigo-500 text-white py-2 px-4 md:py-3 md:px-6 rounded-xl shadow-lg transition-all duration-300 hover:from-teal-600 hover:via-blue-600 hover:to-indigo-600 hover:scale-105 hover:shadow-xl group">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 mr-2 icon-float group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </a>
      
        <!-- Previous Page Button (right) -->
        <a href="../login-pages/login.html" class="nav-button flex pr-2 items-center justify-center bg-gradient-to-r from-purple-500 via-fuchsia-500 to-pink-500 text-white py-2 px-4 md:py-3 md:px-6 rounded-xl shadow-lg transition-all duration-300 hover:from-purple-600 hover:via-fuchsia-600 hover:to-pink-600 hover:scale-105 hover:shadow-xl group">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 mr-2 icon-float group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </a>
      </div>

      <div class="text-center mb-8 relative">
        <!-- Logo Section -->
        <div class="flex justify-center items-center mb-4 relative">
          <div class="logo-pulse"></div>
          <div class="w-24 h-24 overflow-hidden rounded-xl relative z-10">
            <img src="../images/Logo/logo.png" alt="CryBug Logo" class="object-contain w-full h-full" />
          </div>
        </div>

        <!-- Title -->
        <h2 class="text-3xl font-bold text-gradient mb-2 leading-tight sm:leading-snug">Welcome to CryBug</h2>

        <!-- Animated Bars -->
        <div class="flex justify-center gap-2.5 my-2.5">
          <span class="block h-1 w-10 rounded-sm bg-pink-400 animate-pulse-delay-1"></span>
          <span class="block h-1 w-10 rounded-sm bg-purple-400 animate-pulse-delay-2"></span>
          <span class="block h-1 w-10 rounded-sm bg-fuchsia-400 animate-pulse-delay-3"></span>
        </div>

        <!-- Subtext -->
        <p class="text-gray-400 text-sm">Employee Management System</p>
      </div>

      <!-- Status Messages - These will appear before the form -->
      <?php if(!empty($loginSuccess)): ?>
        <div class="status-message show bg-green-800 border-2 border-green-500 text-green-300 py-3 font-medium text-center rounded">
          <?php echo $loginSuccess; ?>
        </div>
      <?php endif; ?>
      
      <?php if(!empty($loginError)): ?>
        <div class="status-message show bg-red-800 border-2 border-red-500 text-red-300 py-3 font-medium text-center rounded">
          <?php echo $loginError; ?>
        </div>
      <?php endif; ?>

      <form id="loginForm" class="space-y-5" method="post" action="">
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-300">Employee ID</label>
          <input type="text" name="identifier" required
            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-300">Password</label>
          <div class="relative">
            <input type="password" name="password" required
              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent pr-10" />
            <span id="passwordToggle" class="absolute right-3 top-2.5 cursor-pointer select-none text-gray-400 hover:text-white transition-colors">👁️</span>
          </div>
        </div>

        <div class="flex justify-between items-center">
          <a href="#" id="forgotPasswordLink" class="text-pink-400 text-sm hover:text-pink-300 transition-colors">Forgot Password?</a>
        </div>

        <button type="submit" name="submit-btn" class="w-full btn-gradient py-3 rounded-lg text-gray-100 font-bold transition-all transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-pink-500">
          Login
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-400">
        Need access? <a href="emp-Signup.php" class="text-pink-400 hover:text-pink-300 transition-colors">Sign up now</a>
      </div>

      <div class="mt-6 pt-6 border-t border-gray-700 text-center">
        <p class="text-sm text-gray-500">Having trouble? Contact <a href="mailto:support@crybug.com" class="text-pink-400 hover:text-pink-300">IT Support</a></p>
      </div>
    </div>
  </div>

  <!-- Forgot Password Modal -->
  <div id="forgotPasswordModal" class="modal backdrop-blur-sm hidden fixed inset-0 w-full h-full bg-black/50 z-50 justify-center items-start pt-[50px] overflow-y-auto">
    <div class="bg-slate-900/95 rounded-xl w-11/12 max-w-md mx-auto mt-[10%] p-6 shadow-xl border border-pink-500/30 relative animate-modal-fade-in">
      <!-- Decorative backdrop elements -->
      <div class="absolute -z-10 inset-0 overflow-hidden rounded-xl">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-pink-500 rounded-full opacity-10 blur-xl"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-blue-500 rounded-full opacity-10 blur-xl"></div>
      </div>

      <span class="absolute top-4 right-4 text-slate-400 hover:text-white text-2xl font-bold cursor-pointer transition-all duration-300" id="closeModal">&times;</span>
      <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 via-pink-400 to-blue-500 text-xl font-semibold mb-2 text-center">Reset Your Password</h2>
      <p class="text-slate-400 mb-6 text-center">Enter your work email address and we'll send you a verification code.</p>

      <form id="forgotPasswordForm" class="space-y-4" method="POST" action="">
        <div>
          <label for="reset-email" class="block text-sm font-medium mb-1.5 text-slate-200">Work Email Address</label>
          <input type="email" id="reset-email" name="reset-email" required placeholder="Enter your company email"
            class="w-full bg-slate-800 border border-slate-700 rounded-lg p-3 text-white focus:outline-none focus:border-pink-400 focus:shadow-input-focus transition-all duration-300" />
          <p id="reset-email-error" class="error-message hidden text-red-500 text-xs mt-1">Please enter a valid company email address</p>
        </div>
        <div id="resetConfirmation" class="bg-green-500/10 border border-green-500/30 text-green-500 rounded-lg p-3 text-center text-sm hidden mt-4">
          OTP sent to your email! Check your inbox.
        </div>
        <button type="submit" id="resetPasswordBtn" name="reset-email-submit" class="w-full bg-gradient-to-r from-pink-700 to-purple-700 py-3 border-none rounded-xl text-base font-bold text-white cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-pink-700/40">
          Send OTP
        </button>
      </form>

      <form id="otpVerificationForm" class="space-y-4 hidden" method="POST" action="">
        <div>
          <label for="otp" class="block text-sm font-medium mb-1.5 text-slate-200">Enter OTP</label>
          <input type="text" id="otp" name="otp" required placeholder="Enter 6-digit OTP"
            class="w-full bg-slate-800 border border-slate-700 rounded-lg p-3 text-white focus:outline-none focus:border-pink-400 focus:shadow-input-focus transition-all duration-300" />
          <p id="otp-error" class="error-message hidden text-red-500 text-xs mt-1">Please enter a valid OTP</p>
        </div>
        <div id="otpConfirmation" class="bg-green-500/10 border border-green-500/30 text-green-500 rounded-lg p-3 text-center text-sm hidden mt-4">
          OTP verified successfully! Check your email for credentials.
        </div>
        <button type="submit" id="verifyOtpBtn" name="verify-otp-submit" class="w-full bg-gradient-to-r from-pink-700 to-purple-700 py-3 border-none rounded-xl text-base font-bold text-white cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-pink-700/40">
          Verify OTP
        </button>
      </form>
    </div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
  const forgotPasswordForm = document.getElementById('forgotPasswordForm');
  const otpVerificationForm = document.getElementById('otpVerificationForm');
  
  // Show OTP form after email submission (simplified example)
  forgotPasswordForm.addEventListener('submit', function(e) {
    e.preventDefault();

    forgotPasswordForm.classList.add('hidden');
    otpVerificationForm.classList.remove('hidden');
  });
});
</script>

</body>
</html>
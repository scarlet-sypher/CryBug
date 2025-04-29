<?php 

  session_start() ;
  include "connection.php";
  $loginError = "";
  $loginSuccess = "";


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

  if (isset($_POST['submit-btn'])) {
    $id = $_POST['cmp_id'];
    $password = $_POST['password'];

    // Check if fields are empty
    if(empty($id) || empty($password)) {
      $loginError = "Please fill in both fields.";
    } else {
      // Query to check credentials
      $query = "SELECT * FROM company WHERE cmp_id='$id' AND cmp_password='$password'";
      $data = mysqli_query($con, $query);
      
      // Check if query executed successfully
      if($data) {
        $num = mysqli_num_rows($data);
        
        if ($num > 0) {
          // Login successful

          $row = mysqli_fetch_assoc($data);
          $_SESSION['cmp_id'] = $row['cmp_id'];
          $_SESSION['cmp_name'] = $row['cmp_name'];
          $_SESSION['cmp_logo'] = $row['cmp_logo'];
          $_SESSION['cmp_mail'] = $row['cmp_mail'];
          $_SESSION['logged_in'] = true;

          $_SESSION['user_role'] = 'company';
          $_SESSION['user_id'] = $id;
          $_SESSION['user_name'] = $row['cmp_name'];

          $_SESSION['github'] = $row['github'] ;
          $_SESSION['linkedin'] = $row['linkedin'] ;
          $_SESSION['x'] = $row['x'] ;
         


          $loginSuccess = "Login successful! Redirecting to workspace...";
          echo "<script>
            setTimeout(() => {
              window.location.href = '../companyProfile/dashboard.php';
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
  <title>Login - CorpConnect</title>
  <link rel="stylesheet" href="company-Login.css" />
  <link rel="stylesheet" href="../src/output.css">
  <script src="company-Login.js" defer></script>

</head>
<body class="bg-gradient-dark min-h-screen font-sans text-white">

  <div class="flex flex-col items-center justify-center min-h-screen p-4 relative">
    <!-- Login Card -->
    <div class="bg-gray-900 rounded-2xl p-8 max-w-md w-full shadow-lg login-card border border-indigo-400 border-opacity-20 mx-auto backdrop-blur-lg shadow-indigo-400/30 animate-card-entrance">
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
            <img src="../images/Logo/logo.png" alt="CorpConnect Logo" class="object-contain w-full h-full" />
          </div>
        </div>

        <!-- Title -->
        <h2 class="text-3xl font-bold text-gradient mb-2 leading-tight sm:leading-snug">Welcome to CorpConnect</h2>

        <!-- Animated Bars -->
        <div class="flex justify-center gap-2.5 my-2.5">
          <span class="block h-1 w-10 rounded-sm bg-indigo-400 animate-pulse-delay-1"></span>
          <span class="block h-1 w-10 rounded-sm bg-purple-400 animate-pulse-delay-2"></span>
          <span class="block h-1 w-10 rounded-sm bg-violet-400 animate-pulse-delay-3"></span>
        </div>

        <!-- Subtext -->
        <p class="text-gray-400 text-sm">Enterprise Management System</p>
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
          <label class="block text-sm font-medium mb-1 text-gray-300">Employee ID or Email</label>
          <input type="text" name="cmp_id" required
            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-300">Password</label>
          <div class="relative">
            <input type="password" name="password" required
              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-10" />
            <span id="passwordToggle" class="absolute right-3 top-2.5 cursor-pointer select-none text-gray-400 hover:text-white transition-colors">👁️</span>
          </div>
        </div>

        <div class="flex justify-between items-center">
          <a href="#" id="forgotPasswordLink" class="text-indigo-400 text-sm hover:text-indigo-300 transition-colors">Forgot Password?</a>
        </div>

        <button type="submit" name="submit-btn" class="w-full btn-gradient py-3 rounded-lg text-gray-100 font-bold transition-all transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Login
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-400">
        Need access? <a href="company-Signup.php" class="text-indigo-400 hover:text-indigo-300 transition-colors">Sign up now</a>
      </div>

      <div class="mt-6 pt-6 border-t border-gray-700 text-center">
        <p class="text-sm text-gray-500">Having trouble? Contact <a href="mailto:support@corpconnect.com" class="text-indigo-400 hover:text-indigo-300">IT Support</a></p>
      </div>
    </div>
  </div>

  <!-- Forgot Password Modal -->
  <div id="forgotPasswordModal" class="modal backdrop-blur-sm hidden fixed inset-0 w-full h-full bg-black/50 z-50 justify-center items-start pt-[50px] overflow-y-auto">
    <div class="bg-slate-900/95 rounded-xl w-11/12 max-w-md mx-auto mt-[10%] p-6 shadow-xl border border-indigo-500/30 relative animate-modal-fade-in">
      <!-- Decorative backdrop elements -->
      <div class="absolute -z-10 inset-0 overflow-hidden rounded-xl">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-indigo-500 rounded-full opacity-10 blur-xl"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-violet-500 rounded-full opacity-10 blur-xl"></div>
      </div>

      <span class="absolute top-4 right-4 text-slate-400 hover:text-white text-2xl font-bold cursor-pointer transition-all duration-300" id="closeModal">&times;</span>
      <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-violet-500 text-xl font-semibold mb-2 text-center">Reset Your Password</h2>
      <p class="text-slate-400 mb-6 text-center">Enter your work email address and we'll send you a password reset link.</p>

      <form id="forgotPasswordForm" class="space-y-4">
        <div>
          <label for="reset-email" class="block text-sm font-medium mb-1.5 text-slate-200">Work Email Address</label>
          <input type="email" id="reset-email" name="reset-email" required placeholder="Enter your company email"
            class="w-full bg-slate-800 border border-slate-700 rounded-lg p-3 text-white focus:outline-none focus:border-indigo-400 focus:shadow-input-focus transition-all duration-300" />
          <p id="reset-email-error" class="error-message hidden text-red-500 text-xs mt-1">Please enter a valid company email address</p>
        </div>

        <div id="resetConfirmation" class="bg-green-500/10 border border-green-500/30 text-green-500 rounded-lg p-3 text-center text-sm hidden mt-4">
          Password reset link sent! Check your inbox.
        </div>

        <button type="submit" id="resetPasswordBtn" class="w-full bg-gradient-to-r from-indigo-700 to-violet-700 py-3 border-none rounded-xl text-base font-bold text-white cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-indigo-700/40">
          Send Reset Link
        </button>
      </form>
    </div>
  </div>

</body>
</html>
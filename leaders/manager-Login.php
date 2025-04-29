<?php 

  
  // if ($_SERVER["REQUEST_METHOD"] == "POST") {
  //   echo "<pre style='color:white;background:black;padding:10px;'>";
  //   echo "POST data received: \n";
  //   print_r($_POST);
  //   echo "</pre>";
  // }
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
    $Pid = $_POST['identifier'];
    $password = $_POST['password'];

    // Check if fields are empty
    if(empty($Pid) || empty($password)) {
      $loginError = "Please fill in both fields.";
    } else {
      // Query to check credentials
      $query = "SELECT * FROM manager WHERE mag_id='$Pid' AND mag_password='$password'";
      $data = mysqli_query($con, $query);
      
      // Check if query executed successfully
      if($data) {
        $num = mysqli_num_rows($data);
        
        if ($num > 0) {
          // Login successful

          $row = mysqli_fetch_assoc($data);
          $_SESSION['mag_id'] = $row['mag_id'];
          $_SESSION['mag_cmp_id'] = $row['mag_cmp_id'] ;
          $_SESSION['mag_name'] = $row['mag_name'];
          $_SESSION['mag_profile'] = $row['mag_profile'];
          $_SESSION['mag_email'] = $row['mag_email'];
          $_SESSION['mag_role'] = $row['mag_role'] ;
          $_SESSION['mag_phone'] = $row['mag_phone'] ;

          $id = $_SESSION['mag_cmp_id'] ;


          $_SESSION['user_role'] = 'manager';
          $_SESSION['user_id'] = $Pid;
          $_SESSION['user_name'] = $row['mag_name'];
          $_SESSION['Profile_Pic'] = $row['mag_profile'] ;
          $_SESSION['github'] = $row['github'] ;
          $_SESSION['linkedin'] = $row['linkedin'] ;
          $_SESSION['x'] = $row['x'] ;



          $sql = "SELECT * FROM company WHERE cmp_id = '" . $_SESSION['mag_cmp_id'] . "'";
          $result = mysqli_query($con, $sql);

          if (mysqli_num_rows($result) > 0) {
              $row = mysqli_fetch_assoc($result);
                $_SESSION['cmp_id'] = $row['cmp_id'] ;
                $_SESSION['cmp_name'] = $row['cmp_name'] ;
                $_SESSION['cmp_descp'] = $row['cmp_descp'] ;
                $_SESSION['cmp_mail'] = $row['cmp_mail'] ;
                $_SESSION['cmp_phone'] = $row['cmp_phone'] ;
                $_SESSION['cmp_address'] = $row['cmp_address'] ;
                $_SESSION['cmp_pincode'] = $row['cmp_pincode'] ;
                

          } else {
            $_SESSION['cmp_name'] = "Unknown Company";
          }

          $_SESSION['logged_in'] = true;


          
          $loginSuccess = "Login successful! Redirecting to workspace...";
          echo "<script>
            setTimeout(() => {
              window.location.href = '../profile/dashboard.php';
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
  <title>Login - CryBug</title>
  <link rel="stylesheet" href="leader-Login.css" />
  <link rel="stylesheet" href="../src/output.css">
  <script src="leader-Login.js" defer></script>
  <style>
    /* Ensure button gradient works, in case CSS is missing */
    .btn-gradient {
      background-image: linear-gradient(to right, #06b6d4, #3b82f6);
    }
  </style>
</head>
<body class="bg-gradient-dark min-h-screen font-sans text-white">
  <div class="flex flex-col items-center justify-center min-h-screen p-4 relative">
    <!-- Home Button at Top -->
    <!-- Login Card -->
    <div class="bg-slate-900/95 rounded-2xl p-8 max-w-lg w-full shadow-xl shadow-cyan-500/30 backdrop-blur-lg border border-cyan-400 border-opacity-20 animate-card-entrance">

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
        <div class="flex justify-center items-center mb-4 relative">
          <div class="logo-pulse"></div>
          <div class="w-24 h-24 overflow-hidden rounded-xl relative z-10">
            <img src="../images/Logo/logo.png" alt="CryBug Logo" class="object-contain w-full h-full" />
          </div>
        </div>
        <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-teal-500 mb-2 leading-tight sm:leading-snug">Welcome to CryBug</h1>
        <div class="flex justify-center gap-2.5 my-2.5">
          <span class="block h-1 w-10 rounded-sm bg-cyan-400 animate-pulse-delay-1"></span>
          <span class="block h-1 w-10 rounded-sm bg-blue-500 animate-pulse-delay-2"></span>
          <span class="block h-1 w-10 rounded-sm bg-teal-500 animate-pulse-delay-3"></span>
        </div>
        <p class="text-slate-400 text-sm">Login as Manager</p>
      </div>

      <!-- Status Messages - Display PHP messages -->
      <?php if(!empty($loginSuccess)): ?>
        <div class="status-message show bg-teal-900 border-2 border-teal-500 text-teal-300 py-3 font-medium text-center rounded mb-4">
          <?php echo $loginSuccess; ?>
        </div>
      <?php endif; ?>
      
      <?php if(!empty($loginError)): ?>
        <div class="status-message show bg-red-900 border-2 border-red-500 text-red-300 py-3 font-medium text-center rounded mb-4">
          <?php echo $loginError; ?>
        </div>
      <?php endif; ?>

      <!-- Fixed form with explicit action -->
      <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-5">
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-300">Unique ID</label>
          <input type="text" name="identifier" required placeholder="Enter your email or ID"
            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-300">Password</label>
          <div class="relative">
            <input type="password" name="password" required placeholder="Enter your password"
              class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent pr-10" />
            <span id="passwordToggle" class="absolute right-3 top-2.5 cursor-pointer select-none text-gray-400 hover:text-white transition-colors">👁️</span>
          </div>
        </div>

        <div class="flex justify-between items-center">
          <a href="#" id="forgotPasswordLink" class="text-cyan-400 text-sm hover:text-cyan-300 transition-colors">Forgot Password?</a>
        </div>

        <!-- Fixed submit button with explicit class and value -->
        <button type="submit" name="submit-btn" value="login" class="w-full btn-gradient py-3 rounded-lg text-gray-100 font-bold transition-all transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-500">
          Login
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-400">
        Don't have an account? <a href="manager-Signup.php" class="text-cyan-400 hover:text-cyan-300 transition-colors">Sign up now</a>
      </div>

      <div class="mt-6 pt-6 border-t border-gray-700 text-center">
        <p class="text-sm text-gray-500">Need help? Contact <a href="mailto:support@crybug.com" class="text-cyan-400 hover:text-cyan-300">IT Support</a></p>
      </div>
    </div>
  </div>

  <!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="modal backdrop-blur-sm hidden fixed inset-0 w-full h-full bg-black/50 z-50 justify-center items-start pt-[50px] overflow-y-auto">
  <div class="bg-slate-900/95 rounded-xl w-11/12 max-w-md mx-auto mt-[10%] p-6 shadow-xl border border-cyan-500/30 relative animate-modal-fade-in">
    <!-- Decorative Backdrop Elements -->
    <div class="absolute -z-10 inset-0 overflow-hidden rounded-xl">
      <div class="absolute -top-20 -right-20 w-40 h-40 bg-cyan-500 rounded-full opacity-10 blur-xl"></div>
      <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-teal-500 rounded-full opacity-10 blur-xl"></div>
    </div>

    <span class="absolute top-4 right-4 text-slate-400 hover:text-white text-2xl font-bold cursor-pointer transition-all duration-300" id="closeModal">&times;</span>
    <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-teal-500 text-xl font-semibold mb-2 text-center">Reset Your Password</h2>
    <p class="text-slate-400 mb-6 text-center">Enter your work email address and we'll send you an OTP.</p>

    <form action="forgot-password.php" method="post" class="space-y-4">
      <div>
        <label for="reset-email" class="block text-sm font-medium mb-1.5 text-slate-200">Work Email Address</label>
        <input type="email" id="reset-email" name="reset_email" required placeholder="Enter your company email"
          class="w-full bg-slate-800 border border-slate-700 rounded-lg p-3 text-white focus:outline-none focus:border-cyan-400 focus:shadow-input-focus transition-all duration-300" />
      </div>

      <button type="submit" name="verify_email" value="1" class="w-full btn-gradient py-3 border-none rounded-xl text-base font-bold text-white cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-cyan-700/40">
        Send Recovery Email
      </button>
    </form>
  </div>
</div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
  // Password toggle functionality
  const passwordToggle = document.getElementById('passwordToggle');
  const passwordInput = document.querySelector('input[name="password"]');
  
  if (passwordToggle && passwordInput) {
    passwordToggle.addEventListener('click', function() {
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordToggle.textContent = '🔒';
      } else {
        passwordInput.type = 'password';
        passwordToggle.textContent = '👁️';
      }
    });
  }
  
  // Modal functionality
  const forgotPasswordLink = document.getElementById('forgotPasswordLink');
  const forgotPasswordModal = document.getElementById('forgotPasswordModal');
  const closeModal = document.getElementById('closeModal');
  
  // Show modal when clicking "Forgot Password"
  if (forgotPasswordLink) {
    forgotPasswordLink.addEventListener('click', function(e) {
      e.preventDefault();
      if (forgotPasswordModal) {
        forgotPasswordModal.classList.remove('hidden');
        forgotPasswordModal.classList.add('flex');
      }
    });
  }
  
  // Close modal when clicking the X
  if (closeModal) {
    closeModal.addEventListener('click', function() {
      if (forgotPasswordModal) {
        forgotPasswordModal.classList.add('hidden');
        forgotPasswordModal.classList.remove('flex');
      }
    });
  }
  
  // Close modal when clicking outside
  if (forgotPasswordModal) {
    forgotPasswordModal.addEventListener('click', function(e) {
      if (e.target === forgotPasswordModal) {
        forgotPasswordModal.classList.add('hidden');
        forgotPasswordModal.classList.remove('flex');
      }
    });
  }
});
  </script>
</body>
</html>
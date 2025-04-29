<?php
session_start(); // Start session at the beginning


error_reporting(E_ALL);
ini_set('display_errors', 1);

include "connection.php";


$registration_success = false;
$cmp_id = "";


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (empty($_POST['companyID'])) {
        $prefix = 'CRYCOM';
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $timestamp = substr(time(), -4);
        $cmp_id = $prefix . $randomDigits . $timestamp;
    } else {
        $cmp_id = $_POST['companyID'];
    }

    // Get form data and sanitize inputs
    $cmp_name = mysqli_real_escape_string($con, $_POST['companyName']);
    $cmp_mail = mysqli_real_escape_string($con, $_POST['email']);
    $cmp_phone = mysqli_real_escape_string($con, $_POST['phone']);
    $cmp_address = mysqli_real_escape_string($con, $_POST['address']);
    $cmp_pincode = mysqli_real_escape_string($con, $_POST['pincode']);
    $cmp_password = mysqli_real_escape_string($con, $_POST['password']); 
    $cmp_descp = mysqli_real_escape_string($con, $_POST['companyDescription']); // New field
    $cmp_clients = isset($_POST['clients']) ? intval($_POST['clients']) : NULL; // New field
    
    $cmp_logo = "";
    $target_path = "";
    if (isset($_FILES['companyLogo']) && $_FILES['companyLogo']['error'] == 0) {
        $logo_tmp_name = $_FILES['companyLogo']['tmp_name'];
        $logo_name = basename($_FILES['companyLogo']['name']);
        $upload_dir = "../uploads/company_images/";

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $target_path = $upload_dir . $logo_name;
        move_uploaded_file($logo_tmp_name, $target_path);
    }

   
    $sql = "INSERT INTO company (
              cmp_id, cmp_name, cmp_pincode, cmp_mail, cmp_phone,
              cmp_password, cmp_logo, cmp_address, cmp_descp, cmp_clients
            ) VALUES (
              '$cmp_id', '$cmp_name', '$cmp_pincode', '$cmp_mail', '$cmp_phone',
              '$cmp_password', '$target_path', '$cmp_address', '$cmp_descp', " . ($cmp_clients !== NULL ? $cmp_clients : "NULL") . "
            )";


    if (mysqli_query($con, $sql)) {
        $registration_success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CorpConnect | Company Sign Up</title>
  <link rel="stylesheet" href="company-Signup.css" />
  <link rel="stylesheet" href="../src/output.css">
  <script defer src="company-Signup.js"></script>
</head>

<body class="m-0 p-0 font-sans min-h-screen flex items-center justify-center bg-gradient-dark text-white">
  <main class="w-full max-w-5xl px-8 py-8">
    <!-- Navigation buttons container -->
    
    <!-- Company Sign Up Form -->
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
          Register Your Company
        </h1>
      
        <!-- Pulse Bar Animation -->
        <div class="flex justify-center gap-2.5 my-2.5">
          <span class="block h-1 w-10 rounded-sm bg-indigo-400 animate-pulse-delay-1"></span>
          <span class="block h-1 w-10 rounded-sm bg-purple-500 animate-pulse-delay-2"></span>
          <span class="block h-1 w-10 rounded-sm bg-violet-500 animate-pulse-delay-3"></span>
        </div>
      
        <!-- Description -->
        <p class="text-slate-400 text-base">
          Manage your organization and team on CryBug platform.
        </p>
      </div>
      
      <form id="companySignupForm" action="" method="post" enctype="multipart/form-data" class="flex flex-wrap gap-6 ">
        <div class="flex-1 min-w-[300px] flex flex-col gap-6">
          <div class="flex flex-col relative mb-3">
            <label for="companyName" class="text-sm mb-1.5 text-slate-200">Company Name</label>
            <input type="text" id="companyName" name="companyName" placeholder="Enter your company name" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="companyName-error" class="error-message">Please enter your company name</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="email" class="text-sm mb-1.5 text-slate-200">Business Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your business email address" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="email-error" class="error-message">Please enter a valid email address</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="phone" class="text-sm mb-1.5 text-slate-200">Business Phone</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your 10-digit phone number" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="phone-error" class="error-message">Please enter a valid 10-digit phone number</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="address" class="text-sm mb-1.5 text-slate-200">Company Address</label>
            <textarea id="address" name="address" placeholder="Enter your company address" rows="3"
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus"></textarea>
            <p id="address-error" class="error-message">Please enter your company address</p>
          </div>
          
          <!-- New field: Company Description -->
          <div class="flex flex-col relative mb-3">
            <label for="companyDescription" class="text-sm mb-1.5 text-slate-200">Company Description</label>
            <textarea id="companyDescription" name="companyDescription" placeholder="Enter your company description" rows="3"
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus"></textarea>
            <p id="companyDescription-error" class="error-message">Please enter your company description</p>
          </div>
        </div>
        
        <!-- Right Column -->
        <div class="flex-1 min-w-[300px] flex flex-col gap-6">
          <div class="flex flex-col relative mb-3">
            <label for="pincode" class="text-sm mb-1.5 text-slate-200">Pincode / ZIP Code</label>
            <input type="text" id="pincode" name="pincode" placeholder="Enter your pincode" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="pincode-error" class="error-message">Please enter a valid pincode</p>
          </div>
          
          <!-- New field: Number of Clients -->
          <div class="flex flex-col relative mb-3">
            <label for="clients" class="text-sm mb-1.5 text-slate-200">Number of Clients</label>
            <input type="number" id="clients" name="clients" placeholder="Enter number of clients" min="0"
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base transition-all duration-300 focus:outline-none focus:border-indigo-400 focus:shadow-input-focus">
            <p id="clients-error" class="error-message">Please enter a valid number</p>
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
            <label for="companyLogo" class="text-sm mb-1.5 text-slate-200">Upload Company Logo</label>
            <div class="relative w-full mb-2.5">
              <input type="file" id="companyLogo" name="companyLogo" accept="image/*" class="absolute top-0 left-0 w-[0.1px] h-[0.1px] opacity-0 overflow-hidden z-[-1]">
              <div class="flex items-center w-full">
                <span id="file-chosen" class="mr-2.5 flex-grow overflow-hidden text-ellipsis whitespace-nowrap p-2 bg-slate-800 rounded-md border border-slate-700 text-slate-400">No file chosen</span>
                <button type="button" class="file-upload-btn bg-gradient-to-r from-indigo-500 to-violet-400 text-white py-2 px-3 rounded-md cursor-pointer border-none text-sm whitespace-nowrap transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/40">Choose File</button>
              </div>
              <div id="image-preview" class="image-preview"></div>
            </div>
            <p id="companyLogo-error" class="error-message">Please select a company logo</p>
          </div>
          
          <div class="flex flex-col relative mb-3">
            <label for="companyID" class="text-sm mb-1.5 text-slate-200">Your Company ID</label>
            <input type="text" id="companyID" name="companyID" readonly placeholder="Generated after registration" 
              class="p-3 rounded-lg border border-slate-700 bg-slate-800 text-white text-base">
          </div>
        </div>
        
        <!-- Full Width Button Row -->
        <div class="w-full mt-4">
        <button type="submit" id="submitButton" name="sub" value='1' class="w-full bg-gradient-to-r from-indigo-700 to-violet-700 py-3 border-none rounded-xl text-base font-bold text-white cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-xl hover:shadow-indigo-700/40 hover:animate-button-pulse">
    Register Company
</button>
          <p class="text-center text-sm mt-6 text-slate-400">
            Already registered your company?
            <a href="company-Login.php" class="text-indigo-400 no-underline transition-all duration-300 hover:text-indigo-300 hover:underline">Login</a>
          </p>
        </div>
      </form>
    </div>
    
    <!-- Success Message (Initially Hidden) -->
    <div id="successCard" class="bg-slate-900/95 rounded-2xl p-8 shadow-xl shadow-indigo-500/30 backdrop-blur-lg border border-indigo-500/10 <?php echo $registration_success ? 'flex' : 'hidden'; ?> flex-col items-center justify-center text-center h-full">
      <div class="text-5xl text-green-500 mb-4 animate-success-entrance animate-float">✓</div>
      <h2 class="text-2xl mb-4 text-slate-100">Company Registration Successful!</h2>
      <p>Your company has been registered successfully.</p>
      <div class="bg-indigo-500/10 py-4 px-8 rounded-lg border border-indigo-500/30 text-xl text-indigo-500 font-bold my-4 animate-pulse" id="successCompanyId"><?php echo $cmp_id; ?></div>
      <p>Please take a screenshot or note down your Company ID.</p>
      <p>You'll need it to access your company dashboard.</p>
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
        window.location.href = 'company-Login.php';
      }
    }, 1000);
    

    document.getElementById('successCompanyId').textContent = '<?php echo $cmp_id; ?>';
    
 
    document.getElementById('doneButton').addEventListener('click', function() {
      clearInterval(intervalId);
      window.location.href = 'company-Login.php';
    });
  </script>
  <?php endif; ?>
</body>
</html>
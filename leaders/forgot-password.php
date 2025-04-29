<?php
// forgot-password.php
session_start();
include "connection.php";

// Function to generate OTP
function generateOTP($length = 6) {
    $characters = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $otp;
}

// Function to send email with OTP
function sendOTPEmail($email, $otp) {
    $subject = "Password Reset OTP - CryBug";
    $message = "Your OTP for password reset is: $otp\n\n";
    $message .= "This OTP will expire in 15 minutes.\n";
    $message .= "If you did not request this reset, please ignore this email.";
    $headers = "From: hacker.3656@gmail.com";
    
    return mail($email, $subject, $message, $headers);
}

// Function to send credentials email
function sendCredentialsEmail($email, $userId, $password) {
    $subject = "Your Account Credentials - CryBug";
    $message = "Here are your account credentials for CryBug:\n\n";
    $message .= "User ID: $userId\n";
    $message .= "Password: $password\n\n";
    $message .= "Please change your password after logging in for security reasons.";
    $headers = "From: hacker.3656@gmail.com";
    
    return mail($email, $subject, $message, $headers);
}

$errorMsg = "";
$successMsg = "";
$step = "email"; // Default step

// Process email verification request
if(isset($_POST['verify_email'])) {
    $email = mysqli_real_escape_string($con, $_POST['reset_email']);
    
    if(empty($email)) {
        $errorMsg = "Please enter your email address";
    } else {
        // Check if email exists in manager table
        $query = "SELECT * FROM manager WHERE mag_email = '$email'";
        $result = mysqli_query($con, $query);
        
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $userId = $row['mag_id'];
            
            // Generate OTP and store in session
            $otp = generateOTP();
            $_SESSION['password_reset'] = [
                'email' => $email,
                'user_id' => $userId,
                'otp' => $otp,
                'expire_time' => time() + (15 * 60) // OTP valid for 15 minutes
            ];
            
            // Send OTP to email
            if(sendOTPEmail($email, $otp)) {
                $successMsg = "OTP sent to your email";
                $step = "otp"; // Move to OTP verification step
            } else {
                $errorMsg = "Failed to send OTP email";
            }
        } else {
            $errorMsg = "Email not found in our records";
        }
    }
}

// Process OTP verification
if(isset($_POST['verify_otp'])) {
    $enteredOTP = mysqli_real_escape_string($con, $_POST['otp']);
    
    if(empty($enteredOTP)) {
        $errorMsg = "Please enter the OTP";
        $step = "otp";
    } elseif(!isset($_SESSION['password_reset'])) {
        $errorMsg = "OTP verification session expired";
        $step = "email";
    } else {
        $resetData = $_SESSION['password_reset'];
        
        // Check if OTP has expired
        if(time() > $resetData['expire_time']) {
            unset($_SESSION['password_reset']);
            $errorMsg = "OTP has expired. Please request a new one";
            $step = "email";
        } elseif($enteredOTP == $resetData['otp']) {
            // Verify OTP
            // Fetch user credentials
            $email = $resetData['email'];
            $query = "SELECT mag_id, mag_password FROM manager WHERE mag_email = '$email'";
            $result = mysqli_query($con, $query);
            
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $userId = $row['mag_id'];
                $password = $row['mag_password'];
                
                // Send credentials to email
                if(sendCredentialsEmail($email, $userId, $password)) {
                    // Clear the session data after successful reset
                    unset($_SESSION['password_reset']);
                    $successMsg = "Your credentials have been sent to your email";
                    $step = "success";
                } else {
                    $errorMsg = "Failed to send credentials email";
                    $step = "otp";
                }
            } else {
                $errorMsg = "User not found";
                $step = "email";
            }
        } else {
            $errorMsg = "Invalid OTP. Please try again";
            $step = "otp";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CryBug</title>
    <link rel="stylesheet" href="../src/output.css">
    <style>
        .btn-gradient {
            background-image: linear-gradient(to right, #06b6d4, #3b82f6);
        }
    </style>
</head>
<body class="bg-gradient-dark min-h-screen font-sans text-white">
    <div class="flex flex-col items-center justify-center min-h-screen p-4 relative">
        <!-- Top Navigation -->
        <div class="fixed top-6 left-0 right-0 w-full flex justify-between px-6 md:px-12 z-50 max-w-7xl mx-auto">
            <!-- Home Button (left) -->
            <a href="../index.php" class="nav-button flex items-center pr-2 justify-center bg-gradient-to-r from-teal-500 via-blue-500 to-indigo-500 text-white py-2 px-4 md:py-3 md:px-6 rounded-xl shadow-lg transition-all duration-300 hover:from-teal-600 hover:via-blue-600 hover:to-indigo-600 hover:scale-105 hover:shadow-xl group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 mr-2 icon-float group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </a>
            
            <!-- Previous Page Button (right) -->
            <a href="javascript:history.back()" class="nav-button flex pr-2 items-center justify-center bg-gradient-to-r from-purple-500 via-fuchsia-500 to-pink-500 text-white py-2 px-4 md:py-3 md:px-6 rounded-xl shadow-lg transition-all duration-300 hover:from-purple-600 hover:via-fuchsia-600 hover:to-pink-600 hover:scale-105 hover:shadow-xl group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 mr-2 icon-float group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
        </div>
        
        <!-- Reset Card -->
        <div class="bg-slate-900/95 rounded-2xl p-8 max-w-lg w-full shadow-xl shadow-cyan-500/30 backdrop-blur-lg border border-cyan-400 border-opacity-20">
            <div class="text-center mb-8 relative">
                <div class="flex justify-center items-center mb-4 relative">
                    <div class="logo-pulse"></div>
                    <div class="w-24 h-24 overflow-hidden rounded-xl relative z-10">
                        <img src="../images/Logo/logo.png" alt="CryBug Logo" class="object-contain w-full h-full" />
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-teal-500 mb-2 leading-tight sm:leading-snug">Password Recovery</h1>
                <div class="flex justify-center gap-2.5 my-2.5">
                    <span class="block h-1 w-10 rounded-sm bg-cyan-400"></span>
                    <span class="block h-1 w-10 rounded-sm bg-blue-500"></span>
                    <span class="block h-1 w-10 rounded-sm bg-teal-500"></span>
                </div>
            </div>

            <!-- Error/Success Messages -->
            <?php if(!empty($errorMsg)): ?>
                <div class="bg-red-900 border-2 border-red-500 text-red-300 py-3 font-medium text-center rounded mb-4">
                    <?php echo $errorMsg; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($successMsg)): ?>
                <div class="bg-teal-900 border-2 border-teal-500 text-teal-300 py-3 font-medium text-center rounded mb-4">
                    <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>

            <!-- Email Verification Form -->
            <?php if($step == "email"): ?>
                <form method="post" action="forgot-password.php" class="space-y-5">
                    <div>
                        <label for="reset-email" class="block text-sm font-medium mb-1.5 text-gray-300">Email Address</label>
                        <input type="email" id="reset-email" name="reset_email" required placeholder="Enter your registered email"
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />
                    </div>

                    <button type="submit" name="verify_email" value="1" class="w-full btn-gradient py-3 rounded-lg text-gray-100 font-bold transition-all transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        Send OTP
                    </button>
                    
                    <div class="text-center">
                        <a href="leader-Login.php" class="text-cyan-400 text-sm hover:text-cyan-300 transition-colors">Back to Login</a>
                    </div>
                </form>
            
            <!-- OTP Verification Form -->
            <?php elseif($step == "otp"): ?>
                <form method="post" action="forgot-password.php" class="space-y-5">
                    <div>
                        <label for="otp-input" class="block text-sm font-medium mb-1.5 text-gray-300">One-Time Password</label>
                        <input type="text" id="otp-input" name="otp" required placeholder="Enter 6-digit OTP"
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />
                    </div>

                    <button type="submit" name="verify_otp" value="1" class="w-full btn-gradient py-3 rounded-lg text-gray-100 font-bold transition-all transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        Verify OTP
                    </button>
                    
                    <div class="text-center">
                        <a href="forgot-password.php" class="text-cyan-400 text-sm hover:text-cyan-300 transition-colors">Back to Email Verification</a>
                    </div>
                </form>
            
            <!-- Success Message -->
            <?php elseif($step == "success"): ?>
                <div class="text-center py-6">
                    <div class="w-16 h-16 bg-green-500/20 rounded-full mx-auto flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-teal-500 text-xl font-semibold mb-2">Success!</h2>
                    <p class="text-slate-300 mb-6">Your credentials have been sent to your email address.</p>
                    <a href="leader-Login.php" class="inline-block w-full btn-gradient py-3 text-center rounded-lg text-gray-100 font-bold transition-all transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        Back to Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
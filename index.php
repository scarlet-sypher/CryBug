<?php
include "connection.php";
session_start();

// Include the session manager functions
include "session_manager.php";

// Get user profile data
$profileData = getUserProfileData();
$userName = $profileData['name'];
$userRole = $profileData['role'];
$profilePic = $profileData['profile_pic'];
$pic = str_replace("../uploads", "uploads", $profilePic);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Remixicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-brands/css/uicons-brands.css'>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.0.0/uicons-regular-straight/css/uicons-regular-straight.css">


    <link rel="stylesheet" href="src/output.css">
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <title>CryBug</title>
</head>
<body class="font-sans bg-black antialiased " id="home">
    <!-- HEADER -->

    <!-- ---------------------------------------------------------Navigation bar starts here------------------------------------------------------------------------------------ -->
    <header class="fixed w-full top-0 left-0 bg-black z-50 shadow-xl mobile-header">
      <div class="infinite-line bg-black">
          <nav class="flex justify-between items-center h-14 md:h-20 px-6 max-w-7xl mx-auto text-white mobile-nav">
              <a href="#home" class="inline-block transition-transform duration-300 hover:scale-105">
                  <img src="images/Logo/logo.png" alt="logo-image" class=" mobile-logo w-[100px] h-auto transition-all duration-300 hover:drop-shadow-[0_0_20px_#1E40AF]">
              </a>
              
              <!-- Navigation Menu -->
              <div class="nav-menu fixed top-[-100%] left-0 w-full md:relative md:top-0 md:w-auto md:bg-transparent md:shadow-none pt-16 md:pt-0 pb-10 md:pb-0 transition-all duration-300 z-[60] bg-black bg-opacity-95" id="nav-menu">
                  <ul class="flex flex-col md:flex-row items-center md:items-stretch md:ml-auto relative">
                      <li class="w-full md:w-auto text-center nav-item">
                          <a href="index.php" class="nav-link text-white  font-semibold relative inline-block px-5 py-4 md:py-2 z-10 w-full hover:bg-blue-800 md:hover:bg-transparent transition-colors">Home</a>
                          

                      </li>
                      <li class="w-full md:w-auto text-center nav-item">
                          <a href="#crybug-progress-section" class="nav-link text-white  font-semibold relative inline-block px-5 py-4 md:py-2 z-10 w-full hover:bg-blue-800 md:hover:bg-transparent transition-colors">Progess</a>
                      </li>
                      <li class="w-full md:w-auto text-center nav-item has-dropdown">
                          <a href="#company" class="nav-link text-white font-semibold relative inline-block px-5 py-4 md:py-2 z-10 w-full hover:bg-blue-800 md:hover:bg-transparent transition-colors dropdown-trigger">
                              Company
                              <i class="fi fi-rs-angle-small-down dropdown-arrow"></i>
                          </a>
                          <div class="nav-dropdown py-0 w-52 rounded-lg z-20">
                              <div class="nav-dropdown-inner bg-gradient-to-br from-slate-800 to-slate-900 rounded-lg">
                                  <a href="#why-us" class="flex items-center gap-2 px-6 py-3 text-white text-base hover:bg-blue-700 hover:bg-opacity-30 transition-colors">
                                      <i class="fi fi-sr-diamond"></i>
                                      <span>Purpose</span>
                                  </a>
                                  <a href="#work-process" class="flex items-center gap-2 px-6 py-3 text-white text-base hover:bg-blue-700 hover:bg-opacity-30 transition-colors">
                                      <i class="fi fi-sr-rocket-lunch"></i>
                                      <span>Work Flow</span>
                                  </a>
                                  <a href="#team-experts" class="flex items-center gap-2 px-6 py-3 text-white text-base hover:bg-blue-700 hover:bg-opacity-30 transition-colors">
                                      <i class="fi fi-sr-interactive"></i>
                                      <span>Experts</span>
                                  </a>
                                  <a href="#crybug-testimonials" class="flex items-center gap-2 px-6 py-3 text-white text-base hover:bg-blue-700 hover:bg-opacity-30 transition-colors">
                                      <i class="fi fi-sr-shield-check"></i>
                                      <span>Review</span>
                                  </a>
                              </div>
                          </div>
                      </li>
                      <li class="w-full md:w-auto text-center nav-item has-dropdown">
                          <a href="#products" class="nav-link text-white font-semibold relative inline-block px-5 py-4 md:py-2 z-10 w-full hover:bg-blue-800 md:hover:bg-transparent transition-colors dropdown-trigger">
    
                              Explore
                              <i class="fi fi-rs-angle-small-down dropdown-arrow"></i>
                          </a>
                          <div class="nav-dropdown py-0 w-52 rounded-lg z-20">
                              <div class="nav-dropdown-inner bg-gradient-to-br from-slate-800 to-slate-900 rounded-lg">
                                  <a href="#partnerships" class="flex items-center gap-2 px-6 py-3 text-white text-base hover:bg-blue-700 hover:bg-opacity-30 transition-colors">
                                      <i class="fi fi-sr-cube"></i>
                                      <span>Giants</span>
                                  </a>
                                  <a href="#projects" class="flex items-center gap-2 px-6 py-3 text-white text-base hover:bg-blue-700 hover:bg-opacity-30 transition-colors">
                                      <i class="fi fi-sr-apps"></i>
                                      <span>Projects</span>
                                  </a>
                                  <a href="#faq-section" class="flex items-center gap-2 px-6 py-3 text-white text-base hover:bg-blue-700 hover:bg-opacity-30 transition-colors">
                                      <i class="fi fi-sr-display-code"></i>
                                      <span>FAQ</span>
                                  </a>
                                  <a href="#carrier" class="flex items-center gap-2 px-6 py-3 text-white text-base hover:bg-blue-700 hover:bg-opacity-30 transition-colors">
                                      <i class="fi fi-sr-chart-tree-map"></i>
                                      <span>Carrier</span>
                                  </a>
                              </div>
                          </div>
                      </li>
                      <li class="w-full md:w-auto text-center nav-item">
                          <a href="#about-us" class="nav-link text-white  font-semibold relative inline-block px-5 py-4 md:py-2 z-10 w-full hover:bg-blue-800 md:hover:bg-transparent transition-colors">About  us</a>
                      </li>
                      
                      <!-- The span that will change on hover (only shown in md screen and up) -->
                      <span class="nav-hover-indicator hidden md:block absolute top-0 left-0 w-20 h-full bg-gradient-to-r from-blue-800 to-blue-500 rounded transition-all duration-500"></span>
                  </ul>
                          
                  <!-- Close button (visible only on mobile) -->
                  <div class="absolute top-4 right-6 text-2xl cursor-pointer md:hidden text-white" id="nav-close">
                      <i class="ri-close-line"></i>
                  </div>
              </div>
              
              <div class="flex items-center gap-10">
                  <!-- Search button -->
                  <div class="search-toggle-btn relative flex items-center justify-center cursor-pointer hover:text-indigo-500 transition-all duration-300" id="search-btn">
                    <i class="ri-search-line"></i>
                      <span class="search-pulse"></span>
                  </div>
                  
                  <!-- Dropdown -->
                  <div class="relative" id="dropdown">
                      <div class="flex items-center cursor-pointer">
                      <div class="hidden sm:block mr-4">
                          <h3 class="dropdown-profile-name text-base font-semibold"><?php echo $userName; ?></h3>
                          <span class="dropdown-profile-desc text-sm font-medium text-right"><?php echo $userRole; ?></span>
                      </div>

                      <div class="relative w-[52px] h-[52px] bg-white rounded-full overflow-hidden flex justify-center items-center transition-transform duration-300 hover:scale-125 ring-2 ring-indigo-500 ring-offset-2 ring-offset-black">
                          <img src="<?php echo htmlspecialchars($pic); ?>" alt="Profile image" class="w-full h-full object-cover">
                      </div>
                      </div>
                      
                      <div class="dropdown-list absolute top-[4.5rem] right-0 bg-slate-900 shadow-lg py-4 px-5 ring-blue-500 rounded grid gap-1 z-10 transform translate-y-[-12px] opacity-0 pointer-events-none transition-all duration-300">
                        <?php if(isset($_SESSION['user_role'])): ?>
                            <?php 
                                // Set proper dashboard link based on role
                                $dashboardLink = '';
                                switch($_SESSION['user_role']) {
                                    case 'employee':
                                        $dashboardLink = 'employeeProfile/dashboard.php';
                                        break;
                                    case 'manager':
                                        $dashboardLink = 'profile/dashboard.php';
                                        break;
                                    case 'company':
                                        $dashboardLink = 'companyProfile/dashboard.php';
                                        break;
                                }
                            ?>
                            <a href="<?php echo $dashboardLink; ?>" class="flex items-center gap-4 py-1 px-2 pr-6 rounded text-white hover:bg-slate-700 transition-colors">
                                <i class="ri-user-line text-base"></i>
                                <span class="text-sm font-medium">Profile</span>
                            </a>
                        <?php else: ?>
                            <a href="login-pages/login.html" class="flex items-center gap-4 py-1 px-2 pr-6 rounded text-white hover:bg-slate-700 transition-colors">
                                <i class="ri-user-line text-base"></i>
                                <span class="text-sm font-medium">Login</span>
                            </a>
                        <?php endif; ?>
                        
                        <!-- Other dropdown items -->
                        
                        <?php if(isset($_SESSION['user_role'])): ?>
                        <a href="logout.php" class="flex items-center gap-4 py-1 px-2 pr-6 rounded text-white hover:bg-slate-700 transition-colors">
                            <i class="ri-logout-box-r-line text-base"></i>
                            <span class="text-sm font-medium">Logout</span>
                        </a>
                        <?php endif; ?>
                    </div>
                  </div>
                  
                  <!-- Toggle button -->
                  <div class="text-2xl cursor-pointer md:hidden" id="nav-toggle">
                      <i class="ri-menu-line"></i>
                  </div>
              </div>
          </nav>
      </div>
  </header>
  
  <!-- Search option -->
  <div class="search fixed inset-0 bg-slate-900/60 backdrop-blur-lg z-[1000] opacity-0 pointer-events-none transition-all duration-500 ease-in-out" id="search">
      <div class="relative w-full h-full">
          <!-- Close button -->
          <div class="flex justify-center items-center">
              <button type="button" id="search-close" class="absolute top-8 right-8 text-white cursor-pointer z-[1001]">
                  <div class="h-12 w-12 flex justify-center items-center rounded-full bg-blue-600 shadow-2xl hover:bg-blue-700 transition-colors">
                      <i class="ri-close-line block leading-none text-3xl"></i>
                  </div>
              </button>
          </div>
  
          <!-- Search form -->
          <div class="max-w-xl mx-auto mt-40 md:mt-48 px-6">
              <div class="search-container relative">
                  <form class="search-form flex items-center gap-2 bg-slate-800 bg-opacity-70 rounded-xl shadow-2xl overflow-hidden border border-blue-500/30 transform scale-100 transition-all duration-500 ease-in-out">
                      <i class="fi fi-sr-search text-2xl text-blue-400 ml-4"></i>
                      <input id="search-input" type="search" placeholder="What are you looking for?" class="w-full py-5 px-3 bg-transparent text-white outline-none text-lg placeholder-blue-300 placeholder-opacity-70">
                      <button type="submit" id="search-submit" class="bg-gradient-to-r from-blue-700 to-blue-500 text-white px-6 py-5 transition-all hover:from-blue-600 hover:to-blue-400 flex items-center gap-2">
                          <span>Search</span>
                          <i class="fi fi-sr-magnifying-glass-eye text-xl"></i>
                      </button>
                  </form>
                  
                  <div id="search-results" class="mt-4 rounded-xl shadow-2xl overflow-hidden transition-all duration-300 max-h-0 opacity-0">
                      <div class="bg-slate-800 bg-opacity-70 border border-blue-500/30 p-6 rounded-xl">
                          <div class="flex items-center gap-3 mb-4">
                              <i class="fi fi-sr-list text-blue-400 text-xl"></i>
                              <h3 class="text-white text-xl font-semibold" id="search-results-title">Search Results</h3>
                          </div>
                          <p id="search-message" class="text-blue-300 mb-4"></p>
                          <ul id="search-results-list" class="space-y-2"></ul>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  
  <!-- Link to the Flaticon icon library -->

    
    <!-----------------------------------------------------------Navigation bar ended here------------------------------------------------------------------------------------ -->
      

    <!-----------------------------------------------------------Main starts ------------------------------------------------------------------------------------ -->


    <main class="min-h-screen pt-5 bg-gradient-to-b from-black to-slate-900 text-white">
        <!-- Hero section with text and image aligned -->

        <!-- ================================================================ Heading and Changing text ================================================================ -->

        <div class="hero py-16 sm:py-20 lg:py-10 min-h-screen flex flex-col justify-between mobile-hero">
          <!-- Header Content -->
          <div class="container mx-auto px-6 md:px-10 pt-10 sm:pt-16">
            <div class="text-center lg:text-left max-w-4xl mx-auto lg:mx-0">
              <!-- Custom decorative element -->
              <div class="flex justify-center lg:justify-start mb-1">
                <div class="w-16 h-1 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full"></div>
              </div>
              
              <h1 class="mobile-heading font-bold mb-2 leading-tight sm:leading-snug animated-gradient-text text-center lg:text-left whitespace-nowrap overflow-hidden text-ellipsis" style="font-size: clamp(2rem, 5vw, 4.5rem);">
                Welcome to CryBug!!!...
              </h1>
              
              <p class="animated-tagline text-base sm:text-lg md:text-xl lg:text-2xl font-light tracking-wide mb-3 flex flex-col sm:flex-row items-center sm:items-start lg:items-center justify-center lg:justify-start gap-2 sm:gap-4 text-center lg:text-left">
                <span>An Advanced System to Help You</span>
                <span class="rotating-text-wrapper">
                  <span class="rotating-text text-blue-400 font-medium"></span>
                  <span class="cursor"></span>
                </span>
              </p>
              
              <!-- Added feature badges -->
              <div class="hidden md:flex flex-wrap gap-3 justify-center lg:justify-start mb-8">
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-900/50 text-blue-300 border border-blue-700/50">
                  Bug Tracking
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-teal-900/50 text-teal-300 border border-teal-700/50">
                  Task Management
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-900/50 text-green-300 border border-green-700/50">
                  Team Collaboration
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-900/50 text-purple-300 border border-purple-700/50">
                  AI-Powered
                </span>
              </div>
            </div>
          </div>
          
          <!-- Hero Image Section -->
          <div class="container mx-auto  flex flex-col lg:flex-row items-center justify-between px-6 pb-16 gap-10 relative ">
            <!-- Left: Hero Image with enhanced container -->
            <div class="w-full lg:w-1/2 hero-image-container rounded-xl p-2 flex justify-center -mt-5">
              <div class="relative">
                <!-- Decorative circles -->
                <div class="absolute w-40 h-40 rounded-full bg-blue-500/5 blur-2xl -top-10 -left-10 z-0"></div>
                <div class="absolute w-40 h-40 rounded-full bg-cyan-500/5 blur-2xl bottom-0 right-0 z-0"></div>
                
                <img src="images/hero/hero-image.png" alt="bug-body-image"
                  class="w-full max-w-md lg:max-w-xl h-auto object-contain transition-all duration-500 hover:scale-105 hover:drop-shadow-[0_0_15px_rgba(59,130,246,0.5)] relative z-10">
                  
                <!-- Pulsing ring -->
                <div class="absolute inset-0 rounded-full border border-sky-500/20 scale-[1.02] animate-pulse"></div>
              </div>
            </div>

            
          
            
            <!-- Right: Text + Buttons in card -->
            <div class="w-full lg:w-1/2 flex flex-col items-center lg:items-start -mt-14">
              <div class="card-bg rounded-2xl p-8 w-full max-w-lg">
                <h2 class="text-3xl font-bold mb-4 leading-tight sm:leading-snug bg-gradient-to-r from-sky-400 via-teal-400 to-green-600 text-transparent bg-clip-text">
                  Ready to squash some bugs?
                </h2>
                
                <!-- Motivational Lines with improved design -->
                <div class="mb-8 space-y-3">
                  <div class="flex items-center">
                    <div class="w-1 h-1 rounded-full bg-sky-400 mr-2"></div>
                    <p class="text-gray-300">Streamline your workflow. Start now.</p>
                  </div>
                  <div class="flex items-center">
                    <div class="w-1 h-1 rounded-full bg-teal-400 mr-2"></div>
                    <p class="text-gray-300">Track smarter, not harder.</p>
                  </div>
                  <div class="flex items-center">
                    <div class="w-1 h-1 rounded-full bg-green-400 mr-2"></div>
                    <p class="text-gray-300">Your project control center awaits.</p>
                  </div>
                  <div class="flex items-center">
                    <div class="w-1 h-1 rounded-full bg-cyan-400 mr-2"></div>
                    <p class="text-gray-300">It's fast, free, and secure.</p>
                  </div>
                </div>
                
                <!-- button -->
                <div class="button-container">
                  <div class="button-wrapper">
                    <div class="btn-effect">
                      <a href="login-pages/login.html"><span>Login</span></a>
                    </div>
                    
                    <div class="btn-effect">
                      <a href="login-pages/signup.html"><span>Sign up</span></a>
                    </div>
                  </div>
                </div>
                
                <p class="mt-6 text-sm text-gray-400 text-center lg:text-left">By continuing, you agree to our <a href="#" class="underline hover:text-blue-500 transition-colors">Terms</a>.</p>
              </div>
              
              <!-- Added trust indicators -->
              <div class="mt-6 flex items-center justify-center lg:justify-start space-x-4">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <span class="ml-1 text-xs text-gray-400">Secure</span>
                </div>
                
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <span class="ml-1 text-xs text-gray-400">No credit card</span>
                </div>
                
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <span class="ml-1 text-xs text-gray-400">24/7 Support</span>
                </div>
              </div>
            </div>
          </div>
        
          <!-- Spider collection (preserved as is) -->
          <div class="spider-collection">
            <div class="hanging-spider" id="spider1">
              <div class="web-thread"></div>
              <div class="spider-image yellow-spider"></div>
            </div>
                  
            <div class="hanging-spider" id="spider2">
              <div class="web-thread"></div>
              <div class="spider-image dark-spider"></div>
            </div>
                  
            <div class="hanging-spider" id="spider3">
              <div class="web-thread"></div>
              <div class="spider-image navy-spider"></div>
            </div>
          </div>
        </div>


<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Hero Section ended  ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->

 






<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Process Section  starts ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->



<section class="py-20 px-6 text-white" id="crybug-progress-section" >
  <div class="max-w-6xl mx-auto text-center relative z-10">
    <!-- Section Eyebrow -->
    <div class="text-center mb-4 crybug-reveal" style="--reveal-delay: 0;">
      <span class="inline-block px-4 py-1 rounded-full bg-gradient-to-r from-yellow-400/20 to-red-500/20 text-yellow-400 text-sm font-medium tracking-wider">HOW IT WORKS</span>
    </div>
  
    <!-- Section Heading -->
    <div class="text-center mb-20">
      <span class="inline-block text-sm font-semibold text-yellow-400 tracking-wider uppercase mb-2">Smart Debugging</span>
      <h2 class="font-bold mb-4 leading-tight sm:leading-snug bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 text-transparent bg-clip-text text-[clamp(2.5rem,6vw,4.5rem)]">
        How CryBug Works
      </h2>
      <p class="max-w-xl mx-auto mb-5 text-gray-400">CryBug simplifies bug tracking with automated workflows, powerful diagnostics, and intuitive tools that help developers stay focused on building.</p>
      <div class="h-1.5 w-48 mx-auto bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 rounded-full shadow-lg shadow-red-500/20"></div>
    </div>

    <div class="grid md:grid-cols-2 gap-16 relative">
      <!-- Timeline vertical line -->
      <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-1 bg-gradient-to-b from-yellow-400 to-orange-500 hidden md:block"></div>

      <!-- Column 1 -->
      <div class="flex flex-col gap-12">

        <!-- Step 1 -->
        <div class="relative flex items-start gap-4 crybug-fade-up transition-transform duration-300 hover:scale-[1.03] hover:bg-yellow-400/5 p-4 rounded-xl crybug-process-card" style="animation-delay: 0.1s;" data-step="1">
          <div class="flex-shrink-0 w-12 h-12 bg-yellow-400 text-black rounded-full flex items-center justify-center font-bold shadow-md z-10 text-2xl crybug-icon-pulse">
            <i class="ri-bug-fill"></i>
          </div>
          <div class="text-left crybug-step-content">
            <h3 class="font-semibold text-xl text-yellow-300">Report a Bug</h3>
            <p class="text-gray-400">Easily log issues with screenshots and descriptions.</p>
            <div class="hidden mt-4 crybug-expandable-content">
              <p class="text-sm text-gray-300">Capture and annotate screenshots directly in the app. Add steps to reproduce, browser details, and priority tags for faster resolution.</p>
            </div>
            <button class="mt-3 text-xs text-yellow-300 hover:underline crybug-expand-btn">Learn more</button>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="relative flex items-start gap-4 crybug-fade-up transition-transform duration-300 hover:scale-[1.03] hover:bg-orange-400/5 p-4 rounded-xl crybug-process-card" style="animation-delay: 0.2s;" data-step="2">
          <div class="flex-shrink-0 w-12 h-12 bg-orange-400 text-black rounded-full flex items-center justify-center font-bold shadow-md z-10 text-2xl crybug-icon-pulse">
            <i class="ri-folder-6-fill"></i>
          </div>
          <div class="text-left crybug-step-content">
            <h3 class="font-semibold text-xl text-orange-300">Categorize & Assign</h3>
            <p class="text-gray-400">Set category, priority, and assign to your team.</p>
            <div class="hidden mt-4 crybug-expandable-content">
              <p class="text-sm text-gray-300">Use smart categorization suggestions based on bug descriptions. Automated team member recommendations based on expertise and current workload.</p>
            </div>
            <button class="mt-3 text-xs text-orange-300 hover:underline crybug-expand-btn">Learn more</button>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="relative flex items-start gap-4 crybug-fade-up transition-transform duration-300 hover:scale-[1.03] hover:bg-pink-400/5 p-4 rounded-xl crybug-process-card" style="animation-delay: 0.3s;" data-step="3">
          <div class="flex-shrink-0 w-12 h-12 bg-pink-400 text-black rounded-full flex items-center justify-center font-bold shadow-md z-10 text-2xl crybug-icon-pulse">
            <i class="ri-chat-3-fill"></i>
          </div>
          <div class="text-left crybug-step-content">
            <h3 class="font-semibold text-xl text-pink-300">Collaborate</h3>
            <p class="text-gray-400">Discuss and tag relevant teammates in comments.</p>
            <div class="hidden mt-4 crybug-expandable-content">
              <p class="text-sm text-gray-300">Real-time thread-based discussions with @mentions and code snippet sharing. Integrated video calls and screen sharing for complex issues.</p>
            </div>
            <button class="mt-3 text-xs text-pink-300 hover:underline crybug-expand-btn">Learn more</button>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="relative flex items-start gap-4 crybug-fade-up transition-transform duration-300 hover:scale-[1.03] hover:bg-red-400/5 p-4 rounded-xl crybug-process-card" style="animation-delay: 0.4s;" data-step="4">
          <div class="flex-shrink-0 w-12 h-12 bg-red-400 text-black rounded-full flex items-center justify-center font-bold shadow-md z-10 text-2xl crybug-icon-pulse">
            <i class="ri-thunderstorms-fill"></i>
          </div>
          <div class="text-left crybug-step-content">
            <h3 class="font-semibold text-xl text-red-300">Set Priority</h3>
            <p class="text-gray-400">Define urgency levels and track timelines.</p>
            <div class="hidden mt-4 crybug-expandable-content">
              <p class="text-sm text-gray-300">AI-powered priority suggestions based on user impact analysis. Custom escalation paths for critical issues with automated alerts.</p>
            </div>
            <button class="mt-3 text-xs text-red-300 hover:underline crybug-expand-btn">Learn more</button>
          </div>
        </div>
      </div>

      <!-- Column 2 -->
      <div class="flex flex-col gap-12">
        <!-- Step 5 -->
        <div class="relative flex items-start gap-4 crybug-fade-up transition-transform duration-300 hover:scale-[1.03] hover:bg-indigo-400/5 p-4 rounded-xl crybug-process-card" style="animation-delay: 0.5s;" data-step="5">
          <div class="flex-shrink-0 w-12 h-12 bg-indigo-400 text-black rounded-full flex items-center justify-center font-bold shadow-md z-10 text-2xl crybug-icon-pulse">
            <i class="ri-database-fill"></i>
          </div>
          <div class="text-left crybug-step-content">
            <h3 class="font-semibold text-xl text-indigo-300">Track Progress</h3>
            <p class="text-gray-400">Visual dashboards help monitor progress in real time.</p>
            <div class="hidden mt-4 crybug-expandable-content">
              <p class="text-sm text-gray-300">Interactive Kanban boards with custom workflow stages. Burndown charts and velocity metrics for team performance insights.</p>
            </div>
            <button class="mt-3 text-xs text-indigo-300 hover:underline crybug-expand-btn">Learn more</button>
          </div>
        </div>

        <!-- Step 6 -->
        <div class="relative flex items-start gap-4 crybug-fade-up transition-transform duration-300 hover:scale-[1.03] hover:bg-blue-400/5 p-4 rounded-xl crybug-process-card" style="animation-delay: 0.6s;" data-step="6">
          <div class="flex-shrink-0 w-12 h-12 bg-blue-400 text-black rounded-full flex items-center justify-center font-bold shadow-md z-10 text-2xl crybug-icon-pulse">
            <i class="ri-notification-3-fill"></i>
          </div>
          <div class="text-left crybug-step-content">
            <h3 class="font-semibold text-xl text-blue-300">Receive Updates</h3>
            <p class="text-gray-400">Get alerts on new activity, comments, or closures.</p>
            <div class="hidden mt-4 crybug-expandable-content">
              <p class="text-sm text-gray-300">Customizable notification preferences per project and priority level. Integration with Slack, Teams, and email for seamless workflow.</p>
            </div>
            <button class="mt-3 text-xs text-blue-300 hover:underline crybug-expand-btn">Learn more</button>
          </div>
        </div>

        <!-- Step 7 -->
        <div class="relative flex items-start gap-4 crybug-fade-up transition-transform duration-300 hover:scale-[1.03] hover:bg-green-300/5 p-4 rounded-xl crybug-process-card" style="animation-delay: 0.7s;" data-step="7">
          <div class="flex-shrink-0 w-12 h-12 bg-green-400 text-black rounded-full flex items-center justify-center font-bold shadow-md z-10 text-2xl crybug-icon-pulse">
            <i class="ri-tools-fill"></i>
          </div>
          <div class="text-left crybug-step-content">
            <h3 class="font-semibold text-xl text-green-300">Fix & Review</h3>
            <p class="text-gray-400">Resolve the issue and perform code/test reviews.</p>
            <div class="hidden mt-4 crybug-expandable-content">
              <p class="text-sm text-gray-300">Integrated code review tools with side-by-side diff view. Automated test case generation for regression prevention.</p>
            </div>
            <button class="mt-3 text-xs text-green-300 hover:underline crybug-expand-btn">Learn more</button>
          </div>
        </div>

        <!-- Step 8 -->
        <div class="relative flex items-start gap-4 crybug-fade-up transition-transform duration-300 hover:scale-[1.03] hover:bg-teal-300/5 p-4 rounded-xl crybug-process-card" style="animation-delay: 0.8s;" data-step="8">
          <div class="flex-shrink-0 w-12 h-12 bg-teal-400 text-black rounded-full flex items-center justify-center font-bold shadow-md z-10 text-2xl crybug-icon-pulse">
            <i class="ri-markdown-fill"></i>
          </div>
          <div class="text-left crybug-step-content">
            <h3 class="font-semibold text-xl text-teal-300">Mark as Resolved</h3>
            <p class="text-gray-400">Close the bug with detailed resolution notes.</p>
            <div class="hidden mt-4 crybug-expandable-content">
              <p class="text-sm text-gray-300">Document solutions with searchable knowledge base entries. Link to related commits, PRs, and test cases for full transparency.</p>
            </div>
            <button class="mt-3 text-xs text-teal-300 hover:underline crybug-expand-btn">Learn more</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Interactive Demo Button (without float animation) -->
    <div class="mt-20 crybug-fade-up" style="animation-delay: 1s;">
      <button id="crybug-demo-button" class="bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-300 hover:to-orange-400 text-black font-bold py-4 px-8 rounded-full transition-all duration-300 transform hover:scale-105 flex items-center mx-auto">
        <i class="ri-play-circle-fill mr-2 text-xl"></i>
        See CryBug in Action
      </button>
    </div>
  </div>

  <div id="crybug-progress-bar" class="fixed bottom-0 left-0 right-0 h-1 bg-gray-800 z-50">
    <div class="crybug-progress-fill bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500" style="width: 0%"></div>
  </div>

  <!-- Step indicator (fixed) -->
  <div id="crybug-step-indicator" class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-800/70 backdrop-blur rounded-full px-4 py-2 z-50 hidden md:flex items-center gap-2">
    <div class="crybug-step-dot w-3 h-3 rounded-full bg-yellow-400 opacity-40" data-step="1"></div>
    <div class="crybug-step-dot w-3 h-3 rounded-full bg-orange-400 opacity-40" data-step="2"></div>
    <div class="crybug-step-dot w-3 h-3 rounded-full bg-pink-400 opacity-40" data-step="3"></div>
    <div class="crybug-step-dot w-3 h-3 rounded-full bg-red-400 opacity-40" data-step="4"></div>
    <div class="crybug-step-dot w-3 h-3 rounded-full bg-indigo-400 opacity-40" data-step="5"></div>
    <div class="crybug-step-dot w-3 h-3 rounded-full bg-blue-400 opacity-40" data-step="6"></div>
    <div class="crybug-step-dot w-3 h-3 rounded-full bg-green-400 opacity-40" data-step="7"></div>
    <div class="crybug-step-dot w-3 h-3 rounded-full bg-teal-400 opacity-40" data-step="8"></div>
  </div>
</section>


<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Industry Section  ended================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->


        <section class="mt-24" id="partnerships">
          <div class="max-w-6xl mx-auto text-center relative z-10 mb-10">
            <!-- Section Eyebrow -->
            <div class="text-center mb-4 reveal" style="--reveal-delay: 0;">
              <span class="inline-block px-4 py-1 rounded-full bg-gradient-to-r from-yellow-400/20 to-purple-600/20 text-yellow-400 text-sm font-medium tracking-wider">PARTNERSHIPS</span>
            </div>
          
            <!-- Section Heading -->
            <div class="text-center mb-20">
              <span class="inline-block text-sm font-semibold text-yellow-400 tracking-wider uppercase mb-2">Trusted by the Best</span>
              <h2 class="font-bold mb-4 leading-tight sm:leading-snug bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 text-transparent bg-clip-text text-[clamp(2.5rem,6vw,4.5rem)]">
                CryBug x Industry Giants
              </h2>
              <p class="max-w-xl mx-auto mb-5 text-gray-400">We collaborate with leading tech enterprises to deliver enterprise-grade debugging tools and ensure high-performance codebases across platforms.</p>
              <div class="h-1.5 w-48 mx-auto bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 rounded-full shadow-lg shadow-purple-600/20"></div>
            </div>
          </div>

            <!-- Scroll Left -->
            <div class="partner-track">
                <div class="partner-slide itemLeft item1 bg-yellow-400 text-black"><i class="fi fi-brands-adobe"></i><span>Adobe</span></div>
                <div class="partner-slide itemLeft item2 bg-pink-500 text-black"><i class="fi fi-brands-discord"></i><span>Discord</span></div>
                <div class="partner-slide itemLeft item3 bg-blue-500 text-black"><i class="fi fi-brands-meta"></i><span>Meta</span></div>
                <div class="partner-slide itemLeft item4 bg-green-500 text-black"><i class="fi fi-brands-microsoft"></i><span>Microsoft</span></div>
                <div class="partner-slide itemLeft item5 bg-red-500 text-black"><i class="fi fi-brands-spotify"></i><span>Spotify</span></div>
                <div class="partner-slide itemLeft item6 bg-indigo-500 text-black"><i class="fi fi-brands-figma"></i><span>Figma</span></div>
                <div class="partner-slide itemLeft item7 bg-orange-400 text-black"><i class="fi fi-brands-slack"></i><span>Slack</span></div>
                <div class="partner-slide itemLeft item8 bg-gray-600 text-black"><i class="fi fi-brands-github"></i><span>GitHub</span></div>
            </div>

            <!-- Scroll Right -->
            <div class="partner-track mt-8">
                <div class="partner-slide itemRight item1 bg-yellow-400 text-black"><i class="fi fi-brands-adobe"></i><span>Adobe</span></div>
                <div class="partner-slide itemRight item2 bg-pink-500 text-black"><i class="fi fi-brands-discord"></i><span>Discord</span></div>
                <div class="partner-slide itemRight item3 bg-blue-500 text-black"><i class="fi fi-brands-meta"></i><span>Meta</span></div>
                <div class="partner-slide itemRight item4 bg-green-500 text-black"><i class="fi fi-brands-microsoft"></i><span>Microsoft</span></div>
                <div class="partner-slide itemRight item5 bg-red-500 text-black"><i class="fi fi-brands-spotify"></i><span>Spotify</span></div>
                <div class="partner-slide itemRight item6 bg-indigo-500 text-black"><i class="fi fi-brands-figma"></i><span>Figma</span></div>
                <div class="partner-slide itemRight item7 bg-orange-400 text-black"><i class="fi fi-brands-slack"></i><span>Slack</span></div>
                <div class="partner-slide itemRight item8 bg-gray-600 text-black"><i class="ri-github-fill"></i><span>GitHub</span></div>
            </div>
        </section>


<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Industry Section  ends ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->









<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ why  us  Section starts ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->





      <section class=" py-24 px-6 text-white" id="why-us">
        <div class="max-w-6xl mx-auto text-center relative z-10">
          <!-- Section Eyebrow -->
          <div class="text-center mb-4 reveal" style="--reveal-delay: 0;">
            <span class="inline-block px-4 py-1 rounded-full bg-gradient-to-r from-yellow-400/20 to-green-700/20 text-yellow-400 text-sm font-medium tracking-wider">OUR PURPOSE</span>
          </div>
        
          <!-- Section Heading with animated gradient -->
          <div class="relative mb-20">
            <span class="inline-block text-sm font-semibold text-yellow-400 tracking-wider uppercase mb-2">Built for Devs</span>
            <h2 class="font-black mb-4 text-center leading-tight sm:leading-snug bg-gradient-to-r from-yellow-400 via-red-400 to-green-700 bg-clip-text text-transparent text-[clamp(2.5rem,6vw,4.5rem)] select-none">
              Why CryBug?
            </h2>
            <p class="max-w-xl mx-auto mb-5 text-gray-400">CryBug was built from the ground up to address the real-world frustrations developers face with traditional debugging tools—fast, efficient, and frustration-free.</p>
            <div class="h-1.5 w-48 mx-auto bg-gradient-to-r from-yellow-400 via-red-400 to-green-700 rounded-full shadow-lg shadow-green-700/20"></div>
          </div>
        
      
          <!-- Grid Layout with smooth animations -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 grid-rows-auto lg:grid-rows-2 gap-6">
      
            <!-- A - Vibrant Community -->
            <div class="feature-card card bg-gray-800/60 border border-gray-700 rounded-2xl p-6 md:p-8 shadow-lg hover:shadow-cyan-500/20 transition-all flex flex-col justify-between transform hover:-translate-y-1 relative overflow-hidden">
              <div class="blob absolute bg-indigo-600/40"></div>
              <div class="fakeblob absolute w-full"></div>
              <div class="inner relative z-10 flex flex-col h-full">
                <div class="flex items-center mb-4">
                  <div class="icon-container text-indigo-400 mr-3">
                    <i class="fi fi-brands-discord text-3xl"></i>
                  </div>
                  <h4 class="font-semibold text-lg">Vibrant Community</h4>
                </div>
                <p class="text-gray-400 text-sm">Join thousands of developers collaborating, sharing feedback, and growing together on our active channels.</p>
                <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a href="#community" class="text-xs text-indigo-400 hover:text-indigo-300 flex items-center">
                    Learn more <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </a>
                </div>
              </div>
            </div>
      
            <!-- C (2 Rows Tall) - Open Source Power -->
            <div class="feature-card card bg-gray-800/60 border border-gray-700 rounded-2xl p-6 md:p-8 row-span-1 lg:row-span-2 shadow-lg hover:shadow-green-500/20 transition-all flex flex-col justify-between transform hover:-translate-y-1 relative overflow-hidden">
              <div class="blob absolute bg-green-600/40"></div>
              <div class="fakeblob absolute w-full"></div>
              <div class="inner relative z-10 flex flex-col h-full">
                <div class="flex items-center mb-4">
                  <div class="icon-container text-green-400 mr-3">
                    <i class="fi fi-brands-github text-3xl"></i>
                  </div>
                  <h4 class="font-semibold text-lg">Open Source Power</h4>
                </div>
                <p class="text-gray-400 text-sm mb-4">CryBug thrives on community contributions. Customize, contribute, or fork it your way.</p>
                <ul class="text-gray-500 text-xs space-y-3 mt-auto">
                  <li class="flex items-center">
                    <span class="inline-block bg-green-400/20 rounded-full p-1 mr-2">
                      <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </span>
                    Transparent codebase
                  </li>
                  <li class="flex items-center">
                    <span class="inline-block bg-green-400/20 rounded-full p-1 mr-2">
                      <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </span>
                    Developer-friendly APIs
                  </li>
                  <li class="flex items-center">
                    <span class="inline-block bg-green-400/20 rounded-full p-1 mr-2">
                      <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </span>
                    Welcoming contributor guidelines
                  </li>
                </ul>
                <div class="mt-4">
                  <a href="#github" class="text-xs text-green-400 hover:text-green-300 flex items-center">
                    View on GitHub <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                  </a>
                </div>
              </div>
            </div>
      
            <!-- D - Design Ready -->
            <div class="feature-card card bg-gray-800/60 border border-gray-700 rounded-2xl p-6 md:p-8 shadow-lg hover:shadow-pink-500/20 transition-all flex flex-col justify-between transform hover:-translate-y-1 relative overflow-hidden">
              <div class="blob absolute bg-pink-600/40"></div>
              <div class="fakeblob absolute w-full"></div>
              <div class="inner relative z-10 flex flex-col h-full">
                <div class="flex items-center mb-4">
                  <div class="icon-container text-pink-400 mr-3">
                    <i class="fi fi-brands-figma text-3xl"></i>
                  </div>
                  <h4 class="font-semibold text-lg">Design Ready</h4>
                </div>
                <p class="text-gray-400 text-sm">Seamless Figma integrations help your design and dev teams stay in perfect sync.</p>
                <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a href="#design" class="text-xs text-pink-400 hover:text-pink-300 flex items-center">
                    See templates <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </a>
                </div>
              </div>
            </div>
      
            <!-- E - Cloud Backup -->
            <div class="feature-card card bg-gray-800/60 border border-gray-700 rounded-2xl p-6 md:p-8 shadow-lg hover:shadow-yellow-500/20 transition-all flex flex-col justify-between transform hover:-translate-y-1 relative overflow-hidden">
              <div class="blob absolute bg-yellow-600/40"></div>
              <div class="fakeblob absolute w-full"></div>
              <div class="inner relative z-10 flex flex-col h-full">
                <div class="flex items-center mb-4">
                  <div class="icon-container text-yellow-400 mr-3">
                    <i class="ri-cloud-fill text-3xl"></i>
                  </div>
                  <h4 class="font-semibold text-lg">Cloud Backup</h4>
                </div>
                <p class="text-gray-400 text-sm">All your data is synced with your cloud provider for secure, automated backup and restore.</p>
                <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a href="#cloud" class="text-xs text-yellow-400 hover:text-yellow-300 flex items-center">
                    How it works <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </a>
                </div>
              </div>
            </div>
      
            <!-- B - Real-Time Chat -->
            <div class="feature-card card bg-gray-800/60 border border-gray-700 rounded-2xl p-6 md:p-8 shadow-lg hover:shadow-blue-500/20 transition-all flex flex-col justify-between transform hover:-translate-y-1 relative overflow-hidden">
              <div class="blob absolute bg-blue-600/40"></div>
              <div class="fakeblob absolute w-full"></div>
              <div class="inner relative z-10 flex flex-col h-full">
                <div class="flex items-center mb-4">
                  <div class="icon-container text-blue-400 mr-3">
                    <i class="fi fi-brands-slack text-3xl"></i>
                  </div>
                  <h4 class="font-semibold text-lg">Real-Time Chat</h4>
                </div>
                <p class="text-gray-400 text-sm">Slack & Discord integrations make collaboration quick, contextual, and always available.</p>
                <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a href="#integrations" class="text-xs text-blue-400 hover:text-blue-300 flex items-center">
                    Connect now <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </a>
                </div>
              </div>
            </div>
      
            <!-- F (2 Cols Wide) - Dev-First Toolkit -->
            <div class="feature-card card bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-700 rounded-2xl p-8 col-span-1 md:col-span-2 flex flex-col justify-between shadow-xl hover:shadow-purple-500/30 transition-all transform hover:-translate-y-1 relative overflow-hidden">
              <div class="blob absolute bg-purple-600/40"></div>
              <div class="fakeblob absolute w-full"></div>
              <div class="inner relative z-10 flex flex-col h-full">
                <div class="flex items-center mb-4">
                  <div class="icon-container text-orange-400 mr-3 relative">
                    <i class="fi fi-brands-stack-overflow text-4xl"></i>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                    </span>
                  </div>
                  <h4 class="text-xl font-semibold">Dev-First Toolkit</h4>
                </div>
                <p class="text-gray-400 text-sm mb-3">Enjoy debugging, auto-ticketing, smart workflows, and AI-enhanced suggestions—all in one place.</p>
                <ul class="text-gray-500 text-xs space-y-3 mb-4">
                  <li class="flex items-center">
                    <span class="inline-block bg-purple-400/20 rounded-full p-1 mr-2">
                      <svg class="w-3 h-3 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </span>
                    AI-assisted bug detection
                  </li>
                  <li class="flex items-center">
                    <span class="inline-block bg-purple-400/20 rounded-full p-1 mr-2">
                      <svg class="w-3 h-3 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                    </span>
                    One-click deployment tracking
                  </li>
                  <li class="flex items-center">
                    <span class="inline-block bg-purple-400/20 rounded-full p-1 mr-2">
                      <svg class="w-3 h-3 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                    </span>
                    Optimized for speed and performance
                  </li>
                </ul>
                <div class="mt-auto">
                  <button id="try-toolkit-btn" class="bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white font-medium py-2 px-4 rounded-lg text-sm transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg flex items-center">
                    Try the toolkit
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M5 7l5 5-5 5"></path></svg>
                  </button>
                </div>
              </div>
            </div>
      
          </div>
          
          <!-- Count-up stats section -->
          <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center fade-in">
              <div class="text-4xl font-bold text-blue-400 mb-2 counter" data-target="5000">0</div>
              <p class="text-gray-400">Active Developers</p>
            </div>
            <div class="text-center fade-in">
              <div class="text-4xl font-bold text-green-400 mb-2 counter" data-target="42">0</div>
              <p class="text-gray-400">Integrations</p>
            </div>
            <div class="text-center fade-in">
              <div class="text-4xl font-bold text-purple-400 mb-2 counter" data-target="99">0</div>
              <p class="text-gray-400">Uptime Percentage</p>
            </div>
          </div>
        </div>
      </section>







<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ why  us  Section ends ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->

      
      
      
<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ How Crybug Works  Section starts  ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->






      
      <section class="py-24 px-6 text-white relative overflow-hidden" id="work-process">
        <!-- Background elements -->
        <div class="absolute top-0 left-0 right-0 h-64 "></div>
        <div class="absolute inset-0"></div>
        
        <!-- Floating elements -->
        <div class="absolute w-64 h-64 rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 opacity-10 blur-xl -top-8 -left-20"></div>
        <div class="absolute w-80 h-80 rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-400 opacity-10 blur-xl -bottom-40 -right-20"></div>
        
        <div class="max-w-6xl mx-auto relative z-10">
          <!-- Section Eyebrow -->
          <div class="text-center mb-4 reveal" style="--reveal-delay: 0;">
              <span class="inline-block px-4 py-1 rounded-full bg-gradient-to-r from-lime-400/20 to-sky-500/20 text-cyan-400 text-sm font-medium tracking-wider">PROCESS</span>
          </div>
          
          <!-- Section Heading -->
          <div class="text-center mb-20">
              <span class="inline-block text-sm font-semibold text-cyan-400 tracking-wider uppercase mb-2">Simple Process</span>
              <h2 class="font-bold mb-4 leading-tight sm:leading-snug bg-gradient-to-r from-lime-400 via-cyan-400 to-sky-500 text-transparent bg-clip-text text-[clamp(2.5rem,6vw,4.5rem)]">
                  Streamlined Workflow
              </h2>
              <p class="max-w-xl mx-auto mb-5 text-gray-400">Experience a streamlined workflow designed for developers who value clarity and simplicity</p>
              <div class="h-1.5 w-48 mx-auto bg-gradient-to-r from-lime-400 to-sky-500 rounded-full shadow-lg shadow-sky-500/20"></div>
          </div>
        

          <!-- Timeline Steps -->
          <div class="space-y-0 pl-12 relative">
            <!-- Timeline connector -->
            <div class="timeline-connector"></div>
            
            <!-- Step 1 -->
            <div class="timeline-item flex items-start gap-8 py-8 pl-6 pr-4" data-step="1">
              <div class="step-icon text-5xl text-lime-400 shrink-0 bg-gray-800 rounded-full p-4 shadow-lg">
                <i class="ri-lightbulb-fill"></i>
              </div>
              <div class="timeline-content">
                <span class="text-xs font-semibold text-lime-400 uppercase tracking-wider">Step 01</span>
                <h4 class="text-2xl font-bold mb-3 mt-1">Create Your Project</h4>
                <p class="text-gray-400 mb-4">Start by setting up your bug-tracking or task-management space in seconds. Customize it to fit your team's workflow.</p>
                <div class="flex gap-6">
                  <div class="bg-gray-800 p-3 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Average setup time</div>
                    <div class="text-lime-400 font-bold">< 2 minutes</div>
                  </div>
                  <div class="bg-gray-800 p-3 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Templates available</div>
                    <div class="text-lime-400 font-bold">12+</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="timeline-item flex items-start gap-8 py-8 pl-6 pr-4" data-step="2">
              <div class="step-icon text-5xl text-cyan-400 shrink-0 bg-gray-800 rounded-full p-4 shadow-lg">
                <i class="ri-file-list-3-fill"></i>
              </div>
              <div class="timeline-content">
                <span class="text-xs font-semibold text-cyan-400 uppercase tracking-wider">Step 02</span>
                <h4 class="text-2xl font-bold mb-3 mt-1">Add Tasks & Bugs</h4>
                <p class="text-gray-400 mb-4">Log bugs, assign tasks, and track everything in one place with powerful filtering and categorization tools.</p>
                <div class="flex gap-6">
                  <div class="bg-gray-800 p-3 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Priority options</div>
                    <div class="text-cyan-400 font-bold">5 levels</div>
                  </div>
                  <div class="bg-gray-800 p-3 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Custom fields</div>
                    <div class="text-cyan-400 font-bold">Unlimited</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="timeline-item flex items-start gap-8 py-8 pl-6 pr-4" data-step="3">
              <div class="step-icon text-5xl text-violet-400 shrink-0 bg-gray-800 rounded-full p-4 shadow-lg">
                <i class="ri-shake-hands-fill"></i>
              </div>
              <div class="timeline-content">
                <span class="text-xs font-semibold text-violet-400 uppercase tracking-wider">Step 03</span>
                <h4 class="text-2xl font-bold mb-3 mt-1">Collaborate in Real-Time</h4>
                <p class="text-gray-400 mb-4">Discuss bugs, drop comments, and keep everyone aligned instantly with our real-time collaboration tools.</p>
                <div class="flex gap-6">
                  <div class="bg-gray-800 p-3 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Comment threading</div>
                    <div class="text-violet-400 font-bold">Unlimited</div>
                  </div>
                  <div class="bg-gray-800 p-3 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Live updates</div>
                    <div class="text-violet-400 font-bold">Real-time</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="timeline-item flex items-start gap-8 py-8 pl-6 pr-4" data-step="4">
              <div class="step-icon text-5xl text-rose-400 shrink-0 bg-gray-800 rounded-full p-4 shadow-lg">
                <i class="ri-rocket-2-fill"></i>
              </div>
              <div class="timeline-content">
                <span class="text-xs font-semibold text-rose-400 uppercase tracking-wider">Step 04</span>
                <h4 class="text-2xl font-bold mb-3 mt-1">Launch Confidently</h4>
                <p class="text-gray-400 mb-4">Resolve, review, and deploy your product with full visibility and confidence in your bug-free releases.</p>
                <div class="flex gap-6">
                  <div class="bg-gray-800 p-3 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Success metrics</div>
                    <div class="text-rose-400 font-bold">10+ reports</div>
                  </div>
                  <div class="bg-gray-800 p-3 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Deployment hooks</div>
                    <div class="text-rose-400 font-bold">CI/CD ready</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- CTA button -->
          <div class="text-center mt-16">
            
            <a href="#home">
            <button id="get-started-btn" class="px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-700 hover:to-blue-600 text-white font-bold rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg ">
              Start Using CryBug Today
            </button>
            </a>
          </div>
        </div>
      </section>





<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ How Crybug Works  Section ends ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->

      


<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Projects CryBug Section starts ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->


<section class="py-24 px-6 " id="projects">
  <div class="max-w-6xl mx-auto">
    <!-- Section Eyebrow -->
    <div class="text-center mb-4 reveal" style="--reveal-delay: 0;">
        <span class="inline-block px-4 py-1 rounded-full bg-gradient-to-r from-fuchsia-500/20 to-amber-300/20 text-fuchsia-500 text-sm font-medium tracking-wider">SHOWCASE</span>
    </div>
    
    <!-- Enhanced Section Heading with Unique Gradient -->
    <div class="relative mb-20 text-center">
        <div class="inline-block">
            <h2 class="text-4xl sm:text-5xl md:text-6xl leading-tight sm:leading-snug font-extrabold mb-3 bg-gradient-to-r from-fuchsia-500 via-emerald-400 to-amber-300 bg-clip-text text-transparent drop-shadow-lg">
                Built with CryBug
            </h2>
            <div class="h-1.5 w-48 mx-auto bg-gradient-to-r from-fuchsia-500 via-emerald-400 to-amber-300 rounded-full shadow-lg shadow-fuchsia-500/20"></div>
        </div>
        
        <p class="text-center text-gray-400 max-w-2xl mx-auto mt-8 text-base sm:text-lg">
            Explore real-world projects tracked and delivered through the CryBug platform.
            <span class="block mt-2 text-emerald-300 font-medium">Helping teams solve problems faster.</span>
        </p>
    </div>
    
    <!-- Enhanced Project Stats with Glow Effects -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20">
      <div class="stat-item text-center p-6 bg-gray-800/80 backdrop-blur-sm rounded-xl border border-gray-700 hover:border-fuchsia-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-fuchsia-500/10">
        <div id="stat1" class="text-3xl md:text-4xl font-bold text-fuchsia-300 mb-2">450+</div>
        <div class="text-sm text-gray-400">Projects Managed</div>
      </div>
      <div class="stat-item text-center p-6 bg-gray-800/80 backdrop-blur-sm rounded-xl border border-gray-700 hover:border-emerald-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
        <div id="stat2" class="text-3xl md:text-4xl font-bold text-emerald-300 mb-2">98%</div>
        <div class="text-sm text-gray-400">Bug Resolution Rate</div>
      </div>
      <div class="stat-item text-center p-6 bg-gray-800/80 backdrop-blur-sm rounded-xl border border-gray-700 hover:border-amber-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/10">
        <div id="stat3" class="text-3xl md:text-4xl font-bold text-amber-300 mb-2">12K+</div>
        <div class="text-sm text-gray-400">Bugs Squashed</div>
      </div>
      <div class="stat-item text-center p-6 bg-gray-800/80 backdrop-blur-sm rounded-xl border border-gray-700 hover:border-rose-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-rose-500/10">
        <div id="stat4" class="text-3xl md:text-4xl font-bold text-rose-300 mb-2">320</div>
        <div class="text-sm text-gray-400">Active Teams</div>
      </div>
    </div>

    <!-- Enhanced Filtering Menu with Pill Buttons -->
    <div class="flex flex-wrap justify-center gap-4 mb-16">
      <button id="filter-all" class="px-6 py-2.5 rounded-full text-sm font-medium bg-gradient-to-r from-fuchsia-600 to-fuchsia-700 text-white transition-all duration-300 hover:from-fuchsia-700 hover:to-fuchsia-800 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:ring-offset-2 focus:ring-offset-gray-900 shadow-md shadow-fuchsia-500/20 hover:shadow-fuchsia-500/30">
        All Projects
      </button>
      <button id="filter-dashboard" class="px-6 py-2.5 rounded-full text-sm font-medium bg-gray-800 text-gray-300 border border-gray-700 transition-all duration-300 hover:bg-gray-700 hover:border-fuchsia-500/50 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:ring-offset-2 focus:ring-offset-gray-900 shadow-md hover:shadow-fuchsia-500/20">
        Dashboards
      </button>
      <button id="filter-ai" class="px-6 py-2.5 rounded-full text-sm font-medium bg-gray-800 text-gray-300 border border-gray-700 transition-all duration-300 hover:bg-gray-700 hover:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-gray-900 shadow-md hover:shadow-emerald-500/20">
        AI Tools
      </button>
      <button id="filter-collaboration" class="px-6 py-2.5 rounded-full text-sm font-medium bg-gray-800 text-gray-300 border border-gray-700 transition-all duration-300 hover:bg-gray-700 hover:border-amber-500/50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-gray-900 shadow-md hover:shadow-amber-500/20">
        Collaboration
      </button>
    </div>

    <!-- Enhanced Project Cards with Glass Effect -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="project-container">
      
      <!-- Project 1 - Enhanced Card -->
      <div class="project-card card-animate rounded-2xl overflow-hidden bg-gray-800/70 backdrop-blur-md shadow-xl border border-gray-700 group" data-category="dashboard">
        <div class="card-image-container relative">
          <img src="images/project/BT3.png" alt="Bug Tracker Dashboard" 
               class="w-full h-64 object-cover transition-transform duration-700 ease-in-out group-hover:scale-110" />
          <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-60"></div>
        </div>
        <div class="p-8 relative z-10">
          <div class="flex justify-between items-start mb-4">
            <h3 class="text-2xl font-bold text-white group-hover:text-fuchsia-300 transition-colors duration-300">Bug Tracker Dashboard</h3>
          </div>
          <p class="text-gray-300 text-sm mb-6">An intuitive dashboard that visualizes project bugs, team stats, and progress in real-time.</p>
          <div class="flex flex-wrap gap-2 mb-6">
            <span class="tag text-xs px-3 py-1.5 rounded-full bg-gray-700/80 text-gray-300 hover:bg-fuchsia-900/80 hover:text-fuchsia-200 transition-all duration-300 border border-gray-600 hover:border-fuchsia-500/50">React</span>
            <span class="tag text-xs px-3 py-1.5 rounded-full bg-gray-700/80 text-gray-300 hover:bg-fuchsia-900/80 hover:text-fuchsia-200 transition-all duration-300 border border-gray-600 hover:border-fuchsia-500/50">D3.js</span>
            <span class="tag text-xs px-3 py-1.5 rounded-full bg-gray-700/80 text-gray-300 hover:bg-fuchsia-900/80 hover:text-fuchsia-200 transition-all duration-300 border border-gray-600 hover:border-fuchsia-500/50">API</span>
          </div>
          <a href="#" class="inline-flex items-center text-sm font-medium text-fuchsia-400 hover:text-fuchsia-300 transition-colors focus:outline-none focus:text-fuchsia-200 group">
            View Details
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </a>
        </div>
      </div>
  
      <!-- Project 2 - Enhanced Card -->
      <div class="project-card card-animate rounded-2xl overflow-hidden bg-gray-800/70 backdrop-blur-md shadow-xl border border-gray-700 group" data-category="ai">
        <div class="card-image-container relative">
          <img src="images/project/AIBT8.png" alt="AI Bug Prediction" 
               class="w-full h-64 object-cover transition-transform duration-700 ease-in-out group-hover:scale-110" />
          <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-60"></div>
          
        </div>
        <div class="p-8 relative z-10">
          <div class="flex justify-between items-start mb-4">
            <h3 class="text-2xl font-bold text-white group-hover:text-emerald-300 transition-colors duration-300">AI Bug Prediction</h3>
          </div>
          <p class="text-gray-300 text-sm mb-6">CryBug's AI-assisted system predicts potential bugs based on commit history and code analysis.</p>
          <div class="flex flex-wrap gap-2 mb-6">
            <span class="tag text-xs px-3 py-1.5 rounded-full bg-gray-700/80 text-gray-300 hover:bg-emerald-900/80 hover:text-emerald-200 transition-all duration-300 border border-gray-600 hover:border-emerald-500/50">Python</span>
            <span class="tag text-xs px-3 py-1.5 rounded-full bg-gray-700/80 text-gray-300 hover:bg-emerald-900/80 hover:text-emerald-200 transition-all duration-300 border border-gray-600 hover:border-emerald-500/50">TensorFlow</span>
            <span class="tag text-xs px-3 py-1.5 rounded-full bg-gray-700/80 text-gray-300 hover:bg-emerald-900/80 hover:text-emerald-200 transition-all duration-300 border border-gray-600 hover:border-emerald-500/50">Git</span>
          </div>
          <a href="#" class="inline-flex items-center text-sm font-medium text-emerald-400 hover:text-emerald-300 transition-colors focus:outline-none focus:text-emerald-200 group">
            View Details
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </a>
        </div>
      </div>
  
      <!-- Project 3 - Enhanced Card -->
      <div class="project-card card-animate rounded-2xl overflow-hidden bg-gray-800/70 backdrop-blur-md shadow-xl border border-gray-700 group" data-category="collaboration">
        <div class="card-image-container relative">
          <img src="images/project/CT4.png" alt="Team Collaboration Tools" 
               class="w-full h-64 object-cover transition-transform duration-700 ease-in-out group-hover:scale-110" />
          <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-60"></div>
          
        </div>
        <div class="p-8 relative z-10">
          <div class="flex justify-between items-start mb-4">
            <h3 class="text-2xl font-bold text-white group-hover:text-amber-300 transition-colors duration-300">Team Collaboration Tools</h3>
          </div>
          <p class="text-gray-300 text-sm mb-6">Custom-built chat, issue mentions, and tag management to keep your dev team in sync.</p>
          <div class="flex flex-wrap gap-2 mb-6">
            <span class="tag text-xs px-3 py-1.5 rounded-full bg-gray-700/80 text-gray-300 hover:bg-amber-900/80 hover:text-amber-200 transition-all duration-300 border border-gray-600 hover:border-amber-500/50">Node.js</span>
            <span class="tag text-xs px-3 py-1.5 rounded-full bg-gray-700/80 text-gray-300 hover:bg-amber-900/80 hover:text-amber-200 transition-all duration-300 border border-gray-600 hover:border-amber-500/50">Socket.io</span>
            <span class="tag text-xs px-3 py-1.5 rounded-full bg-gray-700/80 text-gray-300 hover:bg-amber-900/80 hover:text-amber-200 transition-all duration-300 border border-gray-600 hover:border-amber-500/50">MongoDB</span>
          </div>
          <a href="#" class="inline-flex items-center text-sm font-medium text-amber-400 hover:text-amber-300 transition-colors focus:outline-none focus:text-amber-200 group">
            View Details
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </a>
        </div>
      </div>
    </div> 


  </section>







<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Projects CryBug Section ends ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->



<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Testimonials CryBug Section starts ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->



          <section class="py-32 px-6 text-white relative overflow-hidden" id="crybug-testimonials">
            <!-- Animated background -->
            <div class="animated-bg">
              <div class="animated-bg-shape bg-purple-600" style="width: 40%; height: 40%; top: 10%; left: 5%; animation-delay: 0s;"></div>
              <div class="animated-bg-shape bg-pink-600" style="width: 30%; height: 30%; bottom: 20%; right: 5%; animation-delay: -5s;"></div>
              <div class="animated-bg-shape bg-blue-600" style="width: 50%; height: 50%; top: 40%; left: 50%; animation-delay: -10s;"></div>
              <div class="animated-bg-shape bg-indigo-600" style="width: 35%; height: 35%; bottom: 10%; left: 30%; animation-delay: -15s;"></div>
            </div>
            
            <div class="max-w-6xl mx-auto">
              <!-- Section Eyebrow -->
              <div class="text-center mb-4 reveal" style="--reveal-delay: 0;">
                  <span class="inline-block px-4 py-1 rounded-full bg-gradient-to-r from-indigo-600/20 to-pink-500/20 text-indigo-400 text-sm font-medium tracking-wider">TESTIMONIALS</span>
              </div>
              
              <!-- Enhanced Section Heading with Unique Gradient -->
              <div class="relative mb-20 text-center">
                <div class="inline-block">
                  <h2 class="text-4xl sm:text-5xl md:text-6xl leading-tight sm:leading-snug font-extrabold mb-3 bg-gradient-to-r from-indigo-600 via-purple-500 to-pink-500 bg-clip-text text-transparent drop-shadow-lg">
                    What People Are Saying
                  </h2>
                  <div class="h-1.5 w-48 mx-auto bg-gradient-to-r from-indigo-600 via-purple-500 to-pink-500 rounded-full shadow-lg shadow-purple-500/20"></div>
                </div>
                
                <p class="text-center text-gray-400 max-w-2xl mx-auto mt-8 text-base sm:text-lg">
                  Discover what our users have to say about their experience with CryBug.
                  <span class="block mt-2 text-indigo-300 font-medium">Real voices. Real impact.</span>
                </p>
              </div>

              <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
                <!-- Card 1 -->
                <div class="crybug-testimonial-card crybug-testimonial-border card-accent-pink bg-gray-900 bg-opacity-70 backdrop-filter backdrop-blur-sm rounded-2xl p-8 shadow-lg relative">
                  <div class="crybug-testimonial-glow">
                    <div class="flex items-center gap-5 mb-6">
                      <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-pink-500 to-purple-600 blur-sm"></div>
                        <img src="images/testimonials/1.jpg" alt="Sara Kapoor" class="w-20 h-20 rounded-full object-cover border-2 border-transparent relative z-10" />
                      </div>
                      <div>
                        <h4 class="font-semibold text-xl">Sara Kapoor</h4>
                        <span class="text-base text-pink-300">Full-Stack Developer</span>
                      </div>
                    </div>
                    <p class="text-gray-100 text-lg leading-relaxed mb-6">"CryBug has truly transformed the way our team handles bugs. It's smart, fast, and extremely intuitive!"</p>
                    <div class="mt-6 flex star-rating">
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                    </div>
                    <div class="quote-icon">
                      <i class="fi fi-sr-quote-right"></i>
                    </div>
                  </div>
                </div>

                <!-- Card 2 -->
                <div class="crybug-testimonial-card crybug-testimonial-border card-accent-teal bg-gray-900 bg-opacity-70 backdrop-filter backdrop-blur-sm rounded-2xl p-8 shadow-lg relative">
                  <div class="crybug-testimonial-glow">
                    <div class="flex items-center gap-5 mb-6">
                      <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-teal-500 to-blue-600 blur-sm"></div>
                        <img src="images/testimonials/2.jpg" alt="Jason Li" class="w-20 h-20 rounded-full object-cover border-2 border-transparent relative z-10" />
                      </div>
                      <div>
                        <h4 class="font-semibold text-xl">Jason Li</h4>
                        <span class="text-base text-teal-300">Startup CTO</span>
                      </div>
                    </div>
                    <p class="text-gray-100 text-lg leading-relaxed mb-6">"The AI-assisted bug triage has saved us countless hours. CryBug feels like it's built by devs, for devs."</p>
                    <div class="mt-6 flex star-rating">
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                    </div>
                    <div class="quote-icon">
                      <i class="fi fi-sr-quote-right"></i>
                    </div>
                  </div>
                </div>

                <!-- Card 3 -->
                <div class="crybug-testimonial-card crybug-testimonial-border card-accent-blue bg-gray-900 bg-opacity-70 backdrop-filter backdrop-blur-sm rounded-2xl p-8 shadow-lg relative">
                  <div class="crybug-testimonial-glow">
                    <div class="flex items-center gap-5 mb-6">
                      <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 blur-sm"></div>
                        <img src="images/testimonials/3.jpg" alt="Emily Clark" class="w-20 h-20 rounded-full object-cover border-2 border-transparent relative z-10" />
                      </div>
                      <div>
                        <h4 class="font-semibold text-xl">Emily Clark</h4>
                        <span class="text-base text-blue-300">Project Manager</span>
                      </div>
                    </div>
                    <p class="text-gray-100 text-lg leading-relaxed mb-6">"Tracking, prioritizing, and communicating with the team has never been smoother. Absolutely love it!"</p>
                    <div class="mt-6 flex star-rating">
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star-sharp-half-stroke"></i>
                    </div>
                    <div class="quote-icon">
                      <i class="fi fi-sr-quote-right"></i>
                    </div>
                  </div>
                </div>

                <!-- Card 4 -->
                <div class="crybug-testimonial-card crybug-testimonial-border card-accent-orange bg-gray-900 bg-opacity-70 backdrop-filter backdrop-blur-sm rounded-2xl p-8 shadow-lg relative">
                  <div class="crybug-testimonial-glow">
                    <div class="flex items-center gap-5 mb-6">
                      <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-yellow-500 to-orange-600 blur-sm"></div>
                        <img src="images/testimonials/4.jpg" alt="Carlos Vega" class="w-20 h-20 rounded-full object-cover border-2 border-transparent relative z-10" />
                      </div>
                      <div>
                        <h4 class="font-semibold text-xl">Carlos Vega</h4>
                        <span class="text-base text-yellow-300">DevOps Engineer</span>
                      </div>
                    </div>
                    <p class="text-gray-100 text-lg leading-relaxed mb-6">"CryBug's integration with our CI/CD pipeline was flawless. The alerts and bug insights are next-level!"</p>
                    <div class="mt-6 flex star-rating">
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star-sharp-half-stroke"></i>
                    </div>
                    <div class="quote-icon">
                      <i class="fi fi-sr-quote-right"></i>
                    </div>
                  </div>
                </div>

                <!-- Card 5 (New) -->
                <div class="crybug-testimonial-card crybug-testimonial-border card-accent-green bg-gray-900 bg-opacity-70 backdrop-filter backdrop-blur-sm rounded-2xl p-8 shadow-lg relative">
                  <div class="crybug-testimonial-glow">
                    <div class="flex items-center gap-5 mb-6">
                      <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-green-500 to-teal-600 blur-sm"></div>
                        <img src="images/testimonials/5.jpg" alt="Priya Sharma" class="w-20 h-20 rounded-full object-cover border-2 border-transparent relative z-10" />
                      </div>
                      <div>
                        <h4 class="font-semibold text-xl">Priya Sharma</h4>
                        <span class="text-base text-green-300">QA Specialist</span>
                      </div>
                    </div>
                    <p class="text-gray-100 text-lg leading-relaxed mb-6">"CryBug's reporting feature has revolutionized how our QA team documents issues. The automated severity scoring is a game-changer!"</p>
                    <div class="mt-6 flex star-rating">
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                    </div>
                    <div class="quote-icon">
                      <i class="fi fi-sr-quote-right"></i>
                    </div>
                  </div>
                </div>

                <!-- Card 6 (New) -->
                <div class="crybug-testimonial-card crybug-testimonial-border card-accent-purple bg-gray-900 bg-opacity-70 backdrop-filter backdrop-blur-sm rounded-2xl p-8 shadow-lg relative">
                  <div class="crybug-testimonial-glow">
                    <div class="flex items-center gap-5 mb-6">
                      <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 blur-sm"></div>
                        <img src="images/testimonials/6.jpg" alt="Michael Zhang" class="w-20 h-20 rounded-full object-cover border-2 border-transparent relative z-10" />
                      </div>
                      <div>
                        <h4 class="font-semibold text-xl">Michael Zhang</h4>
                        <span class="text-base text-purple-300">Frontend Engineer</span>
                      </div>
                    </div>
                    <p class="text-gray-100 text-lg leading-relaxed mb-6">"The visual regression testing in CryBug caught inconsistencies that would have been impossible to track manually. Worth every penny!"</p>
                    <div class="mt-6 flex star-rating">
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star"></i>
                      <i class="fi fi-sr-star-sharp-half-stroke"></i>
                    </div>
                    <div class="quote-icon">
                      <i class="fi fi-sr-quote-right"></i>
                    </div>
                  </div>
                </div>
              </div>
              

            </div>
          </section>




<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Testimonials CryBug Section ends ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->




<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Gallery  Section starts ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->

        <section class="py-20 px-6 text-white" id="team-experts">
          <div class="max-w-6xl mx-auto text-center relative z-10 mb-10 slideIn" data-delay="0.1s">
            <!-- Section Eyebrow -->
            <div class="text-center mb-4">
              <span class="inline-block px-4 py-1 rounded-full bg-gradient-to-r from-yellow-400/20 to-purple-600/20 text-yellow-400 text-sm font-medium tracking-wider">OUR EXPERTS</span>
            </div>
          
            <!-- Section Heading -->
            <div class="text-center mb-16">
              <span class="inline-block text-sm font-semibold text-yellow-400 tracking-wider uppercase mb-2">Innovative Minds</span>
              <h2 class="font-bold mb-4 leading-tight sm:leading-snug bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 text-transparent bg-clip-text text-[clamp(2.5rem,6vw,4.5rem)]">
                Meet the Makers
              </h2>
              <p class="max-w-xl mx-auto mb-5 text-gray-400">Our diverse team of experts brings together decades of experience across development, design, and AI to create revolutionary solutions that push boundaries.</p>
              <div class="h-1.5 w-48 mx-auto bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 rounded-full shadow-lg shadow-purple-600/20"></div>
            </div>
          </div>

          <div class="relative overflow-hidden slideIn" data-delay="0.3s">
            <div class="flex team-carousel space-x-8 w-max">
              <!-- Team Member Cards -->
              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-yellow-500/30 bg-gradient-to-br from-yellow-500/20 to-pink-500/20 p-1">
                    <img src="images/gallery/1.jpg" alt="Alex Carter" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/0 via-pink-500/30 to-purple-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-yellow-300">Alex Carter</h4>
                <p class="text-sm text-gray-400 mb-3">Lead Developer</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-yellow-400 to-pink-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">Architecture wizard with 10+ years crafting scalable solutions</p>
              </div>

              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-pink-500/30 bg-gradient-to-br from-pink-500/20 to-purple-600/20 p-1">
                    <img src="images/gallery/2.jpg" alt="Sara Kim" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/0 via-pink-500/30 to-purple-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-pink-300">Sara Kim</h4>
                <p class="text-sm text-gray-400 mb-3">UI/UX Designer</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-pink-400 to-purple-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">Design innovator making complex interfaces intuitive</p>
              </div>

              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-blue-500/30 bg-gradient-to-br from-blue-500/20 to-purple-600/20 p-1">
                    <img src="images/gallery/3.jpg" alt="Jatin Rao" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/0 via-blue-500/30 to-purple-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-blue-300">Jatin Rao</h4>
                <p class="text-sm text-gray-400 mb-3">Backend Engineer</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-blue-400 to-purple-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">Performance optimization specialist and database expert</p>
              </div>

              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-teal-500/30 bg-gradient-to-br from-teal-500/20 to-blue-600/20 p-1">
                    <img src="images/gallery/4.jpg" alt="Maya Lopez" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-teal-400/0 via-teal-500/30 to-blue-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-teal-300">Maya Lopez</h4>
                <p class="text-sm text-gray-400 mb-3">Product Manager</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-teal-400 to-blue-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">Strategic visionary translating ideas into market solutions</p>
              </div>

              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-purple-500/30 bg-gradient-to-br from-purple-500/20 to-pink-600/20 p-1">
                    <img src="images/gallery/5.jpg" alt="Arjun Mehta" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-purple-400/0 via-purple-500/30 to-pink-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-purple-300">Arjun Mehta</h4>
                <p class="text-sm text-gray-400 mb-3">AI Engineer</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-purple-400 to-pink-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">ML specialist implementing cutting-edge algorithms</p>
              </div>

              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-orange-500/30 bg-gradient-to-br from-orange-500/20 to-red-600/20 p-1">
                    <img src="images/gallery/6.jpg" alt="Lina Zhao" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-orange-400/0 via-orange-500/30 to-red-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-orange-300">Lina Zhao</h4>
                <p class="text-sm text-gray-400 mb-3">Frontend Dev</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-orange-400 to-red-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">UX-focused developer creating immersive interfaces</p>
              </div>

              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-cyan-500/30 bg-gradient-to-br from-cyan-500/20 to-blue-600/20 p-1">
                    <img src="images/gallery/7.jpg" alt="Ravi Verma" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-cyan-400/0 via-cyan-500/30 to-blue-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-cyan-300">Ravi Verma</h4>
                <p class="text-sm text-gray-400 mb-3">DevOps Engineer</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-cyan-400 to-blue-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">Infrastructure guru optimizing deployment pipelines</p>
              </div>

              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-pink-500/30 bg-gradient-to-br from-pink-500/20 to-purple-600/20 p-1">
                    <img src="images/gallery/8.jpg" alt="Isla Khan" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-pink-400/0 via-pink-500/30 to-purple-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-pink-300">Isla Khan</h4>
                <p class="text-sm text-gray-400 mb-3">QA Tester</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-pink-400 to-purple-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">Quality assurance expert with eagle-eye attention to detail</p>
              </div>

              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-indigo-500/30 bg-gradient-to-br from-indigo-500/20 to-blue-600/20 p-1">
                    <img src="images/gallery/9.jpg" alt="Yuki Matsuo" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-indigo-400/0 via-indigo-500/30 to-blue-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-indigo-300">Yuki Matsuo</h4>
                <p class="text-sm text-gray-400 mb-3">Security Analyst</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-indigo-400 to-blue-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">Cybersecurity specialist protecting digital assets</p>
              </div>

              <div class="min-w-[250px] text-center team-expert-card group p-4">
                <div class="relative">
                  <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-green-500/30 bg-gradient-to-br from-green-500/20 to-teal-600/20 p-1">
                    <img src="images/gallery/10.jpg" alt="Nikhil Shah" class="w-full h-full object-cover rounded-full member-img shadow-lg" />
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-green-400/0 via-green-500/30 to-teal-600/0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h4 class="mt-6 font-bold text-xl text-green-300">Nikhil Shah</h4>
                <p class="text-sm text-gray-400 mb-3">System Architect</p>
                <div class="h-0.5 w-12 mx-auto bg-gradient-to-r from-green-400 to-teal-500"></div>
                <p class="mt-4 text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">Solutions architect designing elegant systems at scale</p>
              </div>
            </div>
          </div>

          <!-- Team Stats -->
          <div class="max-w-6xl mx-auto mt-24 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 px-4 slideIn" data-delay="0.5s" id="carrier">
            <div class="text-center p-6 rounded-xl bg-gradient-to-b from-gray-800/50 to-gray-900/50 backdrop-blur-sm border border-gray-800 transform hover:scale-105 transition-all">
              <div class="w-12 h-12 flex items-center justify-center mx-auto mb-4 rounded-full bg-gradient-to-r from-yellow-400/30 to-pink-500/30 text-yellow-400 stat-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-4xl font-bold bg-gradient-to-r from-yellow-400 to-pink-500 text-transparent bg-clip-text mb-2">120+</h3>
              <p class="text-gray-400">Years Combined Experience</p>
            </div>
            
            <div class="text-center p-6 rounded-xl bg-gradient-to-b from-gray-800/50 to-gray-900/50 backdrop-blur-sm border border-gray-800 transform hover:scale-105 transition-all">
              <div class="w-12 h-12 flex items-center justify-center mx-auto mb-4 rounded-full bg-gradient-to-r from-pink-500/30 to-purple-600/30 text-pink-400 stat-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
              </div>
              <h3 class="text-4xl font-bold bg-gradient-to-r from-pink-400 to-purple-500 text-transparent bg-clip-text mb-2">85+</h3>
              <p class="text-gray-400">Projects Delivered</p>
            </div>
            
            <div class="text-center p-6 rounded-xl bg-gradient-to-b from-gray-800/50 to-gray-900/50 backdrop-blur-sm border border-gray-800 transform hover:scale-105 transition-all">
              <div class="w-12 h-12 flex items-center justify-center mx-auto mb-4 rounded-full bg-gradient-to-r from-purple-500/30 to-blue-600/30 text-purple-400 stat-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                </svg>
              </div>
              <h3 class="text-4xl font-bold bg-gradient-to-r from-purple-400 to-blue-500 text-transparent bg-clip-text mb-2">12</h3>
              <p class="text-gray-400">Industry Awards</p>
            </div>
            
            <div class="text-center p-6 rounded-xl bg-gradient-to-b from-gray-800/50 to-gray-900/50 backdrop-blur-sm border border-gray-800 transform hover:scale-105 transition-all">
              <div class="w-12 h-12 flex items-center justify-center mx-auto mb-4 rounded-full bg-gradient-to-r from-blue-500/30 to-cyan-600/30 text-blue-400 stat-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                </svg>
              </div>
              <h3 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-cyan-500 text-transparent bg-clip-text mb-2">24</h3>
              <p class="text-gray-400">Countries Represented</p>
            </div>
          </div>

          <!-- Call to Action -->
          <div class="max-w-4xl mx-auto mt-24 text-center p-8 rounded-2xl bg-gradient-to-r from-gray-900/80 to-black/80 border border-gray-800 backdrop-blur-sm slideIn" data-delay="0.7s">
            <h3 class="text-2xl md:text-3xl font-bold mb-4 bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 text-transparent bg-clip-text">Join Our Growing Team</h3>
            <p class="text-gray-300 mb-8 max-w-2xl mx-auto">We're always looking for talented individuals to join our innovative team. If you're passionate about technology and want to work with industry leaders, we'd love to hear from you.</p>
            <a href="login-pages/signup.html" class="inline-block py-3 px-8 rounded-full bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 text-white font-semibold hover:shadow-lg hover:shadow-purple-500/30 transition-all transform hover:-translate-y-1">Join us</a>
          </div>
        </section>










<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Gallery Section ends ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->


<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ About us Section  starts ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->


    <!-- ========================== About Us Section ========================== -->
    <section class="py-20 px-6 text-white relative overflow-hidden" id="about-us">
      <!-- Background Elements -->
      <div class="absolute top-1/4 left-10 w-64 h-64 rounded-full bg-purple-500 opacity-5 blur-3xl"></div>
      <div class="absolute bottom-1/4 right-10 w-96 h-96 rounded-full bg-blue-500 opacity-5 blur-3xl"></div>
      
      <div class="max-w-6xl mx-auto text-center relative z-10">
        <!-- Section Eyebrow -->
        <div class="text-center mb-4 reveal" style="--reveal-delay: 0;">
          <span class="inline-block px-4 py-1 rounded-full bg-gradient-to-r from-pink-500/20 to-purple-500/20 text-pink-400 text-sm font-medium tracking-wider">ABOUT US</span>
        </div>
      
        <!-- Gradient Heading -->
        <div class="text-center mb-20">
          <span class="inline-block text-sm font-semibold text-pink-400 tracking-wider uppercase mb-2">Meet the Minds</span>
          <h2 class="font-bold mb-4 leading-tight sm:leading-snug bg-gradient-to-r from-pink-400 via-purple-400 to-indigo-500 text-transparent bg-clip-text text-[clamp(2.5rem,6vw,4.5rem)]">
            Who We Are
          </h2>
          <p class="max-w-xl mx-auto mb-5 text-gray-400">CryBug is a team of passionate developers, designers, and technologists committed to redefining the debugging experience through innovation and simplicity.</p>
          <div class="h-1.5 w-48 mx-auto bg-gradient-to-r from-pink-400 via-purple-400 to-indigo-500 rounded-full shadow-lg shadow-indigo-500/20"></div>
        </div>
      
      
    
        <div class="grid md:grid-cols-2 gap-10 items-center">
          <!-- Image with enhanced styling -->
          <div class="reveal" style="--reveal-delay: 2;">
            <div class="relative group">
              <!-- Decorative elements -->
              <div class="absolute -top-6 -left-6 w-12 h-12 border-t-2 border-l-2 border-pink-400 opacity-60 group-hover:opacity-100 transition-opacity duration-500"></div>
              <div class="absolute -bottom-6 -right-6 w-12 h-12 border-b-2 border-r-2 border-indigo-400 opacity-60 group-hover:opacity-100 transition-opacity duration-500"></div>
              
              <!-- Image wrapper with glow effect -->
              <div class="relative overflow-hidden rounded-xl border-2 border-gray-700 pulse-border group-hover:border-purple-500 transition-colors duration-500">
                <div class="absolute inset-0 bg-gradient-to-br from-pink-500/20 via-purple-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <img src="images/about-us/about-us2.png" alt="Teamwork Illustration" class="w-full h-auto mx-auto md:mx-0 rounded-xl shadow-xl md:max-w-xl transform group-hover:scale-105 transition duration-700 ease-in-out" />
              </div>
            </div>
          </div>
    
          <!-- Content with enhanced styling -->
          <div class="reveal" style="--reveal-delay: 3;">
            <div class="space-y-6">
              <p class="text-gray-300 text-lg leading-relaxed">
                <span class="text-xl font-medium text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-purple-400">CryBug</span> is a passionate team of developers, designers, and problem-solvers on a mission to redefine bug tracking and task management.
              </p>
              
              <div class="p-6 rounded-xl backdrop-blur-sm bg-gray-800/30 border border-gray-700">
                <h4 class="font-semibold text-lg text-white mb-4 flex items-center">
                  <span class="inline-block w-8 h-1 bg-gradient-to-r from-pink-400 to-purple-400 mr-3"></span>
                  Our Approach
                </h4>
                
                <ul class="space-y-4">
                  <li class="flex items-start">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-green-400/20 text-green-400 mr-3 mt-0.5">
                      <i class="ri-check-line"></i>
                    </span>
                    <span><span class="text-green-400 font-semibold">Fast</span> and intuitive interface for seamless tracking</span>
                  </li>
                  
                  <li class="flex items-start">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-teal-400/20 text-teal-400 mr-3 mt-0.5">
                      <i class="ri-shield-check-line"></i>
                    </span>
                    <span><span class="text-teal-400 font-semibold">Secure</span> platform to keep your data safe</span>
                  </li>
                  
                  <li class="flex items-start">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-400/20 text-blue-400 mr-3 mt-0.5">
                      <i class="ri-team-line"></i>
                    </span>
                    <span><span class="text-blue-400 font-semibold">Collaborative</span> tools for better team communication</span>
                  </li>
                  
                  <li class="flex items-start">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-yellow-400/20 text-yellow-400 mr-3 mt-0.5">
                      <i class="ri-brain-line"></i>
                    </span>
                    <span><span class="text-yellow-400 font-semibold">AI-assisted</span> prioritization and smart workflows</span>
                  </li>
                </ul>
              </div>
              
              <blockquote class="pl-4 border-l-4 border-purple-500 italic text-gray-400">
                Built with love, designed for teams of every size — from solo developers to enterprise squads.
              </blockquote>
            </div>
          </div>
        </div>
    
        <!-- Mission Cards with enhanced styling -->
        <div class="mt-20 grid md:grid-cols-3 gap-6 text-center">
          <!-- Mission Card -->
          <div class="group reveal float-animation" style="--reveal-delay: 4; animation-delay: 0s;">
            <div class="p-8 rounded-xl gradient-bg border border-gray-700/50 shadow-lg relative overflow-hidden h-full transform transition-all duration-500 hover:translate-y-[-8px]">
              <!-- Background glow effect -->
              <div class="absolute inset-0 bg-sky-400/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
              
              <!-- Icon with enhanced styling -->
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-400/20 text-sky-400 mb-5 group-hover:scale-110 transform transition-transform duration-500">
                <i class="ri-rocket-2-fill text-3xl"></i>
              </div>
              
              <h4 class="font-semibold text-xl text-sky-300 mb-3">Our Mission</h4>
              <p class="text-gray-400">To empower developers with the smartest and simplest bug-tracking tools, helping teams build better software faster.</p>
              
              <!-- Decorative element -->
              <div class="w-12 h-1 bg-sky-400/30 mx-auto mt-5 group-hover:bg-sky-400/80 transition-colors duration-500"></div>
            </div>
          </div>
    
          <!-- Promise Card -->
          <div class="group reveal float-animation" style="--reveal-delay: 5; animation-delay: 2s;">
            <div class="p-8 rounded-xl gradient-bg border border-gray-700/50 shadow-lg relative overflow-hidden h-full transform transition-all duration-500 hover:translate-y-[-8px]">
              <!-- Background glow effect -->
              <div class="absolute inset-0 bg-green-400/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
              
              <!-- Icon with enhanced styling -->
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-400/20 text-green-400 mb-5 group-hover:scale-110 transform transition-transform duration-500">
                <i class="ri-shield-check-fill text-3xl"></i>
              </div>
              
              <h4 class="font-semibold text-xl text-green-300 mb-3">Our Promise</h4>
              <p class="text-gray-400">Privacy, performance, and support you can rely on — always there when you need it most.</p>
              
              <!-- Decorative element -->
              <div class="w-12 h-1 bg-green-400/30 mx-auto mt-5 group-hover:bg-green-400/80 transition-colors duration-500"></div>
            </div>
          </div>
    
          <!-- Team Card -->
          <div class="group reveal float-animation" style="--reveal-delay: 6; animation-delay: 4s;">
            <div class="p-8 rounded-xl gradient-bg border border-gray-700/50 shadow-lg relative overflow-hidden h-full transform transition-all duration-500 hover:translate-y-[-8px]">
              <!-- Background glow effect -->
              <div class="absolute inset-0 bg-yellow-400/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
              
              <!-- Icon with enhanced styling -->
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-400/20 text-yellow-400 mb-5 group-hover:scale-110 transform transition-transform duration-500">
                <i class="ri-group-fill text-3xl"></i>
              </div>
              
              <h4 class="font-semibold text-xl text-yellow-300 mb-3">Our Team</h4>
              <p class="text-gray-400">We're builders at heart, driven by curiosity and collaboration to create tools we'd want to use ourselves.</p>
              
              <!-- Decorative element -->
              <div class="w-12 h-1 bg-yellow-400/30 mx-auto mt-5 group-hover:bg-yellow-400/80 transition-colors duration-500"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ About us Section ends ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->






<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Q n A Section stars ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->






        
<section class="py-20 px-4 sm:px-8" id="faq-section">
  <div class="max-w-4xl mx-auto text-center relative z-10">
    <!-- Section Eyebrow -->
    <div class="text-center mb-4 reveal" style="--reveal-delay: 0;">
      <span class="inline-block px-4 py-1 rounded-full bg-gradient-to-r from-indigo-400/20 to-cyan-400/20 text-indigo-400 text-sm font-medium tracking-wider">
        SUPPORT
      </span>
    </div>
    
    <!-- Section Heading -->
    <div class="text-center mb-12 md:mb-20">
      <span class="inline-block text-sm font-semibold text-indigo-400 tracking-wider uppercase mb-2">Need Help?</span>
      <h2 class="font-bold mb-4 leading-tight sm:leading-snug whitespace-pre-wrap md:whitespace-nowrap bg-gradient-to-r from-indigo-400 via-blue-500 to-cyan-400 text-transparent bg-clip-text text-3xl sm:text-4xl md:text-5xl">
        Frequently Asked Questions
      </h2>
      <p class="max-w-2xl mx-auto mb-5 text-gray-400 text-sm sm:text-base">Find quick answers to the most common questions about CryBug's features, usage, and how we support developers like you.</p>
      <div class="h-1.5 w-32 sm:w-48 mx-auto bg-gradient-to-r from-indigo-400 via-blue-500 to-cyan-400 rounded-full shadow-lg shadow-cyan-400/20"></div>
    </div>
    
    <div class="crybug-faq-container space-y-3 sm:space-y-4 text-left">
      
      <!-- Question 1 -->
      <div class="crybug-faq-item border border-gray-700 rounded-xl p-3 sm:p-4 bg-[#1c1c1c] hover:border-emerald-700/50 transition-colors duration-300">
        <button class="crybug-faq-toggle w-full flex justify-between items-center text-left text-white font-medium text-base sm:text-lg focus:outline-none">
          <span class="pr-2">What is CryBug and what does it do?</span>
          <i class="ri-arrow-down-s-line text-xl sm:text-2xl transition-transform duration-300 flex-shrink-0"></i>
        </button>
        <div class="crybug-faq-content max-h-0 overflow-hidden transition-all duration-500 text-xs sm:text-sm text-gray-300 mt-2">
          <p class="pb-4">CryBug is a powerful bug tracking and task management tool designed to streamline your development workflow and keep your team aligned. Our platform offers:</p>
          <ul class="list-disc pl-4 sm:pl-5 space-y-1 sm:space-y-2 pb-2">
            <li>Real-time issue tracking and prioritization</li>
            <li>Customizable workflow templates</li>
            <li>Detailed analytics and reporting</li>
            <li>Seamless integration with your existing tech stack</li>
          </ul>
        </div>
      </div>

      <!-- Question 2 -->
      <div class="crybug-faq-item border border-gray-700 rounded-xl p-3 sm:p-4 bg-[#1c1c1c] hover:border-sky-700/50 transition-colors duration-300">
        <button class="crybug-faq-toggle w-full flex justify-between items-center text-left text-white font-medium text-base sm:text-lg focus:outline-none">
          <span class="pr-2">Can I use CryBug for free?</span>
          <i class="ri-arrow-down-s-line text-xl sm:text-2xl transition-transform duration-300 flex-shrink-0"></i>
        </button>
        <div class="crybug-faq-content max-h-0 overflow-hidden transition-all duration-500 text-xs sm:text-sm text-gray-300 mt-2">
          <p class="pb-4">Yes! We offer a free tier with essential features so teams can get started easily. Premium plans unlock advanced features for scaling projects.</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 pb-2">
            <div class="p-2 sm:p-3 rounded bg-gray-800/50">
              <p class="font-medium text-sky-400">Free Plan</p>
              <p class="text-xs">Up to 5 users, 3 projects, basic reporting</p>
            </div>
            <div class="p-2 sm:p-3 rounded bg-gray-800/50">
              <p class="font-medium text-violet-400">Premium Plan</p>
              <p class="text-xs">Unlimited users, projects, advanced analytics</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Question 3 -->
      <div class="crybug-faq-item border border-gray-700 rounded-xl p-3 sm:p-4 bg-[#1c1c1c] hover:border-violet-700/50 transition-colors duration-300">
        <button class="crybug-faq-toggle w-full flex justify-between items-center text-left text-white font-medium text-base sm:text-lg focus:outline-none">
          <span class="pr-2">Is CryBug suitable for solo developers?</span>
          <i class="ri-arrow-down-s-line text-xl sm:text-2xl transition-transform duration-300 flex-shrink-0"></i>
        </button>
        <div class="crybug-faq-content max-h-0 overflow-hidden transition-all duration-500 text-xs sm:text-sm text-gray-300 mt-2">
          <p class="pb-4">Absolutely! CryBug is lightweight enough for solo use and powerful enough for teams — it adapts to your workflow.</p>
          <div class="flex items-center p-2 sm:p-3 rounded bg-gray-800/50 mb-2 sm:mb-3">
            <div class="mr-2 sm:mr-3 flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p>Personalized dashboard for tracking your individual progress</p>
          </div>
          <div class="flex items-center p-2 sm:p-3 rounded bg-gray-800/50">
            <div class="mr-2 sm:mr-3 flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p>Seamlessly scales as your projects or team grow</p>
          </div>
        </div>
      </div>

      <!-- Question 4 -->
      <div class="crybug-faq-item border border-gray-700 rounded-xl p-3 sm:p-4 bg-[#1c1c1c] hover:border-emerald-700/50 transition-colors duration-300">
        <button class="crybug-faq-toggle w-full flex justify-between items-center text-left text-white font-medium text-base sm:text-lg focus:outline-none">
          <span class="pr-2">How does CryBug integrate with existing tools?</span>
          <i class="ri-arrow-down-s-line text-xl sm:text-2xl transition-transform duration-300 flex-shrink-0"></i>
        </button>
        <div class="crybug-faq-content max-h-0 overflow-hidden transition-all duration-500 text-xs sm:text-sm text-gray-300 mt-2">
          <p class="pb-4">CryBug offers seamless integration with popular development tools to fit into your existing workflow:</p>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 pb-2">
            <div class="p-1 sm:p-2 rounded bg-gray-800/50 text-center">
              <p class="text-xs font-medium">GitHub</p>
            </div>
            <div class="p-1 sm:p-2 rounded bg-gray-800/50 text-center">
              <p class="text-xs font-medium">Slack</p>
            </div>
            <div class="p-1 sm:p-2 rounded bg-gray-800/50 text-center">
              <p class="text-xs font-medium">VS Code</p>
            </div>
            <div class="p-1 sm:p-2 rounded bg-gray-800/50 text-center">
              <p class="text-xs font-medium">Figma</p>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-2">Our API allows for custom integrations with virtually any tool in your stack.</p>
        </div>
      </div>

      <!-- Question 5 -->
      <div class="crybug-faq-item border border-gray-700 rounded-xl p-3 sm:p-4 bg-[#1c1c1c] hover:border-sky-700/50 transition-colors duration-300">
        <button class="crybug-faq-toggle w-full flex justify-between items-center text-left text-white font-medium text-base sm:text-lg focus:outline-none">
          <span class="pr-2">What makes CryBug different from other issue trackers?</span>
          <i class="ri-arrow-down-s-line text-xl sm:text-2xl transition-transform duration-300 flex-shrink-0"></i>
        </button>
        <div class="crybug-faq-content max-h-0 overflow-hidden transition-all duration-500 text-xs sm:text-sm text-gray-300 mt-2">
          <p class="pb-4">CryBug stands out with its intelligent prioritization algorithms, intuitive UI, and developer-first approach. We focus on reducing cognitive load while providing powerful insights.</p>
          <div class="p-2 sm:p-3 rounded bg-gradient-to-r from-emerald-900/20 to-violet-900/20 border border-gray-700">
            <p class="font-medium text-xs sm:text-sm mb-1">💡 Unique to CryBug</p>
            <p class="text-xs">Our AI-powered issue suggestions help identify potential bugs before they're even reported, saving you valuable development time.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>



<!-- ================================================================================================================================================================================================================================ -->
<!-- ================================================================ Q n A Section ends ================================================================ -->
<!-- ================================================================================================================================================================================================================================ -->



    </main>
    
<!-----------------------------------------------------------Main ends------------------------------------------------------------------------------------ -->

<footer class="bg-gradient-to-b from-black to-gray-900 text-gray-300 pt-20 pb-10 relative">
  <!-- Background Subtle Pattern -->
  <div class="absolute inset-0 overflow-hidden opacity-5">
    <div class="absolute -inset-20 bg-[radial-gradient(#ffffff33_1px,transparent_1px)] bg-[length:20px_20px]"></div>
  </div>
  
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <!-- Top Section with Logo -->
    <div class="flex flex-col md:flex-row md:justify-between items-start gap-16">
      
      <!-- Brand with Logo -->
      <div class="text-center md:text-left max-w-sm">
        <!-- Logo -->
        <div class="mb-6 flex items-center justify-center md:justify-start">
          <div class="w-12 h-12 mr-3 bg-gradient-to-br from-teal-400 via-indigo-500 to-fuchsia-600 rounded-lg p-0.5">
            <div class="w-full h-full bg-black rounded-md flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="url(#logoGradient)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">
                <defs>
                  <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#2dd4bf" />
                    <stop offset="50%" stop-color="#6366f1" />
                    <stop offset="100%" stop-color="#d946ef" />
                  </linearGradient>
                </defs>
                <path d="M12 3a9 9 0 0 0-8.5 12l-1.8 1.8a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0L6.5 18A9 9 0 1 0 12 3z" />
                <circle cx="14" cy="10" r="1.5" fill="#d946ef" />
                <circle cx="10" cy="10" r="1.5" fill="#6366f1" />
                <path d="M9.5 15c.828 0 1.5-.672 1.5-1.5s.672-1.5 1.5-1.5 1.5.672 1.5 1.5.672 1.5 1.5 1.5" stroke-linecap="round" />
              </svg>
            </div>
          </div>

          <div class="inline-block relative"> 
          <h3 class="text-4xl font-bold leading-tight sm:leading-snug bg-gradient-to-r from-teal-400 via-indigo-500 to-fuchsia-600 text-transparent bg-clip-text">
            CryBug
          </h3>
          <div class="absolute -bottom-2 left-0 right-0 h-px bg-gradient-to-r from-transparent via-teal-500 to-transparent"></div>
          </div>
        </div>
        <p class="text-base text-gray-400 mb-6">
          CryBug simplifies debugging with clean tools, clear diagnostics, and a dev-first experience.
        </p>
        
        <!-- Newsletter -->
        <div class="hidden md:block">
          <p class="text-sm uppercase tracking-widest text-gray-500 font-medium mb-3">Stay updated</p>
          <div class="flex mt-2">
            <input type="email" placeholder="Your email" class="bg-gray-800 border-0 border-r-0 rounded-l-md px-4 py-3 text-base w-full focus:outline-none focus:ring-1 focus:ring-teal-500 text-gray-300">
            <button class="bg-gradient-to-r from-teal-500 to-indigo-600 text-white rounded-r-md px-4 text-base font-medium hover:opacity-90 transition-opacity">
              Subscribe
            </button>
          </div>
        </div>
      </div>

      <!-- Navigation Links - More Spread Out -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-16 gap-y-10 flex-grow max-w-3xl w-full">
        <!-- Product -->
        <div class="text-center sm:text-left">
          <h4 class="text-xl text-white font-semibold mb-6 relative inline-block">
            Product
            <span class="absolute -left-3 top-1/2 w-1.5 h-1.5 rounded-full bg-teal-400"></span>
          </h4>
          <ul class="space-y-3">
            <li>
              <a href="#" class="text-base hover:text-teal-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group">
                <span class="w-0 group-hover:w-2 h-px bg-teal-400 transition-all duration-300"></span>
                Features
              </a>
            </li>
            <li>
              <a href="#" class="text-base hover:text-teal-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group">
                <span class="w-0 group-hover:w-2 h-px bg-teal-400 transition-all duration-300"></span>
                Integrations
              </a>
            </li>
            <li>
              <a href="#" class="text-base hover:text-teal-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group">
                <span class="w-0 group-hover:w-2 h-px bg-teal-400 transition-all duration-300"></span>
                Pricing
              </a>
            </li>
            <li>
              <a href="#" class="text-base hover:text-teal-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group">
                <span class="w-0 group-hover:w-2 h-px bg-teal-400 transition-all duration-300"></span>
                Roadmap
              </a>
            </li>
          </ul>
        </div>
        
        <!-- Company -->
        <div class="text-center sm:text-left">
          <h4 class="text-xl text-white font-semibold mb-6 relative inline-block">
            Company
            <span class="absolute -left-3 top-1/2 w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
          </h4>
          <ul class="space-y-3">
            <li>
              <a href="#" class="text-base hover:text-indigo-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group">
                <span class="w-0 group-hover:w-2 h-px bg-indigo-400 transition-all duration-300"></span>
                About Us
              </a>
            </li>
            <li>
              <a href="#" class="text-base hover:text-indigo-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group">
                <span class="w-0 group-hover:w-2 h-px bg-indigo-400 transition-all duration-300"></span>
                Careers
              </a>
            </li>
            <li>
              <a href="#" class="text-base hover:text-indigo-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group">
                <span class="w-0 group-hover:w-2 h-px bg-indigo-400 transition-all duration-300"></span>
                Blog
              </a>
            </li>
            <li>
              <a href="#" class="text-base hover:text-indigo-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group">
                <span class="w-0 group-hover:w-2 h-px bg-indigo-400 transition-all duration-300"></span>
                Partners
              </a>
            </li>
          </ul>
        </div>
        
        <!-- Support - Enhanced with hover effects -->
        <div class="text-center sm:text-left">
          <h4 class="text-xl text-white font-semibold mb-6 relative inline-block">
            Support
            <span class="absolute -left-3 top-1/2 w-1.5 h-1.5 rounded-full bg-fuchsia-500"></span>
          </h4>
          <ul class="space-y-3">
            <li>
              <a href="#" class="crybug-support-link text-base hover:text-fuchsia-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group relative">
                <span class="w-0 group-hover:w-2 h-px bg-fuchsia-400 transition-all duration-300"></span>
                <span class="relative z-10">Help Center</span>
                <span class="absolute inset-0 bg-fuchsia-500/10 scale-0 group-hover:scale-100 rounded transition-transform duration-300"></span>
              </a>
            </li>
            <li>
              <a href="#" class="crybug-support-link text-base hover:text-fuchsia-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group relative">
                <span class="w-0 group-hover:w-2 h-px bg-fuchsia-400 transition-all duration-300"></span>
                <span class="relative z-10">FAQs</span>
                <span class="absolute inset-0 bg-fuchsia-500/10 scale-0 group-hover:scale-100 rounded transition-transform duration-300"></span>
              </a>
            </li>
            <li>
              <a href="#" class="crybug-support-link text-base hover:text-fuchsia-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group relative">
                <span class="w-0 group-hover:w-2 h-px bg-fuchsia-400 transition-all duration-300"></span>
                <span class="relative z-10">Contact</span>
                <span class="absolute inset-0 bg-fuchsia-500/10 scale-0 group-hover:scale-100 rounded transition-transform duration-300"></span>
              </a>
            </li>
            <li>
              <a href="#" class="crybug-support-link text-base hover:text-fuchsia-400 transition-colors duration-300 flex items-center justify-center sm:justify-start gap-1.5 group relative">
                <span class="w-0 group-hover:w-2 h-px bg-fuchsia-400 transition-all duration-300"></span>
                <span class="relative z-10">Status</span>
                <span class="absolute inset-0 bg-fuchsia-500/10 scale-0 group-hover:scale-100 rounded transition-transform duration-300"></span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Newsletter (Mobile) -->
    <div class="md:hidden mt-10 text-center">
      <p class="text-sm uppercase tracking-widest text-gray-500 font-medium mb-3">Stay updated</p>
      <div class="flex mt-2 mx-auto max-w-xs">
        <input type="email" placeholder="Your email" class="bg-gray-800 border-0 border-r-0 rounded-l-md px-4 py-3 text-base w-full focus:outline-none focus:ring-1 focus:ring-teal-500 text-gray-300">
        <button class="bg-gradient-to-r from-teal-500 to-indigo-600 text-white rounded-r-md px-4 text-base font-medium hover:opacity-90 transition-opacity">
          Subscribe
        </button>
      </div>
    </div>

    <!-- Divider -->
    <div class="my-12">
      <div class="h-px bg-gradient-to-r from-transparent via-gray-700 to-transparent"></div>
    </div>

    <!-- Bottom Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-8">
      <div class="flex flex-col sm:flex-row items-center gap-4 text-sm text-gray-500">
        <p>&copy; 2025 CryBug. All rights reserved.</p>
        <div class="hidden sm:flex gap-1 items-center">
          <span class="w-1 h-1 rounded-full bg-gray-700"></span>
        </div>
        <a href="#" class="hover:text-teal-400 transition-colors">Privacy Policy</a>
        <div class="hidden sm:flex gap-1 items-center">
          <span class="w-1 h-1 rounded-full bg-gray-700"></span>
        </div>
        <a href="#" class="hover:text-teal-400 transition-colors">Terms of Service</a>
      </div>

      <!-- Social Icons -->
      <div class="flex gap-6">
        <!-- Twitter -->
        <a href="#" class="group relative" aria-label="Twitter">
          <div class="absolute inset-0 rounded-full bg-gradient-to-br from-teal-400 to-indigo-600 opacity-0 group-hover:opacity-100 blur-lg transition-opacity duration-300"></div>
          <div class="w-12 h-12 flex items-center justify-center bg-gray-800 hover:bg-gray-700 rounded-full text-gray-400 group-hover:text-white relative z-10 transition-colors duration-300">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M22.46 6c-.77.35-1.6.59-2.46.69a4.26 4.26 0 001.88-2.35 8.6 8.6 0 01-2.7 1.03 4.24 4.24 0 00-7.21 3.86A12.01 12.01 0 013 5.15a4.24 4.24 0 001.31 5.66 4.22 4.22 0 01-1.92-.53v.05a4.24 4.24 0 003.4 4.16 4.24 4.24 0 01-1.91.07 4.25 4.25 0 003.97 2.95A8.51 8.51 0 013 19.54 12.01 12.01 0 009.29 21c7.55 0 11.68-6.26 11.68-11.69 0-.18-.01-.36-.02-.54A8.35 8.35 0 0022.46 6z"/>
            </svg>
          </div>
        </a>
        
        <!-- GitHub -->
        <a href="https://github.com/scarlet-sypher" target="_blank" class="group relative" aria-label="GitHub">
          <div class="absolute inset-0 rounded-full bg-gradient-to-br from-indigo-500 to-fuchsia-600 opacity-0 group-hover:opacity-100 blur-lg transition-opacity duration-300"></div>
          <div class="w-12 h-12 flex items-center justify-center bg-gray-800 hover:bg-gray-700 rounded-full text-gray-400 group-hover:text-white relative z-10 transition-colors duration-300">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 0a12 12 0 00-3.8 23.4c.6.1.8-.3.8-.6v-2.1c-3.4.7-4.1-1.6-4.1-1.6-.6-1.5-1.5-1.9-1.5-1.9-1.2-.8.1-.8.1-.8 1.3.1 2 1.3 2 1.3 1.2 2 3 1.4 3.8 1.1.1-.9.5-1.4.9-1.8-2.7-.3-5.5-1.3-5.5-5.8 0-1.3.5-2.3 1.2-3.1-.1-.3-.5-1.5.1-3.1 0 0 1-.3 3.3 1.2A11.6 11.6 0 0112 5.8a11.6 11.6 0 012.9.4c2.3-1.5 3.3-1.2 3.3-1.2.6 1.6.2 2.8.1 3.1.8.8 1.2 1.8 1.2 3.1 0 4.5-2.8 5.5-5.5 5.8.5.4.9 1.2.9 2.4v3.5c0 .3.2.7.8.6A12 12 0 0012 0z"/>
            </svg>
          </div>
        </a>
        
        <!-- LinkedIn -->
        <a href="https://www.linkedin.com/in/ayush-jha-a2809b29a/" target="_blank" class="group relative" aria-label="LinkedIn">
          <div class="absolute inset-0 rounded-full bg-gradient-to-br from-fuchsia-500 to-teal-400 opacity-0 group-hover:opacity-100 blur-lg transition-opacity duration-300"></div>
          <div class="w-12 h-12 flex items-center justify-center bg-gray-800 hover:bg-gray-700 rounded-full text-gray-400 group-hover:text-white relative z-10 transition-colors duration-300">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 0h-14a5 5 0 00-5 5v14a5 5 0 005 5h14a5 5 0 005-5v-14a5 5 0 00-5-5zm-11 19h-3v-10h3zm-1.5-11.2a1.75 1.75 0 110-3.5 1.75 1.75 0 010 3.5zm13.5 11.2h-3v-5.5c0-1.3-.5-2.2-1.7-2.2-1 0-1.5.7-1.8 1.4-.1.2-.1.5-.1.8v5.5h-3s.1-9 0-10h3v1.4a3 3 0 012.7-1.5c2 0 3.5 1.3 3.5 4v6.1z"/>
            </svg>
          </div>
        </a>
        
        <!-- Discord -->
        <a href="#" class="group relative" aria-label="Discord">
          <div class="absolute inset-0 rounded-full bg-gradient-to-br from-indigo-400 to-fuchsia-500 opacity-0 group-hover:opacity-100 blur-lg transition-opacity duration-300"></div>
          <div class="w-12 h-12 flex items-center justify-center bg-gray-800 hover:bg-gray-700 rounded-full text-gray-400 group-hover:text-white relative z-10 transition-colors duration-300">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3847-.4058-.874-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.4189-2.1568 2.4189Z"/>
            </svg>
          </div>
        </a>
      </div>
    </div>
    
    <!-- Corner Gradient -->
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-indigo-600/10 to-transparent rounded-full blur-3xl"></div>
    <div class="absolute top-0 left-0 w-96 h-96 bg-gradient-to-br from-teal-600/10 to-transparent rounded-full blur-3xl"></div>
  </div>
</footer>



</body>
</html>


<?php

include "connection.php" ;

?>

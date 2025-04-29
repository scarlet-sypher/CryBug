<?php

  session_start() ;

  include "connection.php" ;

  $mag_name =  $_SESSION['mag_name'] ;
  $mag_profile =  $_SESSION['mag_profile']  ;
  $mag_role =  $_SESSION['mag_role']  ;
  $mag_email =  $_SESSION['mag_email']  ;
  $mag_phone =  $_SESSION['mag_phone']  ;
  $mag_id = $_SESSION['mag_id'] ;


  $cmp_id =  $_SESSION['cmp_id']  ;
  $cmp_name =  $_SESSION['cmp_name'] ;
  $cmp_descp =  $_SESSION['cmp_descp']  ;
  $cmp_mail =  $_SESSION['cmp_mail']  ;
  $cmp_phone =  $_SESSION['cmp_phone']  ;
  $cmp_address =  $_SESSION['cmp_address']  ;



  $emp_id = $_SESSION['emp_id'] ;
  $emp_profile = $_SESSION['emp_profile'] ;

  // Process feedback form submission
  if(isset($_POST['submit_feedback'])) {
    $ef_type = $_POST['ef_type'];
    $ef_issue = $_POST['ef_issue'];
    $ef_priority = $_POST['ef_priority'];
    $ef_emp_id = $emp_id; // Automatically use logged in employee ID
    $ef_mag_id = $mag_id; // Automatically use the manager ID
    $ef_cmp_id = $cmp_id; // Set company ID
    
    // Insert feedback into database with EF_resolved set to 0
    $insert_query = "INSERT INTO emp_feedback (EF_emp_id, EF_mag_id, EF_type, EF_issue, EF_priority, EF_resolved) 
                     VALUES ('$ef_emp_id', '$ef_mag_id', '$ef_type', '$ef_issue', '$ef_priority', 0)";
    
    $result = mysqli_query($con, $insert_query);
    
    if($result) {
      $success_message = "Feedback submitted successfully!";
    } else {
      $error_message = "Error submitting feedback: " . mysqli_error($con);
    }
  }

  // Process feedback deletion
  if(isset($_GET['delete_feedback'])) {
    $feedback_id = $_GET['delete_feedback'];
    
    // Only delete if feedback is not resolved (EF_resolved = 0)
    $delete_query = "DELETE FROM emp_feedback WHERE EF_id = '$feedback_id' AND EF_resolved = 0";
    
    $delete_result = mysqli_query($con, $delete_query);
    
    if($delete_result) {
      $success_message = "Feedback deleted successfully!";
    } else {
      $error_message = "Error deleting feedback: " . mysqli_error($con);
    }
  }

  // Fetch previous feedback for this employee and manager
  $previous_feedback_query = "SELECT * FROM emp_feedback WHERE EF_emp_id = '$emp_id' AND EF_mag_id = '$mag_id' ORDER BY EF_id DESC";
  $previous_feedback_result = mysqli_query($con, $previous_feedback_query);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CryBug | Help Center</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../src/output.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="dashboard.js" defer></script>
  <style>
   
    .faq-item.active .faq-answer {
      max-height: 500px;
      opacity: 1;
    }
    .faq-item.active .faq-toggle i {
      transform: rotate(180deg);
    }
    .faq-answer {
      max-height: 0;
      opacity: 0;
      overflow: hidden;
      transition: all 0.3s ease;
    }
  </style>
</head>
<body class="bg-gradient-custom text-white min-h-screen font-sans bg-black antialiased">

  <div class="overlay" id="sidebarOverlay"></div>
  
  <div class="flex flex-col md:flex-row">
    
    <!-- Sidebar -->
    <aside class="sidebar w-64 bg-gray-900 p-4 md:fixed md:h-screen transition-all">
      <div class="flex items-center justify-between mb-8 p-2">
        <div class="flex items-center">
          <i class="fas fa-bug text-green-500 text-2xl mr-2"></i>
          <h1 class="text-xl font-bold text-green-500">CryBug</h1>
        </div>
        <button class="close-sidebar md:hidden text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <nav>
        <ul class="space-y-2">
          <li>
            <a href="../index.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Home">
              <i class="fas fa-home mr-3"></i>
              <span>Home</span>
            </a>
          </li>
          <li>
            <a href="dashboard.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Dashboard">
              <i class="fas fa-tachometer-alt mr-3"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="project.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Projects">
              <i class="fas fa-project-diagram mr-3"></i>
              <span>Projects</span>
            </a>
          </li>
          <li>
            <a href="bug.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Bugs">
              <i class="fas fa-bug mr-3"></i>
              <span>Bugs</span>
            </a>
          </li>
          <li>
            <a href="update.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Reports">
              <i class="fas fa-tasks mr-3"></i>
              <span>Update Progress</span>
            </a>
          </li>
          <li>
            <a href="setting.php" class="sidebar-link flex items-center p-3 rounded text-gray-300 hover:text-white" data-title="Settings">
              <i class="fas fa-cog mr-3"></i>
              <span>Settings</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <div class="mt-auto pt-8">
        <div class="border-t border-gray-700 pt-4">
          <a href="help.php" class="sidebar-link active flex items-center p-3 rounded text-green-500 hover:text-white" data-title="Help Center">
            <i class="fas fa-question-circle mr-3"></i>
            <span>Help Center</span>
          </a>
          <a href="logout.php"><button class="mt-4 w-full bg-green-600 hover:bg-green-700 p-2 rounded flex items-center justify-center">
            <i class="fas fa-sign-out-alt mr-2"></i>
            <span>Logout</span>
          </button></a>
        </div>
      </div>
    </aside>
    
    <!-- Main Content -->
    <main class="md:ml-64 lg:ml-64 flex-1 p-4 md:p-6 transition-all">

      <!-- Mobile Menu Toggle -->
      <button class="menu-toggle md:hidden mb-4 bg-gray-800 p-2 rounded">
        <i class="fas fa-bars"></i>
      </button>
      
      <!-- Top Bar -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold">Help Center</h1>
          <p class="text-gray-400" id="currentDateTime">April 18, 2025</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
          
          <div class="relative">
            <button id="profileDropdownBtn" class="flex items-center">
              <div class="h-10 w-10 rounded-full border-2 border-green-500 bg-gray-700 flex items-center justify-center">
              <?php if(!empty($emp_profile) && file_exists($emp_profile)): ?>
              <img src="<?php echo htmlspecialchars($emp_profile) ; ?>" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
            <?php else : ?>
              <img src="../images/Profile/guest.png" alt="Profile" class="h-10 w-10 rounded-full border-2 border-green-500" />
            <?php endif ; ?>

              </div>
              <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Help Center Content -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Company Details -->
        <div class="lg:col-span-1">
          <div class="bg-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-xl font-bold flex items-center mb-4">
              <i class="fas fa-building text-green-500 mr-2"></i>
              Company Details
            </h2>
            <div class="space-y-4">
              <div>
                <h3 class="text-green-400 font-medium"><?php echo ucfirst(htmlspecialchars($cmp_name)) ;?></h3>
                <p class="text-gray-300 text-sm"><?php echo ucfirst(htmlspecialchars($cmp_descp)) ;?></p>
              </div>
              <div>
                <p class="text-sm text-gray-400">Address</p>
                <p class="text-gray-300"><?php echo ucfirst(htmlspecialchars($cmp_address)) ;?></p>
                <p class="text-sm mt-2 text-gray-400">Comapany ID</p>
                <p class="text-gray-300"><?php echo ucfirst(htmlspecialchars($cmp_id)) ;?></p>
              </div>
              <div>
                <p class="text-sm text-gray-400">Contact</p>
                <p class="text-gray-300">
                  <i class="fas fa-phone-alt text-green-500 mr-2"></i>
                  <?php echo ucfirst(htmlspecialchars($cmp_phone)) ;?>
                </p>
                <p class="text-gray-300">
                  <i class="fas fa-envelope text-green-500 mr-2"></i>
                  <?php echo ucfirst(htmlspecialchars($cmp_mail)) ;?>
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-400">Hours of Operation</p>
                <p class="text-gray-300">Monday - Friday: 9AM - 6PM PT</p>
                <p class="text-gray-300">Weekend Support: 10AM - 4PM PT</p>
              </div>
            </div>
          </div>
          
          <!-- Support Manager -->
          <div class="bg-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-bold flex items-center mb-4">
              <i class="fas fa-user-headset text-green-500 mr-2"></i>
              Support Manager
            </h2>
            <div class="flex flex-col items-center text-center mb-4">
            <div class="w-20 h-20 rounded-full bg-gray-700 flex items-center justify-center mb-2 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <img 
                  src="<?php echo htmlspecialchars($mag_profile); ?>" 
                  alt="Profile" 
                  class="w-full h-full object-cover rounded-full"
                >
              </div>
              <h3 class="text-lg font-medium"><?php echo ucfirst(htmlspecialchars($mag_name)) ;?></h3>
              <p class="text-green-400 text-sm"><?php echo ucfirst(htmlspecialchars($mag_role)) ;?></p>
            </div>
            <div class="space-y-3">
              <p class="text-gray-300 flex items-center">
                <i class="fas fa-envelope text-green-500 mr-2 w-5"></i>
                <?php echo htmlspecialchars($mag_email) ;?>
              </p>
              <p class="text-gray-300 flex items-center">
                <i class="fas fa-phone-alt text-green-500 mr-2 w-5"></i>
                <?php echo ucfirst(htmlspecialchars($mag_phone)) ;?>
              </p>
              <p class="text-gray-300 flex items-center">
                <i class="fab fa-slack text-green-500 mr-2 w-5"></i>
                <?php echo htmlspecialchars($mag_id) ;?>
              </p>
              <div class="pt-4">
                <button class="w-full bg-green-600 hover:bg-green-700 rounded py-2 flex items-center justify-center">
                  <i class="fas fa-comment-alt mr-2"></i>
                  Schedule a Call
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- FAQs and Feedback Form -->
        <div class="lg:col-span-2">

          <!-- FAQs Section -->
          <div class="bg-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-xl font-bold flex items-center mb-4">
              <i class="fas fa-question-circle text-green-500 mr-2"></i>
              Frequently Asked Questions
            </h2>
            
            <div class="space-y-4">
              <!-- FAQ Item 1 -->
              <div class="faq-item border border-gray-700 rounded-lg">
                <div class="flex justify-between items-center p-4 cursor-pointer faq-header">
                  <h3 class="text-lg font-medium">How do I create a new project?</h3>
                  <button class="faq-toggle text-gray-400">
                    <i class="fas fa-chevron-down transition-transform duration-300"></i>
                  </button>
                </div>
                <div class="faq-answer bg-gray-700 p-4 rounded-b-lg">
                  <p class="text-gray-300">
                    To create a new project, navigate to the Projects page using the sidebar menu and click on the "Create New Project" button at the top right corner. Fill in the required information such as project name, description, and team members, then click "Create Project" to finalize.
                  </p>
                </div>
              </div>
              
              <!-- FAQ Item 2 -->
              <div class="faq-item border border-gray-700 rounded-lg">
                <div class="flex justify-between items-center p-4 cursor-pointer faq-header">
                  <h3 class="text-lg font-medium">How do I assign bugs to team members?</h3>
                  <button class="faq-toggle text-gray-400">
                    <i class="fas fa-chevron-down transition-transform duration-300"></i>
                  </button>
                </div>
                <div class="faq-answer bg-gray-700 p-4 rounded-b-lg">
                  <p class="text-gray-300">
                    From the Bugs page, click on the bug you want to assign. In the bug details panel, look for the "Assignee" dropdown menu. Select the team member you want to assign the bug to and save your changes. The team member will receive a notification about the new assignment.
                  </p>
                </div>
              </div>
              
              <!-- FAQ Item 3 -->
              <div class="faq-item border border-gray-700 rounded-lg">
                <div class="flex justify-between items-center p-4 cursor-pointer faq-header">
                  <h3 class="text-lg font-medium">How do I change my account password?</h3>
                  <button class="faq-toggle text-gray-400">
                    <i class="fas fa-chevron-down transition-transform duration-300"></i>
                  </button>
                </div>
                <div class="faq-answer bg-gray-700 p-4 rounded-b-lg">
                  <p class="text-gray-300">
                    To change your password, go to the Settings page using the sidebar menu. Click on the "Account Security" tab. Under the "Change Password" section, enter your current password, then your new password, and confirm it. Click "Update Password" to save your changes.
                  </p>
                </div>
              </div>
              
              <!-- FAQ Item 4 -->
              <div class="faq-item border border-gray-700 rounded-lg">
                <div class="flex justify-between items-center p-4 cursor-pointer faq-header">
                  <h3 class="text-lg font-medium">Can I export bug reports?</h3>
                  <button class="faq-toggle text-gray-400">
                    <i class="fas fa-chevron-down transition-transform duration-300"></i>
                  </button>
                </div>
                <div class="faq-answer bg-gray-700 p-4 rounded-b-lg">
                  <p class="text-gray-300">
                    Yes, you can export bug reports in various formats including CSV, PDF, and Excel. From the Bugs page, use the filter options to select the bugs you want to include in your report, then click the "Export" button at the top right of the table. Choose your preferred format and download the report.
                  </p>
                </div>
              </div>
              
              <!-- FAQ Item 5 -->
              <div class="faq-item border border-gray-700 rounded-lg">
                <div class="flex justify-between items-center p-4 cursor-pointer faq-header">
                  <h3 class="text-lg font-medium">How do I add team members to my project?</h3>
                  <button class="faq-toggle text-gray-400">
                    <i class="fas fa-chevron-down transition-transform duration-300"></i>
                  </button>
                </div>
                <div class="faq-answer bg-gray-700 p-4 rounded-b-lg">
                  <p class="text-gray-300">
                    Navigate to your project's details page by clicking on the project name from the Projects page. Go to the "Team" tab, then click "Add Member". You can search for existing users by email or name, or invite new users by entering their email address. Set their role and permissions, then click "Add" to send the invitation.
                  </p>
                </div>
              </div>
            </div>
            
            <div class="mt-6 text-center">
              <a href="#" class="text-green-500 hover:text-green-400 inline-flex items-center">
                See all FAQs
                <i class="fas fa-arrow-right ml-2"></i>
              </a>
            </div>
          </div>
          
          <!-- Feedback Form Section -->
          <div class="bg-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-xl font-bold flex items-center mb-4">
              <i class="fas fa-comment-alt text-green-500 mr-2"></i>
              Manager Feedback Form
            </h2>
            
            <?php if(isset($success_message)): ?>
              <div class="bg-green-600 text-white p-3 rounded mb-4">
                <?php echo $success_message; ?>
              </div>
            <?php endif; ?>
            
            <?php if(isset($error_message)): ?>
              <div class="bg-red-600 text-white p-3 rounded mb-4">
                <?php echo $error_message; ?>
              </div>
            <?php endif; ?>
            
            <form action="" method="POST" class="space-y-4">
              <div>
                <label for="ef_type" class="block text-gray-300 mb-1">Feedback Type</label>
                <select id="ef_type" name="ef_type" class="w-full p-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:border-green-500">
                  <option value="Suggestion">Suggestion</option>
                  <option value="Complaint">Complaint</option>
                  <option value="Query">Query</option>
                  <option value="Appreciation">Appreciation</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              
              <div>
                <label for="ef_issue" class="block text-gray-300 mb-1">Issue Description</label>
                <textarea id="ef_issue" name="ef_issue" rows="4" class="w-full p-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:border-green-500" placeholder="Describe your feedback in detail..." required></textarea>
              </div>
              
              <div>
                <label for="ef_priority" class="block text-gray-300 mb-1">Priority</label>
                <select id="ef_priority" name="ef_priority" class="w-full p-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:border-green-500">
                  <option value="Low">Low</option>
                  <option value="Medium">Medium</option>
                  <option value="High">High</option>
                  <option value="Critical">Critical</option>
                </select>
              </div>
              
              <div class="flex items-center space-x-2 text-sm text-gray-400">
                <i class="fas fa-info-circle"></i>
                <p>Your feedback will be sent to your manager and company admin.</p>
              </div>
              
              <div>
                <button type="submit" name="submit_feedback" class="w-full bg-green-600 hover:bg-green-700 text-white p-2 rounded flex items-center justify-center">
                  <i class="fas fa-paper-plane mr-2"></i>
                  Submit Feedback
                </button>
              </div>
            </form>
          </div>
          
          <!-- Previous Feedback Section -->
          <div class="bg-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-bold flex items-center mb-4">
              <i class="fas fa-history text-green-500 mr-2"></i>
              Previous Feedback
            </h2>
            
            <?php if(mysqli_num_rows($previous_feedback_result) > 0): ?>
              <div class="overflow-x-auto">
                <table class="min-w-full bg-gray-900 rounded-lg overflow-hidden">
                  <thead class="bg-gray-700">
                    <tr>
                      <th class="py-3 px-4 text-left">Type</th>
                      <th class="py-3 px-4 text-left">Issue</th>
                      <th class="py-3 px-4 text-left">Priority</th>
                      <th class="py-3 px-4 text-left">Status</th>
                      <th class="py-3 px-4 text-left">Remarks</th>
                      <th class="py-3 px-4 text-left">Action</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-700">
                    <?php while($feedback = mysqli_fetch_assoc($previous_feedback_result)): ?>
                      <tr class="hover:bg-gray-800">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($feedback['EF_type']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($feedback['EF_issue']); ?></td>
                        <td class="py-3 px-4">
                          <span class="px-2 py-1 rounded text-xs
                            <?php 
                              switch($feedback['EF_priority']) {
                                case 'Low': echo 'bg-blue-900 text-blue-300'; break;
                                case 'Medium': echo 'bg-yellow-900 text-yellow-300'; break;
                                case 'High': echo 'bg-orange-900 text-orange-300'; break;
                                case 'Critical': echo 'bg-red-900 text-red-300'; break;
                                default: echo 'bg-gray-700 text-gray-300';
                              }
                            ?>">
                            <?php echo htmlspecialchars($feedback['EF_priority']); ?>
                          </span>
                        </td>
                        <td class="py-3 px-4">
                          <span class="px-2 py-1 rounded text-xs <?php echo $feedback['EF_resolved'] == 0 ? 'bg-yellow-900 text-yellow-300' : 'bg-green-900 text-green-300'; ?>">
                            <?php echo $feedback['EF_resolved'] == 0 ? 'Pending' : 'Resolved'; ?>
                          </span>
                        </td>
                        <td class="py-3 px-4">
                          <?php echo !empty($feedback['EF_remark']) ? htmlspecialchars($feedback['EF_remark']) : '<span class="text-gray-500">No remarks yet</span>'; ?>
                        </td>
                        <td class="py-3 px-4">
                          <?php if($feedback['EF_resolved'] == 0): ?>
                            <a href="?delete_feedback=<?php echo $feedback['EF_id']; ?>" class="text-red-400 hover:text-red-300" onclick="return confirm('Are you sure you want to delete this feedback?');">
                              <i class="fas fa-trash-alt"></i> Delete
                            </a>
                          <?php else: ?>
                            <span class="text-gray-500">No actions available</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="bg-gray-700 p-4 rounded text-center">
                <p class="text-gray-400">You haven't submitted any feedback yet.</p>
              </div>
            <?php endif; ?>
          </div>
          
        </div>
      </div>
      
    </main>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Update current date and time
      
      
      // FAQ toggles
      const faqHeaders = document.querySelectorAll('.faq-header');
      if (faqHeaders.length) {
        faqHeaders.forEach(header => {
          header.addEventListener('click', function() {
            const faqItem = this.parentElement;
            faqItem.classList.toggle('active');
          });
        });
      }
    });
  </script>
</body>
</html>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeButtons = document.querySelectorAll('.close-sidebar');
    
    // Toggle sidebar on menu button click
    menuToggle.addEventListener('click', function() {
      sidebar.classList.toggle('active');
      overlay.classList.toggle('active');
    });
    
    // Close sidebar when clicking overlay or close button
    overlay.addEventListener('click', function() {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
    });
    
    closeButtons.forEach(button => {
      button.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
      });
    });
    
    // Dropdown toggle functions
    const dropdownToggles = {
      'notificationBtn': 'notificationDropdown',
      'messageBtn': 'messageDropdown',
      'profileDropdownBtn': 'profileDropdown'
    };
    
    Object.entries(dropdownToggles).forEach(([toggleId, dropdownId]) => {
      const toggle = document.getElementById(toggleId);
      const dropdown = document.getElementById(dropdownId);
      
      if(toggle && dropdown) {
          toggle.addEventListener('click', function(e) {
          e.stopPropagation();
          dropdown.classList.toggle('hidden');
          
          // Close other dropdowns
          Object.values(dropdownToggles)
            .filter(id => id !== dropdownId)
            .forEach(id => document.getElementById(id).classList.add('hidden'));
        });
      }
    });
    
    // Close dropdowns when clicking elsewhere
    document.addEventListener('click', function() {
      Object.values(dropdownToggles).forEach(id => {
        const dropdown = document.getElementById(id);
        if(dropdown) dropdown.classList.add('hidden');
      });
    });
    
    // Modals
    const modals = {
      'addProjectBtn': 'addProjectModal',
      'generateReportBtn': 'generateReportModal'
    };
    
    // Open modal functions
    Object.entries(modals).forEach(([btnId, modalId]) => {
      const btn = document.getElementById(btnId);
      const modal = document.getElementById(modalId);
      
      if(btn && modal) {
        btn.addEventListener('click', function() {
          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        });
      }
    });
    
    // Close modal on X button click
    document.querySelectorAll('.close-modal').forEach(btn => {
      btn.addEventListener('click', function() {
        const modal = btn.closest('.modal');
        if(modal) {
          modal.style.display = 'none';
          document.body.style.overflow = '';
        }
      });
    });
    
    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
      modal.addEventListener('click', function(e) {
        if(e.target === modal) {
          modal.style.display = 'none';
          document.body.style.overflow = '';
        }
      });
    });
    
    // Handle custom date range toggle in report modal
    const reportPeriod = document.getElementById('reportPeriod');
    const customDateRange = document.getElementById('customDateRange');
    
    if(reportPeriod && customDateRange) {
      reportPeriod.addEventListener('change', function() {
        if(this.value === 'custom') {
          customDateRange.style.display = 'block';
        } else {
          customDateRange.style.display = 'none';
        }
      });
    }
    
    // Update current date and time
    function updateDateTime() {
      const now = new Date();
      const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      const formattedDate = now.toLocaleDateString('en-US', options);
      document.getElementById('currentDateTime').textContent = formattedDate;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute
    
    // Task checkbox functionality
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const taskItem = this.closest('li');
        if(this.checked) {
          taskItem.style.opacity = '0.6';
          taskItem.style.textDecoration = 'line-through';
        } else {
          taskItem.style.opacity = '1';
          taskItem.style.textDecoration = 'none';
        }
      });
    });
    
    // Simulate loading animations
    function simulateLoading() {
      const progressBars = document.querySelectorAll('.progress-bar');
      progressBars.forEach(bar => {
        const targetWidth = bar.style.width || bar.dataset.progress || '0%';
        bar.style.width = '0%';
        setTimeout(() => {
          bar.style.width = targetWidth;
        }, 300);
      });
    }
    
    // Add tooltip functionality for sidebar items
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => {
      const title = link.dataset.title;
      if(title && window.innerWidth <= 1024 && window.innerWidth >= 768) {
        link.setAttribute('title', title);
      } else {
        link.removeAttribute('title');
      }
    });
    
    // Handle window resize events for responsive behavior
    window.addEventListener('resize', function() {
      sidebarLinks.forEach(link => {
        const title = link.dataset.title;
        if(title && window.innerWidth <= 1024 && window.innerWidth >= 768) {
          link.setAttribute('title', title);
        } else {
          link.removeAttribute('title');
        }
      });
    });
    
    // Handle form submissions (prevent default for demo)
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Here you would normally handle the form submission with AJAX or other methods
        // For demo purposes, we'll just close the modal
        const modal = form.closest('.modal');
        if(modal) {
          // Show success message briefly
          const formContent = form.innerHTML;
          form.innerHTML = `
            <div class="text-center py-8">
              <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
              <h3 class="text-xl font-bold mb-2">Success!</h3>
              <p class="text-gray-400">Your request has been processed.</p>
            </div>
          `;
          
          setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
            // Reset form
            setTimeout(() => {
              form.innerHTML = formContent;
            }, 500);
          }, 1500);
        }
      });
    });
    
    // Add pulse effect to priority tasks
    document.querySelectorAll('.priority-high').forEach(task => {
      const indicator = task.querySelector('.bg-white');
      if(indicator) indicator.classList.add('pulse');
    });
    
    // Initialize the page
    simulateLoading();
  });
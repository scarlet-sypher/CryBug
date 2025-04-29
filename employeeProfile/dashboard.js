document.addEventListener('DOMContentLoaded', function() {
    // Set current date and time
    const updateDateTime = () => {
      const now = new Date();
      const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
    };
    
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute
    
    // Toggle sidebar on mobile
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.querySelector('.close-sidebar');
    
    const toggleSidebar = () => {
      sidebar.classList.toggle('active');
      overlay.classList.toggle('active');
    };
    
    if (menuToggle) menuToggle.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);
    if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
    
    // Dropdown functionality
    const dropdownBtns = [
      {btn: 'notificationBtn', dropdown: 'notificationDropdown'},
      {btn: 'messageBtn', dropdown: 'messageDropdown'},
      {btn: 'profileDropdownBtn', dropdown: 'profileDropdown'}
    ];
    
    dropdownBtns.forEach(item => {
      const btn = document.getElementById(item.btn);
      const dropdown = document.getElementById(item.dropdown);
      
      if (btn && dropdown) {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          
          // Close all other dropdowns first
          dropdownBtns.forEach(otherItem => {
            if (otherItem.dropdown !== item.dropdown) {
              const otherDropdown = document.getElementById(otherItem.dropdown);
              if (otherDropdown) otherDropdown.classList.add('hidden');
            }
          });
          
          dropdown.classList.toggle('hidden');
        });
      }
    });
    
    // Close dropdowns when clicking elsewhere
    document.addEventListener('click', () => {
      dropdownBtns.forEach(item => {
        const dropdown = document.getElementById(item.dropdown);
        if (dropdown) dropdown.classList.add('hidden');
      });
    });
    
    // Add tooltips to disabled buttons
    const disabledButtons = document.querySelectorAll('.disabled');
    disabledButtons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        alert("View-only mode: You don't have permission to perform this action.");
      });
    });
  });

  document.addEventListener('DOMContentLoaded', function() {
    const durationRadios = document.querySelectorAll('input[name="duration"]');
    const halfDayOptions = document.getElementById('halfDayOptions');
    
    durationRadios.forEach(radio => {
      radio.addEventListener('change', function() {
        if (this.value === 'half') {
          halfDayOptions.classList.remove('hidden');
        } else {
          halfDayOptions.classList.add('hidden');
        }
      });
    });
  });






  // Add this JavaScript to make the search execute on Enter key press
document.querySelector('input[name="search"]').addEventListener('keyup', function(event) {
  if (event.key === 'Enter') {
      document.getElementById('filterForm').submit();
  }
});

// Add animation for table rows
document.addEventListener('DOMContentLoaded', function() {
  const tableRows = document.querySelectorAll('tbody tr');
  tableRows.forEach((row, index) => {
      row.style.opacity = '0';
      row.style.transform = 'translateY(10px)';
      setTimeout(() => {
          row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          row.style.opacity = '1';
          row.style.transform = 'translateY(0)';
      }, 100 * index);
  });
});




// Add this JavaScript to make the search execute on Enter key press
document.querySelector('input[name="search"]').addEventListener('keyup', function(event) {
  if (event.key === 'Enter') {
      document.getElementById('filterForm').submit();
  }
});

// Add animation for table rows
document.addEventListener('DOMContentLoaded', function() {
  const tableRows = document.querySelectorAll('tbody tr');
  tableRows.forEach((row, index) => {
      row.style.opacity = '0';
      row.style.transform = 'translateY(10px)';
      setTimeout(() => {
          row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          row.style.opacity = '1';
          row.style.transform = 'translateY(0)';
      }, 100 * index);
  });
});






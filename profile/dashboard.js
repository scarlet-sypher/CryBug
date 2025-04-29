document.addEventListener("DOMContentLoaded", () => {

  const menuToggle = document.querySelector('.menu-toggle');
  const closeButton = document.querySelector('.close-sidebar');
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  // Open sidebar
  if (menuToggle) {
    menuToggle.addEventListener('click', () => {
      sidebar.classList.add('active');
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    });
  }

  // Close sidebar
  if (closeButton) {
    closeButton.addEventListener('click', () => {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    });
  }

  // Close sidebar when clicking overlay
  if (overlay) {
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    });
  }

  // Close sidebar on window resize
  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  });

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

  // Dropdown toggle setup
  function setupDropdown(btnId, dropdownId) {
    const btn = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);

    if (btn && dropdown) {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
      });
    }
  }

  setupDropdown('notificationBtn', 'notificationDropdown');
  setupDropdown('messageBtn', 'messageDropdown');
  setupDropdown('profileDropdownBtn', 'profileDropdown');

  // Close dropdowns on outside click
  document.addEventListener('click', () => {
    document.querySelectorAll('#notificationDropdown, #messageDropdown, #profileDropdown').forEach(dropdown => {
      dropdown.classList.add('hidden');
    });
  });

  // Modal setup
  function setupModal(btnId, modalId) {
    const btn = document.getElementById(btnId);
    const modal = document.getElementById(modalId);
    if (!btn || !modal) return;

    const closeBtns = modal.querySelectorAll('.close-modal');

    btn.addEventListener('click', () => {
      modal.style.display = 'flex';
      setTimeout(() => {
        modal.querySelector('.modal-content').style.opacity = '1';
      }, 10);
    });

    closeBtns.forEach(closeBtn => {
      closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
      });
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
  }

  setupModal('addProjectBtn', 'addProjectModal');
  setupModal('reportBugBtn', 'reportBugModal');

  // Stat card animation
  const statCards = document.querySelectorAll('.stat-card');
  statCards.forEach(card => {
    card.addEventListener('mouseover', () => card.classList.add('pulse'));
    card.addEventListener('mouseout', () => card.classList.remove('pulse'));
  });

  // Sidebar link active class
  const sidebarLinks = document.querySelectorAll('.sidebar-link');
  sidebarLinks.forEach(link => {
    link.addEventListener('click', () => {
      sidebarLinks.forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    });
  });

});
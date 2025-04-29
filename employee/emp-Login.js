document.addEventListener('DOMContentLoaded', function() {
  // Password toggle functionality
  const passwordField = document.querySelector('input[name="password"]');
  const passwordToggle = document.getElementById('passwordToggle');
  
  if (passwordToggle && passwordField) {
    passwordToggle.addEventListener('click', function() {
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordToggle.innerHTML = '👁️‍🗨️';
      } else {
        passwordField.type = 'password';
        passwordToggle.innerHTML = '👁️';
      }
    });
  }
  
  // Forgot password modal functionality
  const forgotPasswordLink = document.getElementById('forgotPasswordLink');
  const forgotPasswordModal = document.getElementById('forgotPasswordModal');
  const closeModal = document.getElementById('closeModal');
  const forgotPasswordForm = document.getElementById('forgotPasswordForm');
  const resetEmailError = document.getElementById('reset-email-error');
  const resetConfirmation = document.getElementById('resetConfirmation');
  
  if (forgotPasswordLink && forgotPasswordModal) {
    forgotPasswordLink.addEventListener('click', function(e) {
      e.preventDefault();
      forgotPasswordModal.style.display = 'flex';
      forgotPasswordModal.classList.add('show-modal');
      
      if (resetConfirmation) {
        resetConfirmation.classList.add('hidden');
      }
    });
  }
  
  // Close modal button
  if (closeModal && forgotPasswordModal) {
    closeModal.addEventListener('click', function() {
      forgotPasswordModal.style.display = 'none';
      forgotPasswordModal.classList.remove('show-modal');
      
      if (resetConfirmation) {
        resetConfirmation.classList.add('hidden');
      }
      
      const resetEmailInput = document.getElementById('reset-email');
      if (resetEmailInput) {
        resetEmailInput.value = '';
      }
      
      if (resetEmailError) {
        resetEmailError.classList.add('hidden');
      }
    });
  }
  
  // Close modal when clicking outside
  window.addEventListener('click', function(e) {
    if (forgotPasswordModal && e.target === forgotPasswordModal) {
      forgotPasswordModal.style.display = 'none';
      forgotPasswordModal.classList.remove('show-modal');
      
      if (resetConfirmation) {
        resetConfirmation.classList.add('hidden');
      }
      
      const resetEmailInput = document.getElementById('reset-email');
      if (resetEmailInput) {
        resetEmailInput.value = '';
      }
      
      if (resetEmailError) {
        resetEmailError.classList.add('hidden');
      }
    }
  });
  
  // Email validation function
  function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
  
  // Reset password form handling
  if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const resetEmail = document.getElementById('reset-email');
      if (!resetEmail) {
        return;
      }
      
      const emailValue = resetEmail.value.trim();
      
      if (!validateEmail(emailValue)) {
        if (resetEmailError) {
          resetEmailError.classList.remove('hidden');
        }
        return;
      }
      
      if (resetEmailError) {
        resetEmailError.classList.add('hidden');
      }
      
      // Show loading state on button
      const resetButton = document.getElementById('resetPasswordBtn');
      if (!resetButton) {
        return;
      }
      
      const originalButtonText = resetButton.innerHTML;
      resetButton.innerHTML = '<div class="loading"><div></div><div></div><div></div><div></div></div>';
      resetButton.disabled = true;
      
      // Simulate sending reset email with delay
      setTimeout(function() {
        resetButton.innerHTML = originalButtonText;
        resetButton.disabled = false;
        
        if (resetConfirmation) {
          resetConfirmation.classList.remove('hidden');
        }
        
        // Auto close after 3 seconds
        setTimeout(function() {
          if (forgotPasswordModal) {
            forgotPasswordModal.style.display = 'none';
            forgotPasswordModal.classList.remove('show-modal');
          }
          
          if (resetConfirmation) {
            resetConfirmation.classList.add('hidden');
          }
          
          if (resetEmail) {
            resetEmail.value = '';
          }
        }, 3000);
      }, 1500);
    });
  }
  
  // Add animation to login form fields on page load
  const inputs = document.querySelectorAll('input');
  inputs.forEach((input, index) => {
    input.style.opacity = "0";
    input.style.transform = "translateY(10px)";
    input.style.transition = "all 0.3s ease";
    
    setTimeout(() => {
      input.style.opacity = "1";
      input.style.transform = "translateY(0)";
    }, 300 + (index * 100));
  });
});
document.addEventListener('DOMContentLoaded', function() {
  const signupForm = document.getElementById('signupForm');
  const forgotPasswordLink = document.getElementById('forgotPasswordLink');
  const forgotPasswordModal = document.getElementById('forgotPasswordModal');
  const closeModal = document.getElementById('closeModal');
  const forgotPasswordForm = document.getElementById('forgotPasswordForm');
  const resetConfirmation = document.getElementById('resetConfirmation');
  const passwordToggle = document.getElementById('passwordToggle');
  const confirmPasswordToggle = document.getElementById('confirmPasswordToggle');
  const passwordField = document.getElementById('password');
  const confirmPasswordField = document.getElementById('confirm-password');
  const signupCard = document.getElementById('signupCard');
  const successCard = document.getElementById('successCard');
  
  // Make sure all error message elements are initially hidden
  const errorElements = document.querySelectorAll('.error-message');
  errorElements.forEach(el => {
    el.style.display = 'none';
  });
  
  // Form validation functions
  function validateField(fieldId, errorId, validationFn, errorMessage) {
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(errorId);
    
    if (!field || !errorElement) {
      console.error(`Element not found: ${fieldId} or ${errorId}`);
      return false;
    }
    
    if (!validationFn(field.value)) {
      errorElement.textContent = errorMessage;
      errorElement.style.display = 'block';
      field.style.borderColor = '#ef4444';
      return false;
    } else {
      errorElement.style.display = 'none';
      field.style.borderColor = '#334155';
      return true;
    }
  }
  
  // Individual field validation functions
  function validateFullName(value) {
    return value.trim().length > 0;
  }
  
  function validateEmail(value) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(value);
  }
  
  function validatePhone(value) {
    const phonePattern = /^\d{10}$/;
    return phonePattern.test(value);
  }
  
  function validateCompany(value) {
    return value.trim().length > 0;
  }
  
  function validatePassword(value) {
    return value.length >= 6;
  }
  
  function validateConfirmPassword(value) {
    const password = document.getElementById('password');
    return password && value === password.value;
  }
  
  function validateRole(value) {
    return value.trim().length > 0;
  }
  
  function validateGender(value) {
    return value.trim().length > 0;
  }
  
  function validateProfilePic() {
    const field = document.getElementById('profilePic');
    const errorElement = document.getElementById('profilePic-error');
    
    if (!field || !errorElement) {
      return true;
    }
    
    // Make profile pic optional
    if (!field.files || field.files.length === 0) {
      return true;
    }
    return true;
  }
  
  // Validate all fields
  function validateForm() {
    const isFullNameValid = validateField('fullname', 'fullname-error', validateFullName, 'Please enter your full name');
    const isEmailValid = validateField('email', 'email-error', validateEmail, 'Please enter a valid email address');
    const isPhoneValid = validateField('phone', 'phone-error', validatePhone, 'Please enter a valid 10-digit phone number');
    const isCompanyValid = validateField('cmp_id', 'cmp_id-error', validateCompany, 'Please enter your company ID');
    const isPasswordValid = validateField('password', 'password-error', validatePassword, 'Password must be at least 6 characters');
    const isConfirmPasswordValid = validateField('confirm-password', 'confirm-password-error', validateConfirmPassword, 'Passwords do not match');
    const isRoleValid = validateField('role', 'role-error', validateRole, 'Please select a role');
    const isGenderValid = validateField('gender', 'gender-error', validateGender, 'Please select your gender');
    const isProfilePicValid = validateProfilePic();
    
    return isFullNameValid && isEmailValid && isPhoneValid && isCompanyValid && 
           isPasswordValid && isConfirmPasswordValid && isRoleValid && isGenderValid && isProfilePicValid;
  }
  
  // Form submission handler
  if (signupForm) {
    signupForm.addEventListener('submit', function(e) {
      if (!validateForm()) {
        e.preventDefault(); // Only prevent default if validation fails
        return false; 
      }
      
      // Check if passwords match specifically
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('confirm-password');
      
      if (!password || !confirmPassword || password.value !== confirmPassword.value) {
        const errorElement = document.getElementById('confirm-password-error');
        if (errorElement) {
          errorElement.textContent = 'Passwords do not match';
          errorElement.style.display = 'block';
        }
        if (confirmPassword) {
          confirmPassword.style.borderColor = '#ef4444';
        }
        e.preventDefault();
        return false;
      }
      
      // Show loading state on button
      const submitButton = document.getElementById('submitButton');
      if (submitButton) {
        submitButton.innerHTML = '<div class="loading"><div></div><div></div><div></div><div></div></div>';
        submitButton.disabled = true;
      }
      
      // Form will submit naturally since we didn't prevent default
    });
  }

  // File upload handling
  const fileInput = document.getElementById('profilePic');
  const fileChosen = document.getElementById('file-chosen');
  const fileUploadBtn = document.querySelector('.file-upload-btn');
  const imagePreview = document.getElementById('image-preview');

  if (fileUploadBtn && fileInput) {
    fileUploadBtn.addEventListener('click', () => {
      fileInput.click();
    });
  }

  if (fileInput && fileChosen && imagePreview) {
    fileInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
          fileChosen.textContent = this.files[0].name;
          
          const reader = new FileReader();
          reader.onload = function(e) {
            if (imagePreview) {
              imagePreview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview">`;
              imagePreview.style.height = '100px';
            }
          };
          reader.readAsDataURL(this.files[0]);
      } else {
          if (fileChosen) fileChosen.textContent = 'No file chosen';
          if (imagePreview) {
            imagePreview.innerHTML = '';
            imagePreview.style.height = '0';
          }
      }
    });
  }
      
  // Password visibility toggle
  if (passwordToggle && passwordField) {
    passwordToggle.addEventListener('click', function() {
      togglePasswordVisibility(passwordField, passwordToggle);
    });
  }
  
  if (confirmPasswordToggle && confirmPasswordField) {
    confirmPasswordToggle.addEventListener('click', function() {
      togglePasswordVisibility(confirmPasswordField, confirmPasswordToggle);
    });
  }
  
  function togglePasswordVisibility(field, toggleIcon) {
    if (field.type === 'password') {
      field.type = 'text';
      toggleIcon.textContent = '👁️‍🗨️';
    } else {
      field.type = 'password';
      toggleIcon.textContent = '👁️';
    }
  }
  
  // Forgot password modal handlers
  if (forgotPasswordLink && forgotPasswordModal) {
    forgotPasswordLink.addEventListener('click', function(e) {
      e.preventDefault();
      forgotPasswordModal.style.display = 'block';
    });
  }
  
  if (closeModal && forgotPasswordModal) {
    closeModal.addEventListener('click', function() {
      forgotPasswordModal.style.display = 'none';
      if (resetConfirmation) {
        resetConfirmation.classList.add('hidden');
      }
      const resetEmail = document.getElementById('reset-email');
      if (resetEmail) {
        resetEmail.value = '';
      }
      const resetEmailError = document.getElementById('reset-email-error');
      if (resetEmailError) {
        resetEmailError.style.display = 'none';
      }
    });
  }
  
  // Close modal when clicking outside
  if (forgotPasswordModal) {
    window.addEventListener('click', function(e) {
      if (e.target === forgotPasswordModal) {
        forgotPasswordModal.style.display = 'none';
        if (resetConfirmation) {
          resetConfirmation.classList.add('hidden');
        }
        const resetEmail = document.getElementById('reset-email');
        if (resetEmail) {
          resetEmail.value = '';
        }
        const resetEmailError = document.getElementById('reset-email-error');
        if (resetEmailError) {
          resetEmailError.style.display = 'none';
        }
      }
    });
  }
  
  // Reset password form submission
  if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const resetEmail = document.getElementById('reset-email');
      const resetEmailError = document.getElementById('reset-email-error');
      
      if (!resetEmail || !resetEmailError || !validateEmail(resetEmail.value)) {
        if (resetEmailError) {
          resetEmailError.style.display = 'block';
        }
        return;
      }
      
      resetEmailError.style.display = 'none';
      
      // Show loading state on button
      const resetButton = document.getElementById('resetPasswordBtn');
      if (!resetButton) return;
      
      const originalButtonText = resetButton.innerHTML;
      resetButton.innerHTML = '<div class="loading"><div></div><div></div><div></div><div></div></div>';
      resetButton.disabled = true;
      
      // Simulate sending reset email
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
  
  // Add input validation on blur for all fields
  const validationFields = [
    { id: 'fullname', errorId: 'fullname-error', fn: validateFullName, msg: 'Please enter your full name' },
    { id: 'email', errorId: 'email-error', fn: validateEmail, msg: 'Please enter a valid email address' },
    { id: 'phone', errorId: 'phone-error', fn: validatePhone, msg: 'Please enter a valid 10-digit phone number' },
    { id: 'cmp_id', errorId: 'cmp_id-error', fn: validateCompany, msg: 'Please enter your company ID' },
    { id: 'password', errorId: 'password-error', fn: validatePassword, msg: 'Password must be at least 6 characters' },
    { id: 'confirm-password', errorId: 'confirm-password-error', fn: validateConfirmPassword, msg: 'Passwords do not match' },
    { id: 'role', errorId: 'role-error', fn: validateRole, msg: 'Please select a role' },
    { id: 'gender', errorId: 'gender-error', fn: validateGender, msg: 'Please select your gender' }
  ];
  
  validationFields.forEach(field => {
    const element = document.getElementById(field.id);
    if (element) {
      element.addEventListener('blur', function() {
        validateField(field.id, field.errorId, field.fn, field.msg);
      });
      
      // Also validate on input change for passwords to give immediate feedback
      if (field.id === 'password' || field.id === 'confirm-password') {
        element.addEventListener('input', function() {
          if (field.id === 'confirm-password') {
            validateField(field.id, field.errorId, field.fn, field.msg);
          }
        });
      }
    }
  });

  // Done button handler for success page
  const doneButton = document.getElementById('doneButton');
  if (doneButton) {
    doneButton.addEventListener('click', function() {
      window.location.href = 'manager-Login.php';
    });
  }
  
  // Success page countdown
  
});
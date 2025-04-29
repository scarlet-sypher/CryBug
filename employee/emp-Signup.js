document.addEventListener('DOMContentLoaded', function() {
  const employeeSignupForm = document.getElementById('employeeSignupForm');
  const passwordToggle = document.getElementById('passwordToggle');
  const confirmPasswordToggle = document.getElementById('confirmPasswordToggle');
  const passwordField = document.getElementById('password');
  const confirmPasswordField = document.getElementById('confirm-password');
  const signupCard = document.getElementById('signupCard');
  const successCard = document.getElementById('successCard');
  const secondsLeftSpan = document.getElementById('secondsLeft');
  const successEmployeeIdDiv = document.getElementById('successEmployeeId');
  
  // Form validation functions
  function validateField(fieldId, errorId, validationFn, errorMessage) {
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(errorId);
    
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
  function validateEmployeeName(value) {
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
  
  function validateGender(value) {
    return value.trim().length > 0;
  }
  
  function validateDepartment(value) {
    return value.trim().length > 0;
  }
  
  function validateRole(value) {
    return value.trim().length > 0;
  }
  
  function validateExperience(value) {
    return !isNaN(value) && value.trim().length > 0;
  }
  
  function validatePassword(value) {
    return value.length >= 6;
  }
  
  function validateConfirmPassword(value) {
    const password = document.getElementById('password').value;
    return value === password;
  }
  
  function validateEmployeeProfile() {
    const field = document.getElementById('employeeProfile');
    const errorElement = document.getElementById('employeeProfile-error');
    
    if (field.files.length === 0) {
      errorElement.textContent = 'Please select a profile picture';
      errorElement.style.display = 'block';
      return false;
    } else {
      errorElement.style.display = 'none';
      return true;
    }
  }
  
  // Generate a unique employee ID
  function generateEmployeeId() {
    // Create a prefix and add a random number + timestamp
    const prefix = 'CRYEMP';
    const randomDigits = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
    const timestamp = Date.now().toString().slice(-4);
    return prefix + randomDigits + timestamp;
  }
  
  // Validate all fields
  function validateForm() {
    const isEmployeeNameValid = validateField('employeeName', 'employeeName-error', validateEmployeeName, 'Please enter your full name');
    const isEmailValid = validateField('email', 'email-error', validateEmail, 'Please enter a valid email address');
    const isPhoneValid = validateField('phone', 'phone-error', validatePhone, 'Please enter a valid 10-digit phone number');
    const isGenderValid = validateField('gender', 'gender-error', validateGender, 'Please select your gender');
    const isDepartmentValid = validateField('department', 'department-error', validateDepartment, 'Please enter your department');
    const isRoleValid = validateField('role', 'role-error', validateRole, 'Please enter your role');
    const isExperienceValid = validateField('experience', 'experience-error', validateExperience, 'Please enter valid experience in years');
    const isPasswordValid = validateField('password', 'password-error', validatePassword, 'Password must be at least 6 characters');
    const isConfirmPasswordValid = validateField('confirm-password', 'confirm-password-error', validateConfirmPassword, 'Passwords do not match');
    const isEmployeeProfileValid = validateEmployeeProfile();
    
    return isEmployeeNameValid && isEmailValid && isPhoneValid && isGenderValid && 
           isDepartmentValid && isRoleValid && isExperienceValid &&
           isPasswordValid && isConfirmPasswordValid && isEmployeeProfileValid;
  }
  
  // Form submission handler
  employeeSignupForm.addEventListener('submit', function(e) {
    // Don't prevent default if validation passes
    if (!validateForm()) {
      e.preventDefault(); // Only prevent submission if validation fails
      return;
    }
    
    // Generate employee ID
    const employeeId = generateEmployeeId();
    document.getElementById('employeeID').value = employeeId;
    
    // Show loading state on button
    const submitButton = document.getElementById('submitButton');
    submitButton.innerHTML = '<div class="loading"><div></div><div></div><div></div><div></div></div>';
    submitButton.disabled = true;
    
    // Let the form submit naturally to PHP
    // The success page will be shown by PHP after processing
  });

  // File upload handling
  const fileInput = document.getElementById('employeeProfile');
  const fileChosen = document.getElementById('file-chosen');
  const fileUploadBtn = document.querySelector('.file-upload-btn');
  const imagePreview = document.getElementById('image-preview');

  fileUploadBtn.addEventListener('click', () => {
    fileInput.click();
  });

  fileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
      fileChosen.textContent = this.files[0].name;
      
      const reader = new FileReader();
      reader.onload = function(e) {
        imagePreview.innerHTML = `<img src="${e.target.result}" alt="Profile Picture Preview">`;
        imagePreview.style.height = '100px';
      };
      reader.readAsDataURL(this.files[0]);
    } else {
      fileChosen.textContent = 'No file chosen';
      imagePreview.innerHTML = '';
      imagePreview.style.height = '0';
    }
  });
      
  // Password visibility toggle
  passwordToggle.addEventListener('click', function() {
    togglePasswordVisibility(passwordField, passwordToggle);
  });
  
  confirmPasswordToggle.addEventListener('click', function() {
    togglePasswordVisibility(confirmPasswordField, confirmPasswordToggle);
  });
  
  function togglePasswordVisibility(field, toggleIcon) {
    if (field.type === 'password') {
      field.type = 'text';
      toggleIcon.textContent = '👁️‍🗨️';
    } else {
      field.type = 'password';
      toggleIcon.textContent = '👁️';
    }
  }
  
  // Input validation on blur
  document.getElementById('employeeName').addEventListener('blur', function() {
    validateField('employeeName', 'employeeName-error', validateEmployeeName, 'Please enter your full name');
  });
  
  document.getElementById('email').addEventListener('blur', function() {
    validateField('email', 'email-error', validateEmail, 'Please enter a valid email address');
  });
  
  document.getElementById('phone').addEventListener('blur', function() {
    validateField('phone', 'phone-error', validatePhone, 'Please enter a valid 10-digit phone number');
  });
  
  document.getElementById('gender').addEventListener('blur', function() {
    validateField('gender', 'gender-error', validateGender, 'Please select your gender');
  });
  
  document.getElementById('department').addEventListener('blur', function() {
    validateField('department', 'department-error', validateDepartment, 'Please enter your department');
  });
  
  document.getElementById('role').addEventListener('blur', function() {
    validateField('role', 'role-error', validateRole, 'Please enter your role');
  });
  
  document.getElementById('experience').addEventListener('blur', function() {
    validateField('experience', 'experience-error', validateExperience, 'Please enter valid experience in years');
  });
  
  document.getElementById('password').addEventListener('blur', function() {
    validateField('password', 'password-error', validatePassword, 'Password must be at least 6 characters');
  });
  
  document.getElementById('confirm-password').addEventListener('blur', function() {
    validateField('confirm-password', 'confirm-password-error', validateConfirmPassword, 'Passwords do not match');
  });

  // Done button handler
  const doneButton = document.getElementById('doneButton');
  if (doneButton) {
    doneButton.addEventListener('click', function() {
      window.location.href = 'emp-Login.php';
    });
  }
});


    // Function to update slider values
    function updateSliderValue(sliderId) {
      const slider = document.getElementById(sliderId);
      const valueDisplay = document.getElementById(sliderId + '-value');
      valueDisplay.textContent = slider.value + '%';
    }
    
    // Initialize sliders
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize all sliders
      ['webD', 'auto', 'design', 'verbal'].forEach(sliderId => {
        updateSliderValue(sliderId);
      });
      
    
     
    });
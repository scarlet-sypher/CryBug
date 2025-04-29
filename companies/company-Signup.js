document.addEventListener('DOMContentLoaded', function() {
    const companySignupForm = document.getElementById('companySignupForm');
    const passwordToggle = document.getElementById('passwordToggle');
    const confirmPasswordToggle = document.getElementById('confirmPasswordToggle');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm-password');
    const signupCard = document.getElementById('signupCard');
    const successCard = document.getElementById('successCard');
    const secondsLeftSpan = document.getElementById('secondsLeft');
    const successCompanyIdDiv = document.getElementById('successCompanyId');
    
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
    function validateCompanyName(value) {
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
    
    function validateAddress(value) {
      return value.trim().length > 0;
    }
    
    function validatePincode(value) {
      const pincodePattern = /^\d{5,6}$/;
      return pincodePattern.test(value);
    }
    
    function validatePassword(value) {
      return value.length >= 6;
    }
    
    function validateConfirmPassword(value) {
      const password = document.getElementById('password').value;
      return value === password;
    }
    
    function validateCompanyLogo() {
      const field = document.getElementById('companyLogo');
      const errorElement = document.getElementById('companyLogo-error');
      
      if (field.files.length === 0) {
        errorElement.textContent = 'Please select a company logo';
        errorElement.style.display = 'block';
        return false;
      } else {
        errorElement.style.display = 'none';
        return true;
      }
    }
    
    // Generate a unique company ID
    function generateCompanyId() {
      // Create a prefix and add a random number + timestamp
      const prefix = 'CRYCOM';
      const randomDigits = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
      const timestamp = Date.now().toString().slice(-4);
      return prefix + randomDigits + timestamp;
    }
    
    // Validate all fields
    function validateForm() {
      const isCompanyNameValid = validateField('companyName', 'companyName-error', validateCompanyName, 'Please enter your company name');
      const isEmailValid = validateField('email', 'email-error', validateEmail, 'Please enter a valid email address');
      const isPhoneValid = validateField('phone', 'phone-error', validatePhone, 'Please enter a valid 10-digit phone number');
      const isAddressValid = validateField('address', 'address-error', validateAddress, 'Please enter your company address');
      const isPincodeValid = validateField('pincode', 'pincode-error', validatePincode, 'Please enter a valid pincode (5-6 digits)');
      const isPasswordValid = validateField('password', 'password-error', validatePassword, 'Password must be at least 6 characters');
      const isConfirmPasswordValid = validateField('confirm-password', 'confirm-password-error', validateConfirmPassword, 'Passwords do not match');
      const isCompanyLogoValid = validateCompanyLogo();
      
      return isCompanyNameValid && isEmailValid && isPhoneValid && isAddressValid && 
             isPincodeValid && isPasswordValid && isConfirmPasswordValid && isCompanyLogoValid;
    }
    
    // Form submission handler
    companySignupForm.addEventListener('submit', function(e) {
      // Don't prevent default if validation passes
      if (!validateForm()) {
        e.preventDefault(); // Only prevent submission if validation fails
        return;
      }
      
      // Generate company ID
      const companyId = generateCompanyId();
      document.getElementById('companyID').value = companyId;
      
      // Show loading state on button
      const submitButton = document.getElementById('submitButton');
      submitButton.innerHTML = '<div class="loading"><div></div><div></div><div></div><div></div></div>';
      submitButton.disabled = true;
      
      // Let the form submit naturally to PHP
      // The success page will be shown by PHP after processing
    });
  
    // File upload handling
    const fileInput = document.getElementById('companyLogo');
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
          imagePreview.innerHTML = `<img src="${e.target.result}" alt="Company Logo Preview">`;
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
    document.getElementById('companyName').addEventListener('blur', function() {
      validateField('companyName', 'companyName-error', validateCompanyName, 'Please enter your company name');
    });
    
    document.getElementById('email').addEventListener('blur', function() {
      validateField('email', 'email-error', validateEmail, 'Please enter a valid email address');
    });
    
    document.getElementById('phone').addEventListener('blur', function() {
      validateField('phone', 'phone-error', validatePhone, 'Please enter a valid 10-digit phone number');
    });
    
    document.getElementById('address').addEventListener('blur', function() {
      validateField('address', 'address-error', validateAddress, 'Please enter your company address');
    });
    
    document.getElementById('pincode').addEventListener('blur', function() {
      validateField('pincode', 'pincode-error', validatePincode, 'Please enter a valid pincode (5-6 digits)');
    });
    
    document.getElementById('password').addEventListener('blur', function() {
      validateField('password', 'password-error', validatePassword, 'Password must be at least 6 characters');
    });
    
    document.getElementById('confirm-password').addEventListener('blur', function() {
      validateField('confirm-password', 'confirm-password-error', validateConfirmPassword, 'Passwords do not match');
    });
  
    // Done button handler
    const doneButton = document.getElementById('doneButton');
    doneButton.addEventListener('click', function() {
      window.location.href = 'company-Login.php';
    });
  });
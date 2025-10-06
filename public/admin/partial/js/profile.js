/**
 * Profile Page JavaScript Functions
 */

// Avatar Upload Preview Function
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').css('background-image', 'url('+e.target.result +')');
            $('#imagePreview').hide();
            $('#imagePreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Password Toggle Function
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password Strength Checker
function checkPasswordStrength(password) {
    let strength = 0;
    
    // Length check
    if (password.length >= 8) strength++;
    
    // Contains lowercase
    if (/[a-z]/.test(password)) strength++;
    
    // Contains uppercase
    if (/[A-Z]/.test(password)) strength++;
    
    // Contains numbers
    if (/\d/.test(password)) strength++;
    
    // Contains special characters
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

// Update Password Strength Indicator
function updatePasswordStrength(password) {
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    
    if (!strengthBar || !strengthText) return;
    
    const strength = checkPasswordStrength(password);
    const strengthLevels = ['very-weak', 'weak', 'fair', 'good', 'strong'];
    const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    
    // Remove all strength classes
    strengthLevels.forEach(level => {
        strengthBar.classList.remove('strength-' + level);
    });
    
    if (password.length > 0) {
        // Add current strength class
        strengthBar.classList.add('strength-' + strengthLevels[strength - 1] || 'strength-very-weak');
        strengthText.textContent = strengthTexts[strength - 1] || 'Very Weak';
        strengthText.className = 'small text-' + (strength >= 4 ? 'success' : strength >= 3 ? 'warning' : 'danger');
    } else {
        strengthText.textContent = '';
    }
}

// Form Validation
function validateProfileForm() {
    let isValid = true;
    const requiredFields = ['name', 'email'];
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        const value = field.value.trim();
        
        if (!value) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Email validation
    const emailField = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (emailField.value && !emailRegex.test(emailField.value)) {
        emailField.classList.add('is-invalid');
        isValid = false;
    }
    
    return isValid;
}

// Password Form Validation
function validatePasswordForm() {
    let isValid = true;
    const currentPassword = document.getElementById('current_password');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    
    // Check if all fields are filled
    [currentPassword, newPassword, confirmPassword].forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Check password length
    if (newPassword.value && newPassword.value.length < 8) {
        newPassword.classList.add('is-invalid');
        isValid = false;
    }
    
    // Check password confirmation
    if (newPassword.value !== confirmPassword.value) {
        confirmPassword.classList.add('is-invalid');
        isValid = false;
    }
    
    return isValid;
}

// Auto-hide alerts after 5 seconds
function autoHideAlerts() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
}

// Initialize when document is ready
$(document).ready(function() {
    // Avatar upload change handler
    $("#imageUpload").change(function() {
        readURL(this);
    });
    
    // Password strength checker
    const newPasswordField = document.getElementById('new_password');
    if (newPasswordField) {
        newPasswordField.addEventListener('input', function() {
            updatePasswordStrength(this.value);
        });
    }
    
    // Form validation on submit
    $('form').on('submit', function(e) {
        const formId = $(this).attr('id');
        let isValid = true;
        
        if (formId === 'profile-form') {
            isValid = validateProfileForm();
        } else if (formId === 'password-form') {
            isValid = validatePasswordForm();
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-hide alerts
    autoHideAlerts();
    
    // Remove validation classes on input
    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Phone number formatting (optional)
    $('#phone').on('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 10) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
        }
        this.value = value;
    });
});

// Export functions for global use
window.ProfileJS = {
    readURL: readURL,
    togglePassword: togglePassword,
    checkPasswordStrength: checkPasswordStrength,
    updatePasswordStrength: updatePasswordStrength,
    validateProfileForm: validateProfileForm,
    validatePasswordForm: validatePasswordForm
};

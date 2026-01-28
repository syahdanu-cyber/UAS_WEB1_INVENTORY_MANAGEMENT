// assets/js/validation.js

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Phone number validation (Indonesia)
function validatePhone(phone) {
    const re = /^(\+62|62|0)[0-9]{9,12}$/;
    return re.test(phone.replace(/[\s-]/g, ''));
}

// Number only input
function numberOnly(evt) {
    const charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault();
        return false;
    }
    return true;
}

// Real-time form validation
document.addEventListener('DOMContentLoaded', function() {
    // Email inputs
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.value && !validateEmail(this.value)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                
                let feedback = this.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    this.parentNode.appendChild(feedback);
                }
                feedback.textContent = 'Format email tidak valid';
            } else if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
    
    // Phone inputs
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[name*="telepon"], input[name*="phone"]');
    phoneInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.value && !validatePhone(this.value)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                
                let feedback = this.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    this.parentNode.appendChild(feedback);
                }
                feedback.textContent = 'Format nomor telepon tidak valid';
            } else if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
    
    // Number only inputs
    const numberInputs = document.querySelectorAll('input[type="number"], input.number-only');
    numberInputs.forEach(function(input) {
        input.addEventListener('keypress', numberOnly);
    });
    
    // Required field validation
    const requiredInputs = document.querySelectorAll('input[required], select[required], textarea[required]');
    requiredInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
    
    // Password strength indicator
    const passwordInputs = document.querySelectorAll('input[type="password"][name="password"]');
    passwordInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const strength = getPasswordStrength(this.value);
            updatePasswordStrength(this, strength);
        });
    });
    
    // Confirm password validation
    const confirmPasswordInputs = document.querySelectorAll('input[name="confirm_password"], input[name="password_confirmation"]');
    confirmPasswordInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const password = document.querySelector('input[name="password"]');
            if (password && this.value !== password.value) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                
                let feedback = this.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    this.parentNode.appendChild(feedback);
                }
                feedback.textContent = 'Password tidak sama';
            } else if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
    
    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});

// Password strength checker
function getPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    return strength;
}

function updatePasswordStrength(input, strength) {
    let strengthBar = input.parentNode.querySelector('.password-strength');
    
    if (!strengthBar) {
        strengthBar = document.createElement('div');
        strengthBar.className = 'password-strength mt-2';
        strengthBar.innerHTML = '<div class="progress" style="height: 5px;"><div class="progress-bar" role="progressbar"></div></div><small class="strength-text"></small>';
        input.parentNode.appendChild(strengthBar);
    }
    
    const progressBar = strengthBar.querySelector('.progress-bar');
    const strengthText = strengthBar.querySelector('.strength-text');
    
    let percentage = (strength / 6) * 100;
    let text = '';
    let colorClass = '';
    
    if (strength <= 2) {
        text = 'Lemah';
        colorClass = 'bg-danger';
    } else if (strength <= 4) {
        text = 'Sedang';
        colorClass = 'bg-warning';
    } else {
        text = 'Kuat';
        colorClass = 'bg-success';
    }
    
    progressBar.style.width = percentage + '%';
    progressBar.className = 'progress-bar ' + colorClass;
    strengthText.textContent = 'Kekuatan password: ' + text;
    strengthText.className = 'strength-text ' + colorClass.replace('bg-', 'text-');
}

// Format input sebagai Rupiah
function formatRupiahInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
        input.value = 'Rp ' + value;
    }
}

// Auto format rupiah on input
document.addEventListener('DOMContentLoaded', function() {
    const rupiahInputs = document.querySelectorAll('.rupiah-input');
    
    rupiahInputs.forEach(function(input) {
        input.addEventListener('keyup', function() {
            formatRupiahInput(this);
        });
        
        input.addEventListener('blur', function() {
            formatRupiahInput(this);
        });
    });
});

// Get numeric value from rupiah formatted string
function getNumericValue(rupiahString) {
    return parseInt(rupiahString.replace(/[^0-9]/g, '')) || 0;
}

// File upload validation
function validateFileUpload(input, maxSize = 2048, allowedTypes = ['image/jpeg', 'image/png', 'image/jpg']) {
    const file = input.files[0];
    
    if (!file) return true;
    
    // Check file size (in KB)
    if (file.size > maxSize * 1024) {
        alert(`Ukuran file maksimal ${maxSize}KB`);
        input.value = '';
        return false;
    }
    
    // Check file type
    if (!allowedTypes.includes(file.type)) {
        alert('Tipe file tidak diizinkan. Hanya: ' + allowedTypes.join(', '));
        input.value = '';
        return false;
    }
    
    return true;
}

// Prevent form double submission
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
                
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Submit';
                }, 3000);
            }
        });
    });
});

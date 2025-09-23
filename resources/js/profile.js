// Profile Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize profile page functionality
    initProfilePage();
});

function initProfilePage() {
    // Initialize form validation
    initFormValidation();
    
    // Initialize auto-save functionality
    initAutoSave();
    
    // Initialize password strength checker
    initPasswordStrengthChecker();
    
    // Initialize form animations
    initFormAnimations();
    
    // Initialize modal functionality
    initModalFunctionality();
}

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                showFormErrors(form);
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(input);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(input);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'กรุณากรอกข้อมูลนี้';
    }
    
    // Email validation
    if (fieldName === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'รูปแบบอีเมลไม่ถูกต้อง';
        }
    }
    
    // Phone validation
    if (fieldName === 'phone' && value) {
        const phoneRegex = /^[0-9\-\+\(\)\s]+$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง';
        }
    }
    
    // Password validation
    if (fieldName === 'password' && value) {
        if (value.length < 8) {
            isValid = false;
            errorMessage = 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร';
        }
    }
    
    // Password confirmation validation
    if (fieldName === 'password_confirmation' && value) {
        const passwordField = document.querySelector('input[name="password"]');
        if (passwordField && value !== passwordField.value) {
            isValid = false;
            errorMessage = 'รหัสผ่านไม่ตรงกัน';
        }
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
    field.classList.remove('border-gray-300', 'focus:ring-indigo-500', 'focus:border-indigo-500');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-sm mt-1';
    errorDiv.textContent = message;
    errorDiv.setAttribute('data-field-error', field.name);
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
    field.classList.add('border-gray-300', 'focus:ring-indigo-500', 'focus:border-indigo-500');
    
    const errorDiv = field.parentNode.querySelector(`[data-field-error="${field.name}"]`);
    if (errorDiv) {
        errorDiv.remove();
    }
}

function showFormErrors(form) {
    const firstError = form.querySelector('.border-red-500');
    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstError.focus();
    }
}

// Auto-save functionality
function initAutoSave() {
    const profileForm = document.querySelector('form[action*="profile.update"]');
    if (!profileForm) return;
    
    const inputs = profileForm.querySelectorAll('input, select');
    let saveTimeout;
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                autoSaveProfile(profileForm);
            }, 2000); // Save after 2 seconds of inactivity
        });
    });
}

function autoSaveProfile(form) {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Only save if there are changes
    if (hasChanges(data)) {
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAutoSaveNotification('บันทึกอัตโนมัติแล้ว');
            }
        })
        .catch(error => {
            console.error('Auto-save error:', error);
        });
    }
}

function hasChanges(data) {
    // Compare with original data (you might want to store this on page load)
    return Object.keys(data).some(key => data[key] !== '');
}

function showAutoSaveNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Password Strength Checker
function initPasswordStrengthChecker() {
    const passwordField = document.querySelector('input[name="password"]');
    if (!passwordField) return;
    
    passwordField.addEventListener('input', function() {
        const strength = checkPasswordStrength(this.value);
        updatePasswordStrengthIndicator(strength);
    });
}

function checkPasswordStrength(password) {
    let score = 0;
    let feedback = [];
    
    if (password.length >= 8) score += 1;
    else feedback.push('ความยาวอย่างน้อย 8 ตัวอักษร');
    
    if (/[a-z]/.test(password)) score += 1;
    else feedback.push('มีตัวอักษรพิมพ์เล็ก');
    
    if (/[A-Z]/.test(password)) score += 1;
    else feedback.push('มีตัวอักษรพิมพ์ใหญ่');
    
    if (/[0-9]/.test(password)) score += 1;
    else feedback.push('มีตัวเลข');
    
    if (/[^A-Za-z0-9]/.test(password)) score += 1;
    else feedback.push('มีอักขระพิเศษ');
    
    return { score, feedback };
}

function updatePasswordStrengthIndicator(strength) {
    let indicator = document.querySelector('.password-strength-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'password-strength-indicator mt-2';
        document.querySelector('input[name="password"]').parentNode.appendChild(indicator);
    }
    
    const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
    const labels = ['อ่อนมาก', 'อ่อน', 'ปานกลาง', 'แข็ง', 'แข็งมาก'];
    
    indicator.innerHTML = `
        <div class="flex items-center space-x-2">
            <div class="flex space-x-1">
                ${Array.from({length: 5}, (_, i) => `
                    <div class="w-2 h-2 rounded-full ${i < strength.score ? colors[i] : 'bg-gray-300'}"></div>
                `).join('')}
            </div>
            <span class="text-sm ${strength.score >= 3 ? 'text-green-600' : 'text-red-600'}">
                ${labels[strength.score]}
            </span>
        </div>
        ${strength.feedback.length > 0 ? `
            <div class="text-xs text-gray-500 mt-1">
                ข้อเสนอแนะ: ${strength.feedback.join(', ')}
            </div>
        ` : ''}
    `;
}

// Form Animations
function initFormAnimations() {
    // Animate form elements on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.profile-card').forEach(card => {
        observer.observe(card);
    });
}

// Modal Functionality
function initModalFunctionality() {
    // Enhanced modal interactions
    const modals = document.querySelectorAll('[x-data*="modal"]');
    
    modals.forEach(modal => {
        // Add escape key listener
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                closeModal(modal);
            }
        });
        
        // Add click outside to close
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });
}

function closeModal(modal) {
    modal.classList.remove('show');
    document.body.classList.remove('modal-open');
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export functions for global use
window.ProfilePage = {
    validateForm,
    validateField,
    showFieldError,
    clearFieldError,
    autoSaveProfile,
    checkPasswordStrength,
    closeModal
};

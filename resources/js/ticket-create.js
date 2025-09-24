// Enhanced Ticket Create Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the enhanced ticket create functionality
    initializeTicketCreate();
});

function initializeTicketCreate() {
    // Add smooth scrolling to form sections
    addSmoothScrolling();
    
    // Add form validation enhancements
    enhanceFormValidation();
    
    // Add auto-save functionality
    addAutoSave();
    
    // Add keyboard shortcuts
    addKeyboardShortcuts();
    
    // Add accessibility enhancements
    addAccessibilityFeatures();
    
    // Add animation triggers
    addAnimationTriggers();
}

function addSmoothScrolling() {
    // Smooth scroll to form sections when navigating
    const form = document.querySelector('form[x-data]');
    if (form) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'x-show') {
                    // Scroll to the active step
                    setTimeout(() => {
                        const activeStep = form.querySelector('[x-show="currentStep === ' + getCurrentStep() + '"]');
                        if (activeStep) {
                            activeStep.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'start' 
                            });
                        }
                    }, 100);
                }
            });
        });
        
        observer.observe(form, { attributes: true });
    }
}

function enhanceFormValidation() {
    // Real-time validation feedback
    const inputs = document.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        // Add validation on blur
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        // Add validation on input (for text fields)
        if (input.type === 'text' || input.tagName === 'TEXTAREA') {
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        }
        
        // Add validation on change (for selects)
        if (input.tagName === 'SELECT') {
            input.addEventListener('change', function() {
                clearFieldError(this);
            });
        }
    });
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';
    
    // Validation rules
    switch (fieldName) {
        case 'title':
            if (!value) {
                isValid = false;
                errorMessage = 'กรุณากรอกหัวข้อปัญหา';
            } else if (value.length < 5) {
                isValid = false;
                errorMessage = 'หัวข้อปัญหาต้องมีอย่างน้อย 5 ตัวอักษร';
            }
            break;
            
        case 'description':
            if (!value) {
                isValid = false;
                errorMessage = 'กรุณากรอกรายละเอียดของปัญหา';
            } else if (value.length < 20) {
                isValid = false;
                errorMessage = 'รายละเอียดต้องมีอย่างน้อย 20 ตัวอักษร';
            }
            break;
            
        case 'main_category':
            if (!value) {
                isValid = false;
                errorMessage = 'กรุณาเลือกหมวดหมู่หลัก';
            }
            break;
            
        case 'sub_category_id':
            if (!value) {
                isValid = false;
                errorMessage = 'กรุณาเลือกหมวดหมู่ย่อย';
            }
            break;
            
        case 'priority_id':
            if (!value) {
                isValid = false;
                errorMessage = 'กรุณาเลือกระดับความสำคัญ';
            }
            break;
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    // Remove existing error styling
    field.classList.remove('border-slate-200');
    field.classList.add('border-red-300', 'error-shake');
    
    // Add error message
    let errorElement = field.parentNode.querySelector('.field-error');
    if (!errorElement) {
        errorElement = document.createElement('p');
        errorElement.className = 'field-error text-sm text-red-500 ml-2 mt-1';
        field.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
    
    // Remove shake animation after it completes
    setTimeout(() => {
        field.classList.remove('error-shake');
    }, 500);
}

function clearFieldError(field) {
    field.classList.remove('border-red-300');
    field.classList.add('border-slate-200');
    
    const errorElement = field.parentNode.querySelector('.field-error');
    if (errorElement) {
        errorElement.remove();
    }
}

function addAutoSave() {
    // Auto-save form data to localStorage
    const form = document.querySelector('form[x-data]');
    if (!form) return;
    
    // Wait for Alpine.js to initialize
    setTimeout(() => {
        const alpineElement = form.querySelector('[x-data]');
        if (!alpineElement || !alpineElement.__x) return;
        
        const formData = alpineElement.__x.$data.formData;
    
        // Save data on every change
        const saveData = () => {
            localStorage.setItem('ticketFormData', JSON.stringify(formData));
        };
        
        // Load saved data on page load
        const savedData = localStorage.getItem('ticketFormData');
        if (savedData) {
            try {
                const parsedData = JSON.parse(savedData);
                Object.assign(formData, parsedData);
            } catch (e) {
                console.log('Could not parse saved form data');
            }
        }
        
        // Save data periodically
        setInterval(saveData, 5000);
        
        // Clear saved data on successful submit
        form.addEventListener('submit', () => {
            localStorage.removeItem('ticketFormData');
        });
    }, 100); // Wait 100ms for Alpine.js to initialize
}

function addKeyboardShortcuts() {
    // Add keyboard shortcuts for navigation
    document.addEventListener('keydown', function(e) {
        // Only activate shortcuts when form is focused
        if (!document.querySelector('form[x-data]').contains(document.activeElement)) {
            return;
        }
        
        // Ctrl/Cmd + Enter to submit
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.click();
            }
        }
        
        // Ctrl/Cmd + Arrow keys for navigation
        if ((e.ctrlKey || e.metaKey)) {
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                const nextButton = document.querySelector('button[type="button"]:last-child');
                if (nextButton && nextButton.textContent.includes('ถัดไป')) {
                    nextButton.click();
                }
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                const prevButton = document.querySelector('button[type="button"]:first-child');
                if (prevButton && prevButton.textContent.includes('ย้อนกลับ')) {
                    prevButton.click();
                }
            }
        }
    });
}

function addAccessibilityFeatures() {
    // Add ARIA labels and roles
    const steps = document.querySelectorAll('[data-step]');
    steps.forEach((step, index) => {
        step.setAttribute('role', 'tab');
        step.setAttribute('aria-selected', index === 0 ? 'true' : 'false');
        step.setAttribute('tabindex', index === 0 ? '0' : '-1');
    });
    
    // Add screen reader announcements
    const announceStep = (stepNumber) => {
        const stepNames = ['หมวดหมู่', 'รายละเอียด', 'ความสำคัญ', 'ส่งรายงาน'];
        const announcement = `ขั้นตอนที่ ${stepNumber}: ${stepNames[stepNumber - 1]}`;
        
        // Create announcement element
        const announcer = document.createElement('div');
        announcer.setAttribute('aria-live', 'polite');
        announcer.setAttribute('aria-atomic', 'true');
        announcer.className = 'sr-only';
        announcer.textContent = announcement;
        
        document.body.appendChild(announcer);
        
        // Remove after announcement
        setTimeout(() => {
            document.body.removeChild(announcer);
        }, 1000);
    };
    
    // Monitor step changes
    const form = document.querySelector('form[x-data]');
    if (form) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'x-show') {
                    announceStep(getCurrentStep());
                }
            });
        });
        
        observer.observe(form, { attributes: true });
    }
}

function addAnimationTriggers() {
    // Add entrance animations for form elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);
    
    // Observe form sections
    const formSections = document.querySelectorAll('[x-show]');
    formSections.forEach(section => {
        observer.observe(section);
    });
}

function getCurrentStep() {
    // Get current step from Alpine.js data
    const form = document.querySelector('form[x-data]');
    if (form && form.__x && form.__x.$data) {
        return form.__x.$data.currentStep;
    }
    return 1;
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    .animate-fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
`;
document.head.appendChild(style);

// Export functions for global access
window.TicketCreate = {
    validateField,
    showFieldError,
    clearFieldError,
    getCurrentStep
};

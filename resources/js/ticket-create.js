let selectedFiles = [];

// Alpine.js data function
function ticketFormData() {
    return {
        formData: {
            title: '',
            description: '',
            main_category: '',
            sub_category_id: '',
            priority_id: '',
            reported_at: '',
            links: ''
        },
        subCategories: [],
        errors: {},
        showPreview: false,
        selectedFiles: [],
        
        async loadSubCategories() {
            if (this.formData.main_category) {
                try {
                    const response = await fetch(`/api/categories/subcategories/${this.formData.main_category}`);
                    this.subCategories = await response.json();
                    this.formData.sub_category_id = '';
                } catch (error) {
                    console.error('Error loading subcategories:', error);
                    this.subCategories = [];
                }
            } else {
                this.subCategories = [];
                this.formData.sub_category_id = '';
            }
        },
        
        // ตรวจสอบว่าสามารถกรอกฟิลด์อื่นได้หรือไม่
        canFillOtherFields() {
            return this.formData.main_category && this.formData.sub_category_id;
        },
        
        // ตรวจสอบข้อมูลก่อนส่ง
        validateForm() {
            this.errors = {};
            let isValid = true;
            
            if (!this.formData.main_category) {
                this.errors.main_category = 'กรุณาเลือกหมวดหมู่หลัก';
                isValid = false;
            }
            if (!this.formData.sub_category_id) {
                this.errors.sub_category_id = 'กรุณาเลือกหมวดหมู่ย่อย';
                isValid = false;
            }
            if (!this.formData.title.trim()) {
                this.errors.title = 'กรุณากรอกหัวข้อปัญหา';
                isValid = false;
            }
            if (!this.formData.description.trim()) {
                this.errors.description = 'กรุณากรอกรายละเอียดของปัญหา';
                isValid = false;
            }
            if (!this.formData.priority_id) {
                this.errors.priority_id = 'กรุณาเลือกระดับความสำคัญ';
                isValid = false;
            }
            
            return isValid;
        },
        
        submitForm() {
            if (this.validateForm()) {
                this.showPreview = true;
                this.scrollToPreview();
            }
        },
        
        confirmSubmit() {
            // Find the form element and submit it
            const form = document.getElementById('ticket-form');
            if (form) {
                // Create a new form submission without preventDefault
                const formData = new FormData(form);
                
                // Filter out empty files
                const fileInput = document.getElementById('file-input');
                if (fileInput && fileInput.files) {
                    console.log('Total files in input:', fileInput.files.length);
                    
                    // Remove all attachments first
                    formData.delete('attachments[]');
                    
                    // Add only valid files
                    let validFileCount = 0;
                    let totalSize = 0;
                    const maxTotalSize = 30 * 1024 * 1024; // 30MB total limit
                    
                    for (let i = 0; i < fileInput.files.length; i++) {
                        const file = fileInput.files[i];
                        console.log(`Checking file ${i}: ${file.name} (${file.size} bytes)`);
                        
                        if (file && file.size > 0 && file.type.startsWith('image/')) {
                            // Check if adding this file would exceed total size limit
                            if (totalSize + file.size > maxTotalSize) {
                                console.log(`✗ Skipping file ${i}: ${file.name} - would exceed total size limit`);
                                continue;
                            }
                            
                            formData.append('attachments[]', file);
                            validFileCount++;
                            totalSize += file.size;
                            console.log(`✓ Adding file ${validFileCount}: ${file.name} (${file.size} bytes)`);
                        } else {
                            console.log(`✗ Skipping file ${i}: ${file.name} - size: ${file.size}, type: ${file.type}`);
                        }
                    }
                    console.log('Valid files to send:', validFileCount);
                    console.log('Total size:', (totalSize / 1024 / 1024).toFixed(2), 'MB');
                    
                    // If no valid files, don't send attachments at all
                    if (validFileCount === 0) {
                        console.log('No valid files to send, removing attachments from form data');
                        formData.delete('attachments[]');
                    }
                }
                
                // Submit via fetch to avoid Alpine.js prevent
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Form submission failed');
                    }
                })
                .then(data => {
                    if (data.success) {
                        // Redirect to success page
                        window.location.href = data.redirect || '/tickets';
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            console.error('Validation errors:', data.errors);
                            alert('กรุณาตรวจสอบข้อมูลให้ถูกต้อง');
                        } else {
                            alert(data.message || 'เกิดข้อผิดพลาดในการส่งข้อมูล');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
                });
            }
        },
        
        backToEdit() {
            this.showPreview = false;
            this.scrollToTop();
        },
        
        scrollToPreview() {
            this.$nextTick(() => {
                document.getElementById('preview-section').scrollIntoView({ 
                    behavior: 'smooth' 
                });
            });
        },
        
        scrollToTop() {
            window.scrollTo({ 
                top: 0, 
                behavior: 'smooth' 
            });
        },
        
        getSelectedSubCategoryName() {
            const subCategory = this.subCategories.find(cat => cat.id == this.formData.sub_category_id);
            return subCategory ? subCategory.name : '';
        },
        
        getSelectedPriorityName() {
            const priorities = window.priorities || [];
            const priority = priorities.find(p => p.id == this.formData.priority_id);
            if (priority) {
                const thaiPriority = {
                    'Low': 'ต่ำ (สามารถใช้งานได้ปกติ)',
                    'Medium': 'ปานกลาง (มีผลต่อการทำงานบางส่วน)',
                    'High': 'สูง (ไม่สามารถทำงานต่อได้)',
                    'Critical': 'เร่งด่วน (ส่งผลต่อระบบทั้งหมด)'
                };
                return thaiPriority[priority.name] || priority.name;
            }
            return '';
        },
        
        formatDateTime(dateTimeString) {
            if (!dateTimeString) return '';
            const date = new Date(dateTimeString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    };
}

// Form elements
let mainCategorySelect;
let subCategorySelect;
let problemDetails;
let priorityTimeSection;
let submitBtn;
let categoryWarning;
let priorityWarning;
let fileUploadSection;
let categoryWarningFile;
let uploadInstruction;

// Form fields
let titleInput;
let descriptionInput;
let linksInput;
let prioritySelect;
let reportedAtInput;

// Drag & Drop elements
let dropZone;
let fileInput;

// Initialize form state
function initializeForm() {
    // Get DOM elements
    mainCategorySelect = document.getElementById('main-category');
    subCategorySelect = document.getElementById('sub-category');
    problemDetails = document.getElementById('problem-details');
    priorityTimeSection = document.getElementById('priority-time-section');
    submitBtn = document.getElementById('submit-btn');
    categoryWarning = document.getElementById('category-warning');
    priorityWarning = document.getElementById('priority-warning');
    fileUploadSection = document.getElementById('file-upload-section');
    categoryWarningFile = document.getElementById('category-warning-file');
    uploadInstruction = document.getElementById('upload-instruction');

    titleInput = document.getElementById('title');
    descriptionInput = document.getElementById('description');
    linksInput = document.getElementById('links');
    prioritySelect = document.getElementById('priority_id');
    reportedAtInput = document.getElementById('reported_at');

    dropZone = document.getElementById('drop-zone');
    fileInput = document.getElementById('file-input');

    updateFormState();
    
    // Set current time if no old value (Thailand timezone)
    if (reportedAtInput && !reportedAtInput.value) {
        const now = new Date();
        // Convert to Thailand timezone (UTC+7)
        const thailandTime = new Date(now.getTime() + (7 * 60 * 60 * 1000));
        
        const year = thailandTime.getUTCFullYear();
        const month = String(thailandTime.getUTCMonth() + 1).padStart(2, '0');
        const day = String(thailandTime.getUTCDate()).padStart(2, '0');
        const hours = String(thailandTime.getUTCHours()).padStart(2, '0');
        const minutes = String(thailandTime.getUTCMinutes()).padStart(2, '0');
        const seconds = String(thailandTime.getUTCSeconds()).padStart(2, '0');
        
        if (reportedAtInput) {
            reportedAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
        }
    }

    // Setup event listeners
    setupEventListeners();
}

// Update form state based on category selection
function updateFormState() {
    const mainCategory = mainCategorySelect?.value;
    const subCategory = subCategorySelect?.value;
    const canFill = mainCategory && subCategory;

    if (!problemDetails || !priorityTimeSection || !submitBtn) return;

    // Enable/disable form sections
    problemDetails.style.opacity = canFill ? '1' : '0.5';
    problemDetails.style.pointerEvents = canFill ? 'auto' : 'none';
    priorityTimeSection.style.opacity = canFill ? '1' : '0.5';
    priorityTimeSection.style.pointerEvents = canFill ? 'auto' : 'none';
    
    // File upload section
    if (fileUploadSection) {
        fileUploadSection.style.opacity = canFill ? '1' : '0.5';
        fileUploadSection.style.pointerEvents = canFill ? 'auto' : 'none';
    }

    // Show/hide warnings
    if (categoryWarning) categoryWarning.style.display = canFill ? 'none' : 'block';
    if (priorityWarning) priorityWarning.style.display = canFill ? 'none' : 'block';
    if (categoryWarningFile) categoryWarningFile.style.display = canFill ? 'none' : 'block';
    if (uploadInstruction) uploadInstruction.style.display = canFill ? 'block' : 'none';

    // Enable/disable form fields
    if (titleInput) titleInput.disabled = !canFill;
    if (descriptionInput) descriptionInput.disabled = !canFill;
    if (linksInput) linksInput.disabled = !canFill;
    if (prioritySelect) prioritySelect.disabled = !canFill;
    if (reportedAtInput) reportedAtInput.disabled = !canFill;

    // Check if form is ready to submit
    const canSubmit = canFill && checkFormCompletion();
    
    // Enable/disable submit button
    submitBtn.disabled = !canSubmit;
}

// Check if all required fields are filled
function checkFormCompletion() {
    const title = titleInput?.value?.trim();
    const description = descriptionInput?.value?.trim();
    const priority = prioritySelect?.value;
    
    return title && description && priority;
}

// Load subcategories
async function loadSubCategories() {
    const mainCategory = mainCategorySelect?.value;
    
    if (!mainCategory) {
        if (subCategorySelect) {
            subCategorySelect.innerHTML = '<option value="">-- เลือกหมวดหมู่หลักก่อน --</option>';
            subCategorySelect.disabled = true;
        }
        updateFormState();
        return;
    }

    try {
        const response = await fetch(`/api/categories/${encodeURIComponent(mainCategory)}/subcategories`);
        const subcategories = await response.json();
        
        if (subCategorySelect) {
            subCategorySelect.innerHTML = '<option value="">-- เลือกหมวดหมู่ย่อย --</option>';
            
            if (Array.isArray(subcategories) && subcategories.length > 0) {
                subcategories.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    subCategorySelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = '-- ไม่มีหมวดหมู่ย่อย --';
                subCategorySelect.appendChild(option);
            }
            
            subCategorySelect.disabled = false;
        }
    } catch (error) {
        console.error('Error loading subcategories:', error);
        if (subCategorySelect) {
            subCategorySelect.innerHTML = '<option value="">-- เกิดข้อผิดพลาด --</option>';
        }
    }
    
    updateFormState();
}

// Setup event listeners
function setupEventListeners() {
    if (mainCategorySelect) {
        mainCategorySelect.addEventListener('change', loadSubCategories);
    }
    if (subCategorySelect) {
        subCategorySelect.addEventListener('change', updateFormState);
    }

    // Add event listeners for form fields to check completion
    if (titleInput) {
        titleInput.addEventListener('input', updateFormState);
    }
    if (descriptionInput) {
        descriptionInput.addEventListener('input', updateFormState);
    }
    if (prioritySelect) {
        prioritySelect.addEventListener('change', updateFormState);
    }

    // Drag & Drop functionality
    if (dropZone && fileInput) {
        setupDragAndDrop();
    }
}

// Drag & Drop functionality
function setupDragAndDrop() {
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop zone when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);
    fileInput.addEventListener('change', handleFiles, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    if (dropZone) {
        dropZone.classList.add('border-indigo-400', 'bg-indigo-50', 'scale-105');
    }
}

function unhighlight(e) {
    if (dropZone) {
        dropZone.classList.remove('border-indigo-400', 'bg-indigo-50', 'scale-105');
    }
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles({ target: { files } });
}

function handleFiles(e) {
    const files = Array.from(e.target.files);
    selectedFiles = [...selectedFiles, ...files];
    updateFileInput();
    updatePreview();
    
    // Update Alpine.js data if available
    if (window.Alpine && document.querySelector('[x-data]')) {
        const alpineData = Alpine.$data(document.querySelector('[x-data]'));
        if (alpineData) {
            alpineData.selectedFiles = selectedFiles;
        }
    }
}

function updateFileInput() {
    if (fileInput) {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }
}

function updatePreview() {
    const previewGrid = document.getElementById('file-preview-grid');
    const fileCount = document.getElementById('file-count');
    const previewContainer = document.getElementById('preview-container');

    if (selectedFiles.length > 0 && previewGrid && fileCount && previewContainer) {
        previewGrid.style.display = 'block';
        fileCount.textContent = selectedFiles.length;
        
        previewContainer.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const previewCard = createPreviewCard(file, index);
            previewContainer.appendChild(previewCard);
        });
    } else if (previewGrid) {
        previewGrid.style.display = 'none';
    }
}

function createPreviewCard(file, index) {
    const card = document.createElement('div');
    card.className = 'relative group bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1';
    
    const fileType = file.type.split('/')[1].toUpperCase();
    const fileSize = formatFileSize(file.size);
    
    card.innerHTML = `
        <!-- Image Preview -->
        <div class="aspect-w-16 bg-gray-100 relative overflow-hidden" style="position: relative; padding-bottom: 75%;">
            <img src="${URL.createObjectURL(file)}" 
                 alt="${file.name}"
                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300"></div>
        </div>
        
        <!-- File Info -->
        <div class="p-3">
            <p class="text-xs font-medium text-gray-700 truncate mb-1" title="${file.name}">${file.name}</p>
            <p class="text-xs text-gray-500 mb-2">${fileSize}</p>
            <div class="flex items-center justify-between">
                <span class="px-2 py-1 bg-indigo-100 text-indigo-600 text-xs rounded-full font-medium">
                    ${fileType}
                </span>
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            </div>
        </div>
        
        <!-- Remove Button -->
        <button type="button"
                onclick="removeFile(${index})"
                class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-red-600 hover:scale-110 shadow-lg">
            <i class="fas fa-times text-xs"></i>
        </button>
        
        <!-- Progress indicator -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-green-400 transform scale-x-100 transition-transform duration-500"></div>
    `;
    
    return card;
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateFileInput();
    updatePreview();
    
    // Update Alpine.js data if available
    if (window.Alpine && document.querySelector('[x-data]')) {
        const alpineData = Alpine.$data(document.querySelector('[x-data]'));
        if (alpineData) {
            alpineData.selectedFiles = selectedFiles;
        }
    }
}

function clearAllFiles() {
    selectedFiles = [];
    updateFileInput();
    updatePreview();
    
    // Update Alpine.js data if available
    if (window.Alpine && document.querySelector('[x-data]')) {
        const alpineData = Alpine.$data(document.querySelector('[x-data]'));
        if (alpineData) {
            alpineData.selectedFiles = selectedFiles;
        }
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Make functions globally available
window.loadSubCategories = loadSubCategories;
window.removeFile = removeFile;
window.clearAllFiles = clearAllFiles;
window.ticketFormData = ticketFormData;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeForm);

<?php
$active = 'services';
$title = 'Add New Service';
include __DIR__ . '/../layouts/header.php';

// Helper for form validation errors
function hasError($field) {
    return isset($_SESSION['errors'][$field]) ? 'is-invalid' : '';
}

function getError($field) {
    if (isset($_SESSION['errors'][$field])) {
        $error = $_SESSION['errors'][$field];
        return '<div class="invalid-feedback">' . htmlspecialchars($error) . '</div>';
    }
    return '';
}

function getOldValue($field, $default = '') {
    return $_SESSION['old'][$field] ?? $default;
}

// Clear session data after using it
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

// Default color if not set
$defaultColor = '#3498db';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Service</h1>
        <a href="/services" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Back to Services
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Service Information</h6>
        </div>
        <div class="card-body">
            <form action="/services/store" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Service Name *</label>
                        <input type="text" class="form-control <?= hasError('name') ?>" id="name" name="name" 
                               value="<?= getOldValue('name') ?>" required>
                        <?= getError('name') ?>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="duration" class="form-label">Duration (minutes) *</label>
                        <input type="number" class="form-control <?= hasError('duration') ?>" id="duration" name="duration" 
                               value="<?= getOldValue('duration', '60') ?>" min="5" step="5" required>
                        <?= getError('duration') ?>
                        <div class="form-text">How long this service typically takes</div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="price" class="form-label">Price (R)</label>
                        <input type="number" class="form-control <?= hasError('price') ?>" id="price" name="price" 
                               value="<?= getOldValue('price', '0') ?>" min="0" step="0.01">
                        <?= getError('price') ?>
                        <div class="form-text">Leave 0 for no price display</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Category</label>
                        <div class="input-group">
                            <select class="form-select" id="categorySelect" onchange="updateCategoryField(this)">
                                <option value="">Select or enter a category</option>
                                <option value="new">+ Add new category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['category']) ?>" 
                                            <?= getOldValue('category') == $cat['category'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['category']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" class="form-control d-none" id="newCategoryField" 
                                   placeholder="Enter new category name">
                            <input type="hidden" name="category" id="categoryField" value="<?= getOldValue('category') ?>">
                        </div>
                        <?= getError('category') ?>
                        <div class="form-text">Group similar services together</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="color" class="form-label">Calendar Color</label>
                        <div class="input-group">
                            <span class="input-group-text color-preview-wrapper">
                                <span class="color-preview" id="colorPreview"></span>
                            </span>
                            <input type="text" class="form-control" id="color" name="color" 
                                   value="<?= getOldValue('color', $defaultColor) ?>" maxlength="7">
                            <button type="button" class="btn btn-outline-secondary" id="colorPickerBtn">
                                <i class="bi bi-palette"></i>
                            </button>
                        </div>
                        <div class="form-text">Used to identify this service in the calendar</div>
                        
                        <!-- Hidden color picker -->
                        <div class="color-picker-panel d-none mt-2" id="colorPickerPanel">
                            <div class="d-flex flex-wrap">
                                <?php
                                $presetColors = [
                                    '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', 
                                    '#1abc9c', '#e67e22', '#34495e', '#16a085', '#d35400',
                                    '#8e44ad', '#2980b9', '#27ae60', '#c0392b', '#f1c40f'
                                ];
                                foreach ($presetColors as $presetColor):
                                ?>
                                <div class="color-option" style="background-color: <?= $presetColor ?>" 
                                     data-color="<?= $presetColor ?>" onclick="selectColor('<?= $presetColor ?>')"></div>
                                <?php endforeach; ?>
                            </div>
                            <input type="color" id="customColorPicker" class="visually-hidden" 
                                   value="<?= getOldValue('color', $defaultColor) ?>">
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="customColorBtn">
                                Custom Color...
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= getOldValue('description') ?></textarea>
                    <div class="form-text">Optional details about what this service includes</div>
                </div>
                
                <div class="mb-3">
                    <p class="text-muted small">* Required fields</p>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/services" class="btn btn-secondary me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Service</button>
                </div>
                
                <?php if (isset($errors['db_error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <?= htmlspecialchars($errors['db_error']) ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<style>
.color-preview-wrapper {
    width: 40px;
}

.color-preview {
    display: block;
    width: 20px;
    height: 20px;
    border-radius: 3px;
    background-color: <?= getOldValue('color', $defaultColor) ?>;
}

.color-picker-panel {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
}

.color-option {
    width: 30px;
    height: 30px;
    margin: 3px;
    border-radius: 3px;
    cursor: pointer;
    border: 1px solid #ddd;
}

.color-option:hover {
    transform: scale(1.1);
    border: 1px solid #888;
}
</style>

<script>
// Category selection handling
function updateCategoryField(select) {
    const newCategoryField = document.getElementById('newCategoryField');
    const categoryField = document.getElementById('categoryField');
    
    if (select.value === 'new') {
        // Show the text input for new category
        newCategoryField.classList.remove('d-none');
        newCategoryField.focus();
        categoryField.value = '';
        
        // Listen for input in the new category field
        newCategoryField.addEventListener('input', function() {
            categoryField.value = this.value;
        });
    } else {
        // Hide the text input and use the selected category
        newCategoryField.classList.add('d-none');
        categoryField.value = select.value;
    }
}

// Color picker handling
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorPreview = document.getElementById('colorPreview');
    const colorPickerBtn = document.getElementById('colorPickerBtn');
    const colorPickerPanel = document.getElementById('colorPickerPanel');
    const customColorPicker = document.getElementById('customColorPicker');
    const customColorBtn = document.getElementById('customColorBtn');
    
    // Update preview when input changes
    colorInput.addEventListener('input', function() {
        colorPreview.style.backgroundColor = this.value;
    });
    
    // Initialize preview
    colorPreview.style.backgroundColor = colorInput.value;
    
    // Toggle color picker panel
    colorPickerBtn.addEventListener('click', function() {
        colorPickerPanel.classList.toggle('d-none');
    });
    
    // Handle clicking outside to close
    document.addEventListener('click', function(event) {
        if (!colorPickerPanel.contains(event.target) && event.target !== colorPickerBtn) {
            colorPickerPanel.classList.add('d-none');
        }
    });
    
    // Custom color picker
    customColorBtn.addEventListener('click', function() {
        customColorPicker.click();
    });
    
    customColorPicker.addEventListener('input', function() {
        selectColor(this.value);
    });
});

// Select color and update input
function selectColor(color) {
    const colorInput = document.getElementById('color');
    const colorPreview = document.getElementById('colorPreview');
    const colorPickerPanel = document.getElementById('colorPickerPanel');
    
    colorInput.value = color;
    colorPreview.style.backgroundColor = color;
    colorPickerPanel.classList.add('d-none');
}

// Category select initialization
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('categorySelect');
    const categoryField = document.getElementById('categoryField');
    
    // Initialize hidden field with selected value
    if (categorySelect.value && categorySelect.value !== 'new') {
        categoryField.value = categorySelect.value;
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
<?php
// Set active menu and page title
$active = 'calendar';
$title = 'Edit Appointment';
include __DIR__ . '/../layouts/header.php';

// Helper functions for form errors
function hasError($field) {
    if (isset($_SESSION['errors']) && isset($_SESSION['errors'][$field])) {
        return 'is-invalid';
    }
    return '';
}

function getError($field) {
    if (isset($_SESSION['errors']) && isset($_SESSION['errors'][$field])) {
        return '<div class="invalid-feedback">' . $_SESSION['errors'][$field] . '</div>';
    }
    return '';
}

// Helper function to get old form values - ensuring null safety
function getOldValue($field) {
    if (isset($_SESSION['old'][$field])) {
        return htmlspecialchars($_SESSION['old'][$field]);
    }
    return '';
}

// Make sure we're handling null values safely
function safeHtmlSpecialChars($str) {
    return htmlspecialchars($str ?? '');
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Appointment</h1>
        <a href="/calendar/day?day=<?= date('j', strtotime($appointment['start_time'])) ?>&month=<?= date('n', strtotime($appointment['start_time'])) ?>&year=<?= date('Y', strtotime($appointment['start_time'])) ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Back to Day View
        </a>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Appointment Details</h6>
        </div>
        <div class="card-body">
            <form action="/calendar/update/<?= $appointment['id'] ?>" method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="client_id" class="form-label">Client *</label>
                        <div class="input-group">
                            <select class="form-control <?= hasError('client_id') ?>" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                <?php foreach($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" <?= ($client['id'] == $appointment['client_id']) ? 'selected' : '' ?>>
                                        <?= safeHtmlSpecialChars($client['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newClientModal">
                                <i class="bi bi-plus"></i> New
                            </button>
                        </div>
                        <?= getError('client_id') ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="service_id" class="form-label">Service *</label>
                        <div class="input-group">
                            <select class="form-control <?= hasError('service_id') ?>" id="service_id" name="service_id" required>
                                <option value="">Select Service</option>
                                <?php foreach($services as $service): ?>
                                    <option value="<?= $service['id'] ?>" data-duration="<?= $service['duration'] ?>" <?= ($service['id'] == $appointment['service_id']) ? 'selected' : '' ?>>
                                        <?= safeHtmlSpecialChars($service['name']) ?> (<?= $service['duration'] ?> min)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newServiceModal">
                                <i class="bi bi-plus"></i> New
                            </button>
                        </div>
                        <?= getError('service_id') ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Date *</label>
                        <input type="date" class="form-control <?= hasError('date') ?>" id="date" name="date" 
                               value="<?= date('Y-m-d', strtotime($appointment['start_time'])) ?>" required>
                        <?= getError('date') ?>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="time" class="form-label">Time *</label>
                        <input type="time" class="form-control <?= hasError('time') ?>" id="time" name="time" 
                               value="<?= date('H:i', strtotime($appointment['start_time'])) ?>" required>
                        <?= getError('time') ?>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="duration" class="form-label">Duration (minutes) *</label>
                        <input type="number" class="form-control <?= hasError('duration') ?>" id="duration" name="duration" 
                               value="<?= round((strtotime($appointment['end_time']) - strtotime($appointment['start_time'])) / 60) ?>" 
                               min="5" step="5" required>
                        <?= getError('duration') ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control <?= hasError('status') ?>" id="status" name="status">
                            <option value="pending" <?= ($appointment['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= ($appointment['status'] == 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                            <option value="completed" <?= ($appointment['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= ($appointment['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <?= getError('status') ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= safeHtmlSpecialChars($appointment['notes']) ?></textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Update Appointment</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        Delete Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- New Client Modal -->
<div class="modal fade" id="newClientModal" tabindex="-1" aria-labelledby="newClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newClientModalLabel">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newClientForm" method="post" onsubmit="return false;">
                    <div class="mb-3">
                        <label for="client_name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="client_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="client_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="client_email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="client_phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="client_phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="client_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="client_notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveClientBtn">Save Client</button>
            </div>
        </div>
    </div>
</div>

<!-- New Service Modal -->
<div class="modal fade" id="newServiceModal" tabindex="-1" aria-labelledby="newServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newServiceModalLabel">Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newServiceForm" method="post" onsubmit="return false;">
                    <div class="mb-3">
                        <label for="service_name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="service_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="service_duration" class="form-label">Duration (minutes) *</label>
                        <input type="number" class="form-control" id="service_duration" name="duration" min="5" step="5" value="60" required>
                    </div>
                    <div class="mb-3">
                        <label for="service_price" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="service_price" name="price" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="service_color" class="form-label">Calendar Color</label>
                        <input type="color" class="form-control form-control-color" id="service_color" name="color" value="#3498db">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveServiceBtn">Save Service</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this appointment?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="/calendar/delete/<?= $appointment['id'] ?>" method="post">
                    <input type="hidden" name="referrer" value="/calendar/day?day=<?= date('j', strtotime($appointment['start_time'])) ?>&month=<?= date('n', strtotime($appointment['start_time'])) ?>&year=<?= date('Y', strtotime($appointment['start_time'])) ?>">
                    <button type="submit" class="btn btn-danger">Delete Appointment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update duration when service is selected
    document.getElementById('service_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const duration = selectedOption.getAttribute('data-duration');
        if (duration) {
            document.getElementById('duration').value = duration;
        }
    });

    // Handle new client form submission
    document.getElementById('saveClientBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('newClientForm'));
        
        fetch('/clients/create', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add the new client to the dropdown
                const clientSelect = document.getElementById('client_id');
                const newOption = new Option(data.client.name, data.client.id, true, true);
                clientSelect.add(newOption);
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('newClientModal'));
                modal.hide();
            } else {
                alert('Error saving client: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Handle new service form submission
    document.getElementById('saveServiceBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('newServiceForm'));
        
        fetch('/services/create', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add the new service to the dropdown
                const serviceSelect = document.getElementById('service_id');
                const newOption = new Option(
                    `${data.service.name} (${data.service.duration} min)`, // Display text
                    data.service.id, // Value
                    true, // Selected
                    true // Default selected
                );
                newOption.setAttribute('data-duration', data.service.duration); // Add duration attribute
                serviceSelect.add(newOption);
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('newServiceModal'));
                modal.hide();
            } else {
                alert('Error saving service: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>
<script src="/js/appointment-form.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
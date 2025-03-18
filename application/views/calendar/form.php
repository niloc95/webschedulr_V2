<?php
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= isset($formData['id']) ? 'Edit Appointment' : 'New Appointment' ?></h1>
        <a href="/calendar/day/<?= date('Y-m-d', strtotime($formData['start_time'])) ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Calendar
        </a>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Appointment Details</h6>
        </div>
        <div class="card-body">
            <form action="/calendar/save" method="post">
                <?php if (isset($formData['id'])): ?>
                    <input type="hidden" name="id" value="<?= $formData['id'] ?>">
                <?php endif; ?>
                
                <div class="row">
                    <!-- Client Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $client): ?>
                                <option value="<?= $client['id'] ?>" <?= ($formData['client_id'] == $client['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($client['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#newClientModal">
                                <i class="bi bi-plus"></i> New
                            </button>
                        </div>
                    </div>
                    
                    <!-- Service Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="service_id" class="form-label">Service <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-select" id="service_id" name="service_id" required>
                                <option value="">Select Service</option>
                                <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id'] ?>" 
                                        data-duration="<?= $service['duration'] ?>"
                                        <?= ($formData['service_id'] == $service['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['name']) ?> (<?= $service['duration'] ?> min)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#newServiceModal">
                                <i class="bi bi-plus"></i> New
                            </button>
                        </div>
                    </div>
                    
                    <!-- Date and Time -->
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" 
                               value="<?= date('Y-m-d\TH:i', strtotime($formData['start_time'])) ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time" 
                               value="<?= date('Y-m-d\TH:i', strtotime($formData['end_time'])) ?>" required>
                    </div>
                    
                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?= ($formData['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= ($formData['status'] == 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                            <option value="completed" <?= ($formData['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= ($formData['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <!-- Notes -->
                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($formData['notes']) ?></textarea>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Appointment</button>
                    <a href="/calendar/day/<?= date('Y-m-d', strtotime($formData['start_time'])) ?>" class="btn btn-secondary ms-2">Cancel</a>
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
                <form id="quickClientForm">
                    <div class="mb-3">
                        <label for="client_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="client_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="client_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="client_email" name="email">
                    </div>
                    
                    <div class="mb-3">
                        <label for="client_phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="client_phone" name="phone">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveClientBtn">Add Client</button>
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
                <form id="quickServiceForm">
                    <div class="mb-3">
                        <label for="service_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="service_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="service_duration" name="duration" value="60" min="5" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_price" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="service_price" name="price" step="0.01" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_color" class="form-label">Calendar Color</label>
                        <input type="color" class="form-control form-control-color" id="service_color" name="color" value="#4e73df">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveServiceBtn">Add Service</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle service selection to update end time based on duration
    const serviceSelect = document.getElementById('service_id');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    
    function updateEndTime() {
        if (serviceSelect.selectedIndex > 0 && startTimeInput.value) {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const duration = parseInt(selectedOption.dataset.duration) || 60; // Default to 60 minutes
            
            // Calculate end time based on start time and duration
            const startTime = new Date(startTimeInput.value);
            const endTime = new Date(startTime.getTime() + duration * 60000);
            
            // Format end time for datetime-local input
            const year = endTime.getFullYear();
            const month = String(endTime.getMonth() + 1).padStart(2, '0');
            const day = String(endTime.getDate()).padStart(2, '0');
            const hours = String(endTime.getHours()).padStart(2, '0');
            const minutes = String(endTime.getMinutes()).padStart(2, '0');
            
            endTimeInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    }
    
    serviceSelect.addEventListener('change', updateEndTime);
    startTimeInput.addEventListener('change', updateEndTime);
    
    // Quick Client Add functionality
    document.getElementById('saveClientBtn').addEventListener('click', function() {
        const form = document.getElementById('quickClientForm');
        const formData = new FormData(form);
        
        // Add validation here if needed
        const name = formData.get('name');
        if (!name || name.trim() === '') {
            alert('Client name is required');
            return;
        }
        
        // Send AJAX request to add client
        fetch('/clients/quick-add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new client to dropdown and select it
                const option = document.createElement('option');
                option.value = data.client.id;
                option.textContent = data.client.name;
                
                const clientSelect = document.getElementById('client_id');
                clientSelect.appendChild(option);
                clientSelect.value = data.client.id;
                
                // Close modal and reset form
                const modal = bootstrap.Modal.getInstance(document.getElementById('newClientModal'));
                modal.hide();
                form.reset();
            } else {
                alert('Error: ' + (data.error || 'Could not add client'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
    
    // Quick Service Add functionality
    document.getElementById('saveServiceBtn').addEventListener('click', function() {
        const form = document.getElementById('quickServiceForm');
        const formData = new FormData(form);
        
        // Add validation here if needed
        const name = formData.get('name');
        const duration = formData.get('duration');
        
        if (!name || name.trim() === '') {
            alert('Service name is required');
            return;
        }
        
        if (!duration || isNaN(duration) || parseInt(duration) < 5) {
            alert('Valid duration is required (minimum 5 minutes)');
            return;
        }
        
        // Send AJAX request to add service
        fetch('/services/quick-add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new service to dropdown and select it
                const option = document.createElement('option');
                option.value = data.service.id;
                option.textContent = data.service.name + ' (' + data.service.duration + ' min)';
                option.dataset.duration = data.service.duration;
                
                const serviceSelect = document.getElementById('service_id');
                serviceSelect.appendChild(option);
                serviceSelect.value = data.service.id;
                
                // Update end time based on new service
                updateEndTime();
                
                // Close modal and reset form
                const modal = bootstrap.Modal.getInstance(document.getElementById('newServiceModal'));
                modal.hide();
                form.reset();
            } else {
                alert('Error: ' + (data.error || 'Could not add service'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
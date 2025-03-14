<?php
// Set active menu
$active = 'calendar';
$title = 'Create Appointment';

// Include header
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Appointment</h1>
        <a href="<?= isset($_GET['date']) ? '/calendar/day?date=' . $_GET['date'] : '/calendar' ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Appointment Details</h6>
        </div>
        <div class="card-body">
            <form method="post" action="/calendar/create">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="client_id">Client</label>
                            <select name="client_id" id="client_id" class="form-control" required>
                                <option value="">-- Select Client --</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" <?= isset($_POST['client_id']) && $_POST['client_id'] == $client['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($client['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="mt-2">
                                <a href="/clients/create" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-plus"></i> New Client
                                </a>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="service_id">Service</label>
                            <select name="service_id" id="service_id" class="form-control" required>
                                <option value="">-- Select Service --</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?= $service['id'] ?>" data-duration="<?= $service['duration'] ?>" <?= isset($_POST['service_id']) && $_POST['service_id'] == $service['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($service['name']) ?> (<?= $service['duration'] ?> min - $<?= number_format($service['price'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="mt-2">
                                <a href="/services/create" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-plus"></i> New Service
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="date">Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="<?= $_POST['date'] ?? $_GET['date'] ?? date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="time">Start Time</label>
                            <input type="time" name="time" id="time" class="form-control" value="<?= $_POST['time'] ?? '' ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="duration">Duration (minutes)</label>
                            <input type="number" name="duration" id="duration" class="form-control" value="<?= $_POST['duration'] ?? '60' ?>" min="15" step="15" readonly>
                            <small class="form-text text-muted">Duration is set automatically based on the selected service</small>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="confirmed" <?= (isset($_POST['status']) && $_POST['status'] == 'confirmed') || !isset($_POST['status']) ? 'selected' : '' ?>>Confirmed</option>
                        <option value="pending" <?= isset($_POST['status']) && $_POST['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"><?= $_POST['notes'] ?? '' ?></textarea>
                </div>
                
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">Create Appointment</button>
                    <a href="<?= isset($_GET['date']) ? '/calendar/day?date=' . $_GET['date'] : '/calendar' ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Update duration when service is selected
document.getElementById('service_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        const duration = selectedOption.getAttribute('data-duration');
        document.getElementById('duration').value = duration || '60';
    } else {
        document.getElementById('duration').value = '60';
    }
});

// Set the initial duration if a service is already selected
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_id');
    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
    if (selectedOption.value) {
        const duration = selectedOption.getAttribute('data-duration');
        document.getElementById('duration').value = duration || '60';
    }
});
</script>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
?>
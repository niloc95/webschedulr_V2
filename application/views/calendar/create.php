<?php
$active = 'calendar';
$title = 'Create Appointment';
include __DIR__ . '/../layouts/header.php';

// Get error messages if they exist
$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">New Appointment</h1>
        <a href="/calendar/day/<?= $selectedDate ?? date('Y-m-d') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Calendar
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Appointment Details</h6>
        </div>
        <div class="card-body">
            <form action="/calendar/store" method="post">
                <div class="row">
                    <!-- Client Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['client_id']) ? 'is-invalid' : '' ?>" id="client_id" name="client_id" required>
                            <option value="">Select Client</option>
                            <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>" <?= (isset($oldInput['client_id']) && $oldInput['client_id'] == $client['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($client['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['client_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['client_id'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Service Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="service_id" class="form-label">Service <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['service_id']) ? 'is-invalid' : '' ?>" id="service_id" name="service_id" required>
                            <option value="">Select Service</option>
                            <?php foreach ($services as $service): ?>
                            <option value="<?= $service['id'] ?>" 
                                    data-duration="<?= $service['duration'] ?>"
                                    <?= (isset($oldInput['service_id']) && $oldInput['service_id'] == $service['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($service['name']) ?> (<?= $service['duration'] ?> min)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['service_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['service_id'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Date -->
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control <?= isset($errors['date']) ? 'is-invalid' : '' ?>" 
                               id="date" name="date" 
                               value="<?= $oldInput['date'] ?? $selectedDate ?? date('Y-m-d') ?>" required>
                        <?php if (isset($errors['date'])): ?>
                            <div class="invalid-feedback"><?= $errors['date'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Time -->
                    <div class="col-md-6 mb-3">
                        <label for="time" class="form-label">Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control <?= isset($errors['time']) ? 'is-invalid' : '' ?>" 
                               id="time" name="time" 
                               value="<?= $oldInput['time'] ?? $selectedTime ?? '09:00' ?>" required>
                        <?php if (isset($errors['time'])): ?>
                            <div class="invalid-feedback"><?= $errors['time'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Duration -->
                    <div class="col-md-6 mb-3">
                        <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?= isset($errors['duration']) ? 'is-invalid' : '' ?>" 
                               id="duration" name="duration" min="5" 
                               value="<?= $oldInput['duration'] ?? '60' ?>" required>
                        <?php if (isset($errors['duration'])): ?>
                            <div class="invalid-feedback"><?= $errors['duration'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Notes -->
                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($oldInput['notes'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create Appointment</button>
                    <a href="/calendar/day/<?= $selectedDate ?? date('Y-m-d') ?>" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update duration when service changes
    const serviceSelect = document.getElementById('service_id');
    const durationInput = document.getElementById('duration');
    
    serviceSelect.addEventListener('change', function() {
        if (this.selectedIndex > 0) {
            const selectedOption = this.options[this.selectedIndex];
            const serviceDuration = selectedOption.dataset.duration;
            if (serviceDuration) {
                durationInput.value = serviceDuration;
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
<?php
$active = 'appointments';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Database Schema</h1>
        <a href="/appointments" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Appointments
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Appointments Table Structure</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($appointmentsColumns as $column): ?>
                        <tr>
                            <td><?= htmlspecialchars($column['Field']) ?></td>
                            <td><?= htmlspecialchars($column['Type']) ?></td>
                            <td><?= htmlspecialchars($column['Null']) ?></td>
                            <td><?= htmlspecialchars($column['Key']) ?></td>
                            <td><?= htmlspecialchars($column['Default'] ?? 'NULL') ?></td>
                            <td><?= htmlspecialchars($column['Extra']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if($sampleAppointment): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Sample Appointment Data</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Column</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sampleAppointment as $key => $value): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($key) ?></strong></td>
                            <td><?= htmlspecialchars($value ?? 'NULL') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
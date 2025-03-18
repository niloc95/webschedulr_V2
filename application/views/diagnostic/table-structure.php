<?php
$title = 'Table Structure';

require_once __DIR__ . '/../../config/Database.php';

try {
    $db = (new Database())->getConnection();
    
    // Get structure of the appointments table
    $stmt = $db->query("DESCRIBE appointments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    include __DIR__ . '/../layouts/header.php';
    ?>
    
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Appointments Table Structure</h1>
            <a href="/diagnostic/db" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Diagnostics
            </a>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Table Columns</h6>
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
                            <?php foreach($columns as $column): ?>
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
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sample Data</h6>
            </div>
            <div class="card-body">
                <?php 
                $stmt = $db->query("SELECT * FROM appointments LIMIT 1");
                $sample = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($sample): 
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <?php foreach(array_keys($sample) as $key): ?>
                                <th><?= htmlspecialchars($key) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php foreach($sample as $value): ?>
                                <td><?= htmlspecialchars($value ?? 'NULL') ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    No appointments data found.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php
    include __DIR__ . '/../layouts/footer.php';
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
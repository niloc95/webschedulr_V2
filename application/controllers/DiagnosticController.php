<?php

// First include the BaseController
require_once __DIR__ . '/BaseController.php';

class DiagnosticController extends BaseController {
    
    public function __construct() {
        parent::__construct(); // This will initialize the $db connection from BaseController
    }
    
    public function index() {
        echo '<h1>Diagnostic Tools</h1>';
        echo '<ul>';
        echo '<li><a href="/diagnostic/db">Database Connection Test</a></li>';
        echo '<li><a href="/diagnostic/appointments">Appointments Table Check</a></li>';
        echo '<li><a href="/diagnostic/check-services">Services Table Check</a></li>';
        echo '</ul>';
    }
    
    public function testDatabaseConnection() {
        echo "<h1>WebSchedulr Database Connection Test</h1>";
        echo "<p>Testing connection to database...</p>";
        
        try {
            echo '<p style="color: green;">✅ Database connection successful!</p>';
            
            // Check users table
            $usersTable = $this->db->query("SHOW TABLES LIKE 'users'");
            echo '<p>Users table exists: ' . ($usersTable->rowCount() > 0 ? '✅ Yes' : '❌ No') . '</p>';
            
            if ($usersTable->rowCount() > 0) {
                $users = $this->db->query("SELECT COUNT(*) as count FROM users")->fetch();
                echo '<p>Number of users: ' . $users['count'] . '</p>';
            }
            
            // Test if clients table exists
            $stmt = $this->db->query("SHOW TABLES LIKE 'clients'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✓ The 'clients' table exists.</p>";
                
                // Check table structure
                $stmt = $this->db->query("DESCRIBE clients");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                echo "<p>Table columns:</p><ul>";
                foreach ($columns as $column) {
                    echo "<li>" . htmlspecialchars($column) . "</li>";
                }
                echo "</ul>";
                
            } else {
                echo "<p style='color: red;'>✕ The 'clients' table does not exist!</p>";
                
                echo "<h2>How to fix:</h2>";
                echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
                echo "CREATE TABLE clients (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) DEFAULT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                echo "</pre>";
            }
            
        } catch (PDOException $e) {
            echo "<p style='color: red;'><strong>Error:</strong> Could not connect to the database.</p>";
            echo "<p>Error details: " . htmlspecialchars($e->getMessage()) . "</p>";
            
            echo "<h2>Possible solutions:</h2>";
            echo "<ol>";
            echo "<li>Check if the database '" . Config::DB_NAME . "' exists</li>";
            echo "<li>Verify your database credentials in config.php</li>";
            echo "<li>Ensure the MySQL server is running</li>";
            echo "<li>Check if your MySQL user has proper privileges</li>";
            echo "</ol>";
        }
    }

    public function checkAppointmentsTable() {
        try {
            echo "<h1>Appointments Table Diagnostic</h1>";
            
            // Check if table exists
            $tableCheck = $this->db->query("SHOW TABLES LIKE 'appointments'");
            $tableExists = $tableCheck->rowCount() > 0;
            
            if (!$tableExists) {
                echo "<p style='color:red'>❌ The appointments table does not exist!</p>";
                
                echo "<h2>Create Appointments Table</h2>";
                echo "<pre>
    CREATE TABLE appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        client_id INT NOT NULL,
        service_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        start_time DATETIME NOT NULL,
        end_time DATETIME NOT NULL,
        notes TEXT,
        status ENUM('scheduled', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
        FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
    );
                </pre>";
                
                // Try to create the table
                echo "<p>Attempting to create table...</p>";
                try {
                    $this->db->exec("
                        CREATE TABLE appointments (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            user_id INT NOT NULL,
                            client_id INT NOT NULL,
                            service_id INT NOT NULL,
                            title VARCHAR(255) NOT NULL,
                            start_time DATETIME NOT NULL,
                            end_time DATETIME NOT NULL,
                            notes TEXT,
                            status ENUM('scheduled', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                            updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
                            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
                        )
                    ");
                    echo "<p style='color:green'>✅ The appointments table was created successfully!</p>";
                } catch (PDOException $e) {
                    echo "<p style='color:red'>Failed to create table: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p style='color:green'>✅ The appointments table exists!</p>";
                
                // Check table structure
                $columns = $this->db->query("DESCRIBE appointments")->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<h2>Appointments Table Structure</h2>";
                echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
                
                foreach ($columns as $column) {
                    echo "<tr>";
                    foreach ($column as $key => $value) {
                        echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
                
                // Check for test data
                $count = $this->db->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
                echo "<p>The appointments table contains $count records.</p>";
            }
            
        } catch (PDOException $e) {
            echo "<h1 style='color:red'>Database Error</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
        }
    }

    public function testServicesTable() {
        echo "<h1>WebSchedulr Services Table Test</h1>";
        echo "<p>Testing connection to database and services table...</p>";
        
        try {
            echo '<p style="color: green;">✅ Database connection successful!</p>';
            
            // Test if services table exists
            $stmt = $this->db->query("SHOW TABLES LIKE 'services'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✓ The 'services' table exists.</p>";
                
                // Check table structure
                $stmt = $this->db->query("DESCRIBE services");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                echo "<p>Table columns:</p><ul>";
                foreach ($columns as $column) {
                    echo "<li>" . htmlspecialchars($column) . "</li>";
                }
                echo "</ul>";
                
                // Check if necessary columns exist
                $requiredColumns = ['id', 'user_id', 'name', 'description', 'duration', 'price', 'category', 'color', 'created_at'];
                $missingColumns = array_diff($requiredColumns, $columns);
                
                if (!empty($missingColumns)) {
                    echo "<p style='color: red;'>❌ Missing required columns: " . implode(", ", $missingColumns) . "</p>";
                    
                    echo "<h2>How to add missing columns:</h2>";
                    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
                    echo "ALTER TABLE services\n";
                    foreach ($missingColumns as $column) {
                        switch ($column) {
                            case 'description':
                                echo "ADD COLUMN description TEXT DEFAULT NULL,\n";
                                break;
                            case 'duration':
                                echo "ADD COLUMN duration INT(11) NOT NULL DEFAULT 60,\n";
                                break;
                            case 'price':
                                echo "ADD COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 0.00,\n";
                                break;
                            case 'category':
                                echo "ADD COLUMN category VARCHAR(100) DEFAULT NULL,\n";
                                break;
                            case 'color':
                                echo "ADD COLUMN color VARCHAR(7) DEFAULT '#3498db',\n";
                                break;
                            default:
                                echo "ADD COLUMN {$column} VARCHAR(255) DEFAULT NULL,\n";
                        }
                    }
                    echo ";</pre>";
                } else {
                    echo "<p style='color: green;'>✓ All required columns exist.</p>";
                }
                
            } else {
                echo "<p style='color: red;'>✕ The 'services' table does not exist!</p>";
                
                echo "<h2>How to create the services table:</h2>";
                echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
                echo "CREATE TABLE services (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT DEFAULT NULL,
  duration INT(11) NOT NULL DEFAULT 60,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  category VARCHAR(100) DEFAULT NULL,
  color VARCHAR(7) DEFAULT '#3498db',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                echo "</pre>";
            }
            
        } catch (PDOException $e) {
            echo "<p style='color: red;'><strong>Error:</strong> Could not connect to the database.</p>";
            echo "<p>Error details: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    public function checkServiceColumns() {
        try {
            echo "<h1>Services Table Column Check</h1>";
            
            // Check structure
            $stmt = $this->db->query("DESCRIBE services");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            foreach ($columns as $column) {
                echo "<tr>";
                echo "<td>" . $column['Field'] . "</td>";
                echo "<td>" . $column['Type'] . "</td>";
                echo "<td>" . $column['Null'] . "</td>";
                echo "<td>" . $column['Key'] . "</td>";
                echo "<td>" . $column['Default'] . "</td>";
                echo "<td>" . $column['Extra'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Check for missing columns
            $requiredColumns = [
                'id', 'user_id', 'name', 'description', 
                'duration', 'price', 'category', 'color',
                'created_at', 'updated_at'
            ];
            
            $missingColumns = [];
            $existingColumns = array_column($columns, 'Field');
            
            foreach ($requiredColumns as $column) {
                if (!in_array($column, $existingColumns)) {
                    $missingColumns[] = $column;
                }
            }
            
            if (!empty($missingColumns)) {
                echo "<h3 style='color: red;'>Missing Columns:</h3>";
                echo "<ul>";
                foreach ($missingColumns as $column) {
                    echo "<li>" . $column . "</li>";
                }
                echo "</ul>";
                
                echo "<h3>SQL to fix missing columns:</h3>";
                echo "<pre>";
                echo "ALTER TABLE services\n";
                
                foreach ($missingColumns as $index => $column) {
                    $sql = "ADD COLUMN " . $column;
                    
                    switch ($column) {
                        case 'description':
                            $sql .= " TEXT DEFAULT NULL";
                            break;
                        case 'duration':
                            $sql .= " INT(11) NOT NULL DEFAULT 60";
                            break;
                        case 'price':
                            $sql .= " DECIMAL(10,2) NOT NULL DEFAULT 0.00";
                            break;
                        case 'category':
                            $sql .= " VARCHAR(100) DEFAULT NULL";
                            break;
                        case 'color':
                            $sql .= " VARCHAR(7) DEFAULT '#3498db'";
                            break;
                        case 'created_at':
                            $sql .= " DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP";
                            break;
                        case 'updated_at':
                            $sql .= " DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP";
                            break;
                        default:
                            $sql .= " VARCHAR(255) DEFAULT NULL";
                    }
                    
                    $sql .= ($index < count($missingColumns) - 1) ? "," : ";";
                    echo $sql . "\n";
                }
                
                echo "</pre>";
            } else {
                echo "<h3 style='color: green;'>All required columns exist!</h3>";
            }
            
        } catch (PDOException $e) {
            echo "<h1 style='color: red;'>Database Error</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
        }
    }
}
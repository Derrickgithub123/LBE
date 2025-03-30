<?php
// Database configuration with multiple fallback options
$configs = [
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'name' => 'project'],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root', 'name' => 'project']
];

// Initialize connection variable if not already set
if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_error) {
    $conn = null;
    $last_error = '';
    
    foreach ($configs as $config) {
        $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
        
        if (!$conn->connect_error) {
            break; // Connection successful
        }
        
        $last_error = $conn->connect_error;
        $conn = null;
    }

    if (!$conn) {
        die("<h2>Database Connection Failed</h2>
            <p>Last error: $last_error</p>
            <h3>Troubleshooting:</h3>
            <ol>
                <li>Open XAMPP and ensure MySQL is running</li>
                <li>Verify database 'project' exists in phpMyAdmin</li>
                <li>Try these credentials:
                    <ul>
                        <li>User: root, Password: (empty)</li>
                        <li>User: root, Password: root</li>
                    </ul>
                </li>
                <li><a href='http://localhost/phpmyadmin' target='_blank'>Open phpMyAdmin</a></li>
            </ol>");
    }

    // Set UTF-8 encoding
    $conn->set_charset("utf8mb4");
    
    // Register shutdown function to close connection automatically
    register_shutdown_function(function() use ($conn) {
        if ($conn instanceof mysqli && !$conn->connect_error) {
            $conn->close();
        }
    });
}

// For debugging purposes only - remove in production
function debugDatabaseConnection() {
    global $conn;
    if ($conn instanceof mysqli && !$conn->connect_error) {
        $result = $conn->query("SHOW TABLES");
        echo "<div style='background:#e8f5e9; padding:10px; margin:10px 0; border:1px solid #a5d6a7;'>";
        echo "<h3 style='color:#2e7d32;'>Database Connection Active</h3>";
        echo "<p>Database: project</p>";
        echo "<p>Tables found: " . $result->num_rows . "</p>";
        echo "</div>";
    }
}

// Uncomment for debugging:
// debugDatabaseConnection();
?>
<?php
// Database setup script for Safety & Compliance System
// Run this script to create the safety incidents table

// Include database connection
require_once 'Connections.php';

try {
    echo "<h2>Safety & Compliance Database Setup</h2>";
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Create safety_incidents table
    $sql = "CREATE TABLE IF NOT EXISTS `safety_incidents` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `employee_name` varchar(255) NOT NULL,
        `incident_details` text NOT NULL,
        `incident_type` varchar(100) NOT NULL,
        `severity` enum('low','medium','high') NOT NULL,
        `location` varchar(255) NOT NULL,
        `reported_by` varchar(255) NOT NULL,
        `incident_date` timestamp DEFAULT CURRENT_TIMESTAMP,
        `status` enum('reported','investigating','resolved','closed') DEFAULT 'reported',
        `resolution_notes` text DEFAULT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $Connections->exec($sql);
    echo "<p style='color: green;'>✓ Table 'safety_incidents' created successfully</p>";
    
    // Insert sample data
    $sampleData = [
        ['John Smith', 'Minor cut on hand while using cutting tool. First aid applied immediately.', 'injury', 'low', 'Workshop Area A', 'admin@company.com'],
        ['Sarah Johnson', 'Near miss incident with forklift in warehouse. No injuries occurred.', 'near_miss', 'medium', 'Warehouse Section B', 'admin@company.com'],
        ['Mike Wilson', 'Equipment malfunction reported. Machine shut down safely.', 'equipment_failure', 'medium', 'Production Line 2', 'admin@company.com']
    ];
    
    $stmt = $Connections->prepare("INSERT INTO safety_incidents (employee_name, incident_details, incident_type, severity, location, reported_by) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($sampleData as $data) {
        try {
            $stmt->execute($data);
        } catch (Exception $e) {
            // Ignore duplicate key errors
        }
    }
    
    echo "<p style='color: green;'>✓ Sample data inserted successfully</p>";
    
    // Verify table exists and has data
    $stmt = $Connections->query("SELECT COUNT(*) as count FROM safety_incidents");
    $result = $stmt->fetch();
    echo "<p style='color: blue;'>ℹ Safety incidents table has {$result['count']} records</p>";
    
    echo "<h3 style='color: green;'>🎉 Database setup completed successfully!</h3>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li>Access the safety module at: <code>modules/learning.php</code></li>";
    echo "<li>Login with your admin credentials</li>";
    echo "<li>Report safety incidents and view history</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database setup failed: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Safety Database Setup</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f8f9fa;
        }
        h2 { color: #333; }
        h3 { color: #28a745; }
        p { margin: 5px 0; }
        ul { margin: 10px 0; }
        code { 
            background: #e9ecef; 
            padding: 2px 6px; 
            border-radius: 3px; 
        }
    </style>
</head>
<body>
    <h3>Manual Setup Instructions:</h3>
    <ol>
        <li><strong>Option 1 - Run this script:</strong> Access this file in your browser to automatically set up the database</li>
        <li><strong>Option 2 - Manual SQL:</strong> Copy and paste the contents of <code>safety_incidents_schema.sql</code> into your database management tool</li>
        <li><strong>Option 3 - Command Line:</strong> Run <code>mysql -u username -p database_name < safety_incidents_schema.sql</code></li>
    </ol>
    
    <h3>What this setup creates:</h3>
    <ul>
        <li><strong>safety_incidents</strong> - Safety incident reports and tracking</li>
        <li><strong>Sample Data</strong> - 3 sample incident reports</li>
    </ul>
</body>
</html>



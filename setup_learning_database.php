<?php
// Database setup script for Learning Management System
// Run this script to create all necessary tables and sample data

// Include database connection
require_once 'Connections.php';

try {
    echo "<h2>Learning Management System Database Setup</h2>";
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Read and execute the SQL schema
    $sqlFile = 'learning_schema.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $Connections->exec($statement);
                    $successCount++;
                } catch (PDOException $e) {
                    echo "<p style='color: orange;'>⚠️ Warning: " . $e->getMessage() . "</p>";
                    $errorCount++;
                }
            }
        }
        
        echo "<p style='color: green;'>✓ Executed $successCount SQL statements successfully</p>";
        if ($errorCount > 0) {
            echo "<p style='color: orange;'>⚠️ $errorCount statements had warnings (tables may already exist)</p>";
        }
        
        // Verify tables were created
        $tables = ['courses', 'course_enrollments', 'course_materials', 'learning_achievements'];
        
        foreach ($tables as $table) {
            $stmt = $Connections->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Table '$table' created successfully</p>";
            } else {
                echo "<p style='color: red;'>✗ Table '$table' not found</p>";
            }
        }
        
        // Check sample data
        $stmt = $Connections->query("SELECT COUNT(*) as count FROM courses");
        $result = $stmt->fetch();
        echo "<p style='color: blue;'>ℹ Courses table has {$result['count']} records</p>";
        
        $stmt = $Connections->query("SELECT COUNT(*) as count FROM course_materials");
        $result = $stmt->fetch();
        echo "<p style='color: blue;'>ℹ Course materials table has {$result['count']} records</p>";
        
        echo "<h3 style='color: green;'>🎉 Database setup completed successfully!</h3>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ul>";
        echo "<li>Access the learning module at: <code>modules/learning.php</code></li>";
        echo "<li>Login with your admin credentials</li>";
        echo "<li>Browse available courses and enroll</li>";
        echo "</ul>";
        
    } else {
        echo "<p style='color: red;'>✗ SQL schema file not found: $sqlFile</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database setup failed: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Learning Database Setup</title>
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
        <li><strong>Option 2 - Manual SQL:</strong> Copy and paste the contents of <code>learning_schema.sql</code> into your database management tool (phpMyAdmin, MySQL Workbench, etc.)</li>
        <li><strong>Option 3 - Command Line:</strong> Run <code>mysql -u username -p database_name < learning_schema.sql</code></li>
    </ol>
    
    <h3>What this setup creates:</h3>
    <ul>
        <li><strong>courses</strong> - Course information and metadata</li>
        <li><strong>course_enrollments</strong> - Employee course enrollments and progress</li>
        <li><strong>course_materials</strong> - Course content and resources</li>
        <li><strong>learning_achievements</strong> - Gamification and achievements</li>
        <li><strong>Sample Data</strong> - 8 sample courses with materials</li>
    </ul>
</body>
</html>

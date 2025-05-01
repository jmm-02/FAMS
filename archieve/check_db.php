<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'famsattendance';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Successfully connected to the database.\n\n";
    
    // Check if emp_rec table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'emp_rec'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "Table emp_rec exists.\n\n";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE emp_rec");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Table structure for emp_rec:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})" . 
                 ($column['Null'] === 'NO' ? ' NOT NULL' : '') . 
                 (isset($column['Default']) ? " DEFAULT '{$column['Default']}'" : '') . 
                 ($column['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . "\n";
        }
        
        // Check for records
        $stmt = $pdo->query("SELECT COUNT(*) FROM emp_rec");
        $count = $stmt->fetchColumn();
        echo "\nTotal records in emp_rec: $count\n";
        
        // Check for constraints
        $stmt = $pdo->query("SHOW CREATE TABLE emp_rec");
        $tableInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nTable creation SQL:\n{$tableInfo['Create Table']}\n";
    } else {
        echo "Table emp_rec does not exist!\n";
    }
    
} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
}
?>

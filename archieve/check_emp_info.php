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
    
    echo "Checking emp_info table...\n\n";
    
    // Check if there are any records in emp_info
    $stmt = $pdo->query("SELECT COUNT(*) FROM emp_info");
    $count = $stmt->fetchColumn();
    echo "Total records in emp_info: $count\n\n";
    
    // Get some sample records if available
    if ($count > 0) {
        $stmt = $pdo->query("SELECT * FROM emp_info LIMIT 5");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Sample records:\n";
        foreach ($records as $record) {
            echo "ID: {$record['ID']}, Name: {$record['Name']}, Dept: {$record['Dept.']}\n";
        }
    }
    
    // Now let's manually try to insert a test record
    echo "\nTrying to insert a test record...\n";
    
    try {
        // First check if test record exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM emp_info WHERE ID = ?");
        $stmt->execute(['TEST123']);
        $exists = $stmt->fetchColumn() > 0;
        
        if ($exists) {
            echo "Test record already exists, updating...\n";
            $stmt = $pdo->prepare("UPDATE emp_info SET Name = ?, `Dept.` = ? WHERE ID = ?");
            $stmt->execute(['Test User', 'Test Dept', 'TEST123']);
            echo "Update successful!\n";
        } else {
            echo "Test record doesn't exist, inserting...\n";
            $stmt = $pdo->prepare("INSERT INTO emp_info (ID, Name, `Dept.`) VALUES (?, ?, ?)");
            $stmt->execute(['TEST123', 'Test User', 'Test Dept']);
            echo "Insert successful!\n";
        }
        
        // Verify the record was added/updated
        $stmt = $pdo->prepare("SELECT * FROM emp_info WHERE ID = ?");
        $stmt->execute(['TEST123']);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($record) {
            echo "Verification successful! Record exists in database.\n";
        } else {
            echo "Verification failed! Record was not found in database.\n";
        }
    } catch (PDOException $e) {
        echo "Error during test insert/update: " . $e->getMessage() . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
}
?>

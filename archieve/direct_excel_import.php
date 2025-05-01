<?php
// Define debug mode
define('DEBUG_MODE', true);

// Include time helper functions
require_once __DIR__ . '/time_helper.php';

// Set to true to detect Exception Statistic Report format
$isExceptionStatisticReport = false;

// Function to process time values into proper MySQL format
function processTimeValue($timeValue) {
    // If empty, return NULL
    if (empty($timeValue) || $timeValue === 'NULL') {
        return null;
    }
    
    // If it's already in HH:MM:SS format, return as is
    if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $timeValue)) {
        return $timeValue;
    }
    
    // If it's in HH:MM format, add seconds
    if (preg_match('/^\d{1,2}:\d{2}$/', $timeValue)) {
        return $timeValue . ':00';
    }
    
    // If it's numeric (like 18.31 or 1831)
    if (is_numeric($timeValue)) {
        // If it has a decimal point (like 18.31)
        if (strpos($timeValue, '.') !== false) {
            $parts = explode('.', $timeValue);
            $hours = intval($parts[0]);
            $minutes = isset($parts[1]) ? intval(($parts[1] / 100) * 60) : 0;
            return sprintf('%02d:%02d:00', $hours, $minutes);
        } else {
            // If it's just a number (like 1831), parse it as HHMM
            return sprintf('%02d:%02d:00', floor($timeValue / 100), $timeValue % 100);
        }
    }
    
    // Try to use formatTimeValue as fallback
    $formatted = formatTimeValue($timeValue);
    return $formatted;
}

// Database connection parameters
$host = 'localhost';
$dbname = 'famsattendance';
$username = 'root';
$password = '';

// Clear the debug log
file_put_contents(__DIR__ . '/excel_debug.log', "Starting direct Excel import...\n");

// Check if a file was uploaded
if (!isset($_FILES['excel_file']) || !is_uploaded_file($_FILES['excel_file']['tmp_name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No file was uploaded or upload error occurred'
    ]);
    exit;
}

// Get the uploaded file
$uploadedFile = $_FILES['excel_file']['tmp_name'];
$originalFileName = $_FILES['excel_file']['name'];

// Validate file type
$fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
if ($fileExtension != 'xls' && $fileExtension != 'xlsx' && $fileExtension != 'csv') {
    echo json_encode([
        'success' => false,
        'message' => 'Only .xls, .xlsx, and .csv files are allowed'
    ]);
    exit;
}

// Log file info
file_put_contents(__DIR__ . '/excel_debug.log', "Processing file: $originalFileName\n", FILE_APPEND);

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Counters
    $updated = 0;
    $added = 0;
    $attendanceRecords = 0;
    
    // Arrays to store data
    $employees = [];
    $attendanceData = [];
    
    // Create a temporary CSV file for processing
    $tempCsvFile = tempnam(sys_get_temp_dir(), 'excel_import_') . '.csv';
    
    // Use COM to convert Excel to CSV
    if (class_exists('COM') && ($fileExtension == 'xls' || $fileExtension == 'xlsx')) {
        try {
            file_put_contents(__DIR__ . '/excel_debug.log', "Using COM to convert Excel to CSV\n", FILE_APPEND);
            
            $excel = new COM("Excel.Application") or die("Failed to create Excel object");
            $excel->Visible = false;
            $excel->DisplayAlerts = false;
            
            file_put_contents(__DIR__ . '/excel_debug.log', "Opening workbook: $uploadedFile\n", FILE_APPEND);
            $workbook = $excel->Workbooks->Open($uploadedFile);
            
            file_put_contents(__DIR__ . '/excel_debug.log', "Saving as CSV: $tempCsvFile\n", FILE_APPEND);
            $workbook->SaveAs($tempCsvFile, 6); // 6 = CSV format
            
            $workbook->Close(false);
            $excel->Quit();
            $excel = null;
            
            file_put_contents(__DIR__ . '/excel_debug.log', "Successfully converted Excel to CSV\n", FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/excel_debug.log', "COM Error: " . $e->getMessage() . "\n", FILE_APPEND);
            
            // If COM fails, try to use the original file if it's CSV
            if ($fileExtension == 'csv') {
                $tempCsvFile = $uploadedFile;
            } else {
                throw new Exception("Failed to convert Excel to CSV: " . $e->getMessage());
            }
        }
    } else if ($fileExtension == 'csv') {
        // If it's already CSV, use it directly
        $tempCsvFile = $uploadedFile;
    } else {
        throw new Exception("No method available to read this Excel file. Please save it as CSV format.");
    }
    
    // Now read the CSV file
    if (($handle = fopen($tempCsvFile, "r")) !== FALSE) {
        $row = 0;
        $dataStarted = false;
        $headerRow = null;
        $idColumn = null;
        $nameColumn = null;
        $deptColumn = null;
        $dateColumn = null;
        $amInColumn = null;
        $pmOutColumn = null;
        
        file_put_contents(__DIR__ . '/excel_debug.log', "Reading CSV file: $tempCsvFile\n", FILE_APPEND);
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;
            
            // Skip empty rows
            if (empty($data) || count(array_filter($data)) == 0) {
                continue;
            }
            
            // Log the row for debugging
            file_put_contents(__DIR__ . '/excel_debug.log', "Row $row: " . implode(", ", $data) . "\n", FILE_APPEND);
            
            // Look for the header row containing "ID", "Name", "Department", etc.
            if (!$dataStarted) {
                // Check if this row contains headers
                $possibleHeaders = array_map('strtoupper', $data);
                
                // Look for "ID" column
                foreach ($possibleHeaders as $index => $header) {
                    if ($header == 'ID') {
                        $idColumn = $index;
                    } else if (in_array($header, ['NAME', 'EMPLOYEE NAME'])) {
                        $nameColumn = $index;
                    } else if (in_array($header, ['DEPARTMENT', 'DEPT', 'DEPT.'])) {
                        $deptColumn = $index;
                    } else if ($header == 'DATE') {
                        $dateColumn = $index;
                    } else if (in_array($header, ['ON-DUTY', 'AM_IN', 'AM IN', 'FIRST TIME ZONE'])) {
                        // In your CSV, "First time zone" is the header for the on-duty column
                        $amInColumn = $index;
                    } else if (in_array($header, ['OFF-DUTY', 'PM_OUT', 'PM OUT'])) {
                        $pmOutColumn = $index;
                    }
                }
                
                // Special handling for Exception Statistic Report format
                // If we found "FIRST TIME ZONE" but not the specific on-duty/off-duty columns
                if ($amInColumn !== null && $pmOutColumn === null) {
                    // In this format, the on-duty is at index $amInColumn and off-duty is at index $amInColumn+1
                    $pmOutColumn = $amInColumn + 3; // Off-duty is 3 columns after On-duty
                    file_put_contents(__DIR__ . '/excel_debug.log', "Detected Exception Statistic Report format, using column $amInColumn for AM_IN and column $pmOutColumn for PM_OUT\n", FILE_APPEND);
                }
                
                // If we found the ID column, we've found the header row
                if ($idColumn !== null) {
                    $headerRow = $data;
                    $dataStarted = true;
                    file_put_contents(__DIR__ . '/excel_debug.log', "Found header row: " . implode(", ", $headerRow) . "\n", FILE_APPEND);
                    file_put_contents(__DIR__ . '/excel_debug.log', "ID column: $idColumn, Name column: $nameColumn, Dept column: $deptColumn, Date column: $dateColumn, AM_IN column: $amInColumn, PM_OUT column: $pmOutColumn\n", FILE_APPEND);
                    continue;
                }
            }
            
            // Process data rows
            if ($dataStarted) {
                // Extract data based on identified columns
                $id = isset($data[$idColumn]) ? trim($data[$idColumn]) : '';
                $name = isset($nameColumn) && isset($data[$nameColumn]) ? trim($data[$nameColumn]) : '';
                $dept = isset($deptColumn) && isset($data[$deptColumn]) ? trim($data[$deptColumn]) : '';
                $date = isset($dateColumn) && isset($data[$dateColumn]) ? trim($data[$dateColumn]) : '';
                $amIn = isset($amInColumn) && isset($data[$amInColumn]) ? trim($data[$amInColumn]) : '';
                $pmOut = isset($pmOutColumn) && isset($data[$pmOutColumn]) ? trim($data[$pmOutColumn]) : '';
                
                // Log raw data for debugging
                file_put_contents(__DIR__ . '/excel_debug.log', "Raw data: ID=$id, Name=$name, Dept=$dept, Date=$date, AM_IN=$amIn, PM_OUT=$pmOut\n", FILE_APPEND);
                
                // Skip if ID is empty or not numeric
                if (empty($id) || !is_numeric($id)) {
                    continue;
                }
                
                // Format date if needed
                if (!empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                    // Try to parse various date formats
                    $timestamp = strtotime($date);
                    if ($timestamp !== false) {
                        $date = date('Y-m-d', $timestamp);
                    }
                }
                
                // Process time values while preserving the original format as much as possible
                // Log the original values for debugging
                file_put_contents(__DIR__ . '/excel_debug.log', "Original time values: AM_IN=$amIn, PM_OUT=$pmOut\n", FILE_APPEND);
                
                // Check if the time values are already in a valid format
                if (!empty($amIn)) {
                    // If it's just a number (like "18:31" might be read as just "18.31"), convert it properly
                    if (is_numeric($amIn)) {
                        // If it's a decimal (like 18.31), convert to proper time format
                        if (strpos($amIn, '.') !== false) {
                            $parts = explode('.', $amIn);
                            $hours = intval($parts[0]);
                            $minutes = isset($parts[1]) ? intval(($parts[1] / 100) * 60) : 0;
                            $amIn = sprintf('%02d:%02d:00', $hours, $minutes);
                        } else {
                            // If it's just a number like "1831", parse it as HHMM
                            $amIn = sprintf('%02d:%02d:00', floor($amIn / 100), $amIn % 100);
                        }
                    } 
                    // If it's already in HH:MM format but missing seconds, add them
                    else if (preg_match('/^\d{1,2}:\d{2}$/', $amIn)) {
                        $amIn .= ':00';
                    }
                    // If it's in a completely different format, try to use formatTimeValue as fallback
                    else if (!preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $amIn)) {
                        $amIn = formatTimeValue($amIn);
                    }
                }
                
                // Same processing for PM_OUT
                if (!empty($pmOut)) {
                    if (is_numeric($pmOut)) {
                        if (strpos($pmOut, '.') !== false) {
                            $parts = explode('.', $pmOut);
                            $hours = intval($parts[0]);
                            $minutes = isset($parts[1]) ? intval(($parts[1] / 100) * 60) : 0;
                            $pmOut = sprintf('%02d:%02d:00', $hours, $minutes);
                        } else {
                            $pmOut = sprintf('%02d:%02d:00', floor($pmOut / 100), $pmOut % 100);
                        }
                    } 
                    else if (preg_match('/^\d{1,2}:\d{2}$/', $pmOut)) {
                        $pmOut .= ':00';
                    }
                    else if (!preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $pmOut)) {
                        $pmOut = formatTimeValue($pmOut);
                    }
                }
                
                // Log the processed values
                file_put_contents(__DIR__ . '/excel_debug.log', "Processed time values: AM_IN=$amIn, PM_OUT=$pmOut\n", FILE_APPEND);
                
                // Store employee data
                if (!isset($employees[$id]) && !empty($name)) {
                    $employees[$id] = [
                        'name' => $name,
                        'dept' => $dept
                    ];
                    file_put_contents(__DIR__ . '/excel_debug.log', "Found employee: ID=$id, Name=$name, Dept=$dept\n", FILE_APPEND);
                }
                
                // Store attendance data
                if (!empty($date)) {
                    $attendanceData[] = [
                        'id' => $id,
                        'date' => $date,
                        'amIn' => $amIn,
                        'amOut' => null, // Not used in this format
                        'pmIn' => null,  // Not used in this format
                        'pmOut' => $pmOut
                    ];
                    file_put_contents(__DIR__ . '/excel_debug.log', "Found attendance: ID=$id, Date=$date, AM_IN=$amIn, PM_OUT=$pmOut\n", FILE_APPEND);
                }
            }
        }
        fclose($handle);
        
        // Clean up temporary file if we created one
        if ($tempCsvFile != $uploadedFile && file_exists($tempCsvFile)) {
            unlink($tempCsvFile);
        }
    } else {
        throw new Exception("Failed to open CSV file for reading.");
    }
    
    // Check if we found any data
    if (empty($employees)) {
        // Fallback: Try to extract employee data directly from the CSV content
        file_put_contents(__DIR__ . '/excel_debug.log', "No employee data found using header detection. Trying direct extraction...\n", FILE_APPEND);
        
        // Reopen the CSV file and try a different approach
        if (($handle = fopen($tempCsvFile, "r")) !== FALSE) {
            $row = 0;
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                
                // Skip empty rows
                if (empty($data) || count(array_filter($data)) == 0) {
                    continue;
                }
                
                // Special handling for Exception Statistic Report
                // Check if this row contains "Exception Statistic Report" which indicates the header
                if (isset($data[0]) && stripos($data[0], 'Exception Statistic Report') !== false) {
                    file_put_contents(__DIR__ . '/excel_debug.log', "Found Exception Statistic Report header\n", FILE_APPEND);
                    // We're in an Exception Statistic Report, so we'll look for data rows below
                    $GLOBALS['isExceptionStatisticReport'] = true;
                    continue;
                }
                
                // Skip header rows in Exception Statistic Report
                if ($GLOBALS['isExceptionStatisticReport'] && isset($data[0])) {
                    // Skip the "Stat.Date:" row
                    if (stripos($data[0], 'Stat.Date:') !== false) {
                        file_put_contents(__DIR__ . '/excel_debug.log', "Skipping Stat.Date row\n", FILE_APPEND);
                        continue;
                    }
                    
                    // Skip the header row with "ID, Name, Department, Date, etc."
                    if ($data[0] === 'ID' || (isset($data[3]) && $data[3] === 'Date')) {
                        file_put_contents(__DIR__ . '/excel_debug.log', "Skipping column headers row\n", FILE_APPEND);
                        continue;
                    }
                }
                
                // Look for rows that have the pattern: numeric ID, name, department, date
                // For Exception Statistic Report: A=ID, B=Name, C=Department, D=Date, E=AM_IN, F=AM_OUT, G=PM_IN, H=PM_OUT
                if (count($data) >= 8 && is_numeric(trim($data[0]))) {
                    $id = trim($data[0]);  // Column A = ID (EMP_ID)
                    $name = trim($data[1]); // Column B = Name
                    $dept = trim($data[2]); // Column C = Department
                    $date = trim($data[3]); // Column D = Date
                    
                    // Get all time values from their respective columns
                    $amIn = (isset($data[4]) && !empty(trim($data[4]))) ? trim($data[4]) : '';  // Column E = AM_IN
                    $amOut = (isset($data[5]) && !empty(trim($data[5]))) ? trim($data[5]) : ''; // Column F = AM_OUT
                    $pmIn = (isset($data[6]) && !empty(trim($data[6]))) ? trim($data[6]) : '';  // Column G = PM_IN
                    $pmOut = (isset($data[7]) && !empty(trim($data[7]))) ? trim($data[7]) : ''; // Column H = PM_OUT
                    
                    file_put_contents(__DIR__ . '/excel_debug.log', "Direct extraction - Row $row: ID=$id, Name=$name, Dept=$dept, Date=$date, AM_IN=$amIn, PM_OUT=$pmOut\n", FILE_APPEND);
                    
                    // Store employee data
                    if (!isset($employees[$id]) && !empty($name)) {
                        $employees[$id] = [
                            'name' => $name,
                            'dept' => $dept
                        ];
                        file_put_contents(__DIR__ . '/excel_debug.log', "Direct extraction - Found employee: ID=$id, Name=$name, Dept=$dept\n", FILE_APPEND);
                    }
                    
                    // Format date if needed
                    if (!empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                        // Try to parse various date formats
                        $timestamp = strtotime($date);
                        if ($timestamp !== false) {
                            $date = date('Y-m-d', $timestamp);
                        }
                    }
                    
                    // Process time values while preserving the original format as much as possible
                    if (!empty($amIn)) {
                        // If it's just a number (like "18:31" might be read as just "18.31"), convert it properly
                        if (is_numeric($amIn)) {
                            // If it's a decimal (like 18.31), convert to proper time format
                            if (strpos($amIn, '.') !== false) {
                                $parts = explode('.', $amIn);
                                $hours = intval($parts[0]);
                                $minutes = isset($parts[1]) ? intval(($parts[1] / 100) * 60) : 0;
                                $amIn = sprintf('%02d:%02d:00', $hours, $minutes);
                            } else {
                                // If it's just a number like "1831", parse it as HHMM
                                $amIn = sprintf('%02d:%02d:00', floor($amIn / 100), $amIn % 100);
                            }
                        } 
                        // If it's already in HH:MM format but missing seconds, add them
                        else if (preg_match('/^\d{1,2}:\d{2}$/', $amIn)) {
                            $amIn .= ':00';
                        }
                        // If it's in a completely different format, try to use formatTimeValue as fallback
                        else if (!preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $amIn)) {
                            $amIn = formatTimeValue($amIn);
                        }
                    }
                    
                    // Same processing for PM_OUT
                    if (!empty($pmOut)) {
                        if (is_numeric($pmOut)) {
                            if (strpos($pmOut, '.') !== false) {
                                $parts = explode('.', $pmOut);
                                $hours = intval($parts[0]);
                                $minutes = isset($parts[1]) ? intval(($parts[1] / 100) * 60) : 0;
                                $pmOut = sprintf('%02d:%02d:00', $hours, $minutes);
                            } else {
                                $pmOut = sprintf('%02d:%02d:00', floor($pmOut / 100), $pmOut % 100);
                            }
                        } 
                        else if (preg_match('/^\d{1,2}:\d{2}$/', $pmOut)) {
                            $pmOut .= ':00';
                        }
                        else if (!preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $pmOut)) {
                            $pmOut = formatTimeValue($pmOut);
                        }
                    }
                    
                    // Store attendance data
                    if (!empty($date)) {
                        // Process all time values to ensure proper format
                        $amIn = processTimeValue($amIn);
                        $amOut = processTimeValue($amOut);
                        $pmIn = processTimeValue($pmIn);
                        $pmOut = processTimeValue($pmOut);
                        
                        $attendanceData[] = [
                            'id' => $id,
                            'date' => $date,
                            'amIn' => $amIn,
                            'amOut' => $amOut,
                            'pmIn' => $pmIn,
                            'pmOut' => $pmOut
                        ];
                        file_put_contents(__DIR__ . '/excel_debug.log', "Direct extraction - Found attendance: ID=$id, Date=$date, AM_IN=$amIn, AM_OUT=$amOut, PM_IN=$pmIn, PM_OUT=$pmOut\n", FILE_APPEND);
                    }
                }
            }
            fclose($handle);
        }
    }
    
    // Final check if we found any data
    if (empty($employees)) {
        throw new Exception("No employee data found in the Excel file. Please check the file format.");
    }
    
    if (empty($attendanceData)) {
        throw new Exception("No attendance data found in the Excel file. Please check the file format.");
    }
    
    file_put_contents(__DIR__ . '/excel_debug.log', "Final count: " . count($employees) . " employees and " . count($attendanceData) . " attendance records\n", FILE_APPEND);
    
    file_put_contents(__DIR__ . '/excel_debug.log', "Found " . count($employees) . " employees and " . count($attendanceData) . " attendance records\n", FILE_APPEND);
    
    // Second pass: Process all employee data first
    file_put_contents(__DIR__ . '/excel_debug.log', "Second pass: Processing " . count($employees) . " employee records...\n", FILE_APPEND);
    
    foreach ($employees as $id => $data) {
        // Check if employee exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM emp_info WHERE ID = :id");
        $stmt->execute(['id' => $id]);
        $exists = $stmt->fetchColumn() > 0;
        
        if ($exists) {
            // Update existing employee
            $stmt = $pdo->prepare("UPDATE emp_info SET NAME = :name, DEPT = :dept WHERE ID = :id");
            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'dept' => $data['dept']
            ]);
            
            $updated++;
            file_put_contents(__DIR__ . '/excel_debug.log', "Updated employee: ID=$id, Name={$data['name']}, Dept={$data['dept']}\n", FILE_APPEND);
        } else {
            // Add new employee
            $stmt = $pdo->prepare("INSERT INTO emp_info (ID, NAME, DEPT) VALUES (:id, :name, :dept)");
            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'dept' => $data['dept']
            ]);
            
            $added++;
            file_put_contents(__DIR__ . '/excel_debug.log', "Added employee: ID=$id, Name={$data['name']}, Dept={$data['dept']}\n", FILE_APPEND);
        }
    }
    
    // Third pass: Process all attendance records
    file_put_contents(__DIR__ . '/excel_debug.log', "Third pass: Processing " . count($attendanceData) . " attendance records...\n", FILE_APPEND);
    
    foreach ($attendanceData as $record) {
        // Check if attendance record exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM emp_rec WHERE EMP_ID = :emp_id AND DATE = :date");
        $stmt->execute([
            'emp_id' => $record['id'],
            'date' => $record['date']
        ]);
        $exists = $stmt->fetchColumn() > 0;
        
        if ($exists) {
            // Update existing record
            $updateParts = [];
            $params = [
                'emp_id' => $record['id'],
                'date' => $record['date']
            ];
            
            if (!empty($record['amIn'])) {
                $updateParts[] = "AM_IN = :am_in";
                $params['am_in'] = $record['amIn'];
            }
            
            if (!empty($record['amOut'])) {
                $updateParts[] = "AM_OUT = :am_out";
                $params['am_out'] = $record['amOut'];
            }
            
            if (!empty($record['pmIn'])) {
                $updateParts[] = "PM_IN = :pm_in";
                $params['pm_in'] = $record['pmIn'];
            }
            
            if (!empty($record['pmOut'])) {
                $updateParts[] = "PM_OUT = :pm_out";
                $params['pm_out'] = $record['pmOut'];
            }
            
            if (!empty($updateParts)) {
                $updateQuery = "UPDATE emp_rec SET " . implode(", ", $updateParts) . " WHERE EMP_ID = :emp_id AND DATE = :date";
                $stmt = $pdo->prepare($updateQuery);
                $stmt->execute($params);
                
                $attendanceRecords++;
                file_put_contents(__DIR__ . '/excel_debug.log', "Updated attendance: ID={$record['id']}, Date={$record['date']}\n", FILE_APPEND);
            }
        } else {
            // Prepare columns and values for insert
            $columns = ["EMP_ID", "DATE"];
            $placeholders = [":emp_id", ":date"];
            $params = [
                'emp_id' => $record['id'],
                'date' => $record['date']
            ];
            
            // Only include non-empty values
            if (!empty($record['amIn'])) {
                $columns[] = "AM_IN";
                $placeholders[] = ":am_in";
                $params['am_in'] = $record['amIn'];
            }
            
            if (!empty($record['amOut'])) {
                $columns[] = "AM_OUT";
                $placeholders[] = ":am_out";
                $params['am_out'] = $record['amOut'];
            }
            
            if (!empty($record['pmIn'])) {
                $columns[] = "PM_IN";
                $placeholders[] = ":pm_in";
                $params['pm_in'] = $record['pmIn'];
            }
            
            if (!empty($record['pmOut'])) {
                $columns[] = "PM_OUT";
                $placeholders[] = ":pm_out";
                $params['pm_out'] = $record['pmOut'];
            }
            
            // Insert new record with only non-empty values
            $insertQuery = "INSERT INTO emp_rec (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
            $stmt = $pdo->prepare($insertQuery);
            $stmt->execute($params);
            
            $attendanceRecords++;
            file_put_contents(__DIR__ . '/excel_debug.log', "Added attendance: ID={$record['id']}, Date={$record['date']}\n", FILE_APPEND);
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Import completed successfully. Employees: $updated updated, $added added. Attendance records: $attendanceRecords processed."
    ]);
    
    file_put_contents(__DIR__ . '/excel_debug.log', "Import completed. Employees: $updated updated, $added added. Attendance: $attendanceRecords records processed.\n", FILE_APPEND);
    
} catch (PDOException $e) {
    // Roll back transaction if an error occurred
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    
    file_put_contents(__DIR__ . '/excel_debug.log', "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
} catch (Exception $e) {
    // Roll back transaction if an error occurred
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
    
    file_put_contents(__DIR__ . '/excel_debug.log', "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
}
?>

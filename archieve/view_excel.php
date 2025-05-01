<?php
// Test script to read the 111_StandardReport.xlsx file and display its contents
header('Content-Type: text/html; charset=utf-8');

// Define the Excel file location
$excelFile = __DIR__ . '/111_StandardReport.xlsx';

echo "<h1>Excel File Content: 111_StandardReport.xlsx</h1>";

if (!file_exists($excelFile)) {
    echo "<p style='color:red'>Error: Excel file not found at: $excelFile</p>";
    exit;
} else {
    echo "<p style='color:green'>Excel file found at: " . realpath($excelFile) . "</p>";
}

// Initialize variables to track successful extraction
$dataExtracted = false;

// Method 1: Try COM if available (Windows)
if (class_exists('COM') && !$dataExtracted) {
    try {
        echo "<h2>Method 1: Using COM (Windows Excel)</h2>";
        
        $excel = new COM("Excel.Application");
        $excel->Visible = false;
        $workbook = $excel->Workbooks->Open(realpath($excelFile));
        
        // Display total sheets
        echo "<p>Total sheets: " . $workbook->Worksheets->Count . "</p>";
        
        // First sheet
        $sheet = $workbook->Worksheets(1);
        $usedRange = $sheet->UsedRange;
        $rows = $usedRange->Rows->Count;
        $cols = $usedRange->Columns->Count;
        
        echo "<p>Sheet 1 dimensions: $rows rows x $cols columns</p>";
        
        // Display sheet content as HTML table
        echo "<table border='1' cellpadding='3'>";
        
        // Look specifically for employees starting at row 4
        for ($r = 1; $r <= min(50, $rows); $r++) {
            echo "<tr>";
            for ($c = 1; $c <= min(10, $cols); $c++) {
                $cellValue = $sheet->Cells($r, $c)->Value;
                
                if ($r == 4 && ($cellValue == "ID" || $cellValue == "Name" || $cellValue == "Department")) {
                    echo "<td style='background-color: yellow'><strong>" . htmlspecialchars($cellValue) . "</strong></td>";
                } else if ($r >= 5 && $c <= 3) { // Highlight potential employee data
                    echo "<td style='background-color: #e0ffe0'>" . htmlspecialchars($cellValue) . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($cellValue) . "</td>";
                }
            }
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Extract employee data specifically
        echo "<h3>Extracted Employee Data:</h3>";
        echo "<table border='1' cellpadding='3'>";
        echo "<tr><th>ID</th><th>Name</th><th>Department</th></tr>";
        
        for ($r = 5; $r <= min(100, $rows); $r++) {
            $id = trim($sheet->Cells($r, 1)->Value);
            
            // Skip empty rows
            if (empty($id)) continue;
            
            $name = trim($sheet->Cells($r, 2)->Value);
            $dept = trim($sheet->Cells($r, 3)->Value);
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($id) . "</td>";
            echo "<td>" . htmlspecialchars($name) . "</td>";
            echo "<td>" . htmlspecialchars($dept) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Close workbook and quit Excel
        $workbook->Close(false);
        $excel->Quit();
        $excel = null;
        
        $dataExtracted = true;
        
    } catch (Exception $e) {
        echo "<p style='color:red'>COM Error: " . $e->getMessage() . "</p>";
    }
}

// Method 2: Basic file reading for XML content
if (!$dataExtracted) {
    echo "<h2>Method 2: Using basic file reading</h2>";
    
    $fileContent = file_get_contents($excelFile);
    
    // Check if it's an XML-based XLSX file
    if (stripos($fileContent, '<?xml') !== false) {
        // Extract text content
        preg_match_all('/<t[^>]*>([^<]+)<\/t>/', $fileContent, $matches);
        
        if (!empty($matches[1])) {
            echo "<h3>Extracted text content:</h3>";
            echo "<pre>";
            $i = 0;
            foreach ($matches[1] as $textValue) {
                echo htmlspecialchars($textValue) . " | ";
                $i++;
                if ($i % 5 == 0) echo "\n";
            }
            echo "</pre>";
            
            // Try to extract ID-Name-Department patterns
            $allText = $matches[1];
            $idIdx = array_search('ID', $allText);
            $nameIdx = array_search('Name', $allText);
            $deptIdx = array_search('Department', $allText);
            
            if ($idIdx !== false && $nameIdx !== false && $deptIdx !== false) {
                echo "<h3>Extracted Employee Data:</h3>";
                echo "<table border='1' cellpadding='3'>";
                echo "<tr><th>ID</th><th>Name</th><th>Department</th></tr>";
                
                $i = max($idIdx, $nameIdx, $deptIdx) + 1;
                
                while ($i < count($allText) - 2) {
                    $id = $allText[$i++];
                    $name = $allText[$i++];
                    $dept = $allText[$i++];
                    
                    if (is_numeric($id) || preg_match('/^\d+$/', $id)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($id) . "</td>";
                        echo "<td>" . htmlspecialchars($name) . "</td>";
                        echo "<td>" . htmlspecialchars($dept) . "</td>";
                        echo "</tr>";
                    }
                }
                
                echo "</table>";
            }
        }
    } else {
        echo "<p>File doesn't appear to be XML-based. Raw content (first 300 bytes):</p>";
        echo "<pre>" . htmlspecialchars(substr($fileContent, 0, 300)) . "...</pre>";
    }
}

// Show file info
echo "<h2>File Information:</h2>";
echo "<p>File size: " . filesize($excelFile) . " bytes</p>";
echo "<p>File modification time: " . date("Y-m-d H:i:s", filemtime($excelFile)) . "</p>";
?>

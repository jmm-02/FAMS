<?php
// Test script to read the 111_StandardReport.xlsx file and display its contents
header('Content-Type: text/html; charset=utf-8');

$excelFile = __DIR__ . '/111_StandardReport.xlsx';

echo "<h1>Excel File Content: 111_StandardReport.xlsx</h1>";

// Initialize variables to track successful extraction
$dataExtracted = false;

// Method 1: Try COM if available (Windows)
if (class_exists('COM') && !$dataExtracted) {
    try {
        echo "<h2>Method 1: Using COM (Windows Excel)</h2>";
        
        $excel = new COM("Excel.Application") or die("Failed to create Excel object");
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
        echo "<table border='1'>";
        
        // Scan for headers and data
        for ($r = 1; $r <= min(50, $rows); $r++) {
            echo "<tr>";
            for ($c = 1; $c <= min(10, $cols); $c++) {
                $cellValue = $sheet->Cells($r, $c)->Value;
                echo "<td>" . htmlspecialchars($cellValue) . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Close workbook and quit Excel
        $workbook->Close(false);
        $excel->Quit();
        $excel = null;
        
        $dataExtracted = true;
        
    } catch (Exception $e) {
        echo "<p>COM Error: " . $e->getMessage() . "</p>";
    }
}

// Method 2: Try simple file reading
if (!$dataExtracted) {
    echo "<h2>Method 2: Using basic file reading</h2>";
    
    if (file_exists($excelFile)) {
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
            }
        }
        
        // Try to show some raw content
        echo "<h3>First 1000 bytes of file:</h3>";
        echo "<pre>" . htmlspecialchars(substr($fileContent, 0, 1000)) . "</pre>";
    } else {
        echo "<p>File not found: $excelFile</p>";
    }
}

// Method 3: Try CSV conversion (if it's a simple format)
if (!$dataExtracted) {
    echo "<h2>Method 3: CSV attempt</h2>";
    
    if (($handle = fopen($excelFile, "r")) !== FALSE) {
        echo "<table border='1'>";
        $rowCount = 0;
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $rowCount < 20) {
            echo "<tr>";
            foreach ($data as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
            $rowCount++;
        }
        
        echo "</table>";
        fclose($handle);
    }
}

// Try to identify key cells
echo "<h2>Data Analysis:</h2>";
echo "<p>Looking for ID, Name, and Department headers or data...</p>";

?>

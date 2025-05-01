<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Import - FAMS Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2e7d32;
            margin-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .upload-form {
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="file"] {
            padding: 8px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        button:hover {
            background: #2e7d32;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #e8f5e9;
            border-left: 5px solid #4CAF50;
            color: #2e7d32;
        }
        .alert-danger {
            background-color: #ffebee;
            border-left: 5px solid #f44336;
            color: #c62828;
        }
        .import-results {
            margin-top: 20px;
        }
        .import-results pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .back-link {
            margin-top: 20px;
        }
        .back-link a {
            color: #4CAF50;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .instructions {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .instructions h3 {
            color: #2e7d32;
            margin-top: 0;
        }
        .instructions ul {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Excel Import - FAMS Attendance</h1>
        
        <div class="instructions">
            <h3>Instructions</h3>
            <ul>
                <li><strong>Please upload a CSV file</strong> containing attendance data.</li>
                <li>If you have an Excel file, save it as CSV format first (File → Save As → CSV).</li>
                <li>The file should have the following columns:
                    <ul>
                        <li>Column A = ID (Employee ID)</li>
                        <li>Column B = Name (Employee Name)</li>
                        <li>Column C = Department</li>
                        <li>Column D = Date (in YYYY-MM-DD format)</li>
                        <li>Column E = AM_IN time (in HH:MM:SS format)</li>
                        <li>Column F = AM_OUT time (in HH:MM:SS format)</li>
                        <li>Column G = PM_IN time (in HH:MM:SS format)</li>
                        <li>Column H = PM_OUT time (in HH:MM:SS format)</li>
                    </ul>
                </li>
                <li>The first row should be headers.</li>
                <li>New employees will be automatically added to the system.</li>
                <li>If an employee ID already exists, their information will be updated.</li>
            </ul>
        </div>

        <div class="upload-form">
            <form action="import_excel.php" method="post" enctype="multipart/form-data" id="uploadForm">
                <div class="form-group">
                    <label for="excel_file">Select CSV File:</label>
                    <input type="file" name="excel_file" id="excel_file" accept=".csv" required>
                </div>
                <div class="form-group">
                    <label>Import Options:</label>
                    <div style="margin-top: 8px;">
                        <input type="checkbox" id="update_existing" name="update_existing" value="yes">
                        <label for="update_existing">Update existing records if Name and Date match</label>
                    </div>
                </div>
                <button type="submit" class="btn-primary">Upload and Import</button>
            </form>
        </div>

        <div id="importResults" class="import-results" style="display: none;">
            <h3>Import Results</h3>
            <div id="resultContent"></div>
        </div>

        <div class="back-link">
            <a href="logs.php">&larr; Back to Attendance Logs</a>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('importResults');
            const resultContent = document.getElementById('resultContent');
            
            resultDiv.style.display = 'block';
            resultContent.innerHTML = '<div class="alert alert-success">Processing... Please wait.</div>';
            
            fetch('import_excel.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let html = `<div class="alert alert-success">${data.message}</div>`;
                    if (data.data) {
                        html += `<pre>${JSON.stringify(data.data, null, 2)}</pre>`;
                    }
                    resultContent.innerHTML = html;
                } else {
                    resultContent.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultContent.innerHTML = `<div class="alert alert-danger">Import failed. Error: ${error.message}</div>`;
            });
        });
    </script>
</body>
</html>

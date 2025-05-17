<?php
require_once 'includes/session_handler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="icon" href="assets/logo.png" type="image/x-icon">
    <link rel="manifest" href="/attendance-monitoring/manifest.json">
    <meta name="theme-color" content="#007bff">
    <meta name="description" content="Employee attendance monitoring system">
    <link rel="apple-touch-icon" href="/attendance-monitoring/assets/icons/icon-192x192.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(err => {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
    <style>
        .update-log-btn {
            background: linear-gradient(90deg,rgb(0, 106, 9) 0%,rgb(0, 159, 50) 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .update-log-btn i {
            margin-right: 8px;
        }
        .update-log-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .status-message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .file-upload-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            cursor: pointer;
        }
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .file-input-button {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 15px;
            color: #333;
            display: inline-flex;
            align-items: center;
            font-size: 14px;
        }
        .file-input-button i {
            margin-right: 8px;
        }
        .selected-file {
            margin-top: 6px;
            font-size: 14px;
            color: #666;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close-modal:hover,
        .close-modal:focus {
            color: black;
            text-decoration: none;
        }
        
        .modal-title {
            margin-top: 0;
            color: #333;
        }
        
        .modal-body {
            margin-bottom: 20px;
        }
        
        .modal-success {
            color: #3c763d;
        }
        
        .modal-error {
            color: #a94442;
        }
        
        .header {
            padding: 25px 40px;
            color: #2e7d32;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            width: 100%;
            margin: 0;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            width: 100%;
            margin: 0;
            padding: 0 20px;
            justify-content: space-between;
        }
        
        .header-left {
            display: flex;
            align-items: center;
        }
        
        .datetime-display {
            text-align: right;
            color: #2e7d32;
        }
        
        .datetime-display .date {
            font-size: 1.2em;
            font-weight: 500;
        }
        
        .datetime-display .time {
            font-size: 1.8em;
            font-weight: bold;
        }
        
        .logo {
            margin-right: 30px;
        }
        
        .logo img {
            height: 90px;
            width: auto;
        }
        
        .header-text {
            flex: 1;
        }
        
        .header-text h1 {
            margin: 0;
            font-size: 2.8em;
            font-weight: bold;
            color: #2e7d32;
        }
        
        .header-text h2 {
            margin: 8px 0 0 0;
            font-size: 1.6em;
            font-weight: normal;
            color: #43a047;
        }

        .main-content {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        .instructions {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .instructions h3 {
            color: #2e7d32;
            margin-bottom: 15px;
            font-size: 1.4em;
        }
        
        .instructions ol {
            margin-left: 20px;
            padding-left: 0;
        }
        
        .instructions li {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #333;
        }
        
        .note {
            background-color: #e8f5e9;
            border-left: 4px solid #2e7d32;
            padding: 15px;
            margin-top: 20px;
            border-radius: 0 4px 4px 0;
        }
        
        .note i {
            color: #2e7d32;
            margin-right: 8px;
        }
        
        .note p {
            margin: 0;
            color: #1b5e20;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="logo">
                        <img src="assets/logo.png" alt="Colegio de Los Baños Logo">
                    </div>
                    <div class="header-text">
                        <h1>Colegio de Los Baños</h1>
                        <h2>Fingerprint Attendance Monitoring System</h2>
                    </div>
                </div>
                <div class="datetime-display">
                    <div class="date" id="current-date"></div>
                    <div class="time" id="current-time"></div>
                </div>
            </div>
        </div>
        
        <div class="content-text">
            <h2>Welcome to Colegio de Los Baños Attendance System</h2>
            <div class="instructions">
                <h3>Quick Start Guide:</h3>
                <ol>
                    <li><strong>Fingerprint Registration:</strong> Place your finger on the scanner to record attendance</li>
                    <li><strong>Time In/Out:</strong> Scan your fingerprint at the start and end of your shift</li>
                    <li><strong>Data Management:</strong> Use the Excel import feature below to update employee records</li>
                </ol>
                
                <div class="note">
                    <p><i class="fas fa-info-circle"></i> <strong>Important:</strong> Complete all daily attendance records before uploading the Excel file. This ensures accurate and complete attendance data for the entire day.</p>
                </div>
            </div>
            <form action="import_excel.php" method="post" enctype="multipart/form-data">
            <div class="file-upload-container">
                <div class="file-input-wrapper">
                    <label class="file-input-button">
                        <i class="fas fa-file-excel"></i> Select Excel/CSV File
                        <input type="file" id="excelFileInput" name="excel_file" accept=".xls,.xlsx,.csv" />
                    </label>
                </div>
                <div id="selectedFileName" class="selected-file">No file selected</div>
                <button type="submit" id="updateLogBtn" class="update-log-btn" disabled>
                    <i class="fas fa-sync-alt"></i> Update Employee Data
                </button>
            </div>
            <div id="statusMessage" class="status-message"></div>
        </div>
    </div>
    
    <!-- Modal Dialog for Import Results -->
    <div id="resultModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3 class="modal-title">Import Results</h3>
            <div id="modalBody" class="modal-body"></div>
        </div>
    </div>

    <script>
        // Update date and time
        function updateDateTime() {
            const now = new Date();
            
            // Format date
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', options);
            
            // Format time
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: true 
            });
        }

        // Update immediately and then every second
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Handle file selection
        document.getElementById('excelFileInput').addEventListener('change', function(e) {
            const fileName = e.target.files.length ? e.target.files[0].name : 'No file selected';
            document.getElementById('selectedFileName').textContent = fileName;
            document.getElementById('updateLogBtn').disabled = !e.target.files.length;
        });
        
        // Handle upload button click
        document.getElementById('updateLogBtn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission
            const fileInput = document.getElementById('excelFileInput');
            const statusMessage = document.getElementById('statusMessage');
            const modal = document.getElementById('resultModal');
            const modalBody = document.getElementById('modalBody');
            
            // Get the form element
            const form = this.closest('form');
            const formAction = form.getAttribute('action');
            
            // Show loading message
            statusMessage.textContent = "Uploading and processing file...";
            statusMessage.className = "status-message";
            statusMessage.style.display = "block";
            
            // Create FormData from the form
            const formData = new FormData(form);
            
            // Send the request
            fetch(formAction, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide status message
                statusMessage.style.display = "none";
                
                // Update modal content
                if (data.success) {
                    modalBody.innerHTML = `<p class="modal-success">${data.message}</p>`;
                } else {
                    modalBody.innerHTML = `<p class="modal-error">Error: ${data.message}</p>`;
                }
                
                // Show the modal
                modal.style.display = "block";
            })
            .catch(error => {
                // Hide status message
                statusMessage.style.display = "none";
                
                // Show error in modal
                modalBody.innerHTML = `<p class="modal-error">Error: ${error.message}</p>`;
                modal.style.display = "block";
            });
        });
        
        // Close modal when clicking the X
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('resultModal').style.display = "none";
        });
        
        // Close modal when clicking outside of it
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('resultModal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>
</body>
</html>
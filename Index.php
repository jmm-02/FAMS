<?php
require_once 'includes/session_handler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .update-log-btn {
            background: linear-gradient(90deg, #5c8df6 0%, #6fc8fb 100%);
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
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content">
        <div class="header-banner">
            <img src="assets/wall.png" alt="Colegio de Los Baños Logo">
        </div>
        
        <div class="content-text">
            <h2>The fingerprint attendance monitoring system for Colegio de Los Baños provides a user-friendly interface designed specifically for tracking employee attendance. It features real-time updates on faculty and staff attendance, including the total number of present and absent. A live feed displays names, and timestamps for time-in and time-out records, ensuring efficient monitoring.</h2>
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
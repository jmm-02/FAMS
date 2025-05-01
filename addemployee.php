<?php
// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "famsattendance");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if ID already exists
    $check_id = $conn->prepare("SELECT ID FROM EMP_INFO WHERE ID = ?");
    $check_id->bind_param("s", $_POST['emp_id']);
    $check_id->execute();
    $result = $check_id->get_result();

    if ($result->num_rows > 0) {
        // Construct the redirect URL with all form data
        $redirect_url = "addemployee.php?status=exists" .
            "&id=" . urlencode($_POST['emp_id']) .
            "&first_name=" . urlencode($_POST['first_name']) .
            "&last_name=" . urlencode($_POST['last_name']) .
            "&position=" . urlencode($_POST['position']) .
            "&pin_code=" . urlencode($_POST['pin_code']) .
            "&emp_status=" . urlencode($_POST['status']);
        
        header("Location: " . $redirect_url);
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Debug output
        error_log("Attempting to insert EMP_ID: " . $_POST['emp_id']);
        
        // Insert into EMP_INFO
        $stmt1 = $conn->prepare("INSERT INTO EMP_INFO (ID, FIRST_NAME, LAST_NAME, STATUS) VALUES (?, ?, ?, ?)");
        $stmt1->bind_param("ssss", $_POST['emp_id'], $_POST['first_name'], $_POST['last_name'], $_POST['status']);
        $result1 = $stmt1->execute();
        
        if (!$result1) {
            throw new Exception("Failed to insert into EMP_INFO: " . $stmt1->error);
        }
        
        $emp_id = $_POST['emp_id'];
        
        // Insert into EMP_POSITION
        $stmt2 = $conn->prepare("INSERT INTO EMP_POSITION (EMP_ID, POSITION) VALUES (?, ?)");
        $stmt2->bind_param("ss", $emp_id, $_POST['position']);
        $result2 = $stmt2->execute();
        
        if (!$result2) {
            throw new Exception("Failed to insert into EMP_POSITION: " . $stmt2->error);
        }
        
        // Insert into PASS_KEY
        $stmt3 = $conn->prepare("INSERT INTO PASS_KEY (EMP_ID, PIN_CODE) VALUES (?, ?)");
        $stmt3->bind_param("ss", $emp_id, $_POST['pin_code']);
        $result3 = $stmt3->execute();
        
        if (!$result3) {
            throw new Exception("Failed to insert into PASS_KEY: " . $stmt3->error);
        }
        
        // If everything is successful, commit and redirect
        $conn->commit();
        header("Location: employeeinfo.php");
        exit();
        
    } catch (Exception $e) {
        // If there's an error, rollback and redirect with error
        $conn->rollback();
        header("Location: addemployee.php?status=error&message=" . urlencode($e->getMessage()));
        exit();
    }

    $conn->close();
}

// Add this right after your form's PHP processing code, before the DOCTYPE
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Employee has been successfully added.',
                confirmButtonColor: '#006633',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                },
                customClass: {
                    popup: 'animated-popup'
                }
            });
        });
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
        }

        /* Professional Table Styles */
        .logs-table {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .logs-table table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 14px;
        }

        .logs-table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            padding: 16px;
            text-align: left;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
        }

        .logs-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
            color: #4a5568;
        }

        .logs-table tbody tr:hover {
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .logs-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Badges */
        .status-present, .status-absent {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            display: inline-block;
            min-width: 100px;
        }

        .status-present {
            background-color: #e6f4ea;
            color: #1e7e34;
        }

        .status-absent {
            background-color: #fde8e8;
            color: #c53030;
        }

        /* Employee ID Style */
        .emp-id {
            font-family: 'Courier New', monospace;
            color: #6b7280;
            font-weight: 500;
        }

        /* Timestamp Style */
        .timestamp {
            font-family: 'Roboto Mono', monospace;
            color: #4a5568;
        }

        /* Search and Filter Section */
        .logs-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box, .filter-select, .clear-filters {
            padding: 10px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #4a5568;
            background-color: white;
            transition: all 0.2s;
        }

        .clear-filters {
            cursor: pointer;
            background-color: #f8f9fa;
        }

        .clear-filters:hover {
            background-color: #e9ecef;
        }

        .search-box:focus, .filter-select:focus {
            outline: none;
            border-color: #006633;
            box-shadow: 0 0 0 3px rgba(0, 102, 51, 0.1);
        }

        .filter-select:hover {
            border-color: #006633;
        }

        .no-results-row td {
            background-color: #f8fafc;
            font-style: italic;
        }

        /* Table Header */
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-title {
            font-size: 1.5rem;
            color: #2d3748;
            font-weight: 600;
        }

        mark {
            background-color: #fef3c7;
            padding: 2px;
            border-radius: 2px;
        }

        .results-count {
            color: #6b7280;
            font-size: 14px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        button {
        background-color: #4CAF50; /* Green background */
        color: white; /* White text */
        border: none; /* Remove border */
        padding: 10px 20px; /* Padding around text */
        text-align: center; /* Center text */
        text-decoration: none; /* Remove underline */
        display: inline-block; /* Allow button to sit inline */
        font-size: 16px; /* Font size */
        cursor: pointer; /* Change cursor to pointer on hover */
        border-radius: 5px; /* Rounded corners */
        transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth transition */
        }

        button:hover {
            background-color: #45a049; /* Darker green on hover */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }

        /* Basic input styles */
        input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 2px solid #ccc;
        border-radius: 8px;
        outline: none;
        box-sizing: border-box;
        transition: all 0.3s ease;
        }

        /* Focus state */
        input:focus {
        border-color: #6C63FF; /* Highlight color */
        box-shadow: 0 0 8px rgba(108, 99, 255, 0.4);
        }

        /* Placeholder styling */
        input::placeholder {
        color: #aaa;
        font-style: italic;
        }

        /* Error message styling */
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-bottom: 5px;
            display: none;
            font-weight: bold;
            padding: 5px 0;
        }

        /* Input error state */
        input.error {
            border-color: #dc3545 !important;
            background-color: #fff8f8;
        }

        /* Shake animation */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.8s cubic-bezier(.36,.07,.19,.97) both;
        }

    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <!-- Main Content -->
    <div class="main-content">
        <div class="logs-table">
            <div class="table-header">
                <h2 class="table-title">Add Employee</h2>
                <div class="results-count"></div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Position</th>
                        <th>Pin</th>
                        <th>Active Status</th>
                        <th></th>
                    </tr>
                </thead>
                <form action="addemployee.php" method="POST" onsubmit="return confirmSubmission(event)">
                <tbody id="attendance-body">
                        <tr>
                            <td class="emp-id">
                                <div class="error-message">Employee ID already exists!</div>
                                <input type="text" 
                                       name="emp_id" 
                                       placeholder="Employee ID" 
                                       pattern="[A-Za-z0-9]+" 
                                       title="Please enter letters and numbers only (no spaces or special characters)"
                                       required>
                            </td>
                            <td><input type="text" name="first_name" placeholder="Firstname" required></td>
                            <td><input type="text" name="last_name" placeholder="Lastname" required></td>
                            <td>
                                <label for="position">Select Position:</label>
                                <select id="position" name="position" class="filter-select" required title="Select the employee's position">
                                    <option value="Adminitrator">Adminitrator</option>
                                    <option value="Faculty Member">Faculty Member</option>
                                    <option value="Caregiver Instructor">Caregiver Instructor</option>
                                    <option value="Part-time Faculty Member">Part-time Faculty Member</option>
                                    <option value="Other Personnel">Other Personnel</option>
                                </select>
                            </td>
                            <td><input type="number" name="pin_code" placeholder="0000" minlength="4" maxlength="4" required></td>
                            <td><select name="status" class="filter-select" required title="Select the employee's status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select></td>
                            <td><input type="submit" value="Submit"></td>
                        </tr>
                    </form>
                </tbody>
            </table>                     
        </div>
    </div>

    <script>
        const burgerMenu = document.querySelector('.burger-menu');
        const sidebar = document.querySelector('.sidebar');

        burgerMenu.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });

        // Get the elements
    const nameInput = document.getElementById('name');
    const positionSelect = document.getElementById('position');
    const clearButton = document.querySelector('.clear-filters');
    const tbody = document.getElementById('attendance-body');
    
    // Function to filter the table based on name and position
    function filterTable() {
        const nameValue = nameInput.value.toLowerCase();  // Get the search term and make it lowercase
        const positionValue = positionSelect.value;  // Get the selected position

        // Loop through each row in the table body
        const rows = tbody.getElementsByTagName('tr');
        for (let row of rows) {
            const firstName = row.cells[1].textContent.toLowerCase();
            const lastName = row.cells[2].textContent.toLowerCase();
            const position = row.cells[3].textContent;

            // Check if the row matches the search name and position filter
            const nameMatch = firstName.includes(nameValue) || lastName.includes(nameValue);
            const positionMatch = positionValue === 'all' || position === positionValue;

            // If both conditions are met, show the row, otherwise hide it
            if (nameMatch && positionMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }

        function confirmSubmission(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Confirm Submission',
                text: "Are you sure you want to submit?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
            return false; // Always return false to wait for SweetAlert
        }

    }

    // Event listener for name input field
    nameInput.addEventListener('input', filterTable);

    // Event listener for position filter dropdown
    positionSelect.addEventListener('change', filterTable);

    // Clear filters functionality
    clearButton.addEventListener('click', () => {
        nameInput.value = '';  // Clear name search
        positionSelect.value = 'all';  // Reset position filter to 'all'
        filterTable();  // Apply the clear filter
    });

    // Initial filter on page load (in case default filters need to be applied)
    window.addEventListener('load', filterTable);

    function confirmSubmission(event) {
        event.preventDefault(); // Prevent default form submission
        
        const userConfirmed = confirm("Do you want to save?");
        if (userConfirmed) {
            // If confirmed, submit the form
            event.target.submit();
        }
    }

    document.querySelector('form').addEventListener('submit', function(event) {
        const empId = document.querySelector('input[name="emp_id"]').value;
        
        // Updated validation for Employee ID to allow letters and numbers
        if (!/^[A-Za-z0-9]+$/.test(empId)) {
            alert('Employee ID must contain only letters and numbers (no spaces or special characters)');
            event.preventDefault();
            return false;
        }
        
        // Confirm submission
        if (!confirm('Do you want to save?')) {
            event.preventDefault();
            return false;
        }
    });

    // Optional: Convert input to uppercase as user types
    document.querySelector('input[name="emp_id"]').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });

    // Update the DOMContentLoaded event listener
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.get('status') === 'exists') {
            // Pre-fill the form with the submitted data
            document.querySelector('input[name="emp_id"]').value = urlParams.get('id') || '';
            document.querySelector('input[name="first_name"]').value = urlParams.get('first_name') || '';
            document.querySelector('input[name="last_name"]').value = urlParams.get('last_name') || '';
            document.querySelector('select[name="position"]').value = urlParams.get('position') || '';
            document.querySelector('input[name="pin_code"]').value = urlParams.get('pin_code') || '';
            document.querySelector('select[name="status"]').value = urlParams.get('emp_status') || 'Active';
            
            // Show SweetAlert
            Swal.fire({
                title: 'Employee ID Already Exists',
                text: 'Please use a different Employee ID',
                icon: 'error',
                confirmButtonColor: '#006633',
                confirmButtonText: 'OK'
            }).then((result) => {
                // Add error styling to the Employee ID input
                const empIdInput = document.querySelector('input[name="emp_id"]');
                empIdInput.classList.add('error');
                shakeElement(empIdInput);
                
                // Show the error message
                document.querySelector('.error-message').style.display = 'block';
            });
        }
    });

    // Add input event listener to clear error state when user starts typing new ID
    document.querySelector('input[name="emp_id"]').addEventListener('input', function() {
        const errorMessage = document.querySelector('.error-message');
        errorMessage.style.display = 'none';
        this.classList.remove('error');
    });

    // Function to shake an element
    function shakeElement(element) {
        element.classList.add('shake');
        setTimeout(() => {
            element.classList.remove('shake');
        }, 800); // Duration of shake animation
    }

    // Add event listener to the form
    document.querySelector('form').addEventListener('submit', function(event) {
        const empIdInput = document.querySelector('input[name="emp_id"]');
        
        // If the ID is empty or invalid, shake the field
        if (!empIdInput.value.trim()) {
            event.preventDefault();
            shakeElement(empIdInput);
            empIdInput.classList.add('error');
        }
    });

    // Add this to your existing window.load event listener
    window.addEventListener('load', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const empIdInput = document.querySelector('input[name="emp_id"]');
        
        if (urlParams.get('status') === 'exists') {
            shakeElement(empIdInput);
            empIdInput.classList.add('error');
        }
    });

    // Make sure you have this CSS
    const styleSheet = document.createElement('style');
    styleSheet.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.8s cubic-bezier(.36,.07,.19,.97) both;
        }

        input.error {
            border-color: #dc3545 !important;
            background-color: #fff8f8;
        }
    `;
    document.head.appendChild(styleSheet);

    </script>
</body>

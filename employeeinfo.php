<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .pin-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pin-input {
            border: none;
            background: transparent;
            font-family: monospace;
            width: 60px;
            text-align: center;
        }

        .toggle-pin {
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .toggle-pin:hover {
            color: #006633;
        }

    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="logs-table">
            <div class="table-header">
                <h2 class="table-title">Employees Info</h2>
                <div class="results-count"></div>
            </div>
            
            <div class="logs-controls">
                <input id="name" type="text" class="search-box" placeholder="Search employee...">
                <select id="postion" class="filter-select">
                    <option value="all">position</option>
                    <option value="Faculty Member">Faculty Member</option>
                    <option value="Caregiver">Caregiver</option>
                    <option value="Instructor">Instructor</option>
                    <option value="Part-time FacultyMember">Part-time Faculty Member</option>
                    <option value="Other Personnel">Other Personnel</option>
                </select>
                <button class="clear-filters">Clear Filters</button>
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
                <tbody id="attendance-body">
                    <!-- Data will be populated dynamically -->
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
    const positionSelect = document.getElementById('postion');
    const clearButton = document.querySelector('.clear-filters');
    const tbody = document.getElementById('attendance-body');
    
    // Updated fetchEmployees function with better error handling and status display
    async function fetchEmployees() {
        try {
            const response = await fetch('Fetch/fetch_employees.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const employees = await response.json();
            
            const tbody = document.getElementById('attendance-body');
            tbody.innerHTML = ''; // Clear existing content

            if (employees.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="7" class="no-results-row">No employees found</td>
                `;
                tbody.appendChild(row);
                return;
            }

            employees.forEach((emp, index) => {
                const row = document.createElement('tr');
                const status = emp.STATUS === 'Active' ? 'present' : 'absent';
                
                row.innerHTML = `
                    <td class="emp-id">${emp.emp_id}</td>
                    <td>${emp.FIRST_NAME || ''}</td>
                    <td>${emp.LAST_NAME || ''}</td>
                    <td>${emp.POSITION || ''}</td>
                    <td class="pin-cell">
                        <input type="password" value="${emp.PIN_CODE || ''}" readonly class="pin-input">
                        <i class="fas fa-eye toggle-pin"></i>
                    </td>
                    <td><span class="status-${status}">${emp.STATUS}</span></td>
                    <td><button onclick="editEmployee(${emp.emp_id})">Edit</button></td>
                `;
                tbody.appendChild(row);

                // Add click event listener for the toggle button
                const toggleBtn = row.querySelector('.toggle-pin');
                const pinInput = row.querySelector('.pin-input');
                
                toggleBtn.addEventListener('click', function() {
                    const isVisible = pinInput.type === 'text';
                    pinInput.type = isVisible ? 'password' : 'text';
                    this.className = isVisible ? 'fas fa-eye toggle-pin' : 'fas fa-eye-slash toggle-pin';
                });
            });

            // Update results count
            const resultsCount = document.querySelector('.results-count');
            resultsCount.textContent = `${employees.length} employees found`;

        } catch (error) {
            console.error('Error fetching employees:', error);
            const tbody = document.getElementById('attendance-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="no-results-row">Error loading employees. Please try again later.</td>
                </tr>
            `;
        }
    }

    // Function to handle employee editing
    function editEmployee(empId) {
        // Redirect to edit page with employee ID
        window.location.href = `editemployee.html?id=${empId}`;
    }

    // Call fetchEmployees when page loads
    window.addEventListener('load', () => {
        fetchEmployees();
        filterTable(); // Keep your existing filter functionality
    });

    // Update your existing filter function to work with dynamic content
    function filterTable() {
        const nameValue = nameInput.value.toLowerCase();
        const positionValue = positionSelect.value;

        const rows = tbody.getElementsByTagName('tr');
        for (let row of rows) {
            const firstName = row.cells[1].textContent.toLowerCase();
            const lastName = row.cells[2].textContent.toLowerCase();
            const position = row.cells[3].textContent;

            const nameMatch = firstName.includes(nameValue) || lastName.includes(nameValue);
            const positionMatch = positionValue === 'all' || position === positionValue;

            row.style.display = (nameMatch && positionMatch) ? '' : 'none';
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

    </script>
</body>
</html>
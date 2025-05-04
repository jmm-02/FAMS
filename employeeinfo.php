<?php
require_once 'includes/session_handler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information</title>
    <style>
        body {
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f7f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: -15% auto 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
        }
        h2 {
            color: #2d3a4b;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        #searchName {
            margin-bottom: 18px;
            padding: 10px 14px;
            width: 100%;
            max-width: 350px;
            border: 1px solid #d1d9e6;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        #searchName:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }
        #employeeTable {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 8px;
        }
        #employeeTable th, #employeeTable td {
            padding: 12px 10px;
            text-align: left;
        }
        #employeeTable thead {
            background: linear-gradient(90deg, #4CAF50 0%, #81C784 100%);
            color: #fff;
        }
        #employeeTable tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background 0.2s;
        }
        #employeeTable tbody tr:hover {
            background: #e8f5e9;
        }
        #employeeTable td {
            color: #2d3a4b;
        }
        .toggle-pin-btn {
            margin-left: 6px;
            padding: 2px 7px;
            border-radius: 3px;
            border: 1px solid #d1d9e6;
            background: #f0f7f0;
            color: #2d3a4b;
            cursor: pointer;
            font-size: 1em;
            line-height: 1.1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 26px;
            width: 28px;
        }
        .toggle-pin-btn svg {
            width: 18px;
            height: 18px;
            vertical-align: middle;
            fill: #4CAF50;
            transition: fill 0.2s;
        }
        .toggle-pin-btn:active, .toggle-pin-btn:focus {
            background: #c8e6c9;
            outline: none;
        }
        
        /* Status toggle button styles */
        .toggle-status-btn {
            margin-left: 8px;
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #d1d9e6;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.2s;
        }
        
        .toggle-status-btn[data-current-status="Active"] {
            background: #e8f5e9;
            color: #2d3a4b;
            border-color: #4CAF50;
        }
        
        .toggle-status-btn[data-current-status="Inactive"] {
            background: #f5f5f5;
            color: #666;
            border-color: #ccc;
        }
        
        .toggle-status-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        @media (max-width: 700px) {
            .container {
                padding: 12px 2vw 18px 2vw;
            }
            #employeeTable th, #employeeTable td {
                padding: 8px 4px;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h2>Employee Information</h2>
        <input type="text" id="searchName" placeholder="Search by name...">
        <table id="employeeTable" border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Employee rows will be inserted here -->
            </tbody>
        </table>

    </div>
    <script>
    let allEmployees = [];
    function renderEmployees(data, filter = '') {
        const tbody = document.querySelector('#employeeTable tbody');
        tbody.innerHTML = '';
        let filtered = data;
        if (filter) {
            const search = filter.toLowerCase();
            filtered = data.filter(emp =>
                (emp.Name && emp.Name.toLowerCase().includes(search))
            );
        }
        if (filtered.length > 0) {
            filtered.forEach((emp, idx) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${emp.emp_id || ''}</td>
                    <td>${emp.Name || ''}</td>
                    <td>${emp.department || ''}</td>
                    <td>${emp.status || ''}</td>
                    <td>
                        <button type="button" class="view-records-btn" data-emp-id="${emp.emp_id}">View Records</button>
                        <button type="button" class="toggle-status-btn" data-emp-id="${emp.emp_id}" data-current-status="${emp.status || 'Inactive'}">
                            ${emp.status === 'Active' ? 'Set Inactive' : 'Set Active'}
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="5">No employees found.</td>';
            tbody.appendChild(row);
        }
        // Add event listeners for view records buttons
        tbody.querySelectorAll('.view-records-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const empId = this.getAttribute('data-emp-id');
                window.location.href = `employee_records.php?emp_id=${empId}`;
            });
        });
        
        // Add event listeners for toggle status buttons
        tbody.querySelectorAll('.toggle-status-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const empId = this.getAttribute('data-emp-id');
                const currentStatus = this.getAttribute('data-current-status');
                const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
                
                if (confirm(`Are you sure you want to change this employee's status to ${newStatus}?`)) {
                    updateEmployeeStatus(empId, newStatus);
                }
            });
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        fetch('Fetch/fetch_employees.php')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    allEmployees = data;
                    renderEmployees(allEmployees);
                } else if (data.error) {
                    renderEmployees([], '');
                    const tbody = document.querySelector('#employeeTable tbody');
                    tbody.innerHTML = `<tr><td colspan="5">Error: ${data.error}</td></tr>`;
                }
            })
            .catch(error => {
                const tbody = document.querySelector('#employeeTable tbody');
                tbody.innerHTML = `<tr><td colspan="5">Fetch error: ${error}</td></tr>`;
            });
        document.getElementById('searchName').addEventListener('input', function() {
            renderEmployees(allEmployees, this.value);
        });
    });
    
    // Function to update employee status
    function updateEmployeeStatus(empId, newStatus) {
        const formData = new FormData();
        formData.append('emp_id', empId);
        formData.append('status', newStatus);
        
        fetch('update_employee_status.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Refresh employee data
                fetch('Fetch/fetch_employees.php')
                    .then(response => response.json())
                    .then(data => {
                        allEmployees = data;
                        renderEmployees(allEmployees);
                    });
            } else {
                alert('Status update failed: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error updating status: ' + error);
        });
    }
    </script>
</body>
</html>
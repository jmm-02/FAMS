<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Records</title>
    <style>
        body {
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6fb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: -15% auto 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
        }
        .employee-info {
            margin-bottom: 24px;
            padding: 16px;
            background: #f0f6ff;
            border-radius: 8px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .employee-info div {
            margin-bottom: 8px;
            min-width: 200px;
        }
        .employee-info strong {
            display: block;
            color: #5c8df6;
            font-size: 0.9rem;
            margin-bottom: 4px;
        }
        h2 {
            color: #2d3a4b;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        h3 {
            color: #2d3a4b;
            margin-top: 24px;
            margin-bottom: 16px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 16px;
            color: #5c8df6;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .table-header-title th {
            background: linear-gradient(90deg, #5c8df6 0%, #6fc8fb 100%);
            color: #fff;
            padding: 12px 10px;
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid #fff;
        }
        #recordsTable {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 0;
        }
        #recordsTable th, #recordsTable td {
            padding: 12px 10px;
            text-align: left;
        }
        #recordsTable thead {
            background: linear-gradient(90deg, #5c8df6 0%, #6fc8fb 100%);
            color: #fff;
        }
        #recordsTable tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background 0.2s;
        }
        #recordsTable tbody tr:hover {
            background: #f0f6ff;
        }
        #recordsTable td {
            color: #2d3a4b;
        }
        .date-filter {
            margin-bottom: 16px;
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .date-filter input {
            padding: 8px 12px;
            border: 1px solid #d1d9e6;
            border-radius: 6px;
        }
        .date-filter button {
            padding: 8px 16px;
            background: #5c8df6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .date-filter button:hover {
            background: #4a7de0;
        }
        @media (max-width: 700px) {
            .container {
                padding: 12px 2vw 18px 2vw;
            }
            #recordsTable th, #recordsTable td {
                padding: 8px 4px;
                font-size: 0.95rem;
            }
            .employee-info {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <a href="employeeinfo.php" class="back-link">← Back to Employee List</a>
        <h2>Employee Attendance Records</h2>
        
        <div id="employeeInfo" class="employee-info">
            <!-- Employee info will be inserted here -->
        </div>
        
        <h3>Attendance Records</h3>
        
        <div class="date-filter">
            <label for="startDate">From:</label>
            <input type="date" id="startDate">
            <label for="endDate">To:</label>
            <input type="date" id="endDate">
            <button id="filterBtn">Filter</button>
            <button id="resetBtn">Reset</button>
        </div>
        
        <table id="recordsTable" border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr class="table-header-title">
                    <th colspan="5">Attendance Record Details</th>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>AM In</th>
                    <th>AM Out</th>
                    <th>PM In</th>
                    <th>PM Out</th>
                </tr>
            </thead>
            <tbody>
                <!-- Records will be inserted here -->
            </tbody>
        </table>
    </div>
    
    <script>
    // Get employee ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const empId = urlParams.get('emp_id');
    
    if (!empId) {
        window.location.href = 'employeeinfo.php';
    }
    
    let allRecords = [];
    
    // Format time function
    function formatTime(timeStr) {
        if (!timeStr) return '—';
        return timeStr;
    }
    
    // Format date function
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }
    
    // Render employee info
    function renderEmployeeInfo(employee) {
        const infoDiv = document.getElementById('employeeInfo');
        infoDiv.innerHTML = `
            <div>
                <strong>Employee ID</strong>
                ${employee.emp_id || '—'}
            </div>
            <div>
                <strong>Name</strong>
                ${employee.Name || '—'}
            </div>
            <div>
                <strong>Department</strong>
                ${employee.department || '—'}
            </div>
            <div>
                <strong>Status</strong>
                ${employee.status || '—'}
            </div>
        `;
    }
    
    // Render attendance records
    function renderRecords(records, startDate = null, endDate = null) {
        const tbody = document.querySelector('#recordsTable tbody');
        tbody.innerHTML = '';
        
        // Filter records by date if needed
        let filteredRecords = records;
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            end.setHours(23, 59, 59); // Include the entire end day
            
            filteredRecords = records.filter(record => {
                const recordDate = new Date(record.date);
                return recordDate >= start && recordDate <= end;
            });
        }
        
        if (filteredRecords.length > 0) {
            filteredRecords.forEach(record => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatDate(record.date)}</td>
                    <td>${formatTime(record.am_in)}</td>
                    <td>${formatTime(record.am_out)}</td>
                    <td>${formatTime(record.pm_in)}</td>
                    <td>${formatTime(record.pm_out)}</td>
                `;
                tbody.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="5">No records found.</td>';
            tbody.appendChild(row);
        }
    }
    
    // Fetch employee data and records
    document.addEventListener('DOMContentLoaded', function() {
        fetch(`Fetch/fetch_employee_records.php?emp_id=${empId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(`Error: ${data.error}`);
                    return;
                }
                
                // Store all records
                allRecords = data.records;
                
                // Render employee info
                renderEmployeeInfo(data.employee);
                
                // Render all records initially
                renderRecords(allRecords);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                alert('Failed to fetch employee records. Please try again later.');
            });
            
        // Filter button click event
        document.getElementById('filterBtn').addEventListener('click', function() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (!startDate || !endDate) {
                alert('Please select both start and end dates.');
                return;
            }
            
            renderRecords(allRecords, startDate, endDate);
        });
        
        // Reset button click event
        document.getElementById('resetBtn').addEventListener('click', function() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            renderRecords(allRecords);
        });
    });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Records</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #e8f5e9;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            margin-right: 150px; /* Slightly to the right */
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
            overflow: visible;
            max-height: none;
            position: relative;
        }
        @media (max-width: 900px) {
            .container {
                margin: 20px auto; /* Center on mobile/tablet */
                width: 98vw;
                max-width: 99vw;
            }
        }
        .employee-info {
            margin-bottom: 24px;
            padding: 16px;
            background: #e0f2e0;
            border-radius: 8px;
            display: flex; /* Ensure it's visible */
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .employee-info div {
            margin-bottom: 8px;
            min-width: 200px;
        }
        .employee-info strong {
            display: block;
            color: #2e7d32;
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
            color: #2e7d32;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .table-header-title th {
            background: linear-gradient(90deg, #2e7d32 0%, #388e3c 100%);
            color: #fff;
            padding: 14px 12px;
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid #fff;
            position: sticky;
            top: 0;
            z-index: 11;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        #recordsTable {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 0;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border-radius: 8px;
        }
        #recordsTable th, #recordsTable td {
            padding: 14px 12px;
            text-align: left;
        }
        
        /* Fixed column widths for consistent alignment */
        #recordsTable th:nth-child(1),
        #recordsTable td:nth-child(1) {
            width: 20%;
        }
        #recordsTable th:nth-child(2),
        #recordsTable td:nth-child(2),
        #recordsTable th:nth-child(3),
        #recordsTable td:nth-child(3),
        #recordsTable th:nth-child(4),
        #recordsTable td:nth-child(4),
        #recordsTable th:nth-child(5),
        #recordsTable td:nth-child(5) {
            width: 20%;
        }
        
        #recordsTable thead {
            background: linear-gradient(90deg, #2e7d32 0%, #388e3c 100%);
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        /* Table wrapper with proper scrolling */
        .table-wrapper {
            max-height: 500px;
            overflow: auto;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        /* Both header rows */
        #recordsTable thead tr {
            width: 100%;
            table-layout: fixed;
        }
        
        /* First row in thead (title row) */
        #recordsTable thead tr:first-child th {
            background: linear-gradient(90deg, #2e7d32 0%, #388e3c 100%);
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        
        /* Second row in thead (column headers) */
        #recordsTable thead tr:nth-child(2) th {
            background: linear-gradient(90deg, #388e3c 0%, #2e7d32 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        #recordsTable th {
            font-weight: 600;
            letter-spacing: 0.3px;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        #recordsTable tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background 0.2s, transform 0.15s;
        }
        #recordsTable tbody tr:hover {
            background: #e0f2e0;
            transform: translateY(-1px);
        }
        #recordsTable tbody {
            color: #2d3a4b;
        }
        #recordsTable td {
            color: #2d3a4b;
        }
        .date-filter {
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            padding: 16px;
            background: #f8faff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 5;
            flex-shrink: 0;
        }
        .date-filter input {
            padding: 10px 14px;
            border: 1px solid #d1d9e6;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
        }
        .date-filter input:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46,125,50,0.2);
        }
        .date-filter button {
            padding: 10px 18px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .date-filter button:hover {
            background: #1b5e20;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        #resetBtn {
            background: #546e7a;
        }
        #resetBtn:hover {
            background: #455a64;
        }
        @media (max-width: 700px) {
            .container {
                padding: 12px 2vw 18px 2vw;
                height: calc(100vh - 40px); /* Smaller margin for mobile */
            }
            #recordsTable th, #recordsTable td {
                padding: 8px 4px;
                font-size: 0.95rem;
            }
            #recordsTable thead tr:nth-child(2) th {
                top: 40px; /* Adjusted for smaller header on mobile */
            }
            .employee-info {
                flex-direction: column;
            }
        }

        /* Style for the "Save" button */
        .save-note-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #4CAF50;
            background: #e8f5e9;
            color: #2d3a4b;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.2s;
        }

        .save-note-btn:hover {
            background: #c8e6c9;
            border-color: #388E3C;
            color: #1b5e20;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .save-note-btn:active {
            background: #a5d6a7;
            border-color: #2E7D32;
        }

        /* Style for late minutes */
        #recordsTable td.late-minutes {
            color: #d32f2f !important;
            font-weight: 700;
        }

        /* Style for undertime minutes */
        #recordsTable td.undertime-minutes {
            color: #d32f2f !important;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <a href="employeeinfo.php" class="back-link">← Back to Employee List</a>
        <h2>Employee Attendance Records</h2>
        
        <!-- Update the employee info section -->
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
            <button id="exportBtn">Export to Excel</button>
        </div>
        
        <div class="table-wrapper">
            <table id="recordsTable" border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr class="table-header-title">
                    <th colspan="9">Attendance Record Details</th>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>AM In</th>
                    <th>AM Out</th>
                    <th>PM In</th>
                    <th>PM Out</th>
                    <th>Late(min)</th>
                    <th>Undertime(min)</th>
                    <th>Total Time</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <!-- Records will be inserted here -->
            </tbody>
        </table>
        </div>
        

    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
    // Get employee ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const empId = urlParams.get('emp_id');
    
    if (!empId) {
        window.location.href = 'employeeinfo.php';
    }
    
    // Update sticky header positioning after content loads
    document.addEventListener('DOMContentLoaded', function() {
        // Set a small timeout to allow browser to calculate dimensions
        setTimeout(function() {
            const headerHeight = document.querySelector('#recordsTable thead tr:first-child th').offsetHeight;
            const headerElements = document.querySelectorAll('#recordsTable thead tr:nth-child(2) th');
            headerElements.forEach(function(el) {
                el.style.top = headerHeight + 'px';
            });
        }, 100);
    });
    
    let allRecords = [];
    let employeeData = {};
    
    // Format time function
    function formatTime(timeStr) {
        if (!timeStr) return '—';
        
        // Split the time string into hours and minutes
        const [hours, minutes] = timeStr.split(':');
        
        // Convert to 12-hour format
        let hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12;
        hour = hour ? hour : 12; // Convert 0 to 12
        
        // Return formatted time with AM/PM
        return `${hour}:${minutes} ${ampm}`;
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
        infoDiv.innerHTML = ''; // Clear existing content
        infoDiv.innerHTML = `
            <div>
                <strong>Employee ID:</strong> ${employee.emp_id || '—'}
            </div>
            <div>
                <strong>Name:</strong> ${employee.Name || '—'}
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

        // Get department from employeeData
        const department = employeeData && employeeData.department ? employeeData.department : '';

        if (filteredRecords.length > 0) {
            filteredRecords.forEach(record => {
                console.log('OB value for', record.date, ':', record.OB, typeof record.OB);
                let totalTime = computeTotalTime(record.am_in, record.am_out, record.pm_in, record.pm_out);

                const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
                if (record.OB == 1) {
                    totalTime = isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
                }

                const undertime = computeUndertime(totalTime, department);
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatDate(record.date)}</td>
                    <td>${formatTime(record.am_in)}</td>
                    <td>${formatTime(record.am_out)}</td>
                    <td>${formatTime(record.pm_in)}</td>
                    <td>${formatTime(record.pm_out)}</td>
                    <td class="${(Number(record.late) > 0) ? 'late-minutes' : ''}">${record.late == 0 || record.late === '0' ? '' : record.late}</td>
                    <td class="${undertime ? 'undertime-minutes' : ''}">${undertime}</td>
                    <td>${totalTime}</td>
                    <td>
                        <input type="text" class="note-input" value="${record.note || ''}" data-date="${record.date}" />
                        <button class="save-note-btn" data-emp-id="${empId}" data-date="${record.date}">Save</button>
                        ${
                          record.OB == 1
                            ? `<button class="deny-ob-btn" data-emp-id="${empId}" data-date="${record.date}">Deny OB</button>`
                            : (totalTime === '—' ? `<button class="mark-ob-btn" data-emp-id="${empId}" data-date="${record.date}">Mark OB</button>` : '')
                        }
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Calculate total minutes for all records
            let totalMinutes = 0;
            let totalLateMinutes = 0;
            let totalUndertimeMinutes = 0;

            filteredRecords.forEach(record => {
                let totalTime = computeTotalTime(record.am_in, record.am_out, record.pm_in, record.pm_out);

                const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
                if (record.OB == 1) {
                    totalTime = isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
                }

                if (totalTime !== '—') {
                    const [hours, minutes] = totalTime.replace(' hrs.', '').split(':').map(Number);
                    totalMinutes += (hours * 60) + minutes;
                }
                // Calculate total late minutes
                totalLateMinutes += Number(record.late) || 0;
                // Calculate total undertime minutes
                const undertime = computeUndertime(totalTime, department);
                totalUndertimeMinutes += Number(undertime) || 0;
            });

            const totalHours = Math.floor(totalMinutes / 60);
            const totalMins = totalMinutes % 60;
            const totalTimeStr = totalMinutes === 0 ? '—' : `${totalHours}:${totalMins.toString().padStart(2, '0')} hrs.`;
            
            const totalRow = document.createElement('tr');
            totalRow.style.fontWeight = 'bold';
            totalRow.innerHTML = `
                <td colspan="5" style="text-align:right;">Total</td>
                <td class="${totalLateMinutes ? 'late-minutes' : ''}">${totalLateMinutes || ''}</td>
                <td class="${totalUndertimeMinutes ? 'undertime-minutes' : ''}">${totalUndertimeMinutes || ''}</td>
                <td>${totalTimeStr}</td>
                <td></td>
            `;
            tbody.appendChild(totalRow);
        } else {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="9">No records found.</td>';
            tbody.appendChild(row);
        }

        // Add event listeners for "Save" buttons
        document.querySelectorAll('.save-note-btn').forEach(button => {
            button.addEventListener('click', function () {
                const empId = this.getAttribute('data-emp-id');
                const date = this.getAttribute('data-date');
                const noteInput = this.previousElementSibling.value;

                saveNoteToDatabase(empId, date, noteInput);
            });
        });

        // Add event listeners for "Mark OB" buttons
        document.querySelectorAll('.mark-ob-btn').forEach(button => {
            button.addEventListener('click', function() {
                const empId = this.getAttribute('data-emp-id');
                const date = this.getAttribute('data-date');
                markAsOB(empId, date);
            });
        });

        // Add event listeners for "Deny OB" buttons
        document.querySelectorAll('.deny-ob-btn').forEach(button => {
            button.addEventListener('click', function() {
                const empId = this.getAttribute('data-emp-id');
                const date = this.getAttribute('data-date');
                denyOB(empId, date);
            });
        });
    }
    
    // Function to export filtered data to Excel
    function exportToExcel(records, employee) {
        // Update records with the latest notes from the input fields
        document.querySelectorAll('.note-input').forEach(input => {
            const date = input.getAttribute('data-date');
            const note = input.value;
            const record = records.find(r => r.date === date);
            if (record) {
                record.note = note;
            }
        });

        if (records.length === 0) {
            alert('No data to export.');
            return;
        }

        // Prepare employee info
        const employeeInfo = [
            ['Employee ID:', employee.emp_id || '—'],
            ['Name:', employee.Name || '—'],
            [], // Empty row for spacing
        ];

        // Get department from employee
        const department = employee && employee.department ? employee.department : '';

        // Prepare attendance records
        const attendanceData = [
            ['Date', 'AM In', 'AM Out', 'PM In', 'PM Out', 'Late(min)', 'Undertime(min)', 'Total Time', 'Note'],
            ...records.map(record => {
                let totalTime = computeTotalTime(record.am_in, record.am_out, record.pm_in, record.pm_out);
                const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
                if (record.OB == 1) {
                    totalTime = isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
                }
                const undertime = computeUndertime(totalTime, department);
                return [
                    formatDate(record.date),
                    formatTime(record.am_in),
                    formatTime(record.am_out),
                    formatTime(record.pm_in),
                    formatTime(record.pm_out),
                    record.late == 0 || record.late === '0' ? '' : record.late,
                    undertime,
                    totalTime,
                    record.note || '—'
                ];
            })
        ];

        // Calculate totals
        let totalMinutes = 0;
        let totalLateMinutes = 0;
        let totalUndertimeMinutes = 0;

        records.forEach(record => {
            let totalTime = computeTotalTime(record.am_in, record.am_out, record.pm_in, record.pm_out);
            const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
            if (record.OB == 1) {
                totalTime = isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
            }
            if (totalTime !== '—') {
                const [hours, minutes] = totalTime.replace(' hrs.', '').split(':').map(Number);
                totalMinutes += (hours * 60) + minutes;
            }
            // Calculate total late minutes
            totalLateMinutes += Number(record.late) || 0;
            // Calculate total undertime minutes
            const undertime = computeUndertime(totalTime, department);
            totalUndertimeMinutes += Number(undertime) || 0;
        });

        const totalHours = Math.floor(totalMinutes / 60);
        const totalMins = totalMinutes % 60;
        const totalTimeStr = totalMinutes === 0 ? '—' : `${totalHours}:${totalMins.toString().padStart(2, '0')} hrs.`;
        
        // Add the total row to the attendanceData
        attendanceData.push([
            '', '', '', '', '', 
            totalLateMinutes || '', 
            totalUndertimeMinutes || '', 
            totalTimeStr, 
            ''
        ]);

        // Combine employee info and attendance data
        const worksheetData = [...employeeInfo, ...attendanceData];

        // Create a new workbook and worksheet
        const worksheet = XLSX.utils.aoa_to_sheet(worksheetData);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Attendance Records');

        // Export the workbook to an Excel file
        XLSX.writeFile(workbook, 'Attendance_Records.xlsx');
    }

    // Save note to database
    function saveNoteToDatabase(empId, date, note) {
        fetch('Fetch/save_note.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                emp_id: empId,
                date: date,
                note: note,
            }),
        })
            .then(response => {
                return response.text().then(text => {
                    console.log("Response Text:", text); // Debugging
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return JSON.parse(text);
                });
            })
            .then(data => {
                console.log('Save Note Response:', data); // Debugging
                if (data.success) {
                    alert('Note saved successfully!');
                } else {
                    alert(`Error saving note: ${data.error}`);
                }
            })
            .catch(error => {
                console.error('Error saving note:', error);
                alert('Failed to save note. Please try again later.');
            });
    }

    // Add the markAsOB function
    function markAsOB(empId, date) {
        fetch('Fetch/mark_ob.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                emp_id: empId,
                date: date
            }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Mark OB Response:', data); // Debug log
            if (data.success) {
                alert('Successfully marked as OB!');
                // Refresh the records
                fetch(`Fetch/fetch_employee_records.php?emp_id=${empId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(`Error: ${data.error}`);
                            return;
                        }
                        allRecords = data.records;
                        renderRecords(allRecords);
                    })
                    .catch(error => {
                        console.error('Error refreshing records:', error);
                        alert('Failed to refresh records. Please refresh the page manually.');
                    });
            } else {
                alert(`Error marking as OB: ${data.error || 'Unknown error occurred'}`);
            }
        })
        .catch(error => {
            console.error('Error marking as OB:', error);
            alert(`Failed to mark as OB: ${error.message}`);
        });
    }

    // Add the denyOB function
    function denyOB(empId, date) {
        fetch('Fetch/mark_ob.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                emp_id: empId,
                date: date,
                ob_value: 0 // Set OB to 0
            }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('OB denied successfully!');
                // Refresh the records
                fetch(`Fetch/fetch_employee_records.php?emp_id=${empId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(`Error: ${data.error}`);
                            return;
                        }
                        allRecords = data.records;
                        renderRecords(allRecords);
                    });
            } else {
                alert(`Error denying OB: ${data.error || 'Unknown error occurred'}`);
            }
        })
        .catch(error => {
            console.error('Error denying OB:', error);
            alert(`Failed to deny OB: ${error.message}`);
        });
    }

    // Fetch employee data and records
    document.addEventListener('DOMContentLoaded', function() {
        fetch(`Fetch/fetch_employee_records.php?emp_id=${empId}`)
            .then(response => response.json())
            .then(data => {
                console.log(data); // Log the response to check its structure
                if (data.error) {
                    alert(`Error: ${data.error}`);
                    return;
                }
                
                // Store all records
                allRecords = data.records;
                employeeData = data.employee;
                
                // Render employee info
                renderEmployeeInfo(employeeData);
                
                // Render all records initially
                renderRecords(allRecords);
            })
            .catch(error => {
            console.error('Error fetching data:', error);
            alert('Failed to fetch employee records. Please try again later.');
            window.location.href = 'employeeinfo.php'; // Replace with your actual login URL
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

        // Add event listener to the Export button
        document.getElementById('exportBtn').addEventListener('click', function() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            // Filter records based on selected dates
            let filteredRecords = allRecords;
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                end.setHours(23, 59, 59); // Include the entire end day

                filteredRecords = allRecords.filter(record => {
                    const recordDate = new Date(record.date);
                    return recordDate >= start && recordDate <= end;
                });
            }

            // Export the filtered records with employee info
            exportToExcel(filteredRecords, employeeData);
        });
    });

    function computeTotalTime(am_in, am_out, pm_in, pm_out) {
        function toMinutes(time) {
            if (!time) return null;
            const [h, m] = time.split(':').map(Number);
            return h * 60 + m;
        }
        const amInMin = toMinutes(am_in);
        const amOutMin = toMinutes(am_out);
        const pmInMin = toMinutes(pm_in);
        const pmOutMin = toMinutes(pm_out);

        let total = 0;

        // Case 1: All four times present
        if (
            amInMin !== null && amOutMin !== null &&
            pmInMin !== null && pmOutMin !== null
        ) {
            total = (amOutMin - amInMin) + (pmOutMin - pmInMin);
        }
        // Case 2: Only AM In and PM Out present (no AM Out, no PM In)
        else if (
            amInMin !== null && pmOutMin !== null &&
            amOutMin === null && pmInMin === null
        ) {
            total = pmOutMin - amInMin - 60; // Subtract 1 hour break
        }
        // Case 3: AM In, AM Out, and PM In present, PM Out missing
        else if (
            amInMin !== null && amOutMin !== null &&
            pmInMin !== null && pmOutMin === null
        ) {
            total = (amOutMin - amInMin) + 60; // Add 1 hour for lunch
        }
        // Case 4: Sum all valid pairs
        else {
            if (amInMin !== null && amOutMin !== null && amOutMin > amInMin) {
                total += amOutMin - amInMin;
            }
            if (pmInMin !== null && pmOutMin !== null && pmOutMin > pmInMin) {
                total += pmOutMin - pmInMin;
            }
        }

        if (total <= 0) return '—';
        const hours = Math.floor(total / 60);
        const minutes = total % 60;
        return `${hours}:${minutes.toString().padStart(2, '0')} hrs.`;
    }

    function computeUndertime(totalTime, department) {
        if (totalTime === '—') return '';
        // Remove ' hrs.' suffix if present
        const timeStr = totalTime.replace(' hrs.', '');
        const [hours, minutes] = timeStr.split(':').map(Number);
        const totalMinutes = (hours * 60) + minutes;
        // Standard working time: 12 hours for 'Other_Perssonel', else 8 hours (case-insensitive)
        const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
        const standardMinutes = isOtherPersonnel ? 720 : 480;
        // Calculate undertime
        const undertime = standardMinutes - totalMinutes;
        // Return empty string if no undertime, otherwise return the undertime in minutes
        return undertime <= 0 ? '' : undertime.toString();
    }
    </script>
</body>
</html>

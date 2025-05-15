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
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
            position: relative;
        }
        @media (max-width: 900px) {
            .container {
                margin: 20px auto; /* Center on mobile/tablet */
                width: 98vw;
                max-width: 99vw;
            }
        }
        .parent {
            display: grid;
            place-items: center;
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
            background-color: #ffebee !important; /* Light red background */
            color: #d32f2f !important;
            font-weight: 700;
        }

        /* Style for undertime minutes */
        #recordsTable td.undertime-minutes {
            background-color: #ffebee !important; /* Light red background */
            color: #d32f2f !important;
            font-weight: 700;
        }

        /* Style for Mark OB button */
        .mark-ob-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #2196F3;
            background: #E3F2FD;
            color: #1976D2;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: 8px;
        }

        .mark-ob-btn:hover {
            background: #BBDEFB;
            border-color: #1976D2;
            color: #1565C0;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .mark-ob-btn:active {
            background: #90CAF9;
            border-color: #1565C0;
        }

        /* Style for Deny OB button */
        .deny-ob-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #F44336;
            background: #FFEBEE;
            color: #D32F2F;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: 8px;
        }

        .deny-ob-btn:hover {
            background: #FFCDD2;
            border-color: #D32F2F;
            color: #B71C1C;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .deny-ob-btn:active {
            background: #EF9A9A;
            border-color: #B71C1C;
        }

        /* Style for Mark SL button */
        .mark-sl-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #9C27B0;
            background: #F3E5F5;
            color: #7B1FA2;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: 8px;
        }

        .mark-sl-btn:hover {
            background: #E1BEE7;
            border-color: #7B1FA2;
            color: #6A1B9A;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .mark-sl-btn:active {
            background: #CE93D8;
            border-color: #6A1B9A;
        }

        /* Style for Deny SL button */
        .deny-sl-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #F44336;
            background: #FFEBEE;
            color: #D32F2F;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: 8px;
        }

        .deny-sl-btn:hover {
            background: #FFCDD2;
            border-color: #D32F2F;
            color: #B71C1C;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .deny-sl-btn:active {
            background: #EF9A9A;
            border-color: #B71C1C;
        }

        .warning-row-yellow {
            background-color: #fff9c4 !important; /* Soft yellow */
        }
        .warning-row-orange {
            background-color: #ffe0b2 !important; /* Soft orange */
        }
        .warning-row-red {
            background-color: #ffebee !important; /* Soft red */
        }

        .warning-legend {
            margin-bottom: 10px;
            padding: 10px 15px;
            background: #f4f4f4;
            border-radius: 6px;
            font-size: 14px;
            color: #444;
        }
        .legend-box {
            display: inline-block;
            width: 18px;
            height: 18px;
            margin-right: 6px;
            border-radius: 3px;
            vertical-align: middle;
        }
        .legend-yellow {
            background: #fff9c4;
            border: 1px solid #fbc02d;
        }
        .legend-orange {
            background: #ffe0b2;
            border: 1px solid #ff9800;
        }
        .legend-red {
            background: #ffebee;
            border: 1px solid #ff0000;
        }
        /* Style for late row highlight */
        .late-row {
            background-color: #ffebee !important;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <a href="employeeinfo.php" class="back-link">← Back to Employee List</a>
        <h2>Employee Attendance Records</h2>
        <!-- Warning Legend -->
        <div class="warning-legend">
            <strong>Legend:</strong><br>
            <span class="legend-box legend-yellow">&nbsp;</span> Only one time entry, cannot compute interval<br>
            <span class="legend-box legend-orange">&nbsp;</span> Out-of-order time entries detected<br>
            <span class="legend-box legend-red">&nbsp;</span> Late
        </div>
        
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
        
        // Get day of week
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const dayOfWeek = days[date.getDay()];
        
        return `${date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        })} (${dayOfWeek})`;
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
        console.log('Records with SL values:', records);
        const tbody = document.querySelector('#recordsTable tbody');
        tbody.innerHTML = '';

        // Filter records by date if needed
        let filteredRecords = records;
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            end.setHours(23, 59, 59); // Include the entire end day

            filteredRecords = records.filter(record => {
                const recordDate = new Date(record.DATE);
                return recordDate >= start && recordDate <= end;
            });
        }

        // Get department from employeeData
        const department = employeeData && employeeData.department ? employeeData.department : '';

        if (filteredRecords.length > 0) {
            filteredRecords.forEach(record => {
                console.log('SL value for', record.DATE, ':', record.SL, typeof record.SL);
                let totalTime = computeTotalTime(record.AM_IN, record.AM_OUT, record.PM_IN, record.PM_OUT, department, record.HOLIDAY, record.OB, record.SL);

                const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
                const isJanitor = department && department.trim().toLowerCase() === 'janitor';
                if (record.OB == 1) {
                    totalTime = isOtherPersonnel ? '12:00 hrs.' : (isJanitor ? '8:00 hrs.' : '8:00 hrs.');
                }
                
                const undertime = computeUndertime(totalTime, department, record.AM_IN, record.HOLIDAY);
                
                // Use actual time entries without adjustment
                let displayAmIn = record.AM_IN;
                let displayAmOut = record.AM_OUT;
                let displayPmIn = record.PM_IN;

                // For AM Out and PM In, always display defaults if both AM In and PM Out are present (full-day attendance)
                if (record.AM_IN && record.PM_OUT) {
                    displayAmOut = '12:00';
                    displayPmIn = '13:00';
                }

                // Build an array of present times
                const times = [];
                if (record.AM_IN) times.push(toMinutes(record.AM_IN));
                if (record.AM_OUT) times.push(toMinutes(record.AM_OUT));
                if (record.PM_IN) times.push(toMinutes(record.PM_IN));
                if (record.PM_OUT) times.push(toMinutes(record.PM_OUT));

                let hasSingleEntry = times.length === 1;
                let outOfOrder = false;
                let validIntervals = 0;
                for (let i = 0; i < times.length - 1; i++) {
                    if (times[i+1] < times[i]) outOfOrder = true;
                    if (times[i+1] > times[i]) validIntervals++;
                }

                // Calculate late based on base time: 8:00 for regular, 6:00 for Other_Personnel and Janitor
                let baseHour = isOtherPersonnel || isJanitor ? 6 : 8;
                let baseMinute = 0;
                let lateMinutes = 0;
                // Only compute late if more than one time entry, no out-of-order, and at least one valid interval, and not SL, and not holiday
                if (!hasSingleEntry && !outOfOrder && validIntervals > 0 && record.AM_IN && record.SL != 1 && record.HOLIDAY != 1) {
                    const [h, m] = record.AM_IN.split(':').map(Number);
                    if (h > baseHour || (h === baseHour && m > baseMinute)) {
                        lateMinutes = (h - baseHour) * 60 + (m - baseMinute);
                    } else {
                        lateMinutes = 0;
                    }
                } else {
                    lateMinutes = 0;
                }

                let rowClass = '';
                if (hasSingleEntry) {
                    rowClass = 'warning-row-yellow';
                } else if (outOfOrder || (totalTime === '—' && times.length > 0)) {
                    rowClass = 'warning-row-orange';
                } else if (lateMinutes > 0 && record.OB != 1 && record.SL != 1) {
                    rowClass = 'late-row';
                }

                // Prepare remarks
                let remarks = '';
                if (record.NOTE) {
                    remarks = record.NOTE;
                }

                // Do not add out-of-order message to remarks
                let remarksText = remarks;

                const row = document.createElement('tr');
                row.className = rowClass;
                row.innerHTML = `
                    <td>${formatDate(record.DATE)}</td>
                    <td>${formatTime(displayAmIn)}</td>
                    <td>${formatTime(displayAmOut)}</td>
                    <td>${formatTime(displayPmIn)}</td>
                    <td>${formatTime(record.PM_OUT)}</td>
                    <td class="${(!hasSingleEntry && !outOfOrder && validIntervals > 0 && lateMinutes > 0 && record.OB != 1 && record.SL != 1) ? 'late-minutes' : ''}">${(!hasSingleEntry && !outOfOrder && validIntervals > 0 && record.OB != 1 && record.SL != 1 && lateMinutes > 0) ? `${Math.floor(lateMinutes/60)}h ${lateMinutes%60}m (${lateMinutes} mins)` : ''}</td>
                    <td class="${undertime && record.OB != 1 ? 'undertime-minutes' : ''}">${record.OB == 1 ? '' : undertime ? `${Math.floor(undertime/60)}h ${undertime%60}m (${undertime} mins)` : ''}</td>
                    <td>${totalTime}</td>
                    <td>
                        <input type="text" class="note-input" value="${record.NOTE || ''}" data-date="${record.DATE}" />
                        <button class="save-note-btn" data-emp-id="${empId}" data-date="${record.DATE}">Save</button>
                        ${
                          record.OB == 1
                            ? `<button class="deny-ob-btn" data-emp-id="${empId}" data-date="${record.DATE}">Deny OB</button>`
                            : `<button class="mark-ob-btn" data-emp-id="${empId}" data-date="${record.DATE}" ${record.SL == 1 ? 'disabled' : ''}>Mark OB</button>`
                        }
                        ${
                          record.SL == 1
                            ? `<button class="deny-sl-btn" data-emp-id="${empId}" data-date="${record.DATE}" data-sl-value="0">Deny SL</button>`
                            : `<button class="mark-sl-btn" data-emp-id="${empId}" data-date="${record.DATE}" data-sl-value="1" ${record.OB == 1 ? 'disabled' : ''}>Mark SL</button>`
                        }
                        ${
                          record.HOLIDAY == 1 
                            ? `<span class="holiday-tag" style="background: #ff9800; color: white; padding: 3px 8px; border-radius: 4px; margin-left: 8px;">Holiday</span>` 
                            : ''
                        }
                        <div style="color:#ff9800;font-size:0.95em;margin-top:2px;">${remarksText}</div>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Calculate total minutes for all records
            let totalMinutes = 0;
            let totalLateMinutes = 0;
            let totalUndertimeMinutes = 0;

            filteredRecords.forEach(record => {
                let totalTime = computeTotalTime(record.AM_IN, record.AM_OUT, record.PM_IN, record.PM_OUT, department, record.HOLIDAY, record.OB, record.SL);
                const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
                if (record.OB == 1) {
                    totalTime = isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
                }
                // Recalculate outOfOrder for this record
                const times = [];
                if (record.AM_IN) times.push(toMinutes(record.AM_IN));
                if (record.AM_OUT) times.push(toMinutes(record.AM_OUT));
                if (record.PM_IN) times.push(toMinutes(record.PM_IN));
                if (record.PM_OUT) times.push(toMinutes(record.PM_OUT));
                let outOfOrder = false;
                for (let i = 0; i < times.length - 1; i++) {
                    if (times[i+1] < times[i]) outOfOrder = true;
                }
                if (totalTime !== '—') {
                    const [hours, minutes] = totalTime.replace(' hrs.', '').split(':').map(Number);
                    totalMinutes += (hours * 60) + minutes;
                }
                // Only add late and undertime if not OB, not SL, not outOfOrder
                if (record.OB != 1 && record.SL != 1 && !outOfOrder) {
                    let hasSingleEntry = times.length === 1;
                    let validIntervals = 0;
                    for (let i = 0; i < times.length - 1; i++) {
                        if (times[i+1] > times[i]) validIntervals++;
                    }
                    let lateMinutes = 0;
                    if (!hasSingleEntry && !outOfOrder && validIntervals > 0 && record.AM_IN) {
                        const [h, m] = record.AM_IN.split(':').map(Number);
                        let baseHour = isOtherPersonnel ? 6 : 8;
                        let baseMinute = 0;
                        if (h > baseHour || (h === baseHour && m > baseMinute)) {
                            lateMinutes = (h - baseHour) * 60 + (m - baseMinute);
                        }
                    }
                    totalLateMinutes += lateMinutes;
                    const undertime = computeUndertime(totalTime, department, record.AM_IN, record.HOLIDAY);
                    totalUndertimeMinutes += Number(undertime) || 0;
                }
            });

            const totalHours = Math.floor(totalMinutes / 60);
            const totalMins = totalMinutes % 60;
            const totalTimeStr = totalMinutes === 0 ? '—' : `${totalHours}:${totalMins.toString().padStart(2, '0')} hrs.`;
            
            const totalRow = document.createElement('tr');
            totalRow.style.fontWeight = 'bold';
            totalRow.innerHTML = `
                <td colspan="5" style="text-align:right;">Total</td>
                <td class="${totalLateMinutes ? 'late-minutes' : ''}">${totalLateMinutes ? `${Math.floor(totalLateMinutes/60)}h ${totalLateMinutes%60}m (${totalLateMinutes} mins)` : ''}</td>
                <td class="${totalUndertimeMinutes ? 'undertime-minutes' : ''}">${totalUndertimeMinutes ? `${Math.floor(totalUndertimeMinutes/60)}h ${totalUndertimeMinutes%60}m (${totalUndertimeMinutes} mins)` : ''}</td>
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

        // Add event listeners for "Mark SL" buttons
        document.querySelectorAll('.mark-sl-btn').forEach(button => {
            button.addEventListener('click', function() {
                const empId = this.getAttribute('data-emp-id');
                const date = this.getAttribute('data-date');
                toggleSL(empId, date, 1);
            });
        });

        // Add event listeners for "Deny SL" buttons
        document.querySelectorAll('.deny-sl-btn').forEach(button => {
            button.addEventListener('click', function() {
                const empId = this.getAttribute('data-emp-id');
                const date = this.getAttribute('data-date');
                toggleSL(empId, date, 0);
            });
        });
    }
    
    // Add the toggleSL function with fixed parameters
    function toggleSL(empId, date, newValue) {
        console.log('toggleSL called with:', empId, date, newValue);
        
        // First check if this record has OB active
        const record = allRecords.find(r => r.DATE === date);
        if (record && record.OB == 1 && newValue == 1) {
            alert('Cannot mark as SL because this record is already marked as OB!');
            return;
        }

        fetch('Fetch/mark_sl.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                emp_id: empId,
                date: date,
                sl_value: parseInt(newValue)
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
                alert(newValue === '1' ? 'Successfully marked as SL!' : 'SL denied successfully!');
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
                alert(`Error updating SL status: ${data.error || 'Unknown error occurred'}`);
            }
        })
        .catch(error => {
            console.error('Error updating SL status:', error);
            alert(`Failed to update SL status: ${error.message}`);
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
                    const recordDate = new Date(record.DATE);
                    return recordDate >= start && recordDate <= end;
                });
            }

            // Export the filtered records with employee info
            exportToExcel(filteredRecords, employeeData);
        });
    });

    function toMinutes(time) {
        if (!time) return null;
        let h, m;
        if (/AM|PM/i.test(time)) {
            // 12-hour format
            let [raw, ampm] = time.split(/\s+/);
            [h, m] = raw.split(':').map(Number);
            ampm = ampm.toUpperCase();
            if (ampm === 'PM' && h < 12) h += 12;
            if (ampm === 'AM' && h === 12) h = 0;
        } else {
            [h, m] = time.split(':').map(Number);
        }
        return h * 60 + m;
    }

    function computeTotalTime(am_in, am_out, pm_in, pm_out, department, isHoliday, isOB, isSL) {
        if (isOB == 1 || isSL == 1) {
            const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
            const isJanitor = department && department.trim().toLowerCase() === 'janitor';
            return isOtherPersonnel ? '12:00 hrs.' : (isJanitor ? '8:00 hrs.' : '8:00 hrs.');
        }
        if (isHoliday == 1) {
            // For holidays, always return fixed hours based on department
            const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
            return isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
        }

        const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
        const isJanitor = department && department.trim().toLowerCase() === 'janitor';
        
        // Set standard time based on department
        const standardTime = (isOtherPersonnel || isJanitor) ? '06:00' : '08:00';
        const standardMin = toMinutes(standardTime);
        
        // Convert time strings to minutes for calculation
        let amInMinRaw = toMinutes(am_in);
        
        // Use standard time if employee arrives earlier than their standard time
        let amInMin = (amInMinRaw !== null && amInMinRaw < standardMin) ? standardMin : amInMinRaw;
        
        const amOutMin = toMinutes(am_out);
        const pmInMin = toMinutes(pm_in);
        const pmOutMin = toMinutes(pm_out);
        let total = 0;

        // Special handling for Janitor schedule
        if (isJanitor) {
            if (amInMin !== null && amOutMin !== null && pmInMin !== null && pmOutMin !== null) {
                // All four time entries
                total = (amOutMin - amInMin) + (pmOutMin - pmInMin) - 60;
            } else if (amInMin !== null && pmOutMin !== null) {
                // Only AM In and PM Out
                total = pmOutMin - amInMin - 60;
            } else if (amInMin !== null && amOutMin === null && pmInMin !== null && pmOutMin === null) {
                // Only AM In and PM In
                total = pmInMin - amInMin - 60;
            } else if (amInMin === null && amOutMin !== null && pmInMin !== null && pmOutMin === null) {
                // Only AM Out and PM In
                if (pmInMin > amOutMin) {
                    total = pmInMin - amOutMin;
                } else {
                    total = 0;
                }
            } else {
                // Flexible: sum all valid consecutive pairs
                const times = [];
                if (amInMin !== null) times.push(amInMin);
                if (amOutMin !== null) times.push(amOutMin);
                if (pmInMin !== null) times.push(pmInMin);
                if (pmOutMin !== null) times.push(pmOutMin);
                total = 0;
                for (let i = 0; i < times.length - 1; i++) {
                    if (times[i+1] > times[i]) {
                        total += times[i+1] - times[i];
                    }
                }
            }
        } else {
            // Original logic for other employees
            if (amInMin !== null && amOutMin === null && pmInMin !== null && pmOutMin === null) {
                total = pmInMin - amInMin - 60;
            } else if (amInMin !== null && pmOutMin !== null) {
                total = pmOutMin - amInMin - 60;
            } else if (amInMin === null && amOutMin !== null && pmInMin !== null && pmOutMin === null) {
                if (pmInMin > amOutMin) {
                    total = pmInMin - amOutMin;
                } else {
                    total = 0;
                }
            } else {
                const times = [];
                if (amInMin !== null) times.push(amInMin);
                if (amOutMin !== null) times.push(amOutMin);
                if (pmInMin !== null) times.push(pmInMin);
                if (pmOutMin !== null) times.push(pmOutMin);
                total = 0;
                for (let i = 0; i < times.length - 1; i++) {
                    if (times[i+1] > times[i]) {
                        total += times[i+1] - times[i];
                    }
                }
            }
        }

        if (total <= 0) return '—';
        
        // Remove the cap on total minutes and display actual time worked
        const hours = Math.floor(total / 60);
        const minutes = total % 60;
        return `${hours}:${minutes.toString().padStart(2, '0')} hrs.`;
    }

    function computeUndertime(totalTime, department, am_in, isHoliday) {
        // If it's a holiday with no time entries (showing as —), there is no undertime
        if (isHoliday == 1 && totalTime === '—') {
            return '';
        }
        
        // If it's a holiday with time entries, calculate normally
        if (totalTime === '—') return '';
        // Remove ' hrs.' suffix if present
        const timeStr = totalTime.replace(' hrs.', '');
        const [hours, minutes] = timeStr.split(':').map(Number);
        const totalMinutes = (hours * 60) + minutes;
        // Check if employee is Other_Personnel or Janitor
        const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
        const isJanitor = department && department.trim().toLowerCase() === 'janitor';

        // Adjust base time for undertime calculation
        let baseStart = 0;
        if (isOtherPersonnel) {
            baseStart = 360; // 6:00 AM in minutes
            if (am_in) {
                const [h, m] = am_in.split(':').map(Number);
                if (h < 6) baseStart = 360; // Still 6:00 AM
                else baseStart = h * 60 + m;
            }
            // Standard working time: 12 hours (720 minutes)
            const standardMinutes = 720;
            const undertime = standardMinutes - totalMinutes;
            return undertime <= 0 ? '' : undertime.toString();
        } else if (isJanitor) {
            baseStart = 360; // 6:00 AM in minutes
            if (am_in) {
                const [h, m] = am_in.split(':').map(Number);
                if (h < 6) baseStart = 360; // Still 6:00 AM
                else baseStart = h * 60 + m;
            }
            // For Janitors: 8 hours (480 minutes) including 1-hour break
            if (totalMinutes >= 480) { // 8 hours or more
                return ''; // No undertime
            } else {
                return (480 - totalMinutes).toString(); // Calculate undertime from 8 hours
            }
        } else {
            baseStart = 480; // 8:00 AM in minutes
            if (am_in) {
                const [h, m] = am_in.split(':').map(Number);
                if (h < 8) baseStart = 480; // Still 8:00 AM
                else baseStart = h * 60 + m;
            }
            // For regular employees
            if (totalMinutes >= 480) { // 8 hours or more
                return ''; // No undertime
            } else if (totalMinutes >= 360) { // Between 6 and 8 hours
                return (480 - totalMinutes).toString(); // Calculate undertime from 8 hours
            } else { // Less than 6 hours
                return (480 - totalMinutes).toString(); // Calculate undertime from 8 hours
            }
        }
    }

    // Function to export filtered data to Excel
    function exportToExcel(records, employee) {
        // Update records with the latest notes from the input fields
        document.querySelectorAll('.note-input').forEach(input => {
            const date = input.getAttribute('data-date');
            const note = input.value;
            const record = records.find(r => r.DATE === date);
            if (record) {
                record.NOTE = note;
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
                let totalTime = computeTotalTime(record.AM_IN, record.AM_OUT, record.PM_IN, record.PM_OUT, department, record.HOLIDAY, record.OB, record.SL);
                const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
                if (record.OB == 1) {
                    totalTime = isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
                }
                const undertime = computeUndertime(totalTime, department, record.AM_IN, record.HOLIDAY);
                
                // Prepare note field with OB and SL information
                let noteText = record.NOTE || '';
                
                // Add OB and SL information to the note
                if (record.OB == 1) {
                    noteText = noteText ? noteText + ' | Official Business' : 'Official Business';
                }
                
                if (record.SL == 1) {
                    noteText = noteText ? noteText + ' | Sick Leave' : 'Sick Leave';
                }
                
                // Add Holiday information if applicable
                if (record.HOLIDAY == 1) {
                    noteText = noteText ? noteText + ' | Holiday' : 'Holiday';
                    if (record.HOLIDAY_DESC) {
                        noteText += ': ' + record.HOLIDAY_DESC;
                    }
                }
                
                return [
                    formatDate(record.DATE),
                    formatTime(record.AM_IN),
                    formatTime(record.AM_OUT),
                    formatTime(record.PM_IN),
                    formatTime(record.PM_OUT),
                    record.late == 0 || record.late === '0' ? '' : `${Math.floor(record.late/60)}h ${record.late%60}m (${record.late} mins)`,
                    undertime ? `${Math.floor(undertime/60)}h ${undertime%60}m (${undertime} mins)` : '',
                    totalTime,
                    noteText || '—'
                ];
            })
        ];

        // Calculate totals
        let totalMinutes = 0;
        let totalLateMinutes = 0;
        let totalUndertimeMinutes = 0;

        records.forEach(record => {
            let totalTime = computeTotalTime(record.AM_IN, record.AM_OUT, record.PM_IN, record.PM_OUT, department, record.HOLIDAY, record.OB, record.SL);
            const isOtherPersonnel = department && department.trim().toLowerCase() === 'other_personnel';
            if (record.OB == 1) {
                totalTime = isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
            }
            // Recalculate outOfOrder for this record
            const times = [];
            if (record.AM_IN) times.push(toMinutes(record.AM_IN));
            if (record.AM_OUT) times.push(toMinutes(record.AM_OUT));
            if (record.PM_IN) times.push(toMinutes(record.PM_IN));
            if (record.PM_OUT) times.push(toMinutes(record.PM_OUT));
            let outOfOrder = false;
            for (let i = 0; i < times.length - 1; i++) {
                if (times[i+1] < times[i]) outOfOrder = true;
            }
            if (totalTime !== '—') {
                const [hours, minutes] = totalTime.replace(' hrs.', '').split(':').map(Number);
                totalMinutes += (hours * 60) + minutes;
            }
            // Only add late and undertime if not OB, not SL, not outOfOrder
            if (record.OB != 1 && record.SL != 1 && !outOfOrder) {
                let hasSingleEntry = times.length === 1;
                let validIntervals = 0;
                for (let i = 0; i < times.length - 1; i++) {
                    if (times[i+1] > times[i]) validIntervals++;
                }
                let lateMinutes = 0;
                if (!hasSingleEntry && !outOfOrder && validIntervals > 0 && record.AM_IN) {
                    const [h, m] = record.AM_IN.split(':').map(Number);
                    let baseHour = isOtherPersonnel ? 6 : 8;
                    let baseMinute = 0;
                    if (h > baseHour || (h === baseHour && m > baseMinute)) {
                        lateMinutes = (h - baseHour) * 60 + (m - baseMinute);
                    }
                }
                totalLateMinutes += lateMinutes;
                const undertime = computeUndertime(totalTime, department, record.AM_IN, record.HOLIDAY);
                totalUndertimeMinutes += Number(undertime) || 0;
            }
        });

        const totalHours = Math.floor(totalMinutes / 60);
        const totalMins = totalMinutes % 60;
        const totalTimeStr = totalMinutes === 0 ? '—' : `${totalHours}:${totalMins.toString().padStart(2, '0')} hrs.`;
        
        // Add the total row to the attendanceData
        attendanceData.push([
            '', '', '', '', '', 
            totalLateMinutes ? `${Math.floor(totalLateMinutes/60)}h ${totalLateMinutes%60}m (${totalLateMinutes} mins)` : '', 
            totalUndertimeMinutes ? `${Math.floor(totalUndertimeMinutes/60)}h ${totalUndertimeMinutes%60}m (${totalUndertimeMinutes} mins)` : '', 
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
        // Check if this record has SL active
        const record = allRecords.find(r => r.DATE === date);
        if (record && record.SL == 1) {
            alert('Cannot mark as OB because this record is already marked as SL!');
            return;
        }

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
    </script>
</body>
</html>

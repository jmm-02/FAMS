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

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
            position: relative;
        }

        .header-banner {
            width: 100%;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .header-banner img {
            width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .content-text {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            font-size: 15px;
            line-height: 1.6;
            color: #333;
        }

        /* Remove all footer-related styles */
        .footer-decoration,
        .wave-shape,
        .abstract-shape,
        .moving-wave {
            display: none;
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

        .profile-icon {
        width: 50px; /* Size of the icon container */
        height: 50px; /* Make it a circle */
        display: flex;
        align-items: center;
        margin: 20px auto;
        justify-content: center;
        background-color: #4CAF50; /* Circle background color */
        border-radius: 50%; /* Makes it circular */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Adds shadow for depth */
        color: white; /* Icon color */
        font-size: 24px; /* Icon size */
        transition: transform 0.3s ease-in-out; /* Adds animation */
        }

        .profile-icon:hover {
            transform: scale(1.1); /* Slightly enlarge the icon on hover */
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="logs-table">
            <div class="table-header">
                <h2 class="table-title">Attendance Records</h2>
                <div class="results-count"></div>
            </div>
            
            <div class="logs-controls">
                <input id="name" type="text" class="search-box" placeholder="Search employee...">
                <input id="date" type="date" class="filter-select">
                <select id="status" class="filter-select">
                    <option value="all">All Status</option>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                </select>
                <button class="clear-filters">Clear Filters</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="attendance-body">
                    <tr>
                        <td class="emp-id">EMP001</td>
                        <td>Maria Santos Cruz</td>
                        <td>2024-03-30</td>
                        <td class="timestamp">07:45 AM</td>
                        <td class="timestamp">04:30 PM</td>
                        <td><span class="status-present">Present</span></td>
                    </tr>
                    <tr>
                        <td class="emp-id">EMP002</td>
                        <td>Juan dela Cruz</td>
                        <td>2024-03-20</td>
                        <td class="timestamp">07:55 AM</td>
                        <td class="timestamp">05:00 PM</td>
                        <td><span class="status-present">Present</span></td>
                    </tr>
                    <tr>
                        <td class="emp-id">EMP003</td>
                        <td>Ana Reyes</td>
                        <td>2024-03-20</td>
                        <td class="timestamp">-</td>
                        <td class="timestamp">-</td>
                        <td><span class="status-absent">Absent</span></td>
                    </tr>
                    <tr>
                        <td class="emp-id">EMP004</td>
                        <td>Miguel Rodriguez</td>
                        <td>2024-03-20</td>
                        <td class="timestamp">07:45 AM</td>
                        <td class="timestamp">04:55 PM</td>
                        <td><span class="status-present">Present</span></td>
                    </tr>
                    <tr>
                        <td class="emp-id">EMP005</td>
                        <td>Isabella Garcia</td>
                        <td>2024-03-20</td>
                        <td class="timestamp">08:00 AM</td>
                        <td class="timestamp">05:00 PM</td>
                        <td><span class="status-present">Present</span></td>
                    </tr>
                    <tr>
                        <td class="emp-id">EMP006</td>
                        <td>Ricardo Lim</td>
                        <td>2024-03-20</td>
                        <td class="timestamp">08:30 AM</td>
                        <td class="timestamp">05:15 PM</td>
                        <td><span class="status-present">Present</span></td>
                    </tr>
                    <tr>
                        <td class="emp-id">EMP007</td>
                        <td>Carmen Tan</td>
                        <td>2024-03-20</td>
                        <td class="timestamp">-</td>
                        <td class="timestamp">-</td>
                        <td><span class="status-absent">Absent</span></td>
                    </tr>
                    <tr>
                        <td class="emp-id">EMP008</td>
                        <td>Alex Smith</td>
                        <td>2024-03-20</td>
                        <td class="timestamp">09:00 AM</td>
                        <td class="timestamp">06:00 PM</td>
                        <td><span class="status-present">Present</span></td>
                    </tr>
                    <tr>
                        <td class="emp-id">EMP009</td>
                        <td>Jasmine Johnson</td>
                        <td>2024-03-20</td>
                        <td class="timestamp">-</td>
                        <td class="timestamp">-</td>
                        <td><span class="status-absent">Absent</span></td>
                    </tr>
                    <tr>
                        <td class="emp-id">EMP010</td>
                        <td>Liam Williams</td>
                        <td>2024-03-20</td>
                        <td class="timestamp">08:45 AM</td>
                        <td class="timestamp">05:30 PM</td>
                        <td><span class="status-present">Present</span></td>
                    </tr>
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

        document.addEventListener('DOMContentLoaded', () => {
        const nameInput = document.getElementById('name');
        const dateInput = document.getElementById('date');
        const statusInput = document.getElementById('status');
        const tableBody = document.getElementById('attendance-body');
        const rows = tableBody.getElementsByTagName('tr');
        const clearFiltersButton = document.querySelector('.clear-filters');

        const filterTable = () => {
            const nameFilter = nameInput.value.toLowerCase();
            const dateFilter = dateInput.value;
            const statusFilter = statusInput.value;

            for (let row of rows) {
                const name = row.cells[1].textContent.toLowerCase();
                const date = row.cells[2].textContent;
                const status = row.cells[5].textContent.toLowerCase();

                const matchesName = name.includes(nameFilter);
                const matchesDate = dateFilter === "" || date === dateFilter;
                const matchesStatus = statusFilter === "all" || status.includes(statusFilter);

                if (matchesName && matchesDate && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        };

        // Event listeners for filter changes
        nameInput.addEventListener('input', filterTable);
        dateInput.addEventListener('change', filterTable);
        statusInput.addEventListener('change', filterTable);

        // Clear filters function
        clearFiltersButton.addEventListener('click', () => {
            // Reset the filter inputs
            nameInput.value = '';
            dateInput.value = '';
            statusInput.value = 'all';

            // Show all rows again
            for (let row of rows) {
                row.style.display = '';
            }
        });
    });

    </script>
</body>

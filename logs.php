<?php
session_start();
require_once 'includes/session_handler.php';
require_once 'includes/db_connect.php';

// Function to convert 24-hour time to 12-hour format
function convertTo12Hour($time) {
    if (empty($time)) return '';
    return date('h:i A', strtotime($time));
}

// Handle filtering
$filter_start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$filter_end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$filter_name = isset($_GET['employee_name']) ? $_GET['employee_name'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Build query based on filters
$query = "SELECT e.ID as EMP_ID, e.Name, e.DEPT, e.STATUS, r.DATE, r.AM_IN, r.AM_OUT, r.PM_IN, r.PM_OUT, r.LATE
          FROM emp_info e 
          LEFT JOIN emp_rec r ON e.ID = r.EMP_ID 
          WHERE 1=1";

$params = [];
if ($filter_start_date && $filter_end_date) {
    $query .= " AND r.DATE BETWEEN ? AND ?";
    $params[] = $filter_start_date;
    $params[] = $filter_end_date;
}
if ($filter_name) {
    $query .= " AND e.Name LIKE ?";
    $params[] = "%$filter_name%";
}
if ($filter_status !== '') {
    if ($filter_status === 'Not Set') {
        $query .= " AND (e.STATUS IS NULL OR e.STATUS = '')";
    } else {
        $query .= " AND e.STATUS = ?";
        $params[] = $filter_status;
    }
}

$query .= " ORDER BY e.ID, r.DATE DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

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

        .logs-table thead {
            position: sticky;
            top: 90px; /* Space for the filter section */
            z-index: 99;
        }

        .logs-table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            padding: 16px;
            text-align: left;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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

        /* Filter Styles */
        .filter-container {
            position: sticky;
            top: 0;
            z-index: 100;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .filter-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .filter-button {
            background-color: #007bff;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
            flex-shrink: 0;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filter-button:hover {
            background-color: #0056b3;
        }

        .export-button {
            background-color:rgb(0, 135, 14);
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
            flex-shrink: 0;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .export-button:hover {
            background-color:rgb(17, 255, 0);
        }

        .reset-button {
            background-color: #6c757d;
            text-decoration: none;
            color: white;
        }

        .reset-button:hover {
            background-color: #5a6268;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .filter-actions {
                margin-top: 10px;
                width: 100%;
            }

            .filter-button {
                flex: 1;
            }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="logs-table">
            <div class="filter-container">
                <form method="GET" action="" class="filter-form">
                    <div class="filter-group">
                        <label class="filter-label">Employee Name</label>
                        <input type="text" name="employee_name" class="filter-input" value="<?php echo htmlspecialchars($filter_name); ?>" placeholder="Enter employee name">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Start Date</label>
                        <input type="date" name="start_date" class="filter-input" value="<?php echo htmlspecialchars($filter_start_date); ?>">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">End Date</label>
                        <input type="date" name="end_date" class="filter-input" value="<?php echo htmlspecialchars($filter_end_date); ?>">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select name="status" class="filter-input" <?php echo htmlspecialchars($filter_status); ?>>
                            <option value="">Status</option">   
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-button">Apply Filters</button>
                        <a href="logs.php" class="filter-button reset-button">Reset</a>
                        <button id="exportExcelBtn" class="export-button">Export to Excel</button>
                    </div>
                </form>
            </div>
            <div class="table-header">
                <h2 class="table-title">Attendance Records</h2>
                <div class="results-count">Total records: <?php echo count($records); ?></div>
            </div>
            


            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>AM IN</th>
                        <th>AM OUT</th>
                        <th>PM IN</th>
                        <th>PM OUT</th>
                        <th>Late(min)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['EMP_ID']); ?></td>
                        <td><?php echo htmlspecialchars($record['Name']); ?></td>
                        <td><?php echo htmlspecialchars($record['DEPT']); ?></td>
                        <td><?php echo htmlspecialchars($record['DATE']); ?></td>
                        <td><?php echo convertTo12Hour($record['AM_IN']); ?></td>
                        <td><?php echo convertTo12Hour($record['AM_OUT']); ?></td>
                        <td><?php echo convertTo12Hour($record['PM_IN']); ?></td>
                        <td><?php echo convertTo12Hour($record['PM_OUT']); ?></td>
                        <td><?php echo (isset($record['LATE']) && ($record['LATE'] === '0' || $record['LATE'] == 0)) ? '' : htmlspecialchars($record['LATE']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const exportButton = document.getElementById('exportExcelBtn');
            if (exportButton) {
                exportButton.addEventListener('click', function () {
                    // Get table data
                    const table = document.querySelector('.logs-table table');
                    if (!table) {
                        alert('Table not found!');
                        return;
                    }

                    const rows = Array.from(table.querySelectorAll('tr'));
                    const data = rows.map(row =>
                        Array.from(row.querySelectorAll('th, td')).map(cell => cell.textContent.trim())
                    );

                    // Create a worksheet and workbook
                    const worksheet = XLSX.utils.aoa_to_sheet(data);
                    const workbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(workbook, worksheet, 'Attendance Records');

                    // Export the workbook to an Excel file
                    XLSX.writeFile(workbook, 'Attendance_Records.xlsx');
                });
            } else {
                console.error('Export button not found!');
            }
        });
    </script>
</body>
</html>

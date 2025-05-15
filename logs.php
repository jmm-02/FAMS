<?php
session_start();
require_once 'includes/session_handler.php';
require_once 'includes/db_connect.php';

// Function to convert time to minutes (improved, flexible)
function toMinutes($time) {
    if (!$time) return null;
    if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM|am|pm)?$/', $time, $matches)) {
        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];
        $ampm = isset($matches[3]) ? strtoupper($matches[3]) : null;
        if ($ampm) {
            if ($ampm === 'PM' && $hours < 12) {
                $hours += 12;
            } else if ($ampm === 'AM' && $hours === 12) {
                $hours = 0;
            }
        }
        return $hours * 60 + $minutes;
    }
    $time = date('H:i', strtotime($time));
    list($h, $m) = explode(':', $time);
    return $h * 60 + $m;
}

// Function to compute total time
function computeTotalTime($am_in, $am_out, $pm_in, $pm_out, $department = null, $isHoliday = 0, $isOB = 0, $isSL = 0) {
    // If it's OB or SL, return standard time based on department
    if ($isOB == 1 || $isSL == 1) {
        $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';
        return $isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
    }
    
    // For holidays, always return standard hours based on department
    if ($isHoliday == 1) {
        $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';
        return $isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
    }

    // Convert time strings to minutes for calculation
    $amInMinRaw = toMinutes($am_in);
    
    // Set standard time based on department
    $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';
    $isJanitor = $department && strtolower(trim($department)) === 'janitor';
    $standardMin = ($isOtherPersonnel || $isJanitor) ? toMinutes('06:00') : toMinutes('08:00');
    
    // Use standard time if employee arrives earlier than their standard time
    $amInMin = ($amInMinRaw !== null && $amInMinRaw < $standardMin) ? $standardMin : $amInMinRaw;
    
    $amOutMin = toMinutes($am_out);
    $pmInMin = toMinutes($pm_in);
    $pmOutMin = toMinutes($pm_out);

    // If only AM In and PM In are present (and AM Out and PM Out are missing), subtract 1 hour for break
    if ($amInMin !== null && $amOutMin === null && $pmInMin !== null && $pmOutMin === null) {
        $total = $pmInMin - $amInMin - 60;
    }
    // Special case: Only AM IN and PM OUT are present (no AM OUT, no PM IN)
    else if ($amInMin !== null && $amOutMin === null && $pmInMin === null && $pmOutMin !== null) {
        $total = $pmOutMin - $amInMin - 60; // Deduct 1 hour for break
    }
    // Special case: Only AM OUT and PM OUT are present (no AM IN, no PM IN)
    else if ($amInMin === null && $amOutMin !== null && $pmInMin === null && $pmOutMin !== null) {
        $total = $pmOutMin - $amOutMin - 60; // Deduct 1 hour for break
    }
    // If both AM In and PM Out are present, use (PM Out - AM In) - 1hr rule
    else if ($amInMin !== null && $pmOutMin !== null) {
        $total = $pmOutMin - $amInMin - 60; // Subtract 1 hour for break
    } 
    // Special case: only AM OUT and PM IN are present
    else if ($amInMin === null && $amOutMin !== null && $pmInMin !== null && $pmOutMin === null) {
        if ($pmInMin > $amOutMin) {
            $total = $pmInMin - $amOutMin;
        } else {
            $total = 0;
        }
    }
    else {
        // Collect all non-null times in order
        $times = [];
        if ($amInMin !== null) $times[] = $amInMin;
        if ($amOutMin !== null) $times[] = $amOutMin;
        if ($pmInMin !== null) $times[] = $pmInMin;
        if ($pmOutMin !== null) $times[] = $pmOutMin;
        // Sum all valid consecutive pairs
        $total = 0;
        for ($i = 0; $i < count($times) - 1; $i++) {
            if ($times[$i+1] > $times[$i]) {
                $total += $times[$i+1] - $times[$i];
            }
        }
    }

    if ($total <= 0) return '—';

    // Remove the cap on total minutes and display actual time worked
    $hours = floor($total / 60);
    $minutes = $total % 60;
    return sprintf("%d:%02d hrs.", $hours, $minutes);
}

// Function to compute undertime
function computeUndertime($totalTime, $department, $am_in = null, $isHoliday = 0, $isOB = 0, $isSL = 0) {
    // If it's a holiday with no time entries (showing as —), there is no undertime
    if ($isHoliday == 1 && $totalTime === '—') {
        return '';
    }
    
    // If it's OB or SL, no undertime
    if ($isOB == 1 || $isSL == 1) {
        return '';
    }
    
    if ($totalTime === '—') return '';
    
    // Remove ' hrs.' suffix if present
    $timeStr = str_replace(' hrs.', '', $totalTime);
    list($hours, $minutes) = explode(':', $timeStr);
    $totalMinutes = ($hours * 60) + $minutes;
    
    // Use 12 hours (720 min) for Other_Personnel, 8 hours (480 min) for others
    $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';
    $standardMinutes = $isOtherPersonnel ? 720 : 480;
    
    // Calculate undertime
    $undertime = $standardMinutes - $totalMinutes;
    
    // Return empty string if no undertime, otherwise return formatted undertime
    if ($undertime <= 0) return '';
    
    return sprintf("%dh %dm (%d mins)", 
        floor($undertime/60), 
        $undertime%60, 
        $undertime
    );
}

// Function to convert 24-hour time to 12-hour format
function convertTo12Hour($time) {
    if (empty($time)) return '';
    return date('h:i A', strtotime($time));
}

// Function to format date with day of week
function formatDateWithDay($dateStr) {
    if (empty($dateStr)) return '';
    $timestamp = strtotime($dateStr);
    $dayOfWeek = date('l', $timestamp); // Gets the full day name
    return date('M j, Y', $timestamp) . ' (' . $dayOfWeek . ')';
}

// Function to compute late minutes
function computeLate($am_in, $department) {
    if (empty($am_in)) return '';
    
    $amInMin = toMinutes($am_in);
    
    // Set standard time based on department
    $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';
    $isJanitor = $department && strtolower(trim($department)) === 'janitor';
    $standardMin = ($isOtherPersonnel || $isJanitor) ? toMinutes('06:00') : toMinutes('08:00');
    
    if ($amInMin === null || $standardMin === null) return '';
    
    $late = $amInMin - $standardMin;
    if ($late <= 0) return '';
    
    return sprintf("%dh %dm (%d mins)", 
        floor($late/60), 
        $late%60, 
        $late
    );
}

// Handle filtering
$filter_start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$filter_end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$filter_name = isset($_GET['employee_name']) ? $_GET['employee_name'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Build query based on filters
$query = "SELECT e.ID as EMP_ID, e.Name, e.DEPT, e.STATUS, r.DATE, r.AM_IN, r.AM_OUT, r.PM_IN, r.PM_OUT, r.OB, r.note, r.HOLIDAY, r.SL, h.DESCRIPTION as HOLIDAY_DESC
          FROM emp_info e 
          LEFT JOIN emp_rec r ON e.ID = r.EMP_ID 
          LEFT JOIN holidays h ON r.DATE = h.DATE
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
    <title>Logs</title>
    <link rel="icon" href="assets/logo.png" type="image/x-icon">
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

        .warning-row {
            background-color: #fffbe6 !important;
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
        /* Ensure orange warning row always applies */
        .logs-table tr.warning-row-orange {
            background-color: #ffe0b2 !important;
        }
        .late-row {
            background-color: #ffebee !important;
        }

        .table-scroll-wrapper {
            max-height: 60vh;
            overflow-y: auto;
        }
        .logs-table thead {
            position: sticky;
            top: 0; /* Adjust if needed for filter bar height */
            z-index: 99;
            background: #f8f9fa;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="logs-table">
            <!-- Warning Legend -->
            <div class="warning-legend">
                <strong>Legend:</strong><br>
                <span class="legend-box legend-yellow"></span> Only one time entry, cannot compute interval<br>
                <span class="legend-box legend-orange"></span> Out-of-order time entries detected<br>
                <span class="legend-box legend-red"></span> Late<br>
            </div>
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
            
            <div class="table-scroll-wrapper">
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
                            <th>Undertime(min)</th>
                            <th>Total Time</th>
                            <th>Overtime</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $record): 
                            $totalTime = computeTotalTime($record['AM_IN'], $record['AM_OUT'], $record['PM_IN'], $record['PM_OUT'], $record['DEPT'], $record['HOLIDAY'], $record['OB'], $record['SL']);
                            $undertime = computeUndertime($totalTime, $record['DEPT'], $record['AM_IN'], $record['HOLIDAY'], $record['OB'], $record['SL']);
                            
                            // Calculate overtime
                            $overtime = '';
                            if ($totalTime !== '—' && $record['OB'] != 1 && $record['SL'] != 1) {
                                // Get standard working hours based on department
                                $isOtherPersonnel = $record['DEPT'] && strtolower(trim($record['DEPT'])) === 'other_personnel';
                                $standardHours = $isOtherPersonnel ? 12 : 8;
                                
                                // Convert total time to minutes
                                $timeStr = str_replace(' hrs.', '', $totalTime);
                                list($hours, $minutes) = explode(':', $timeStr);
                                $totalMinutes = ($hours * 60) + $minutes;
                                
                                // Calculate overtime
                                $standardMinutes = $standardHours * 60;
                                $overtimeMinutes = $totalMinutes - $standardMinutes;
                                
                                // Only show overtime if it's greater than 0
                                if ($overtimeMinutes > 0) {
                                    $overtime = sprintf("%dh %dm (%d mins)", 
                                        floor($overtimeMinutes/60), 
                                        $overtimeMinutes%60, 
                                        $overtimeMinutes
                                    );
                                }
                            }

                            // Prepare remarks text
                            $remarks = [];
                            if (!empty($record['note'])) {
                                $remarks[] = $record['note'];
                            }
                            if (isset($record['OB']) && $record['OB'] == 1) {
                                $remarks[] = 'Official Business';
                            }
                            if (isset($record['SL']) && $record['SL'] == 1) {
                                $remarks[] = 'Sick Leave';
                            }
                            if (isset($record['HOLIDAY']) && $record['HOLIDAY'] == 1) {
                                $remarks[] = 'Holiday' . (!empty($record['HOLIDAY_DESC']) ? ': ' . $record['HOLIDAY_DESC'] : '');
                            }

                            // --- Custom warnings and highlights ---
                            $intervalsUsed = [];
                            $outOfOrder = false;
                            $times = [];
                            $labels = [];
                            if (!empty($record['AM_IN'])) { $times[] = toMinutes($record['AM_IN']); $labels[] = 'AM IN'; }
                            if (!empty($record['AM_OUT'])) { $times[] = toMinutes($record['AM_OUT']); $labels[] = 'AM OUT'; }
                            if (!empty($record['PM_IN'])) { $times[] = toMinutes($record['PM_IN']); $labels[] = 'PM IN'; }
                            if (!empty($record['PM_OUT'])) { $times[] = toMinutes($record['PM_OUT']); $labels[] = 'PM OUT'; }
                            $hasSingleEntry = false;
                            if (count($times) == 1) {
                                $hasSingleEntry = true;
                            }
                            for ($i = 0; $i < count($times) - 1; $i++) {
                                if ($times[$i+1] > $times[$i]) {
                                    $intervalsUsed[] = $labels[$i] . ' → ' . $labels[$i+1];
                                } else if ($times[$i+1] < $times[$i]) {
                                    $outOfOrder = true;
                                }
                            }
                            // Remove warning messages from remarks
                            $remarksText = implode(' | ', $remarks);

                            // Display rule: if both AM_IN and PM_OUT are present, show AM_OUT as 12:00 and PM_IN as 13:00
                            $displayAmOut = $record['AM_OUT'];
                            $displayPmIn = $record['PM_IN'];
                            if (!empty($record['AM_IN']) && !empty($record['PM_OUT'])) {
                                $displayAmOut = '12:00';
                                $displayPmIn = '13:00';
                            }

                            // Use actual time entries without adjustment
                            $displayAmIn = $record['AM_IN'];

                            // Add warning-row class if any warning present
                            $rowClass = '';
                            if ($hasSingleEntry) {
                                $rowClass = 'warning-row-yellow';
                            } else if ($outOfOrder) {
                                $rowClass = 'warning-row-orange';
                            }
                            // Mark as out-of-order if totalTime is '—' and there is at least one log and not a single entry
                            $hasAnyLog = !empty($record['AM_IN']) || !empty($record['AM_OUT']) || !empty($record['PM_IN']) || !empty($record['PM_OUT']);
                            if ($totalTime === '—' && $hasAnyLog && !$hasSingleEntry) {
                                $rowClass = 'warning-row-orange';
                            }
                            // Compute late only if more than one time entry, no out-of-order entries, and at least one valid interval, and not OB/SL
                            $lateValue = '';
                            $validIntervals = 0;
                            for ($i = 0; $i < count($times) - 1; $i++) {
                                if ($times[$i+1] > $times[$i]) {
                                    $validIntervals++;
                                }
                            }
                            if (
                                !$hasSingleEntry && 
                                !$outOfOrder && 
                                $validIntervals > 0 && 
                                (!isset($record['OB']) || $record['OB'] != 1) && 
                                (!isset($record['SL']) || $record['SL'] != 1)
                            ) {
                                $lateValue = computeLate($record['AM_IN'], $record['DEPT']);
                            } else {
                                $lateValue = '';
                            }
                            // Highlight row if late > 0 and not OB/SL
                            $isLate = (is_numeric($lateValue) && $lateValue > 0);
                            if ($isLate && (!isset($record['OB']) || $record['OB'] != 1) && (!isset($record['SL']) || $record['SL'] != 1)) {
                                $rowClass = 'late-row';
                            }
                        ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td><?php echo htmlspecialchars($record['EMP_ID']); ?></td>
                            <td><?php echo htmlspecialchars($record['Name']); ?></td>
                            <td><?php echo htmlspecialchars($record['DEPT']); ?></td>
                            <td><?php echo formatDateWithDay($record['DATE']); ?></td>
                            <td><?php echo convertTo12Hour($displayAmIn); ?></td>
                            <td><?php echo convertTo12Hour($displayAmOut); ?></td>
                            <td><?php echo convertTo12Hour($displayPmIn); ?></td>
                            <td><?php echo convertTo12Hour($record['PM_OUT']); ?></td>
                            <td><?php echo $lateValue; ?></td>
                            <td><?php echo $undertime; ?></td>
                            <td><?php echo $totalTime; ?></td>
                            <td><?php echo $overtime; ?></td>
                            <td><?php echo htmlspecialchars($remarksText); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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

                    // Create a worksheet with better formatting
                    const worksheet = XLSX.utils.aoa_to_sheet(data);
                    
                    // Set column widths
                    const columnWidths = [
                        { wch: 15 }, // Employee ID
                        { wch: 25 }, // Name
                        { wch: 15 }, // Department
                        { wch: 15 }, // Date
                        { wch: 15 }, // AM IN
                        { wch: 15 }, // AM OUT
                        { wch: 15 }, // PM IN
                        { wch: 15 }, // PM OUT
                        { wch: 15 }, // Late
                        { wch: 15 }, // Undertime
                        { wch: 15 }, // Total Time
                        { wch: 15 }, // Overtime
                        { wch: 35 }  // Remarks
                    ];
                    worksheet['!cols'] = columnWidths;
                    
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

<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAMS - Attendance Monitoring System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #2980b9;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --text-color: #2c3e50;
            --border-radius: 6px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--box-shadow);
            position: relative;
            z-index: 100;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 50px;
            margin-right: 10px;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .logo-text span {
            color: var(--secondary-color);
            font-weight: 300;
        }
        
        .header-right {
            display: flex;
            align-items: center;
        }
        
        .date-time {
            margin-right: 20px;
            text-align: right;
            font-size: 0.9rem;
        }
        
        .date-time .date {
            font-weight: 600;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            padding-left: 20px;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .user-info .user-name {
            margin-right: 10px;
            font-weight: 600;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            background-color: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .page-title {
            background-color: var(--light-color);
            padding: 1rem 2rem;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .breadcrumb {
            display: flex;
            list-style: none;
        }
        
        .breadcrumb li {
            margin-right: 5px;
        }
        
        .breadcrumb li:after {
            content: '/';
            margin-left: 5px;
            color: #aaa;
        }
        
        .breadcrumb li:last-child:after {
            content: '';
        }
        
        .breadcrumb a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .breadcrumb .current {
            color: var(--text-color);
            font-weight: 600;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #eee;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background-color: var(--accent-color);
        }
        
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
        }
        
        .btn-success:hover {
            background-color: #219653;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-icon {
            display: inline-flex;
            align-items: center;
        }
        
        .btn-icon i {
            margin-right: 5px;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 1rem;
            }
            
            .logo {
                margin-bottom: 1rem;
            }
            
            .header-right {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .date-time {
                margin-right: 0;
                margin-bottom: 0.5rem;
                text-align: left;
            }
            
            .user-info {
                padding-left: 0;
                border-left: none;
            }
            
            .page-title {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .breadcrumb {
                margin-top: 0.5rem;
            }
            
            .container {
                padding: 0 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <div class="logo-text">FAMS <span>Attendance</span></div>
        </div>
        <div class="header-right">
            <div class="date-time">
                <div class="date" id="current-date"></div>
                <div class="time" id="current-time"></div>
            </div>
            <div class="user-info">
                <div class="user-name">Administrator</div>
                <div class="user-avatar">A</div>
            </div>
        </div>
    </header>
    
    <div class="page-title">
        <div class="title">
            <?php
            // Set page title based on current page
            if ($current_page == 'employeeinfo.php') {
                echo 'Employee Management';
            } elseif ($current_page == 'employee_records.php') {
                echo 'Employee Attendance Records';
            } elseif ($current_page == 'welcome.php') {
                echo 'Dashboard';
            } else {
                echo 'FAMS Attendance System';
            }
            ?>
        </div>
        <ul class="breadcrumb">
            <li><a href="welcome.php">Home</a></li>
            <?php
            // Set breadcrumb based on current page
            if ($current_page == 'employeeinfo.php') {
                echo '<li class="current">Employee Management</li>';
            } elseif ($current_page == 'employee_records.php') {
                echo '<li><a href="employeeinfo.php">Employee Management</a></li>';
                echo '<li class="current">Attendance Records</li>';
            } elseif ($current_page == 'welcome.php') {
                echo '<li class="current">Dashboard</li>';
            }
            ?>
        </ul>
    </div>
    
    <script>
        // Update date and time
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions);
        }
        
        // Update time every second
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-width-collapsed: 80px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            position: sticky;
            display: flex;
            min-height: 100vh;
            height: 100vh; /* Ensure the sidebar takes the full viewport height */
            overflow-y: auto; /* Enable vertical scrolling */
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: #006633;
            color: white;
            padding: 20px 0;
            transition: width 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: var(--sidebar-width-collapsed);
        }

        .sidebar.collapsed .nav-links a {
            justify-content: center;  /* Center the icon when collapsed */
            padding: 12px 0;
        }

        .sidebar.collapsed .nav-links a span.nav-text,
        .sidebar.collapsed .logout-btn span.nav-text,
        .sidebar.collapsed .profile-icon {
            display: none;  /* Hide text and profile icon */
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

        .nav-links {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;  /* Take up available space */
        }

        .nav-links a, 
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            text-decoration: none;
            padding: 12px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);  /* Top border for all items */
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: 16px;
        }

        /* General button styling for sidebar */
        button.nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            text-decoration: none;
            padding: 12px 0;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1); /* Optional: Add a border for separation */
        }

        /* Hover effect for buttons */
        button.nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Add bottom border to last nav item */
        .nav-links a:last-of-type {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Add borders to logout */
        .logout-btn {
            margin-top: auto;  /* Push to bottom */
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-icon i {
            font-size: 18px;
            margin-right: 10px; /* Add spacing between the icon and text */
        }

        .nav-icon img {
            width: 20px;
            height: 20px;
            object-fit: contain;
            margin-right: 10px; /* Add spacing between the icon and text */
        }

        /* Optional: Add consistent spacing for all nav items */
        .nav-links a,
        button.nav-link {
            margin: 0; /* Remove any extra margin */
        }

        /* Hover effect for both nav links and logout */
        .nav-links a:hover,
        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Collapsed state styles */
        .sidebar.collapsed .nav-links a,
        .sidebar.collapsed .logout-btn {
            justify-content: center;
            padding: 12px 0;
        }

        .sidebar.collapsed .nav-text {
            display: none;
        }

        /* Main Content Styles */
        .main-content,
        .container {
            margin-left: var(--sidebar-width);
            padding: 24px;
            transition: margin-left 0.3s;
        }

        .sidebar.collapsed ~ .main-content,
        .sidebar.collapsed ~ .container {
            margin-left: var(--sidebar-width-collapsed);
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

        /* Burger Menu Button Styles */
        .burger-menu {
            position: absolute;
            top: 20px;
            left: 20px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            z-index: 100;
        }

        .burger-bar {
            width: 100%;
            height: 3px;
            background-color: white;
            border-radius: 2px;
            transition: all 0.3s ease-in-out;
        }

        /* Optional: Hover effect */
        .burger-menu:hover .burger-bar {
            background-color: rgba(255, 255, 255, 0.8);
        }

        /* Updated Nav Links Styles */
        .nav-links a {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            text-decoration: none;
            padding: 12px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        button {
            display: inline-block;
            visibility: visible;
            z-index: 1000;
        }

        @media (max-width: 900px) {
            .sidebar {
                width: var(--sidebar-width-collapsed);
            }
            .main-content,
            .container {
                margin-left: var(--sidebar-width-collapsed);
                padding: 12px 2vw;
            }
        }

        .nav-links a,
        .logout-btn,
        button.nav-link {
            padding-left: 30px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Add burger menu at the top -->
        <div class="burger-menu">
            <div class="burger-bar"></div>
            <div class="burger-bar"></div>
            <div class="burger-bar"></div>
        </div>

        <div class="profile-icon">
            <i class="fas fa-user"></i>
        </div>        
        <div class="nav-links">
            <a href="index.php">
                <span class="nav-icon">
                    <i class="fas fa-home"></i>
                </span>
                <span class="nav-text">Home</span>
            </a>
            <a href="logs.php">
                <span class="nav-icon">
                    <i class="fas fa-clipboard-list"></i>
                </span>
                <span class="nav-text">Logs</span>
            </a>
            <a href="employeeinfo.php">
                <span class="nav-icon">
                    <i class="fas fa-users"></i>
                </span>
                <span class="nav-text">Employee Info</span>
            </a>
            <!-- <a href="addemployee.php">
                <span class="nav-icon">
                    <i class="fas fa-user-plus"></i>
                </span>
                <span class="nav-text">Add Employee</span>
            </a> -->

            <a href="change_password.php" class="nav-link">
                <span class="nav-icon">
                    <i class="fas fa-key"></i> <!-- Font Awesome Key Icon -->
                </span>
                <span class="nav-text">Change Password</span>
            </a>
            
            <!-- Add holiday link before the logout button -->
            <a href="holidays.php" class="nav-link">
                <div class="nav-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <span class="nav-text">Manage Holidays</span>
            </a>
            
            <!-- Logout button -->
            <form action="logout.php" method="post" style="margin-top: auto;">
                <button type="submit" class="logout-btn">
                    <div class="nav-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <span class="nav-text">Logout</span>
                </button>
            </form>
        </div>
    </div>


    <!-- Change Password Modal -->
    <div id="changePasswordModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.2); z-index:1000;">
        <form method="POST" action="change_password.php">
            <h3>Change Password</h3>
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter Current Password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter New Password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
            </div>
            <button type="submit">Change Password</button>
            <button type="button" onclick="closeChangePasswordModal()">Cancel</button>
        </form>
    </div>

    <script>
        const burgerMenu = document.querySelector('.burger-menu');
        const sidebar = document.querySelector('.sidebar');

        burgerMenu.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });

        function openChangePasswordModal() {
            document.getElementById('changePasswordModal').style.display = 'block';
        }

        function closeChangePasswordModal() {
            document.getElementById('changePasswordModal').style.display = 'none';
        }
    </script>
</body>
</html>
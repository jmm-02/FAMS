    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">FAMS <span>Attendance</span></div>
                <p>Formal Attendance Monitoring System for efficient employee time tracking and management.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="welcome.php">Dashboard</a></li>
                    <li><a href="employeeinfo.php">Employee Management</a></li>
                    <li><a href="reports.php">Reports</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p><i class="fas fa-envelope"></i> support@fams.example.com</p>
                <p><i class="fas fa-phone"></i> +1 (123) 456-7890</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> FAMS Attendance System. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem 0 0 0;
            margin-top: 3rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 0 1rem;
        }
        
        .footer-section {
            flex: 1;
            min-width: 250px;
            margin-bottom: 1.5rem;
            padding-right: 2rem;
        }
        
        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .footer-logo span {
            color: var(--secondary-color);
            font-weight: 300;
        }
        
        .footer-section h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-section h3:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: var(--secondary-color);
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 0.5rem;
        }
        
        .footer-section a {
            color: #ecf0f1;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section a:hover {
            color: var(--secondary-color);
        }
        
        .footer-section p {
            margin-bottom: 0.5rem;
        }
        
        .footer-section i {
            margin-right: 0.5rem;
            color: var(--secondary-color);
        }
        
        .footer-bottom {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            text-align: center;
            margin-top: 1rem;
        }
        
        .footer-bottom p {
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
            }
            
            .footer-section {
                padding-right: 0;
            }
        }
    </style>
</body>
</html>

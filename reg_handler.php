<?php
require_once 'includes/db_connect.php';

// Create connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $fname = trim($_POST["Fname"]);
    $lname = trim($_POST["Lname"]);
    $email = trim($_POST["email"]);
    $phone_number = trim($_POST["Phone_Number"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    // Basic validation
    if (empty($fname) || empty($lname) || empty($email) || empty($phone_number) || empty($username) || empty($password)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please fill all required fields.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.history.back();
                    });
                });
              </script>";
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Invalid email format.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.history.back();
                    });
                });
              </script>";
        exit();
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $sql = "INSERT INTO info (Fname, Lname, email, Phone_Number, username, password) 
                VALUES (:fname, :lname, :email, :phone_number, :username, :password)";
        
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Display success message with SweetAlert and redirect to login page
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registration Successful</title>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Success!",
                            text: "Registration completed successfully!",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#003300",
                            backdrop: `
                                rgba(0,80,0,0.4)
                                url("assets/logo.png")
                                center top
                                no-repeat
                            `
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "login.php";
                            }
                        });
                    });
                </script>
            </body>
            </html>';
            exit();
        }
        
    } catch(PDOException $e) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Error!',
                        text: '".str_replace("'", "\\'", $e->getMessage())."',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.history.back();
                    });
                });
              </script>";
        exit();
    }
    
    // Close connection
    $conn = null;
}
?>
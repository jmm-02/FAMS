<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Attendance Monitoring System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            text-align: center;
        }
        .offline-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        .offline-icon {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        h1 {
            color: #2e7d32;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .retry-button {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .retry-button:hover {
            background: #1b5e20;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">📶</div>
        <h1>You're Offline</h1>
        <p>It seems you've lost your internet connection. Some features may not be available while you're offline.</p>
        <p>Please check your internet connection and try again.</p>
        <button class="retry-button" onclick="window.location.reload()">Retry Connection</button>
    </div>
    <script>
        // Check for online status
        window.addEventListener('online', () => {
            window.location.reload();
        });
    </script>
</body>
</html> 
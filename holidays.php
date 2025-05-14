<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holiday Management</title>
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
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
            overflow: visible;
            max-height: none;
            position: relative;
        }
        h2 {
            color: #2d3a4b;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        .add-holiday-form {
            background: #f8faff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2d3a4b;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d9e6;
            border-radius: 6px;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            margin-right: 10px;
        }
        .btn-primary {
            background: #2e7d32;
            color: white;
        }
        .btn-danger {
            background: #d32f2f;
            color: white;
        }
        .btn-warning {
            background: #f57c00;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-primary:hover {
            background: #1b5e20;
        }
        .btn-danger:hover {
            background: #b71c1c;
        }
        .btn-warning:hover {
            background: #e65100;
        }
        .edit-form {
            display: none;
            background: #fff3e0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #ffe0b2;
        }
        .edit-form.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        th {
            background: linear-gradient(90deg, #2e7d32 0%, #388e3c 100%);
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e0f2e0;
        }
        .no-holidays {
            text-align: center;
            padding: 20px;
            color: #555;
            font-style: italic;
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
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <a href="employeeinfo.php" class="back-link">← Back to Employee List</a>
        <h2>Holiday Management</h2>
        
        <div class="add-holiday-form">
            <h3>Add New Holiday</h3>
            <div class="form-group">
                <label for="holidayDate">Date:</label>
                <input type="date" id="holidayDate" name="holidayDate">
            </div>
            <div class="form-group">
                <label for="holidayDescription">Description:</label>
                <textarea id="holidayDescription" name="holidayDescription" placeholder="Enter holiday description"></textarea>
            </div>
            <button id="addHolidayBtn" class="btn btn-primary">Add Holiday</button>
        </div>
        
        <div class="holidays-list">
            <h3>Existing Holidays</h3>
            <table id="holidaysTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Holidays will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load all existing holidays
        loadHolidays();
        
        // Add event listener for the Add Holiday button
        document.getElementById('addHolidayBtn').addEventListener('click', function() {
            addHoliday();
        });
    });
    
    // Format date for display
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }
    
    // Load all holidays
    function loadHolidays() {
        fetch('Fetch/manage_holidays.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'get_all'
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
                displayHolidays(data.holidays);
            } else {
                console.error('Error loading holidays:', data.error);
                alert(`Error loading holidays: ${data.error || 'Unknown error occurred'}`);
            }
        })
        .catch(error => {
            console.error('Error loading holidays:', error);
            alert(`Failed to load holidays: ${error.message}`);
        });
    }
    
    // Display holidays in the table
    function displayHolidays(holidays) {
        const tbody = document.querySelector('#holidaysTable tbody');
        tbody.innerHTML = '';
        
        if (holidays.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="3" class="no-holidays">No holidays found</td>';
            tbody.appendChild(row);
            return;
        }
        
        holidays.forEach(holiday => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${formatDate(holiday.DATE)}</td>
                <td>${holiday.DESCRIPTION}</td>
                <td>
                    <button class="btn btn-warning" onclick="showEditForm('${holiday.DATE}', '${holiday.DESCRIPTION}')">Edit</button>
                    <button class="btn btn-danger" onclick="removeHoliday('${holiday.DATE}')">Remove</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    
    // Show edit form for a holiday
    function showEditForm(date, description) {
        const row = event.target.closest('tr');
        const existingForm = document.querySelector('.edit-form');
        if (existingForm) {
            existingForm.remove();
        }
        
        const editForm = document.createElement('div');
        editForm.className = 'edit-form active';
        editForm.innerHTML = `
            <div class="form-group">
                <label for="editHolidayDate">Date:</label>
                <input type="date" id="editHolidayDate" value="${date}">
            </div>
            <div class="form-group">
                <label for="editHolidayDescription">Description:</label>
                <textarea id="editHolidayDescription">${description}</textarea>
            </div>
            <button class="btn btn-primary" onclick="updateHoliday('${date}')">Update</button>
            <button class="btn btn-danger" onclick="this.closest('.edit-form').remove()">Cancel</button>
        `;
        
        row.insertAdjacentElement('afterend', editForm);
    }
    
    // Update a holiday
    function updateHoliday(oldDate) {
        const newDate = document.getElementById('editHolidayDate').value;
        const description = document.getElementById('editHolidayDescription').value;
        
        if (!newDate) {
            alert('Please select a date');
            return;
        }
        
        if (!description) {
            alert('Please enter a description');
            return;
        }
        
        fetch('Fetch/manage_holidays.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update',
                oldDate: oldDate,
                newDate: newDate,
                description: description
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
                alert('Holiday updated successfully!');
                // Remove edit form
                document.querySelector('.edit-form').remove();
                // Reload holidays
                loadHolidays();
            } else {
                console.error('Error updating holiday:', data.error);
                alert(`Error updating holiday: ${data.error || 'Unknown error occurred'}`);
            }
        })
        .catch(error => {
            console.error('Error updating holiday:', error);
            alert(`Failed to update holiday: ${error.message}`);
        });
    }
    
    // Add a new holiday
    function addHoliday() {
        const date = document.getElementById('holidayDate').value;
        const description = document.getElementById('holidayDescription').value;
        
        if (!date) {
            alert('Please select a date');
            return;
        }
        
        if (!description) {
            alert('Please enter a description');
            return;
        }
        
        fetch('Fetch/manage_holidays.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'add',
                date: date,
                description: description
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
                alert('Holiday added successfully!');
                // Clear form fields
                document.getElementById('holidayDate').value = '';
                document.getElementById('holidayDescription').value = '';
                // Reload holidays
                loadHolidays();
            } else {
                console.error('Error adding holiday:', data.error);
                alert(`Error adding holiday: ${data.error || 'Unknown error occurred'}`);
            }
        })
        .catch(error => {
            console.error('Error adding holiday:', error);
            alert(`Failed to add holiday: ${error.message}`);
        });
    }
    
    // Remove a holiday
    function removeHoliday(date) {
        if (!confirm('Are you sure you want to remove this holiday?')) {
            return;
        }
        
        fetch('Fetch/manage_holidays.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'remove',
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
            if (data.success) {
                alert('Holiday removed successfully!');
                // Reload holidays
                loadHolidays();
            } else {
                console.error('Error removing holiday:', data.error);
                alert(`Error removing holiday: ${data.error || 'Unknown error occurred'}`);
            }
        })
        .catch(error => {
            console.error('Error removing holiday:', error);
            alert(`Failed to remove holiday: ${error.message}`);
        });
    }
    </script>
</body>
</html> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information</title>
    <style>
        body {
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6fb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: -15% auto 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
        }
        h2 {
            color: #2d3a4b;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        #searchName {
            margin-bottom: 18px;
            padding: 10px 14px;
            width: 100%;
            max-width: 350px;
            border: 1px solid #d1d9e6;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        #searchName:focus {
            border-color: #5c8df6;
            outline: none;
        }
        #employeeTable {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 8px;
        }
        #employeeTable th, #employeeTable td {
            padding: 12px 10px;
            text-align: left;
        }
        #employeeTable thead {
            background: linear-gradient(90deg, #5c8df6 0%, #6fc8fb 100%);
            color: #fff;
        }
        #employeeTable tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background 0.2s;
        }
        #employeeTable tbody tr:hover {
            background: #f0f6ff;
        }
        #employeeTable td {
            color: #2d3a4b;
        }
        .toggle-pin-btn {
            margin-left: 6px;
            padding: 2px 7px;
            border-radius: 3px;
            border: 1px solid #d1d9e6;
            background: #f4f6fb;
            color: #2d3a4b;
            cursor: pointer;
            font-size: 1em;
            line-height: 1.1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 26px;
            width: 28px;
        }
        .toggle-pin-btn svg {
            width: 18px;
            height: 18px;
            vertical-align: middle;
            fill: #5c8df6;
            transition: fill 0.2s;
        }
        .toggle-pin-btn:active, .toggle-pin-btn:focus {
            background: #e3eaf7;
            outline: none;
        }
        @media (max-width: 700px) {
            .container {
                padding: 12px 2vw 18px 2vw;
            }
            #employeeTable th, #employeeTable td {
                padding: 8px 4px;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h2>Employee Information</h2>
        <input type="text" id="searchName" placeholder="Search by name...">
        <table id="employeeTable" border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Employee rows will be inserted here -->
            </tbody>
        </table>
        <!-- Edit Employee Modal -->
        <div id="editModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:1000;">
          <form id="editForm" style="background:#fff;padding:24px;border-radius:8px;max-width:400px;margin:auto;position:relative;">
            <h3>Edit Employee</h3>
            <input type="hidden" name="emp_id" id="edit_emp_id">
            <div style="margin-bottom:16px;">
              <label for="edit_name">Name:</label>
              <input type="text" name="name" id="edit_name" required>
            </div>
            <div style="margin-bottom:16px;">
              <label for="edit_department">Department:</label>
              <input type="text" name="department" id="edit_department" required>
            </div>
            <div style="margin-bottom:16px;">
              <label for="edit_status">Status:</label>
              <input type="text" name="status" id="edit_status">
            </div>
            <button type="submit">Save</button>
            <button type="button" onclick="closeEditModal()">Cancel</button>
          </form>
        </div>
    </div>
    <script>
    let allEmployees = [];
    function renderEmployees(data, filter = '') {
        const tbody = document.querySelector('#employeeTable tbody');
        tbody.innerHTML = '';
        let filtered = data;
        if (filter) {
            const search = filter.toLowerCase();
            filtered = data.filter(emp =>
                (emp.Name && emp.Name.toLowerCase().includes(search))
            );
        }
        if (filtered.length > 0) {
            filtered.forEach((emp, idx) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${emp.emp_id || ''}</td>
                    <td>${emp.Name || ''}</td>
                    <td>${emp.department || ''}</td>
                    <td>${emp.status || ''}</td>
                    <td>
                        <button type="button" class="edit-btn" data-emp='${JSON.stringify(emp)}'>Edit</button>
                        <button type="button" class="view-records-btn" data-emp-id="${emp.emp_id}">View Records</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="5">No employees found.</td>';
            tbody.appendChild(row);
        }
        // Add event listeners for view records buttons
        tbody.querySelectorAll('.view-records-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const empId = this.getAttribute('data-emp-id');
                window.location.href = `employee_records.php?emp_id=${empId}`;
            });
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        fetch('Fetch/fetch_employees.php')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    allEmployees = data;
                    renderEmployees(allEmployees);
                } else if (data.error) {
                    renderEmployees([], '');
                    const tbody = document.querySelector('#employeeTable tbody');
                    tbody.innerHTML = `<tr><td colspan="5">Error: ${data.error}</td></tr>`;
                }
            })
            .catch(error => {
                const tbody = document.querySelector('#employeeTable tbody');
                tbody.innerHTML = `<tr><td colspan="5">Fetch error: ${error}</td></tr>`;
            });
        document.getElementById('searchName').addEventListener('input', function() {
            renderEmployees(allEmployees, this.value);
        });
    });

    function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('edit-btn')) {
    const emp = JSON.parse(e.target.getAttribute('data-emp'));
    document.getElementById('edit_emp_id').value = emp.emp_id || '';
    document.getElementById('edit_name').value = emp.Name || '';
    document.getElementById('edit_department').value = emp.department || '';
    document.getElementById('edit_status').value = emp.status || '';
    document.getElementById('editModal').style.display = 'flex';
  }
});

document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('update_employee.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      closeEditModal();
      // Refresh employee data
      fetch('Fetch/fetch_employees.php')
        .then(response => response.json())
        .then(data => {
          allEmployees = data;
          renderEmployees(allEmployees);
        });
    } else {
      alert('Update failed: ' + (data.error || 'Unknown error'));
    }
  });
});
    </script>
</body>
</html>
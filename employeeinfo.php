<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Status</th>
                    <th>Position</th>
                    <th>PIN Code</th>
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
            <label>First Name:<input type="text" name="FIRST_NAME" id="edit_first_name" required></label><br>
            <label>Last Name:<input type="text" name="LAST_NAME" id="edit_last_name" required></label><br>
            <label>Status:<input type="text" name="STATUS" id="edit_status" required></label><br>
            <label for="position">Select Position:</label>
            <select name="POSITION" id="edit_position">
                <option value="Faculty Member">Faculty Member</option>
                <option value="Caregiver">Caregiver</option>
                <option value="Instructor">Instructor</option>
                <option value="Part-time Faculty Member">Part-time Faculty Member</option>
                <option value="Other Personnel">Other Personnel</option>
            </select><br>
            <label>PIN Code:<input type="text" name="PIN_CODE" id="edit_pin_code" maxlength="4" pattern="\d{4}" required oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4);"></label><br>
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
                (emp.FIRST_NAME && emp.FIRST_NAME.toLowerCase().includes(search)) ||
                (emp.LAST_NAME && emp.LAST_NAME.toLowerCase().includes(search))
            );
        }
        if (filtered.length > 0) {
            filtered.forEach((emp, idx) => {
                const row = document.createElement('tr');
                // Use a unique id for each PIN cell/button
                const pinId = `pin_${emp.emp_id || idx}`;
                row.innerHTML = `
                    <td>${emp.emp_id || ''}</td>
                    <td>${emp.FIRST_NAME || ''}</td>
                    <td>${emp.LAST_NAME || ''}</td>
                    <td>${emp.STATUS || ''}</td>
                    <td>${emp.POSITION || ''}</td>
                    <td>
                        <span id="${pinId}" style="letter-spacing:2px;">••••</span>
                        <button type="button" class="toggle-pin-btn" data-pin="${emp.PIN_CODE || ''}" data-target="${pinId}" aria-label="Show PIN">
                            <span class="icon-eye">
                                <svg viewBox="0 0 24 24"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z"/></svg>
                            </span>
                        </button>
                    </td>
                    <td>
                    <button type="button" class="edit-btn" data-emp='${JSON.stringify(emp)}'>Edit</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="6">No employees found.</td>';
            tbody.appendChild(row);
        }
        // Add event listeners for all show/hide PIN buttons
        tbody.querySelectorAll('.toggle-pin-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const pinSpan = document.getElementById(this.getAttribute('data-target'));
                const iconSpan = this.querySelector('.icon-eye');
                if (this.getAttribute('data-state') !== 'shown') {
                    pinSpan.textContent = this.getAttribute('data-pin');
                    this.setAttribute('data-state', 'shown');
                    // Change to eye-slash
                    iconSpan.innerHTML = `<svg viewBox=\"0 0 24 24\"><path d=\"M1 12s3-7 11-7c2.5 0 4.7.6 6.5 1.7l1.3-1.5 1.4 1.4-1.5 1.3C21.4 8.7 23 10.2 23 12c0 2-3 7-11 7-2.5 0-4.7-.6-6.5-1.7l-1.3 1.5-1.4-1.4 1.5-1.3C2.6 15.3 1 13.8 1 12zm11 5c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z\"/></svg>`;
                    this.setAttribute('aria-label', 'Hide PIN');
                } else {
                    pinSpan.textContent = '••••';
                    this.setAttribute('data-state', 'hidden');
                    // Change to eye
                    iconSpan.innerHTML = `<svg viewBox=\"0 0 24 24\"><path d=\"M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z\"/></svg>`;
                    this.setAttribute('aria-label', 'Show PIN');
                }
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
                    tbody.innerHTML = `<tr><td colspan="6">Error: ${data.error}</td></tr>`;
                }
            })
            .catch(error => {
                const tbody = document.querySelector('#employeeTable tbody');
                tbody.innerHTML = `<tr><td colspan="6">Fetch error: ${error}</td></tr>`;
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
    document.getElementById('edit_first_name').value = emp.FIRST_NAME || '';
    document.getElementById('edit_last_name').value = emp.LAST_NAME || '';
    document.getElementById('edit_status').value = emp.STATUS || '';
    document.getElementById('edit_position').value = emp.POSITION || '';
    document.getElementById('edit_pin_code').value = emp.PIN_CODE || '';
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
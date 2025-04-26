<?php
$servername = "localhost";
$username = "root";
$password = "root1234";
$dbname = "GH";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['action']) && $_GET['action'] === 'get_active_managers') {
    // جلب المدراء النشطين
    $sql = "SELECT ID, Name_Manager FROM Manager WHERE status = 'active'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['ID']}'>{$row['Name_Manager']}</option>";
        }
    } else {
        echo "<option value='' disabled>No active managers</option>";
    }

    $conn->close();
    exit;
}




// Handle GET actions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'];

    if ($action === 'search' && isset($_GET['employee_name'])) {
        // جلب اسم الموظف من الطلب
        $employee_name = $_GET['employee_name'];

        // استعلام البحث عن الموظف باستخدام LIKE
        $sql = "SELECT E.E_ID, E.Name_Emp, E.gender,E.HireDate, E.phone, E.Email,E.password_cus,E.ID_Manager, M.Name_Manager, E.Status
                FROM Employee E, Manager M
                WHERE E.ID_Manager = M.ID AND E.Name_Emp LIKE ?";
        
        // تحضير الاستعلام
        $stmt = $conn->prepare($sql);
        $search_term = "%" . $employee_name . "%"; // إضافة الـ % للبحث الجزئي
        $stmt->bind_param("s", $search_term); // ربط المعامل (اسم الموظف)

        // تنفيذ الاستعلام
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h2>List of employees:</h2>";
            echo "<table border='1' style='width: 100%; text-align: center;'>";
            // إنشاء الجدول مع الحقول المطلوبة
            echo "
                <tr>
                    <th>E_ID</th>
                    <th>Name_Emp</th>
                    <th>gender</th>
                    <th>HireDate</th>
                    <th>phone</th>
                    <th>Email</th>
                    <th>password_cus</th>
                    <th>Manager ID</th>
                    <th>Name Manager</th>
                    <th>Status Employee</th>
                </tr>";

            // عرض البيانات في الصفوف
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['E_ID']}</td>
                    <td>{$row['Name_Emp']}</td>
                    <td>{$row['gender']}</td>
                    <td>{$row['HireDate']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['Email']}</td>
                    <td>{$row['password_cus']}</td>
                    <td>{$row['ID_Manager']}</td>
                    <td>{$row['Name_Manager']}</td>
                    <td>{$row['Status']}</td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "No employees found with the name '{$employee_name}'.";
        }

        $stmt->close();
    }
}



if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$action = ''; // Ensure it's always defined

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
}

if ($action === 'add') {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $hireDate = $_POST['hire_date'];
    $phone = $_POST['phone'];
    $Email = $_POST['Email'];
    $password = $_POST['password_cus'];
    $manager_id = $_POST['manager_id'];
    $job_type = $_POST['job_type'];
    $salary = isset($_POST['salary']) ? $_POST['salary'] : null;
    $hour_rate = isset($_POST['hour_rate']) ? $_POST['hour_rate'] : null;
    $price_hour = isset($_POST['price_hour']) ? $_POST['price_hour'] : null;

    // Insert into Employee table
    $sql = "INSERT INTO Employee (Name_Emp, gender, HireDate, phone, Email, password_cus, ID_Manager) VALUES (?, ?, ?, ? ,? ,? ,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $name, $gender, $hireDate, $phone, $Email, $password,$manager_id);
    $stmt->execute();

    // Get the last inserted Employee ID
    $employee_id = $conn->insert_id;

    // Insert into Full_time or Part_time based on job type
    if ($job_type === 'full_time') {
        $sql = "INSERT INTO Full_time (E_ID, salary) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $employee_id, $salary);
    } elseif ($job_type === 'part_time') {
        $sql = "INSERT INTO Part_time (E_ID, Hour_rate, price_hour) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iid", $employee_id, $hour_rate, $price_hour);
    }
        $stmt->execute();
        $stmt->close();
        echo "Employee added successfully!";

    } elseif ($action === 'delete') {
        $id = $_POST['id'];

        // حذف الموظف من جدول part_time
        $sqlPartTime = "DELETE FROM part_time WHERE E_ID = ?";
        $stmtPartTime = $conn->prepare($sqlPartTime);
        $stmtPartTime->bind_param("i", $id);
        $stmtPartTime->execute();
        $stmtPartTime->close();
    
        // حذف الموظف من جدول full_time
        $sqlFullTime = "DELETE FROM full_time WHERE E_ID = ?";
        $stmtFullTime = $conn->prepare($sqlFullTime);
        $stmtFullTime->bind_param("i", $id);
        $stmtFullTime->execute();
        $stmtFullTime->close();
    
        // تحديث حالة الموظف إلى "inactive" في جدول Employee
        $sqlUpdateStatus = "UPDATE Employee SET status = 'inactive' WHERE E_ID = ?";
        $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
        $stmtUpdateStatus->bind_param("i", $id);
    
        if ($stmtUpdateStatus->execute()) {
            echo "Employee status updated to inactive successfully!";
        } else {
            echo "Error: " . $stmtUpdateStatus->error;
        }
        $stmtUpdateStatus->close();
    } elseif ($action === 'update') {
        if (isset($_POST['E_ID1'])) {
            $employee_id = $_POST['E_ID1'];
    
            // جلب القيم القديمة من قاعدة البيانات
            $sql = "SELECT * FROM Employee WHERE E_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
            // جلب بيانات النموذج
          
            $new_name = !empty($_POST['Name_Emp1']) ? $_POST['Name_Emp1'] : $row['Name_Emp'];
            $new_gender = !empty($_POST['gender1']) ? $_POST['gender1'] : $row['gender'];
            $hireDate = !empty($_POST['HireDate1']) ? $_POST['HireDate1'] : $row['HireDate1'];
            $new_phone = !empty($_POST['phone1']) ? $_POST['phone1'] : $row['phone'];
            $new_Email = !empty($_POST['Email1']) ? $_POST['Email1'] : $row['Email'];
            $new_password = !empty($_POST['password_cus1']) ? $_POST['password_cus1'] : $row['password_cus'];
            $new_manager_id = !empty($_POST['ID_Manager1']) ? $_POST['ID_Manager1'] : $row['ID_Manager'];
            $new_job_type = $_POST['jobtype1'];
            $new_salary = $_POST['salary1'] ?? null; // إذا كان الموظف بدوام كامل
            $new_hourly_rate = $_POST['Hour_rate1'] ?? null; // إذا كان الموظف بدوام جزئي
            $new_hours = $_POST['price_hour1'] ?? null;


            // التحقق من حالة الموظف
            $sql_check_status = "SELECT status FROM Employee WHERE E_ID = ?";
            $stmt = $conn->prepare($sql_check_status);
            $stmt->bind_param("i", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row['status'] != 'active') {
                    die("Cannot update this employee because the status is not active.");
                }
            } else {
                die("Employee not found.");
            }

            // تحديث بيانات الموظف العامة
            $sql_update_employee = "UPDATE Employee SET Name_Emp = ?, gender = ?,HireDate = ?, phone = ?, Email = ?,password_cus = ?,ID_Manager = ? WHERE E_ID = ?";
            $stmt = $conn->prepare($sql_update_employee);
            $stmt->bind_param("ssssssii", $new_name, $new_gender,$hireDate, $new_phone, $new_Email, $new_password,$new_manager_id, $employee_id);
            $stmt->execute();

            // تحديث بيانات الوظيفة
            if ($new_job_type == "Full-time") {
                // حذف البيانات من جدول Part_time (إذا كانت موجودة)
                $sql_delete_part_time = "DELETE FROM Part_time WHERE E_ID = ?";
                $stmt = $conn->prepare($sql_delete_part_time);
                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                // تحديث أو إدخال البيانات في جدول Full_time
                $sql_insert_full_time = "INSERT INTO Full_time (E_ID, salary) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE salary = ?";
                $stmt = $conn->prepare($sql_insert_full_time);
                $stmt->bind_param("idi", $employee_id, $new_salary, $new_salary);
                $stmt->execute();
            } elseif ($new_job_type == "Part-time") {
                // حذف البيانات من جدول Full_time (إذا كانت موجودة)
                $sql_delete_full_time = "DELETE FROM Full_time WHERE E_ID = ?";
                $stmt = $conn->prepare($sql_delete_full_time);
                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                // تحديث أو إدخال البيانات في جدول Part_time
                $sql_insert_part_time = "INSERT INTO Part_time (E_ID, Hour_rate, price_hour) VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE Hour_rate = ?, price_hour = ?";
                $stmt = $conn->prepare($sql_insert_part_time);
                $stmt->bind_param("ididi", $employee_id, $new_hourly_rate, $new_hours, $new_hourly_rate, $new_hours);
                $stmt->execute();
            }
        }else{
            echo "No employee found with the provided ID.";
        }
    } else {
        echo "Error: Missing required fields.";
    }
            echo "Employee updated successfully.";
}



// Handle GET actions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'];

    if ($action === 'selectAll') {
        // استعلام استرجاع جميع الموظفين مع الحالة
        $sql = "SELECT E.E_ID, E.Name_Emp, E.gender ,E.HireDate, E.phone, E.Email, E.password_cus,E.ID_Manager, M.Name_Manager,E.Status
                FROM Employee E, Manager M
                WHERE E.ID_Manager = M.ID";
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            // إنشاء الجدول مع الحقول المطلوبة
            echo "<table><thead><tr><th>E_ID</th><th>Name_Emp</th><th>gender</th><th>HireDate</th><th>Phone</th><th>Email</th><th>password_cus</th><th>Manager ID</th><th>Name Manager</th><th>Status Employee</th></tr></thead><tbody>";
    
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['E_ID']}</td>
                        <td>{$row['Name_Emp']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['HireDate']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['Email']}</td>
                        <td>{$row['password_cus']}</td>
                        <td>{$row['ID_Manager']}</td>
                        <td>{$row['Name_Manager']}</td>
                        <td>{$row['Status']}</td>
                      </tr>";
            }
    
            echo "</tbody></table>";
        } else {
            echo "No employees found.";
        }
    }elseif ($action === 'full_time') {
        $sql = "SELECT E.E_ID, E.Name_Emp, E.gender,E.HireDate, E.phone, E.Email, E.password_cus,FT.salary 
                FROM Employee E 
                JOIN Full_time FT ON E.E_ID = FT.E_ID";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table><thead><tr><th>E_ID</th><th>Name</th><th>gender</th><th>HireDate</th><th>Phone</th><th>Email</th><th>password_cus</th><th>Salary</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['E_ID']}</td>
                        <td>{$row['Name_Emp']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['HireDate']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['Email']}</td>
                        <td>{$row['password_cus']}</td>
                        <td>{$row['salary']}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "No full-time employees found.";
        }
    } elseif ($action === 'part_time') {
        $sql = "SELECT E.E_ID, E.Name_Emp, E.gender,E.HireDate, E.phone, E.Email, E.password_cus,PT.Hour_rate, PT.price_hour 
                FROM Employee E 
                JOIN Part_time PT ON E.E_ID = PT.E_ID";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table><thead><tr><th>E_ID</th><th>Name</th><th>gender</th><th>HireDate</th><th>Phone</th><th>Email</th><th>password_cus</th><th>Hourly Rate</th><th>Price per Hour</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['E_ID']}</td>
                        <td>{$row['Name_Emp']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['HireDate']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['Email']}</td>
                        <td>{$row['password_cus']}</td>
                        <td>{$row['Hour_rate']}</td>
                        <td>{$row['price_hour']}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "No part-time employees found.";
        }
    }
}
    
$conn->close();
?>
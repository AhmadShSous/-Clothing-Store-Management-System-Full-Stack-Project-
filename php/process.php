<?php
$servername = "localhost";
$username = "root";
$password = "root1234";
$dbname = "GH";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$action = ''; // Ensure it's always defined

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    if ($action === 'add') {
        $name = $_POST['name'];
        $Gender = $_POST['Gender'];
        $email = $_POST['email'];
        $Password = $_POST['Password'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $salary = $_POST['salary'];
        $age = $_POST['age'];

        $sql = "INSERT INTO Manager (Name_Manager, Email,Manager_Address, Age, Salary, Phone, status,Password,Gender) 
                VALUES (?, ?, ?, ?, ?, ?, 'active',?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssidsss", $name ,$email,$address, $age, $salary, $phone,$Password, $Gender);
        $stmt->execute();
        $stmt->close();
        echo "Manager added successfully!";
    } elseif ($action === 'delete') {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $sql = "UPDATE Manager SET status = 'inactive' WHERE ID = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    echo "Manager marked as inactive!";
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "Error: Missing 'id' for deletion.";
        }

    } elseif ($action === 'update') {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
    
            // جلب القيم القديمة من قاعدة البيانات
            $sql = "SELECT * FROM Manager WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
    
                 // استخدام القيم المدخلة إذا كانت موجودة وغير فارغة، وإلا استخدام القيم القديمة
                $name = !empty($_POST['name']) ? $_POST['name'] : $row['Name_Manager'];
                $Gender = !empty($_POST['Gender']) ? $_POST['Gender'] : $row['Gender'];
                $email = !empty($_POST['email']) ? $_POST['email'] : $row['Email'];
                $Password = !empty($_POST['Password']) ? $_POST['Password'] : $row['Password'];
                $address = !empty($_POST['address']) ? $_POST['address'] : $row['Manager_Address'];
                $phone = !empty($_POST['phone']) ? $_POST['phone'] : $row['Phone'];
                $salary = !empty($_POST['salary']) ? $_POST['salary'] : $row['Salary'];
                $age = !empty($_POST['age']) ? $_POST['age'] : $row['Age'];
                // فحص إذا كان الـ status موجودًا وإذا كانت قيمة الـ status صحيحة
                $status = !empty($_POST['status']) ? $_POST['status'] : $row['status']; // 
                
            
    
                // SQL query to update the manager information
                $sql = "UPDATE Manager SET Name_Manager = ?, Gender = ?, Email = ?, Password = ?, Manager_Address = ?, Phone = ?, Salary = ?, Age = ?, status = ? WHERE ID = ?";
                $stmt = $conn->prepare($sql);
    
                if ($stmt) {
                    // إذا كانت الحالة نصية أو عددية، تأكد من أن الـ bind_param يتوافق
                    $stmt->bind_param("ssssssddss", $name, $Gender, $email, $Password, $address, $phone, $salary, $age, $status, $id);
    
                    if ($stmt->execute()) {
                        echo "Manager updated successfully!";
                    } else {
                        echo "Error: " . $stmt->error;
                    }
    
                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $conn->error;
                }
            } else {
                echo "No manager found with the provided ID.";
            }
        } else {
            echo "Error: Missing required fields.";
        }
    
    
    } elseif ($action === 'search') {
        $searchName = $_POST['search_name'] ?? '';
        $searchEmail = $_POST['search_email'] ?? '';
        
        $sql = "SELECT * FROM Manager WHERE Name_Manager LIKE ? OR Email LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchName = "%$searchName%";
        $searchEmail = "%$searchEmail%";
        $stmt->bind_param("ss", $searchName, $searchEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h2>List of managers:</h2>";
            echo "<table border='1' style='width: 100%; text-align: center;'>";
            echo "<tr><th>ID</th><th>Name_Manager</th><th>Email</th><th>Manager_Address</th><th>Phone</th><th>Salary</th><th>Age</th><th>Status</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['ID']}</td><td>{$row['Name_Manager']}</td><td>{$row['Email']}</td><td>{$row['Manager_Address']}</td><td>{$row['Phone']}</td><td>$" . number_format($row['Salary'], 2) ."</td><td>{$row['Age']}</td><td>{$row['status']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No matching managers found.";
        }
    }

}
// Handle GET actions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'];

    if ($action === 'selectAll') {
        $sql = "SELECT * FROM Manager";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table><tr><th>ID</th><th>Name_Manager</th><th>Gender</th><th>Email</th><th>Password</th><th>Manager_Address</th><th>Phone</th><th>Salary</th><th>Age</th><th>Status</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['ID']}</td><td>{$row['Name_Manager']}</td><td>{$row['Gender']}</td><td>{$row['Email']}</td><td>{$row['Password']}</td><td>{$row['Manager_Address']}</td><td>{$row['Phone']}</td><td>$" . number_format($row['Salary'], 2) ."</td><td>{$row['Age']}</td><td>{$row['status']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No managers found.";
        }
    }
}

$conn->close();













/*
$servername = "localhost";
$username = "root";
$password = "root1234";
$dbname = "GH";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$action = ''; // Ensure it's always defined

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
}
    if ($action === 'add') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $salary = $_POST['salary'];
        $age = $_POST['age'];

        $sql = "INSERT INTO Manager (Name_Manager, Email, Manager_Address, Age, Salary, Phone) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssids",$name, $email, $address, $age, $salary, $phone);
        $stmt->execute();
        $stmt->close();
        echo "Manager added successfully!";
    } elseif ($action === 'delete') {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                $sql = "DELETE FROM Manager WHERE ID = ?";
                $stmt = $conn->prepare($sql);
        
                if ($stmt) {
                    $stmt->bind_param("i", $id);
        
                    if ($stmt->execute()) {
                        echo "Manager deleted successfully!";
                    } else {
                        echo "Error: " . $stmt->error;
                    }
        
                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $conn->error;
                }
            } else {
                echo "Error: Missing 'id' for deletion.";
            }
        

        echo "Manager deleted successfully!";
    } elseif ($action === 'update') {
        if (isset($_POST['id'], $_POST['name'], $_POST['email'], $_POST['address'], $_POST['phone'], $_POST['salary'], $_POST['age'])) {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];
            $salary = $_POST['salary'];
            $age = $_POST['age'];
    
            // Use the correct column name in the WHERE clause
            $sql = "UPDATE Manager SET ID = ?, Name_Manager = ?, Email = ?, Manager_Address = ?, Phone = ?, Salary = ?, Age = ? WHERE ID = ?";
            $stmt = $conn->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("issssdii", $id,$name, $email, $address, $phone, $salary, $age, $id);
    
                if ($stmt->execute()) {
                    echo "Manager updated successfully!";
                } else {
                    echo "Error: " . $stmt->error;
                }
    
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "Error: Missing required fields.";
        }
        
}

// Handle GET actions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'];

    if ($action === 'selectAll') {
        $sql = "SELECT * FROM Manager";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table><tr><th>ID</th><th>Name_Manager</th><th>Email</th><th>Manager_Address</th><th>Phone</th><th>Salary</th><th>Age</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['ID']}</td><td>{$row['Name_Manager']}</td><td>{$row['Email']}</td><td>{$row['Manager_Address']}</td><td>{$row['Phone']}</td><td>$" . number_format($row['Salary'], 2) ."</td><td>{$row['Age']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No managers found.";
        }
    }
}

$conn->close();*/
?>

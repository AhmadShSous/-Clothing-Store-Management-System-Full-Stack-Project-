<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root1234", "GH");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// استقبال بيانات المزود
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['supplier_name'];
    $email = $_POST['supplier_email'];
    $phone = $_POST['supplier_phone'];

    // التحقق من صحة البيانات
    if (!empty($name) && !empty($email) && !empty($phone)) {
        // إدخال البيانات في قاعدة البيانات
        $stmt = $conn->prepare("INSERT INTO Supplier (Name_Sup, Email, Phone, Status) VALUES (?, ?, ?, 'active')");
        $stmt->bind_param("sss", $name, $email, $phone);

        if ($stmt->execute()) {
            echo "Supplier added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required.";
    }
}

$conn->close();
?>

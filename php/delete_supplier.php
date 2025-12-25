<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root1234", "GH");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// التحقق من وجود البيانات في الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supplier_id'])) {
    $supplier_id = $conn->real_escape_string($_POST['supplier_id']);

    // تحديث حالة المزود إلى inactive
    $sql = "UPDATE Supplier SET status='inactive' WHERE ID_Sup='$supplier_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Supplier status updated to inactive successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// غلق الاتصال
$conn->close();
?>

<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root1234", "GH");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// التحقق إذا تم إرسال الطلب لعرض الموردين
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['show_suppliers'])) {
    $sql = "SELECT ID_Sup, Name_Sup, Email, phone, status FROM Supplier";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>List of Suppliers:</h2>";
        echo "<table border='1' style='width: 100%; text-align: center;'>";
        echo "<tr>
                <th>Supplier ID</th>
                <th>Supplier Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>status</th>

              </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['ID_Sup']}</td>
                    <td>{$row['Name_Sup']}</td>
                    <td>{$row['Email']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['status']}</td>

                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No suppliers are currently registered.</p>";
    }
} else {
    echo "<p>Invalid Request</p>";
}

// غلق الاتصال
$conn->close();
?>

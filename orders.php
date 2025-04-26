<?php
$conn = new mysqli("localhost", "root", "root1234", "GH");

// التحقق من الاتصال بقاعدة البيانات
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'show_orders') {
        // جلب جميع الطلبيات
        $sql = "SELECT o.ID_order, o.order_Date, ol.quantity, ol.unit_price, ol.discount, ol.total_price, p.Name_product 
                FROM Orders o
                JOIN order_line ol ON o.ID_order = ol.ID_order
                JOIN Product p ON ol.ID_P = p.ID_P";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Discount</th>
                        <th>Total Price</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['ID_order']}</td>
                        <td>{$row['order_Date']}</td>
                        <td>{$row['Name_product']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['unit_price']}</td>
                        <td>{$row['discount']}%</td>
                        <td>{$row['total_price']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No orders found.</p>";
        }
    } elseif ($_GET['action'] == 'query_total') {
        // الاستعلام عن المجموع الإجمالي حسب تاريخ الطلب
        $order_date = date('Y-m-d', strtotime($_GET['order_date'])); // التأكد من التنسيق

        $sql = "SELECT SUM(ol.total_price) AS total_price_sum
                FROM Orders o
                JOIN order_line ol ON o.ID_order = ol.ID_order
                WHERE o.order_Date = ?";
    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $order_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        if ($row['total_price_sum'] !== null) {
            echo "<p>Total price for orders on {$order_date}: {$row['total_price_sum']} units.</p>";
        } else {
            echo "<p>No orders found on this date.</p>";
        }
    
        // إضافة المزيد من الإحصائيات هنا ...
    }
}

$conn->close();
?>

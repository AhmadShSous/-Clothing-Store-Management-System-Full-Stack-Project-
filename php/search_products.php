<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root1234", "GH");

// التحقق من الاتصال بقاعدة البيانات
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// التحقق من وجود اسم المنتج في طلب البحث
if (isset($_GET['product_name'])) {
    $product_name = $conn->real_escape_string($_GET['product_name']);

    // استعلام البحث عن المنتجات
    $sql = "SELECT Product.ID_P, Product.Name_product, Product.category,Import_price, Profit_margin, Product.price, 
                   Product.color, Product.material, Product.discount, Product.Stock, 
                   Supplier.Name_Sup, Manager.Name_Manager
            FROM Product
            JOIN Import_product ON Product.ID_P = Import_product.ID_P
            JOIN Supplier ON Import_product.ID_Sup = Supplier.ID_Sup
            JOIN Manager ON Import_product.ID_Man = Manager.ID
            WHERE Product.Name_product LIKE '%$product_name%'";

    $result = $conn->query($sql);

    // التحقق من وجود نتائج
    if ($result->num_rows > 0) {
        echo "<h2>Search Results</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>category</th>
                    <th>Import Price</th>
                    <th>Profit Margin</th>
                    <th>Price</th>
                    <th>Color</th>
                    <th>Material</th>
                    <th>Discount</th>
                    <th>Stock</th>
                    <th>Supplier</th>
                    <th>Manager</th>
                </tr>";

        // عرض النتائج في جدول
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['ID_P'] . "</td>
                    <td>" . $row['Name_product'] . "</td>
                    <td>" . $row['category'] . "</td>
                    <td>$" . $row['Import_price'] . "</td>
                    <td>" . $row['Profit_margin'] . "%</td>
                    <td>$" . $row['price'] . "</td>
                    <td>" . $row['color'] . "</td>
                    <td>" . $row['material'] . "</td>
                    <td>" . $row['discount'] . "</td>
                    <td>" . $row['Stock'] . "</td>
                    <td>" . $row['Name_Sup'] . "</td>
                    <td>" . $row['Name_Manager'] . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<h2>No products found matching your search.</h2>";
    }
} 
// التحقق من وجود اسم المنتج في طلب البحث
elseif (isset($_GET['Caterogy_type'])) {
    $Caterogy_type = $conn->real_escape_string($_GET['Caterogy_type']);

    // استعلام البحث عن المنتجات
    $sql = "SELECT Product.ID_P, Product.Name_product, Product.category,Import_price, Profit_margin, Product.price, 
                   Product.color, Product.material, Product.discount, Product.Stock, 
                   Supplier.Name_Sup, Manager.Name_Manager
            FROM Product
            JOIN Import_product ON Product.ID_P = Import_product.ID_P
            JOIN Supplier ON Import_product.ID_Sup = Supplier.ID_Sup
            JOIN Manager ON Import_product.ID_Man = Manager.ID
            WHERE Product.category LIKE '%$Caterogy_type%'";

    $result = $conn->query($sql);

    // التحقق من وجود نتائج
    if ($result->num_rows > 0) {
        echo "<h2>Search Results</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>category</th>
                    <th>Import Price</th>
                    <th>Profit Margin</th>
                    <th>Price</th>
                    <th>Color</th>
                    <th>Material</th>
                    <th>Discount</th>
                    <th>Stock</th>
                    <th>Supplier</th>
                    <th>Manager</th>
                </tr>";

        // عرض النتائج في جدول
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['ID_P'] . "</td>
                    <td>" . $row['Name_product'] . "</td>
                    <td>" . $row['category'] . "</td>
                    <td>$" . $row['Import_price'] . "</td>
                    <td>" . $row['Profit_margin'] . "%</td>
                    <td>$" . $row['price'] . "</td>
                    <td>" . $row['color'] . "</td>
                    <td>" . $row['material'] . "</td>
                    <td>" . $row['discount'] . "</td>
                    <td>" . $row['Stock'] . "</td>
                    <td>" . $row['Name_Sup'] . "</td>
                    <td>" . $row['Name_Manager'] . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<h2>No products found matching your search.</h2>";
    }
} 

// غلق الاتصال بقاعدة البيانات
$conn->close();
?>

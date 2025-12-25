<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "root1234", "GH");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // قراءة البيانات من النموذج
    
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $import_price = $_POST['import_price'];
    $profit_margin = $_POST['profit_margin'];
    $color = $_POST['color'];
    $material = $_POST['material'];
    $discount = $_POST['discount'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $image_url = $_POST['image_url'];

    // حساب السعر النهائي
    $price = $import_price + ($import_price * ($profit_margin / 100));

    // التحقق من الحقول المطلوبة
    if ( empty($product_id) || empty($product_name) || empty($import_price) || empty($profit_margin) || empty($color) || empty($material) || empty($stock) || empty($category)) {
        die("Please fill in all required fields.");
    }

    // تحديث المنتج في قاعدة البيانات
    $stmt = $conn->prepare("UPDATE Product SET Name_product = ?, price = ?, Import_price = ?, Profit_margin = ?, color = ?, material = ?, discount = ?, Stock = ?, category = ?, image_url = ? WHERE ID_P = ?");
    $stmt->bind_param("sdddssisssi", $product_name, $price, $import_price, $profit_margin, $color, $material, $discount, $stock, $category, $image_url, $product_id);

    if ($stmt->execute()) {
        echo "Product updated successfully!";
    } else {
        echo "Error updating product: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root1234", "GH");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// الإحصائيات
$stats = [];

// المنتج صاحب أعلى نسبة ربح
$stats['highest_margin'] = $conn->query("SELECT Name_product, Profit_margin FROM Product ORDER BY Profit_margin DESC LIMIT 1")->fetch_assoc();

// المنتج صاحب أعلى قيمة ربح
$stats['highest_profit_value'] = $conn->query("SELECT Name_product, (price - Import_price) AS profit_value FROM Product ORDER BY profit_value DESC LIMIT 1")->fetch_assoc();

// المنتج صاحب أقل نسبة ربح
$stats['lowest_margin'] = $conn->query("SELECT Name_product, Profit_margin FROM Product ORDER BY Profit_margin ASC LIMIT 1")->fetch_assoc();

// المنتج صاحب أقل قيمة ربح
$stats['lowest_profit_value'] = $conn->query("SELECT Name_product, (price - Import_price) AS profit_value FROM Product ORDER BY profit_value ASC LIMIT 1")->fetch_assoc();

// المنتج صاحب أعلى نسبة خصم
$stats['highest_discount'] = $conn->query("SELECT Name_product, discount FROM Product ORDER BY discount DESC LIMIT 1")->fetch_assoc();

// المنتج صاحب أقل  نسبة خصم
$stats['lowest_discount'] = $conn->query("SELECT Name_product, discount FROM Product ORDER BY discount ASC LIMIT 1")->fetch_assoc();

// المنتج صاحب أعلى خصم
$stats['highest_discount_value'] = $conn->query("SELECT Name_product, (price * (discount / 100)) AS discount_value FROM Product ORDER BY discount_value DESC LIMIT 1")->fetch_assoc();

// المنتج صاحب أقل خصم
$stats['lowest_discount_value'] = $conn->query("SELECT Name_product, (price * (discount / 100)) AS discount_value FROM Product ORDER BY discount_value ASC LIMIT 1")->fetch_assoc();


// المنتج صاحب أعلى مخزون
$stats['highest_stock'] = $conn->query("SELECT Name_product, Stock FROM Product ORDER BY Stock DESC LIMIT 1")->fetch_assoc();

// المنتج صاحب أقل مخزون
$stats['lowest_stock'] = $conn->query("SELECT Name_product, Stock FROM Product ORDER BY Stock ASC LIMIT 1")->fetch_assoc();

// مجموع سعر الاستيراد * الكمية
$stats['total_import_price'] = $conn->query("SELECT SUM(Import_price * Stock) AS total_import_price FROM Product")->fetch_assoc()['total_import_price'];

// مجموع قيمة الربح * الكمية
$stats['total_profit_value'] = $conn->query("SELECT SUM((price - Import_price) * Stock) AS total_profit_value FROM Product")->fetch_assoc()['total_profit_value'];

// مجموع قيمة الخصم * الكمية
$stats['total_discount_value'] = $conn->query(" SELECT SUM(price * (discount / 100) * Stock) AS total_discount_value FROM Product")->fetch_assoc()['total_discount_value'];


//مجموع المبيعات من دون خصم
$stats['total_price_value'] = $conn->query(" SELECT SUM(price * Stock) AS total_price_value FROM Product")->fetch_assoc()['total_price_value'];


//مجموع المبيعات مع خصم
$stats['total_price_value_discount'] = $conn->query("SELECT SUM((price - (price * (discount / 100))) * Stock) AS total_price_value_discount FROM Product")->fetch_assoc()['total_price_value_discount'];

// أكثر مزود تم التعامل معه
$stats['most_frequent_supplier'] = $conn->query("SELECT Supplier.Name_Sup, COUNT(*) AS frequency FROM Import_product JOIN Supplier ON Import_product.ID_Sup = Supplier.ID_Sup GROUP BY Supplier.ID_Sup ORDER BY frequency DESC LIMIT 1")->fetch_assoc();

// أقل مزود تم التعامل معه
$stats['least_frequent_supplier'] = $conn->query("SELECT Supplier.Name_Sup, COUNT(*) AS frequency FROM Import_product JOIN Supplier ON Import_product.ID_Sup = Supplier.ID_Sup GROUP BY Supplier.ID_Sup ORDER BY frequency ASC LIMIT 1")->fetch_assoc();





// جلب الأسعار والمنتجات للرسم البياني
$chart_data = $conn->query("SELECT Name_product, price, discount FROM Product");
$chart_cycle = $conn->query("SELECT Name_product, Profit_margin FROM Product");

// تحويل البيانات إلى JSON
$response = [
    'stats' => $stats,
    'chart_data' => $chart_data->fetch_all(MYSQLI_ASSOC),
    'chart_cycle' => $chart_cycle->fetch_all(MYSQLI_ASSOC)
];

// إرجاع البيانات كـ JSON
header('Content-Type: application/json');
echo json_encode($response);


// غلق الاتصال بقاعدة البيانات
$conn->close();
?>

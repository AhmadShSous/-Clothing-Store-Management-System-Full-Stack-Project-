<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root1234", "GH");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// استعلام: مجموع السعر الكلي لجميع الطلبيات
$total_price_query = "SELECT SUM(total_price) AS total_price FROM order_line";
$total_price_result = $conn->query($total_price_query);
$total_price = $total_price_result->fetch_assoc()['total_price'];

// استعلام: أكثر منتج مبيعاً كمية
$most_sold_query = "SELECT Product.Name_product, SUM(order_line.quantity) AS total_quantity 
                    FROM order_line 
                    JOIN Product ON order_line.ID_P = Product.ID_P 
                    GROUP BY Product.ID_P 
                    ORDER BY total_quantity DESC LIMIT 1";
$most_sold_result = $conn->query($most_sold_query);
$most_sold = $most_sold_result->fetch_assoc();

// استعلام: أقل منتج مبيعاً كمية
$least_sold_query = "SELECT Product.Name_product, SUM(order_line.quantity) AS total_quantity 
                     FROM order_line 
                     JOIN Product ON order_line.ID_P = Product.ID_P 
                     GROUP BY Product.ID_P 
                     ORDER BY total_quantity ASC LIMIT 1";
$least_sold_result = $conn->query($least_sold_query);
$least_sold = $least_sold_result->fetch_assoc();

// استعلام: التاريخ الأقل مبيعاً
$least_sales_date_query = "SELECT Orders.order_Date AS order_date, SUM(order_line.quantity) AS total_quantity 
                           FROM order_line 
                           JOIN Orders ON order_line.ID_order = Orders.ID_order 
                           GROUP BY Orders.order_Date 
                           ORDER BY total_quantity ASC LIMIT 1";
$least_sales_date_result = $conn->query($least_sales_date_query);
$least_sales_date = $least_sales_date_result->fetch_assoc();

// استعلام: التاريخ الأكثر مبيعاً
$most_sales_date_query = "SELECT Orders.order_Date AS order_date, SUM(order_line.quantity) AS total_quantity 
                          FROM order_line 
                          JOIN Orders ON order_line.ID_order = Orders.ID_order 
                          GROUP BY Orders.order_Date 
                          ORDER BY total_quantity DESC LIMIT 1";
$most_sales_date_result = $conn->query($most_sales_date_query);
$most_sales_date = $most_sales_date_result->fetch_assoc();

// استعلام: التاريخ صاحب أكبر مجموع للسعر الإجمالي
$highest_price_date_query = "SELECT Orders.order_Date AS order_date, SUM(order_line.total_price) AS total_price 
                             FROM order_line 
                             JOIN Orders ON order_line.ID_order = Orders.ID_order 
                             GROUP BY Orders.order_Date 
                             ORDER BY total_price DESC LIMIT 1";
$highest_price_date_result = $conn->query($highest_price_date_query);
$highest_price_date = $highest_price_date_result->fetch_assoc();

// استعلام: التاريخ صاحب أقل مجموع للسعر الإجمالي
$lowest_price_date_query = "SELECT Orders.order_Date AS order_date, SUM(order_line.total_price) AS total_price 
                            FROM order_line 
                            JOIN Orders ON order_line.ID_order = Orders.ID_order 
                            GROUP BY Orders.order_Date 
                            ORDER BY total_price ASC LIMIT 1";
$lowest_price_date_result = $conn->query($lowest_price_date_query);
$lowest_price_date = $lowest_price_date_result->fetch_assoc();




$chart_data = $conn->query("SELECT Product.Name_product, SUM(order_line.quantity) AS total_quantity 
                        FROM order_line 
                        JOIN Product ON order_line.ID_P = Product.ID_P 
                        GROUP BY Product.ID_P ");
$chart_cycle = $conn->query("SELECT Orders.order_Date AS order_date, SUM(order_line.total_price) AS total_price, 
    (SELECT SUM(order_line.total_price) FROM order_line JOIN Orders ON order_line.ID_order = Orders.ID_order) AS grand_total 
    FROM order_line 
    JOIN Orders ON order_line.ID_order = Orders.ID_order 
    GROUP BY Orders.order_Date
    ");


// إعداد البيانات بتنسيق JSON
$response = [
    'total_price' => $total_price,
    'most_sold' => $most_sold,
    'least_sold' => $least_sold,
    'least_sales_date' => $least_sales_date,
    'most_sales_date' => $most_sales_date,
    'highest_price_date' => $highest_price_date,
    'lowest_price_date' => $lowest_price_date,
    'chart_data' => $chart_data->fetch_all(MYSQLI_ASSOC),
    'chart_cycle' => $chart_cycle->fetch_all(MYSQLI_ASSOC)
];

// إرسال الرد بتنسيق JSON
header('Content-Type: application/json');
echo json_encode($response);

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>

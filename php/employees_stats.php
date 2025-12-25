<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root1234", "GH");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// الإحصائيات
$stats = [];

// 1. نسبة موظفين الفل تايم من الموظفين الكلي
$data = $conn->query("
    SELECT COUNT(*) AS total_employees, 
           (SELECT COUNT(*) FROM Full_time ft 
            JOIN Employee e ON ft.E_ID = e.E_ID WHERE e.status = 'active') AS full_time_count 
    FROM Employee WHERE status = 'active'
")->fetch_assoc();
$stats['full_time_percentage'] = ($data['full_time_count'] / $data['total_employees']) * 100;

// 2. نسبة موظفين البارت تايم من الموظفين الكلي
$data['part_time_count'] = $conn->query("
    SELECT COUNT(*) 
    FROM Part_time pt 
    JOIN Employee e ON pt.E_ID = e.E_ID 
    WHERE e.status = 'active'
")->fetch_row()[0];
$stats['part_time_percentage'] = ($data['part_time_count'] / $data['total_employees']) * 100;

// 3. نسبة موظفين الإناث من جميع الموظفين
$data = $conn->query("
    SELECT COUNT(*) AS total_employees, 
           SUM(gender = 'female') AS female_count 
    FROM Employee WHERE status = 'active'
")->fetch_assoc();
$stats['female_percentage'] = ($data['female_count'] / $data['total_employees']) * 100;

// 4. نسبة موظفين الذكور من جميع الموظفين
$stats['male_percentage'] = 100 - $stats['female_percentage'];

// 5. نسبة موظفين الذكور من موظفين الفل تايم
$data = $conn->query("
    SELECT COUNT(*) AS total_full_time, 
           SUM(e.gender = 'male') AS male_count 
    FROM Full_time ft 
    JOIN Employee e ON ft.E_ID = e.E_ID 
    WHERE e.status = 'active'
")->fetch_assoc();
$stats['male_full_time_percentage'] = ($data['male_count'] / $data['total_full_time']) * 100;
$stats['female_full_time_percentage'] = 100 - $stats['male_full_time_percentage'];
// 6. نسبة موظفين الإناث من موظفين البارت تايم
$data = $conn->query("
    SELECT COUNT(*) AS total_part_time, 
           SUM(e.gender = 'female') AS female_count 
    FROM Part_time pt 
    JOIN Employee e ON pt.E_ID = e.E_ID 
    WHERE e.status = 'active'
")->fetch_assoc();
$stats['female_part_time_percentage'] = ($data['female_count'] / $data['total_part_time']) * 100;
$stats['male_part_time_percentage'] = 100 - $stats['female_part_time_percentage'];
// 7. أقدم موظف
$stats['oldest_employee'] = $conn->query("
    SELECT Name_Emp, HireDate 
    FROM Employee 
    WHERE status = 'active' 
    ORDER BY HireDate ASC LIMIT 1
")->fetch_assoc();

// 8. أحدث موظف
$stats['newest_employee'] = $conn->query("
    SELECT Name_Emp, HireDate 
    FROM Employee 
    WHERE status = 'active' 
    ORDER BY HireDate DESC LIMIT 1
")->fetch_assoc();

// 9. نسبة الموظفين النشطين
$data = $conn->query("
    SELECT COUNT(*) AS total_employees, 
           SUM(status = 'active') AS active_count 
    FROM Employee WHERE status = 'active'
")->fetch_assoc();
$stats['active_percentage'] = ($data['active_count'] / $data['total_employees']) * 100;

// 10. نسبة الموظفين غير النشطين
$stats['inactive_percentage'] = 100 - $stats['active_percentage'];

// 11. متوسط رواتب الفل تايم
$stats['avg_full_time_salary'] = $conn->query("
    SELECT AVG(salary) AS avg_salary 
    FROM Full_time ft 
    JOIN Employee e ON ft.E_ID = e.E_ID 
    WHERE e.status = 'active'
")->fetch_assoc()['avg_salary'];

// 12. أعلى راتب فل تايم
$stats['highest_full_time_salary'] = $conn->query("
    SELECT salary 
    FROM Full_time ft 
    JOIN Employee e ON ft.E_ID = e.E_ID 
    WHERE e.status = 'active' 
    ORDER BY salary DESC LIMIT 1
")->fetch_assoc()['salary'];

// 13. أقل راتب فل تايم
$stats['lowest_full_time_salary'] = $conn->query("
    SELECT salary 
    FROM Full_time ft 
    JOIN Employee e ON ft.E_ID = e.E_ID 
    WHERE e.status = 'active' 
    ORDER BY salary ASC LIMIT 1
")->fetch_assoc()['salary'];

// 14. أكبر عدد ساعات اشتغلها موظف بارت تايم
$stats['max_part_time_hours'] = $conn->query("
    SELECT Hour_rate 
    FROM Part_time pt 
    JOIN Employee e ON pt.E_ID = e.E_ID 
    WHERE e.status = 'active' 
    ORDER BY Hour_rate DESC LIMIT 1
")->fetch_assoc()['Hour_rate'];

// 15. أقل عدد ساعات اشتغلها موظف بارت تايم
$stats['min_part_time_hours'] = $conn->query("
    SELECT Hour_rate 
    FROM Part_time pt 
    JOIN Employee e ON pt.E_ID = e.E_ID 
    WHERE e.status = 'active' 
    ORDER BY Hour_rate ASC LIMIT 1
")->fetch_assoc()['Hour_rate'];

// 16. أعلى سعر ساعة لموظف بارت تايم
$stats['highest_part_time_hour_rate'] = $conn->query("
    SELECT price_hour 
    FROM Part_time pt 
    JOIN Employee e ON pt.E_ID = e.E_ID 
    WHERE e.status = 'active' 
    ORDER BY price_hour DESC LIMIT 1
")->fetch_assoc()['price_hour'];

// 17. أقل سعر ساعة لموظف بارت تايم
$stats['lowest_part_time_hour_rate'] = $conn->query("
    SELECT price_hour 
    FROM Part_time pt 
    JOIN Employee e ON pt.E_ID = e.E_ID 
    WHERE e.status = 'active' 
    ORDER BY price_hour ASC LIMIT 1
")->fetch_assoc()['price_hour'];

// 18. أعلى أجر مدفوع لموظف بارت تايم
$stats['highest_part_time_payment'] = $conn->query("
    SELECT (Hour_rate * price_hour) AS total_payment 
    FROM Part_time pt 
    JOIN Employee e ON pt.E_ID = e.E_ID 
    WHERE e.status = 'active' 
    ORDER BY total_payment DESC LIMIT 1
")->fetch_assoc()['total_payment'];

// 19. أقل أجر مدفوع لموظف بارت تايم
$stats['lowest_part_time_payment'] = $conn->query("
    SELECT (Hour_rate * price_hour) AS total_payment 
    FROM Part_time pt 
    JOIN Employee e ON pt.E_ID = e.E_ID 
    WHERE e.status = 'active' 
    ORDER BY total_payment ASC LIMIT 1
")->fetch_assoc()['total_payment'];

// 20. مجموع رواتب موظفي الفل تايم
$stats['total_full_time_salary'] = $conn->query("
    SELECT SUM(salary) AS total_salary 
    FROM Full_time ft 
    JOIN Employee e ON ft.E_ID = e.E_ID 
    WHERE e.status = 'active'
")->fetch_assoc()['total_salary'];

// 21. مجموع الأجور المدفوعة لموظفي البارت تايم
$stats['total_part_time_payment'] = $conn->query("
    SELECT SUM(Hour_rate * price_hour) AS total_payment 
    FROM Part_time pt 
    JOIN Employee e ON pt.E_ID = e.E_ID 
    WHERE e.status = 'active'
")->fetch_assoc()['total_payment'];

// 22. مجموع جميع الأجور المدفوعة
$stats['total_payments'] = $stats['total_full_time_salary'] + $stats['total_part_time_payment'];


// جلب راتب كل موظف كنسبة مئوية من إجمالي الرواتب
$chart_cycle = $conn->query("
    SELECT 
        'Full Time' AS category,
        (COUNT(F.E_ID) / (SELECT COUNT(E_ID) FROM Employee WHERE status = 'active') * 100) AS percentage
    FROM Employee E
    JOIN Full_time F ON E.E_ID = F.E_ID
    WHERE E.status = 'active'
");

$chart_data = $conn->query("
   SELECT 
    E.Name_Emp AS name, 
    F.salary AS full_time_salary,
    NULL AS part_time_total_payment
FROM Employee E
JOIN Full_time F ON E.E_ID = F.E_ID
WHERE E.status = 'active'

UNION

SELECT 
    E.Name_Emp AS name, 
    NULL AS full_time_salary,
    P.price_hour * P.Hour_rate AS part_time_total_payment
FROM Employee E
JOIN Part_time P ON E.E_ID = P.E_ID
WHERE E.status = 'active';

");


// تحويل الإحصائيات إلى JSON
$response = [
    'chart_data' => $chart_data->fetch_all(MYSQLI_ASSOC),
    'chart_cycle' => $chart_cycle->fetch_all(MYSQLI_ASSOC),
    'stats' => $stats
];
//تحويل الإحصائيات إلى JSON
header('Content-Type: application/json');
echo json_encode($response);

// غلق الاتصال بقاعدة البيانات
$conn->close();
?>

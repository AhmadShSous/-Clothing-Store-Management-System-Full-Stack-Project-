<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root1234", "GH");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// الإحصائيات
$stats = [];

// 1. متوسط الرواتب
$stats['avg_salary'] = $conn->query("SELECT AVG(Salary) AS avg_salary FROM Manager WHERE status = 'active'")->fetch_assoc()['avg_salary'];

// 2. المدير صاحب أعلى راتب
$stats['highest_salary_manager'] = $conn->query("SELECT Name_Manager, Salary FROM Manager WHERE status = 'active' ORDER BY Salary DESC LIMIT 1")->fetch_assoc();

// 3. المدير صاحب أقل راتب
$stats['lowest_salary_manager'] = $conn->query("SELECT Name_Manager, Salary FROM Manager WHERE status = 'active' ORDER BY Salary ASC LIMIT 1")->fetch_assoc();

// 4. متوسط الأعمار
$stats['avg_age'] = $conn->query("SELECT AVG(Age) AS avg_age FROM Manager WHERE status = 'active'")->fetch_assoc()['avg_age'];

// 5. المدير الأصغر سنًا
$stats['youngest_manager'] = $conn->query("SELECT Name_Manager, Age FROM Manager WHERE status = 'active' ORDER BY Age ASC LIMIT 1")->fetch_assoc();

// 6. نسبة الإناث من المديرين
$data = $conn->query("SELECT COUNT(*) AS total_managers, SUM(Gender = 'female') AS female_count FROM Manager WHERE status = 'active'")->fetch_assoc();
$stats['female_percentage'] = ($data['female_count'] / $data['total_managers']) * 100;

// 7. نسبة الذكور من المديرين
$stats['male_percentage'] = 100 - $stats['female_percentage'];

// 8. العنوان الذي منه أكثر المديرين
$stats['most_common_address'] = $conn->query("SELECT Manager_Address, COUNT(*) AS count FROM Manager WHERE status = 'active' GROUP BY Manager_Address ORDER BY count DESC LIMIT 1")->fetch_assoc();

// 9. العنوان الذي منه أقل المديرين
$stats['least_common_address'] = $conn->query("SELECT Manager_Address, COUNT(*) AS count FROM Manager WHERE status = 'active' GROUP BY Manager_Address ORDER BY count ASC LIMIT 1")->fetch_assoc();

// 10. نسبة المديرين "Active" 
// (بما أن الاستعلام كله مخصص للـ Active، هذه النقطة ليست ضرورية، ولكن سأتركها هنا للتوضيح)
$data = $conn->query("SELECT COUNT(*) AS total, SUM(status = 'active') AS active_count FROM Manager WHERE status = 'active'")->fetch_assoc();
$stats['active_percentage'] = ($data['active_count'] / $data['total']) * 100;

// 11. نسبة المديرين "Inactive"
$stats['inactive_percentage'] = 100 - $stats['active_percentage'];

// جلب راتب كل موظف كنسبة مئوية من إجمالي الرواتب
$chart_data = $conn->query("
    SELECT 
        Name_Manager AS name, 
        (Salary / (SELECT SUM(Salary) FROM Manager WHERE status = 'active') * 100) AS salary_percentage 
    FROM Manager 
    WHERE status = 'active'
");

// جلب نسب الجنس
$chart_cycle = $conn->query("
    SELECT 
        Gender, 
        (COUNT(*) / (SELECT COUNT(*) FROM Manager WHERE status = 'active') * 100) AS gender_percentage 
    FROM Manager 
    WHERE status = 'active'
    GROUP BY Gender
");

// تحويل الإحصائيات إلى JSON
$response = [
    'chart_data' => $chart_data->fetch_all(MYSQLI_ASSOC),
    'chart_cycle' => $chart_cycle->fetch_all(MYSQLI_ASSOC),
    'stats' => $stats
];

// إرجاع البيانات كـ JSON
header('Content-Type: application/json');
echo json_encode($response);

// غلق الاتصال بقاعدة البيانات
$conn->close();
?>

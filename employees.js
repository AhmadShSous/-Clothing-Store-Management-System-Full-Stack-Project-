function toggleJobFields() {
    const jobType = document.getElementById('job_type').value;
    const fullTimeFields = document.getElementById('full_time_fields');
    const partTimeFields = document.getElementById('part_time_fields');

    if (jobType === 'full_time') {
        fullTimeFields.classList.remove('hidden');
        partTimeFields.classList.add('hidden');
    } else if (jobType === 'part_time') {
        partTimeFields.classList.remove('hidden');
        fullTimeFields.classList.add('hidden');
    } else {
        fullTimeFields.classList.add('hidden');
        partTimeFields.classList.add('hidden');
    }
}

function showForm(formId) {
    document.querySelectorAll(".form").forEach((form) => {
        form.classList.add("hidden");
    });
    document.getElementById(formId).classList.remove("hidden");

    // Fetch the active managers for the update form dynamically
    if (formId === 'updateForm') {
        fetch('employees.php?action=get_active_managers')
            .then(response => response.text())
            .then(data => {
                document.getElementById('update_manager_id').innerHTML = data;
            })
            .catch(error => console.error('Error fetching managers:', error));
    }
}

// Fetch query results for the employee table
function fetchQuery(action) {
    fetch('employees.php?action=' + action)
        .then(response => response.text())
        .then(data => {
            document.getElementById('queryContent').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}

// Search employee based on name
function searchEmployee() {
    const employeeName = document.getElementById('employee_name').value;
    fetch('employees.php?action=search&employee_name=' + encodeURIComponent(employeeName))
        .then(response => response.text())
        .then(data => {
            document.getElementById('queryContent').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}





function toggleShowStatesForm() {
    const form = document.getElementById("states-form");
    form.style.display = form.style.display === "none" ? "block" : "none";

    // تأكد من وجود الرسم البياني عند إظهار النموذج
    if (form.style.display === "block") {
        fetchData('employees_stats.php')
            .then(data => {
                displayStats(data.stats);
                if (data.chart_data) {
                    createChart(data.chart_data);
                } else {
                    console.error('Chart data is missing.');
                }
                if (data.chart_cycle) {
                    createProfitChart(data.chart_cycle);  // رسم بياني دائري لعرض نسبة الربح
                } else {
                    console.error('Chart data is missing.');
                }
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }
}

// دالة لجلب البيانات من ملف PHP
function fetchData(url) {
    return fetch(url)
        .then(response => response.json())
        .catch(error => {
            console.error('Error fetching data:', error);
            throw error;
        });
}





// دالة لعرض الإحصائيات في الجدول
function displayStats(stats) {
    const statsTable = document.querySelector('#statsTable tbody');
    statsTable.innerHTML = `
        <tr>
            <td>Full-time Employees Percentage</td>
            <td>${stats.full_time_percentage}%</td>
        </tr>
        <tr>
            <td>Part-time Employees Percentage</td>
            <td>${stats.part_time_percentage}%</td>
        </tr>
        <tr>
            <td>Female Employees Percentage</td>
            <td>${stats.female_percentage}%</td>
        </tr>
        <tr>
            <td>Male Employees Percentage</td>
            <td>${stats.male_percentage}%</td>
        </tr>
        <tr>
            <td>Male Employees in Full-time Percentage</td>
            <td>${stats.male_full_time_percentage}%</td>
        </tr>
        <tr>
            <td>Female Employees in Full-time Percentage</td>
            <td>${stats.female_full_time_percentage}%</td>
        </tr>
        <tr>
            <td>Male Employees in Part-time Percentage</td>
            <td>${stats.male_part_time_percentage}%</td>
        </tr>
        <tr>
            <td>Female Employees in Part-time Percentage</td>
            <td>${stats.female_part_time_percentage}%</td>
        </tr>
        <tr>
            <td>Oldest Employee</td>
            <td>${stats.oldest_employee.Name_Emp} (Hired on: ${stats.oldest_employee.HireDate})</td>
        </tr>
        <tr>
            <td>Newest Employee</td>
            <td>${stats.newest_employee.Name_Emp} (Hired on: ${stats.newest_employee.HireDate})</td>
        </tr>
        <tr>
            <td>Active Employees Percentage</td>
            <td>${stats.active_percentage}%</td>
        </tr>
        <tr>
            <td>Inactive Employees Percentage</td>
            <td>${stats.inactive_percentage}%</td>
        </tr>
        <tr>
            <td>Average Full-time Salary</td>
            <td>${stats.avg_full_time_salary}</td>
        </tr>
        <tr>
            <td>Highest Full-time Salary</td>
            <td>${stats.highest_full_time_salary}</td>
        </tr>
        <tr>
            <td>Lowest Full-time Salary</td>
            <td>${stats.lowest_full_time_salary}</td>
        </tr>
        <tr>
            <td>Maximum Hours Worked by Part-time Employee</td>
            <td>${stats.max_part_time_hours}</td>
        </tr>
        <tr>
            <td>Minimum Hours Worked by Part-time Employee</td>
            <td>${stats.min_part_time_hours}</td>
        </tr>
        <tr>
            <td>Highest Hourly Rate for Part-time Employee</td>
            <td>${stats.highest_part_time_hour_rate}</td>
        </tr>
        <tr>
            <td>Lowest Hourly Rate for Part-time Employee</td>
            <td>${stats.lowest_part_time_hour_rate}</td>
        </tr>
        <tr>
            <td>Highest Payment for Part-time Employee</td>
            <td>${stats.highest_part_time_payment}</td>
        </tr>
        <tr>
            <td>Lowest Payment for Part-time Employee</td>
            <td>${stats.lowest_part_time_payment}</td>
        </tr>
        <tr>
            <td>Total Full-time Salaries</td>
            <td>${stats.total_full_time_salary}</td>
        </tr>
        <tr>
            <td>Total Part-time Payments</td>
            <td>${stats.total_part_time_payment}</td>
        </tr>
        <tr>
            <td>Total Payments (Full-time + Part-time)</td>
            <td>${stats.total_payments}</td>
        </tr>
    `;
}




// دالة لإنشاء الرسم البياني
let chartInstance; // متغير عالمي لتخزين الرسم البياني

function createChart(chartData) {
    const labels = chartData.map(item => item.name); // أسماء الموظفين
    const fullTimeSalaries = chartData.map(item => item.full_time_salary || 0); // رواتب الفل تايم
    const partTimePayments = chartData.map(item => item.part_time_total_payment || 0); // إجمالي أجر البارت تايم

    const ctx = document.getElementById('productChart').getContext('2d');

    // إذا كان هناك رسم بياني موجود، قم بإتلافه
    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Full-Time Salaries',
                    data: fullTimeSalaries,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Part-Time Total Payments',
                    data: partTimePayments,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Full-Time Salaries vs Part-Time Payments' },
                datalabels: {
                    display: true,
                    color: 'black',
                    font: {
                        size: 12
                    }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}
let chartInstance1; // متغير عالمي لتخزين الرسم البياني

function createProfitChart(data) {
    const ctx = document.getElementById('profitChart').getContext('2d');

    // إذا كان هناك رسم بياني موجود، قم بإتلافه
    if (chartInstance1) {
        chartInstance1.destroy();
    }

    const labels = ['Full Time', 'Part Time']; // التصنيفات (فل تايم وبارت تايم)
    const fullTimePercentage = data.find(item => item.category === 'Full Time').percentage; // نسبة الفل تايم
    const partTimePercentage = 100 - fullTimePercentage; // نسبة البارت تايم بناءً على نسبة الفل تايم

    const percentages = [fullTimePercentage, partTimePercentage]; // إعداد البيانات بالنسبتين

    chartInstance1 = new Chart(ctx, {
        type: 'pie', // نوع الرسم البياني دائري
        data: {
            labels: labels,
            datasets: [{
                label: 'Employment Type Percentage (%)',
                data: percentages,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)', // لون للفل تايم
                    'rgba(255, 159, 64, 0.5)'  // لون للبارت تايم
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Full-Time vs Part-Time Employees (%)'
                }
            }
        }
    });
}
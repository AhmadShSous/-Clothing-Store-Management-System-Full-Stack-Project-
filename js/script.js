function showForm(formId) {
    document.querySelectorAll(".form").forEach((form) => {
        form.classList.add("hidden");
    });
    document.getElementById(formId).classList.remove("hidden");
}


function fetchQuery(action) {
    fetch(`process.php?action=${action}`)
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
        fetchData('manager_stats.php')
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
            <td>Average Salary</td>
            <td>${stats.avg_salary}</td>

        </tr>
        <tr>
            <td>Manager with Highest Salary</td>
            <td>${stats.highest_salary_manager.Name_Manager} ($${stats.highest_salary_manager.Salary})</td>
        </tr>
        <tr>
            <td>Manager with Lowest Salary</td>
            <td>${stats.lowest_salary_manager.Name_Manager} ($${stats.lowest_salary_manager.Salary})</td>
        </tr>
        <tr>
            <td>Average Age</td>
            <td>${stats.avg_age}</td>
        </tr>
        <tr>
            <td>Youngest Manager</td>
            <td>${stats.youngest_manager.Name_Manager} (${stats.youngest_manager.Age} years)</td>
        </tr>
        <tr>
            <td>Percentage of Female Managers</td>
            <td>${stats.female_percentage}%</td>           
        </tr>
        <tr>
            <td>Percentage of Male Managers</td>
            <td>${stats.male_percentage}%</td>         
        </tr>
        <tr>
            <td>Most Common Address</td>
            <td>${stats.most_common_address.Manager_Address} (#${stats.most_common_address.count})</td>           
        </tr>
        <tr>
            <td>Least Common Address</td>
            <td>${stats.least_common_address.Manager_Address} (#${stats.least_common_address.count})</td>           
        </tr>
        <tr>
            <td>Percentage of Active Managers</td>
            <td>${stats.active_percentage}%</td>
        </tr>
        <tr>
            <td>Percentage of Inactive Managers</td>
            <td>${stats.inactive_percentage}%</td>
        </tr>
    `;
}

// دالة لإنشاء الرسم البياني
let chartInstance; // متغير عالمي لتخزين الرسم البياني
function createChart(chartData) {
    const labels = chartData.map(item => item.name); // أسماء المديرين
    const percentages = chartData.map(item => item.salary_percentage); // النسب المئوية للرواتب


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
                    label: 'Salary Percentage (%)',
                    data: percentages,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    // إضافة التعليقات التوضيحية
                    
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Salary Percentage of Each Manager' },
                datalabels: {
                    display: true,
                    color: 'black',  // لون النص داخل الأعمدة
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

// دالة لإنشاء الرسم البياني الدائري
function createProfitChart(data) {
    const ctx = document.getElementById('profitChart').getContext('2d');

    // إذا كان هناك رسم بياني موجود، قم بإتلافه
    if (chartInstance1) {
        chartInstance1.destroy();
    }

    const labels = data.map(item => item.Gender); // الجنس (ذكر/أنثى)
    const percentages = data.map(item => item.gender_percentage); // النسب المئوية لكل جنس

    chartInstance1 = new Chart(ctx, {
        type: 'pie',  // نوع الرسم البياني دائري
        data: {
            labels: labels,
            datasets: [{
                label: 'Gender Percentage (%)',
                data: percentages,
                backgroundColor: ['rgba(75, 192, 192, 0.5)',
                    'rgba(255, 159, 64, 0.5)'],
                borderColor: ['rgba(75, 192, 192, 1)',
                    'rgba(255, 159, 64, 1)'],
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
                    text: 'Gender Distribution in Percentage'
                }
            }
        }
    });
}


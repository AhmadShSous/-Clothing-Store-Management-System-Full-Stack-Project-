document.getElementById("show-orders-btn").addEventListener("click", function () {
    fetch("orders.php?action=show_orders")
        .then(response => response.text())
        .then(data => {
            document.getElementById("orders-list").innerHTML = data;
        })
        .catch(error => console.error("Error fetching orders:", error));
});

document.getElementById("query-form").addEventListener("submit", function (event) {
    event.preventDefault();
    const orderDate = document.getElementById("order-date").value;

    fetch("orders.php?action=query_total&order_date=" + orderDate)
        .then(response => response.text())
        .then(data => {
            document.getElementById("query-result").innerHTML = data;
        })
        .catch(error => console.error("Error querying orders:", error));
});


// دالة لتحميل الإحصائيات من orders_stats.php
function loadOrderStatistics() {
    console.log("Fetching statistics...");

    // طلب بيانات الإحصائيات من orders_stats.php
    fetch("orders_stats.php")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {

            // تحديد الجدول الذي سيتم عرض الإحصائيات فيه
            const table = document.getElementById("stats-table");
            table.innerHTML = ""; // تفريغ الجدول قبل إضافة البيانات الجديدة

           // إضافة البيانات للإحصائيات
           table.innerHTML += `
           <tr><td>Total Price of Orders</td><td>$${data.total_price}</td></tr>
           <tr><td>Most Sold Product</td><td>${data.most_sold.Name_product} (${data.most_sold.total_quantity} units)</td></tr>
           <tr><td>Least Sold Product</td><td>${data.least_sold.Name_product} (${data.least_sold.total_quantity} units)</td></tr>
           <tr><td>Date with Least quantity Sales</td><td>${data.least_sales_date.order_date} (${data.least_sales_date.total_quantity} units)</td></tr>
           <tr><td>Date with Most quantity Sales</td><td>${data.most_sales_date.order_date} (${data.most_sales_date.total_quantity} units)</td></tr>
           <tr><td>Date with Highest Total Price</td><td>${data.highest_price_date.order_date} ($${data.highest_price_date.total_price})</td></tr>
           <tr><td>Date with Lowest Total Price</td><td>${data.lowest_price_date.order_date} ($${data.lowest_price_date.total_price})</td></tr>`;
        
           if (data.chart_data) {
            createChart(data.chart_data);
        } else {
            console.error('Chart data is missing.');
        }
        if (data.chart_cycle ) {
            createProfitChart(data.chart_cycle);  // رسم بياني دائري لعرض نسبة الربح
        } else {
            console.error('Chart data is missing.');
        }
        
        })
        .catch(error => console.error("Error fetching order statistics:", error));
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

// دالة لإنشاء الرسم البياني
let chartInstance; // متغير عالمي لتخزين الرسم البياني
function createChart(chartData) {
    const labels = chartData.map(item => item.Name_product);
    const prices = chartData.map(item => item.total_quantity);

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
                    label: 'Quantity for soled product',
                    data: prices,
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
                title: { display: true, text: 'Quantities for soled products' },
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

    const labels = data.map(item => item.order_date);
    const profitMargins = data.map(item => (item.total_price/item.grand_total)*100);

    chartInstance1 = new Chart(ctx, {
        type: 'pie',  // نوع الرسم البياني دائري
        data: {
            labels: labels,
            datasets: [{
                label: 'toatl sales in date %',
                data: profitMargins,
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)', 'rgba(255, 99, 132, 1)'],
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
                    text: 'total sales in a date by total sales'
                }
            }
        }
    });
}




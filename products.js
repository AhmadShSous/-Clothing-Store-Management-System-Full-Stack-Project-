
// دالة لعرض أو إخفاء قائمة المنتجات
function toggleProductList() {
    const productList = document.getElementById("product-list");
    productList.style.display = productList.style.display === "none" ? "block" : "none";
};

// دالة لعرض أو إخفاء نموذج إضافة مزود جديد
function toggleSupplierForm() {
    const supplierForm = document.getElementById("supplier-form");
    supplierForm.style.display = supplierForm.style.display === "none" ? "block" : "none";
};

function toggleProductForm() {
    var form = document.getElementById("product-form");
    form.style.display = (form.style.display === "none") ? "block" : "none";
};

function toggleDeleteSupplierForm() {
    const form = document.getElementById('delete-supplier-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
};



function toggleUpdateProductForm() {
    const form = document.getElementById('update-product-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
};

function toggleShowStatesForm() {
    const form = document.getElementById("states-form");
    form.style.display = form.style.display === "none" ? "block" : "none";

    // تأكد من وجود الرسم البياني عند إظهار النموذج
    if (form.style.display === "block") {
        fetchData('products_stats.php')
            .then(data => {
                displayStats(data.stats);
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
            <td>${stats.highest_margin.Name_product} (${stats.highest_margin.Profit_margin}%)</td>
            <td>Product with Highest Profit Margin</td>

        </tr>
        
        <tr>
            <td>${stats.highest_profit_value.Name_product} ($${stats.highest_profit_value.profit_value})</td>
            <td>Product with Highest Profit Value(without discount)</td>

        </tr>
        <tr>
            <td>${stats.lowest_margin.Name_product} (${stats.lowest_margin.Profit_margin}%)</td>
            <td>Product with Lowest Profit Margin</td>
        </tr>
        <tr>
            <td>${stats.lowest_profit_value.Name_product} ($${stats.lowest_profit_value.profit_value})</td>
            <td>Product with Lowest Profit Value(without discount)</td>
        </tr>
         <tr>
            <td>${stats.highest_discount.Name_product} (${stats.highest_discount.discount}%)</td>
            <td>Product with higest discount</td>
        </tr>
         <tr>
            <td>${stats.lowest_discount.Name_product} (${stats.lowest_discount.discount}%)</td>
            <td>Product with Lowest discount</td>
        </tr>
         <tr>
            <td>${stats.highest_discount_value.Name_product} ($${stats.highest_discount_value.discount_value})</td>
            <td>Product with highest discount value</td>
        </tr>
         <tr>
            <td>${stats.lowest_discount_value.Name_product} ($${stats.lowest_discount_value.discount_value})</td>
            <td>Product with lowest discount value</td>
        </tr>
        <tr>
            <td>${stats.highest_stock.Name_product} (${stats.highest_stock.Stock})</td>
            <td>Product with Highest Stock</td>
        </tr>
        <tr>
            <td>${stats.lowest_stock.Name_product} (${stats.lowest_stock.Stock})</td>
            <td>Product with Lowest Stock</td>
        </tr>
        <tr>
            <td>$${stats.total_import_price}</td>
            <td>Total Import Price</td>
        </tr>
        <tr>
            <td>$${stats.total_profit_value}</td>
            <td>Total Profit Value</td>
        </tr>
        <tr>
            <td>$${stats.total_discount_value}</td>
            <td>Total discount Value</td>
        </tr>
        <tr>
            <td>$${stats.total_price_value}</td>
            <td>Total price Value without discount</td>
        </tr>
        <tr>
            <td>$${stats.total_price_value_discount}</td>
            <td>Total price Value with discount</td>
        </tr>
         <tr>
            <td>${stats.most_frequent_supplier.Name_Sup} (#${stats.most_frequent_supplier.frequency})</td>
            <td>Most Frequent Supplier</td>
        </tr>
        <tr>
            <td>${stats.least_frequent_supplier.Name_Sup} (#${stats.least_frequent_supplier.frequency})</td>
            <td>Least Frequent Supplier</td>
        </tr>
    `;
}

// دالة لإنشاء الرسم البياني
let chartInstance; // متغير عالمي لتخزين الرسم البياني
function createChart(chartData) {
    const labels = chartData.map(item => item.Name_product);
    const prices = chartData.map(item => item.price);
    const discountedPrices = chartData.map(item => item.price * (1 - item.discount / 100)); // تطبيق الخصم على الأسعار

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
                    label: 'Original Prices',
                    data: prices,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    // إضافة التعليقات التوضيحية
                    
                },
                {
                    label: 'Discounted Prices',
                    data: discountedPrices,
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
                title: { display: true, text: 'Product Prices (With and Without Discount)' },
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

    const labels = data.map(item => item.Name_product);
    const profitMargins = data.map(item => item.Profit_margin);

    chartInstance1 = new Chart(ctx, {
        type: 'pie',  // نوع الرسم البياني دائري
        data: {
            labels: labels,
            datasets: [{
                label: 'Profit Margin %',
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
                    text: 'Profit Margin by Product'
                }
            }
        }
    });
}




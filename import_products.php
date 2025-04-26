<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root1234", "GH");

// التحقق من الاتصال بقاعدة البيانات
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// جلب المديرين من قاعدة البيانات
$manager_result = $conn->query("SELECT ID, Name_Manager FROM Manager where status='active'");

// جلب الموردين من قاعدة البيانات
$supplier_result = $conn->query("SELECT ID_Sup, Name_Sup FROM Supplier where status='active'");
$supplier_result1 = $conn->query("SELECT ID_Sup, Name_Sup FROM Supplier where status='active'");

// جلب المنتجات من قاعدة البيانات
$product_result = $conn->query("SELECT Product.ID_P, Product.Name_product, Product.category,Import_price,Profit_margin,Product.price, Product.color, Product.material, Product.discount, Product.Stock, Supplier.Name_Sup, Manager.Name_Manager
FROM Product
JOIN Import_product ON Product.ID_P = Import_product.ID_P
JOIN Supplier ON Import_product.ID_Sup = Supplier.ID_Sup
JOIN Manager ON Import_product.ID_Man = Manager.ID");

// غلق الاتصال بقاعدة البيانات
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Products</title>
    <link rel="stylesheet" href="products.css">
</head>

<body>

    <div class="header">
        <img src="projectBase/project.jpg" alt="Logo">
        <div class="navbar">
            <div class="navbar-links">
                <a href="http://localhost/index.html">Manager Management</a>
                <a href="http://localhost/employeesAll.html">Employees Management</a>
                <a href="http://localhost/import_products.php">Product Management</a>
                <a href="http://localhost/orders.html">All Orders</a>
            </div>
        </div>
    </div>
 


    <h1 style="text-align: center;">Import Products</h1>

    <!-- زر لفتح نموذج إضافة مزود -->
    
        <button onclick="toggleSupplierForm()">Add New Supplier</button>

        <!-- نموذج إضافة مزود جديد -->
        <div id="supplier-form" style="display: none;">
            <form method="POST" action="add_supplier.php">
                <h2>Add New Supplier</h2>
                <label for="supplier-name">Supplier Name:</label>
                <input type="text" name="supplier_name" id="supplier-name" required>

                <label for="supplier-email">Supplier Email:</label>
                <input type="email" name="supplier_email" id="supplier-email" required>

                <label for="supplier-phone">Supplier Phone:</label>
                <input type="text" name="supplier_phone" id="supplier-phone" required>

                <button type="submit">Add Supplier</button>
            </form>
        </div>
    
    <!-- زر حذف المزود -->
        <button onclick="toggleDeleteSupplierForm()">Delete Supplier</button>

    <!-- نموذج حذف المزود -->
        <div id="delete-supplier-form" style="display: none;">
            <form method="POST" action="delete_supplier.php">
                <h2>Delete Supplier</h2>
                <label for="supplier-id">Select Supplier:</label>
                <select name="supplier_id" id="supplier-id" required>
                    <option value="">-- Select Supplier --</option>
                    <?php
                // جلب المزودين من قاعدة البيانات
                    if ($supplier_result1->num_rows > 0) {
                        while ($row = $supplier_result1->fetch_assoc()) {
                            echo '<option value="' . $row['ID_Sup'] . '">' . $row['Name_Sup'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <button type="submit">Delete Supplier</button>
            </form>
        </div>

    
    <!-- زر لفتح نموذج إضافة منتج -->
    <button onclick="toggleProductForm()">Add New Product</button>

    <!-- نموذج إضافة منتج جديد -->
    <div id="product-form" style="display: none;">
        <form method="POST" action="products.php">
            <h2>Add New Product</h2>
            <label for="manager">Select Manager:</label>
            <select name="manager_id" id="manager" required>
                <option value="">-- Select Manager --</option>
                <?php
                if ($manager_result->num_rows > 0) {
                    while ($row = $manager_result->fetch_assoc()) {
                        echo '<option value="' . $row['ID'] . '">' . $row['Name_Manager'] . '</option>';
                    }
                }
                ?>
            </select>

            <label for="supplier">Select Supplier:</label>
            <select name="supplier_id" id="supplier" required>
                <option value="">-- Select Supplier --</option>
                <?php
                if ($supplier_result->num_rows > 0) {
                    while ($row = $supplier_result->fetch_assoc()) {
                        echo '<option value="' . $row['ID_Sup'] . '">' . $row['Name_Sup'] . '</option>';
                    }
                }
                ?>
            </select>

            <label for="product-name">Product Name:</label>
            <input type="text" name="product_name" id="product-name" required>

            <label for="import-price">Import Price:</label>
            <input type="number" name="import_price" id="import-price" step="0.01" required>

            <label for="profit-margin">Profit Margin (%):</label>
            <input type="number" name="profit_margin" id="profit-margin" step="0.01" required>

            <label for="color">Color:</label>
            <input type="text" name="color" id="color" required>

            <label for="material">Material:</label>
            <input type="text" name="material" id="material" required>

            <label for="discount">Discount:</label>
            <input type="number" name="discount" id="discount" value="0">

            <label for="stock">Stock:</label>
            <input type="number" name="stock" id="stock" required>

            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <option value="Pants">Pants</option>
                <option value="Shirts">Shirts</option>
                <option value="Hoodies">Hoodies</option>
                <option value="Qamees">Qamees</option>
                <option value="Sweaters">Sweaters</option>
            </select>

            <label for="image_url">Image URL:</label>
            <input type="text" name="image_url" id="image_url">

            <button type="submit">Import Product</button>
        </form>
    </div>

    <button onclick="toggleUpdateProductForm()">Update product</button>
        <!-- نموذج تحديث المنتج -->
    <div id="update-product-form" style="display: none;">
        <form method="POST" action="update_product.php">
            <h2>Update Product</h2>
        
            <label for="product-id">Product ID:</label>
            <input type="number" name="product_id" id="product-id" required>

            <label for="product-name">Product Name:</label>
            <input type="text" name="product_name" id="product-name" required>

            <label for="import-price">Import Price:</label>
            <input type="number" name="import_price" id="import-price" step="0.01" required>

            <label for="profit-margin">Profit Margin (%):</label>
            <input type="number" name="profit_margin" id="profit-margin" step="0.01" required>

            <label for="color">Color:</label>
            <input type="text" name="color" id="color" required>

            <label for="material">Material:</label>
            <input type="text" name="material" id="material" required>

            <label for="discount">Discount:</label>
            <input type="number" name="discount" id="discount" value="0">

            <label for="stock">Stock:</label>
            <input type="number" name="stock" id="stock" required>

            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <option value="Pants">Pants</option>
                <option value="Shirts">Shirts</option>
                <option value="Hoodies">Hoodies</option>
                <option value="Qamees">Qamees</option>
                <option value="Sweaters">Sweaters</option>
            </select>

            <label for="image_url">Image URL:</label>
            <input type="text" name="image_url" id="image_url">

            <button type="submit">Update Product</button>
        </form>
    </div>

    <!-- أزرار إظهار المزودين والمنتجات -->
    <form>
    <button onclick="toggleProductList()">Show All Products</button>
    </form>
    <form method="POST" action="manage_products.php">
    <button type="submit" name="show_suppliers">Show Suppliers</button>
    </form>


    <!-- قائمة المنتجات -->
    <div id="product-list">
        <h2>Product List</h2>
        <table>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Import Price</th>
                <th>Profit Margin</th>
                <th>Price</th>
                <th>Color</th>
                <th>Material</th>
                <th>Discount</th>
                <th>Stock</th>
                <th>Supplier</th>
                <th>Manager</th>
            </tr>
            <?php
            if ($product_result->num_rows > 0) {
                while ($row = $product_result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row['ID_P'] . "</td>
                            <td>" . $row['Name_product'] . "</td>
                            <td>" . $row['category'] . "</td>
                            <td>$" . $row['Import_price'] . "</td> <!-- إضافة $ -->
                            <td>" . $row['Profit_margin'] . "%</td> <!-- إضافة % -->
                            <td>$" . $row['price'] . "</td> <!-- إضافة $ -->
                            <td>" . $row['color'] . "</td>
                            <td>" . $row['material'] . "</td>
                            <td>" . $row['discount'] . "%</td>
                            <td>" . $row['Stock'] . "</td>
                            <td>" . $row['Name_Sup'] . "</td>
                            <td>" . $row['Name_Manager'] . "</td>
                        </tr>";
                }
            }
            ?>
        </table>
    </div>
    <!-- نموذج البحث عن المنتجات -->
    <div>
        <h2>Search Products</h2>
        <form method="GET" action="search_products.php">
            <label for="search-product">Product Name:</label>
            <input type="text" name="product_name" id="search-product" required>
            <button type="submit">Search</button>
        </form>
        <form method="GET" action="search_products.php">
            <label for="search-product">Caterogy:</label>
            <input type="text" name="Caterogy_type" id="search-product" required>
            <button type="submit">Search</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <div id="states-form" style="display: none;">
        <h2>Product Statistics</h2>
        <table id="statsTable" border="1">
            <thead>
                <tr>
                    <th>Value</th>
                    <th>Statistic</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div id="charts-container" style="display: flex; justify-content: space-between; width: 100%; padding: 10px;">
            <canvas id="productChart" style="width: 48%; height: 300px;"></canvas>
            <canvas id="profitChart" style="width: 48%; height: 300px;"></canvas>
        </div>
    </div>
    <button onclick="toggleShowStatesForm()">Show States</button>
    <!-- رسم بياني -->
    
    
    <script src="products.js"></script>
</body>
</html>

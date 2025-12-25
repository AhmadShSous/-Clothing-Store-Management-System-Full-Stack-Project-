
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Connect to the database
    $conn = new mysqli("localhost", "root", "root1234", "GH");

    if ($conn->connect_error) {
        die("Failed to connect to the database: " . $conn->connect_error);
    }

    // Read data from the form
    $manager_id = $_POST['manager_id'];
    $supplier_id = $_POST['supplier_id'];
    $product_name = $_POST['product_name'];
    $import_price = $_POST['import_price'];
    $profit_margin = $_POST['profit_margin'];
    $color = $_POST['color'];
    $material = $_POST['material'];
    $discount = $_POST['discount'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];  // Read category
    $image_url = $_POST['image_url'];  // Read image URL
    
    // Calculate the final price
    $price = $import_price + ($import_price * ($profit_margin / 100));

    // Validate data
    if ( empty($category) || empty($manager_id) || empty($supplier_id) || empty($product_name) || empty($import_price) || empty($profit_margin) || empty($color) || empty($material) || empty($stock)) {
        die("Please fill in all required fields.");
    }

    
    
    // Insert product into the Product table
    $stmt = $conn->prepare("INSERT INTO Product (Name_product, price, Import_price, Profit_margin, color, material, discount, Stock, category, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdddssiiss", $product_name, $price, $import_price, $profit_margin, $color, $material, $discount, $stock, $category, $image_url);
    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;

        // Link the product with the supplier and manager in the Import_product table
        $stmt = $conn->prepare("INSERT INTO Import_product (ID_Man, ID_Sup, ID_P) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $manager_id, $supplier_id, $product_id);
        if ($stmt->execute()) {
            echo "Product imported and linked successfully!";
        } else {
            echo "Error inserting link data: " . $conn->error;
        }
    } else {
        echo "Error inserting product data: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

?>

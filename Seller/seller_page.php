<!DOCTYPE html>
<html>
<head>
    <title>LazShopee Selling</title>
    <link rel="stylesheet" href="seller_page.css?version=4">
</head>
<body>

    <a href="../main_page.php"><button>Logout</button></a>

    <a href="products.php"><button>View Products</button></a>
    <a href="products_sold.php"><button>View Products Sold</button></a>
    
    
    <form class="product_form" method="POST" enctype="multipart/form-data">
        
        <h1>Sell in LazShopee</h1>
            <input class="product_name" type="text" name="product_name" placeholder="Product Name" required><br><br>
            <input class="product_desc" type="text" name="product_desc" placeholder="Product Description" required><br><br>
            <input class="product_price" type="text" name="product_price" placeholder="Product Price" required><br><br>
            <input class="product_quantity" type="number" name="product_quantity" placeholder="Quantity" required><br><br>
            <input class="product_pic" type="file" name="file_pic">
            <input type="hidden" name="seller_id">
            <br><br><br>
            <input class="submit_button" type="submit" value="Add Product">
            
        </div>
    </form>
    
    <?php
include("../admin/database.php");
include"functions.php";


?>


</body>
</html>

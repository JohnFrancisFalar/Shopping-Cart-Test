<?php
?>

<!DOCTYPE html>
<html>
    <head>
        <title>LazShopee</title>
        <link rel="stylesheet" href="main_page.css?version=3">
    </head>
    <body>
        <div class = "Main_Page_Menu">
        <h1>
            LazShopee
        </h1>
        
        <div class = "Links">
        <a href="Seller/signin.php" class = "Sell-Products"><button>Sell Products</button></a>
        <a href="Buyer/signin.php" class = "Buy-Products"><button>Buy Products</button></a>
        <?php
        session_start();
        ?>
        </div>
        </form>
        </div>
        <p class = "Desc">
        Shop 'til you drop <br>with LazShopee
        </p>
    </body>
</html>
<?php
session_destroy();
?>
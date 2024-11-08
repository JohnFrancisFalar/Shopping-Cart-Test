<?php
require('fpdf.php');
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="receipt.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'LAZSHOPEE', 0, 1, 'C');
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 10, 'Receipt', 0, 1, 'C');
$pdf->Ln(10);

include "../Admin/database.php";
include "functions.php";

if (!isset($_SESSION['acct_email'])) {
    die("You need to log in to view your order history.");
}

$buyer_email = $_SESSION['acct_email'];
$buyer_id_query = "SELECT buyer_id, buyer_name FROM buyer_info WHERE buyer_email = ?";
$stmt = mysqli_prepare($conn, $buyer_id_query);
mysqli_stmt_bind_param($stmt, "s", $buyer_email);
mysqli_stmt_execute($stmt);
$buyer_id_result = mysqli_stmt_get_result($stmt);
$buyer_id_row = mysqli_fetch_assoc($buyer_id_result);
$buyer_id = $buyer_id_row['buyer_id'];
$buyer_name = $buyer_id_row['buyer_name'];

if (!$buyer_id) {
    die("Buyer ID not found.");
}

$latest_order_query = "
    SELECT order_id 
    FROM orders_history
    WHERE buyer_id = ? 
    ORDER BY order_date DESC 
    LIMIT 1";

$latest_order_stmt = mysqli_prepare($conn, $latest_order_query);
mysqli_stmt_bind_param($latest_order_stmt, "i", $buyer_id);
mysqli_stmt_execute($latest_order_stmt);
$latest_order_result = mysqli_stmt_get_result($latest_order_stmt);
$latest_order_row = mysqli_fetch_assoc($latest_order_result);

if (!$latest_order_row) {
    die("No orders found.");
}

$latest_order_id = $latest_order_row['order_id'];

$order_date = date("Y-m-d H:i:s");
$formatted_date = date("F j, Y", strtotime($order_date));

$get_order_details = "
    SELECT 
        p.product_name AS 'Product Name', 
        p.product_price AS 'Product Price', 
        oh.order_quantity AS 'Order Quantity', 
        oh.total_amount AS 'Total Amount',
        oh.payment_method AS 'Payment Method'
    FROM 
        orders_history oh
    JOIN 
        products p ON p.product_id = oh.product_id
    WHERE 
        oh.order_id = ?";

$get_order_stmt = mysqli_prepare($conn, $get_order_details);
mysqli_stmt_bind_param($get_order_stmt, "i", $latest_order_id);
$execute_result = mysqli_stmt_execute($get_order_stmt);

if ($execute_result === false) {
    die("Error executing statement: " . mysqli_stmt_error($get_order_stmt));
}

$result = mysqli_stmt_get_result($get_order_stmt);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Order ID: ' . $latest_order_id, 0, 1);
$pdf->Cell(0, 10, 'Order Date: ' . $formatted_date, 0, 1);
$pdf->Cell(0, 10, 'Buyer Name: ' . $buyer_name, 0, 1);

$payment_method = '';

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $payment_method = $row['Payment Method'];
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Payment Method: ' . $payment_method, 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);

    // Set a fixed table width (adjust as needed)
    $tableWidth = 200; // Adjust this value based on your desired table width

    // Calculate cell widths based on the table width
    $productNameWidth = 80;
    $priceWidth = 40;
    $quantityWidth = 30;
    $totalWidth = 40;

    // Draw header with dashed lines
    drawDashedLine($pdf);
    $pdf->Cell($productNameWidth, 10, 'Product Name', 0, 0, 'C');
    $pdf->Cell($priceWidth, 10, 'Price', 0, 0, 'C');
    $pdf->Cell($quantityWidth, 10, 'Quantity', 0, 0, 'C');
    $pdf->Cell($totalWidth, 10, 'Total', 0, 1, 'C');
    drawDashedLine($pdf);

    do {
        $pdf->Cell($productNameWidth, 10, $row['Product Name'], 0, 0);
        $pdf->Cell($priceWidth, 10, 'P' . number_format($row['Product Price'], 2), 0, 0);
        $pdf->Cell($quantityWidth, 10, $row['Order Quantity'], 0, 0);
        $pdf->Cell($totalWidth, 10, 'P' . number_format($row['Total Amount'], 2), 0, 1);
        drawDashedLine($pdf); // Draw dashed line after each row
    } while ($row = mysqli_fetch_assoc($result));
} else {
    $pdf->Cell(0, 10, 'No order details found.', 0, 1);
}

mysqli_stmt_close($get_order_stmt);
mysqli_stmt_close($latest_order_stmt);

// Function to draw a dashed line
function drawDashedLine($pdf) {
    $pdf->SetDrawColor(200, 200, 200); // Light gray color
    $pdf->SetLineWidth(0.5);

    for ($x = 10; $x < 200; $x += 5) { // Draw short dashes
        $pdf->Line($x, $pdf->GetY(), $x + 3, $pdf->GetY());
    }
}

$pdf->Output();
?>

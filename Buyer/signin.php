<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="signin.css">
</head>
<body>
    <form method="POST" class="login" action="">
        <h1>Sign in</h1>
        <input class="login_email" type="email" name="acct_email" placeholder="Enter your email" required>
        <br><br>
        <input class="login_pass" type="password" name="acct_pass" placeholder="Enter your pass" required>
        <br><br>
        <input class="login_submit" type="submit" value="Login">
        <br><br>
        Don't have an account? <a class="log_button" href="signup.php">Register here</a>
    </form>

    <?php
    session_start();
    include "../admin/database.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = mysqli_real_escape_string($conn, $_POST['acct_email']);
        $pass = $_POST['acct_pass'];


        $query = "SELECT buyer_id, buyer_pass FROM buyer_info WHERE buyer_email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {

            if (password_verify($pass, $row['buyer_pass'])) {

                $_SESSION['acct_email'] = $email;
                $_SESSION['buyer_id'] = $row['buyer_id'];


                header("Location: buyer_page.php");
                exit;
            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "Invalid email or password.";
        }
    }
    ?>
</body>
</html>

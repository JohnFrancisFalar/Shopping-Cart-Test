<!DOCTYPE html>
<html>
    <head>
        <title>Sign Up</title>
        <link rel="stylesheet" href="signup_page.css">
</head>
<body>
    
    <form class="sign_up" method="POST" action = "">
    <h1>Sign up</h1>
        <input class="signup_email" type="email" name="acct_email" placeholder="Enter your email" required>
        <br>
        <br>
        <input class="signup_name" type="text" name="acct_name" placeholder="Enter your name" required>
        <br>
        <br>
        <input class="signup_pass"type="password" name="acct_pass" placeholder="Enter your password" required>
        <br>
        <br>
        <input class="signup_submit" type="submit" value="Register Now">
        <br><br>
        Already have an account? <a class="Login_link" link rel="" href="buyerlogin_page.php">Login now</a>
</form>
        <?php
        include("database.php");
    if ($_SERVER['REQUEST_METHOD'] == 'POST'
    && !empty($_POST['acct_email'])
    && !empty($_POST['acct_name'])
    && !empty($_POST['acct_pass'])
    ){
        $user_email = $_POST['acct_email'];
        $user_name = $_POST['acct_name'];
        $user_pass = $_POST['acct_pass'];

    
        if(empty($user_email)){
        echo "Please enter email";
    }
    if(empty($user_pass)){
        echo "Please enter password";
}
if(!empty($user_email) && !empty($user_pass)){
    $password_hash = password_hash($user_pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO user_info (user_name, user_email, user_pass) VALUES ('$user_name', '$user_email', '$password_hash')";
    mysqli_query($conn, $sql);
    echo "Registered";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
    }
    mysqli_close($conn);
        ?>
</body>
</html>
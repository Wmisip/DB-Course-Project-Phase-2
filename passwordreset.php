<html>

<head>

</head>

<body>
    <?php
        echo $_SESSION["enabled"];
        echo $_SESSION["role"];
        echo $_SESSION["username"];
    ?>
    <p>Welcome, it's your first time logging in! Please reset your password to continue</p>;
    <?php
    echo '
    <form action="passwordreset.php" method="POST">
        <label for="password">New Password:</label>
        <input type="password" name="password" id="password_box" >
        <br>
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" name="confirmPassword" id="password_box">
    </form>
        ';
    ?>
</body>

</html>

<?php
    require "db.php";

    if($_POST["password"] == $_POST["confirmPassword"]){
        resetPassword($_SESSION["username"], $_POST["password"]);
        header("REDIRECT:main.php");
    }else{
        echo '<p style="color: red">Password not the same!</p>';
    }

?>
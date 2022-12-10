<?php
    require "common.php";
    session_start();
?>

<html>

<head>

</head>

<body>
    <?php
        echo "<pre>";
        print_r($_SESSION);
        print_r($_POST);
        echo "</pre>";
    ?>
    <?php
    echo "
    <p>Welcome, " . $_SESSION["role"] . " it's your first time logging in! Please reset your password to continue</p>
    ";
    ?>

    <?php
    echo '
    <form action="passwordreset.php" method="POST">
        <label for="password">New Password:</label>
        <input type="password" name="password" id="password_box" >
        <br>
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" name="confirmPassword" id="password_box">
        <br>
        <input type="submit" name="newPasswordSubmit" value="Submit">
      
    </form>
        ';
    ?>
</body>

</html>

<?php

    if(isset($_POST["newPasswordSubmit"])){
        if($_POST["password"] == $_POST["confirmPassword"]){
        echo 'passwords the same';
        resetPassword($_SESSION["username"], $_POST["password"]);
        if(isEnabled($_SESSION["username"]) == true){
            $_SESSION["enabled"] = "true";
        } else{
            echo '<p style="color: red">Problem resetting password!</p>';
        }
        header("LOCATION:main.php");
        }
        else{
            echo '<p style="color: red">Password not the same!</p>';
        }
    }
?>
<?php
    require "common.php";
    session_start();
?>

<?php
    if(isset($_POST["newPasswordSubmit"])){
        if($_POST["password"] == $_POST["confirmPassword"]){
        echo 'passwords the same';
        resetPassword($_SESSION["username"], $_POST["password"]);
        if(isEnabled($_SESSION["username"]) == true){
            $_SESSION["enabled"] = "true";
        } else{
            echo '<div>';
            echo '<b style="color: red">Problem resetting password!</b>';
            echo '</div>';
        }
        header("LOCATION:main.php");
        }
        else{
            echo '<div>';
            echo '<b style="color: red">Password not the same!</b>';
            echo '</div>';
        }
    }
?>

<style>
<?php include 'CSS/main.css'; ?>
</style>

<html>

<head>

</head>

<body>
    <?php
    echo '<div class="resetPasswordContainer">
    <p>Welcome, ' . $_SESSION["role"] . " it's your first time logging in! Please reset your password to continue</p>
    ";
    ?>

    <?php
    echo '
    <form action="passwordreset.php" method="POST">
        <label class="textLabel" for="password">New Password:</label> <br>
        <input type="password" name="password" class="textInput" placeholder="New Password">
        <br><br>
        <label class="textLabel" for="confirmPassword">Confirm Password:</label> <br>
        <input type="password" name="confirmPassword" class="textInput" placeholder="Confirm Password">
        <br>
        <input type="submit" name="newPasswordSubmit" class="submitBtn" value="Submit">
      
    </form>
    </div>
        ';
    ?>
</body>

</html>


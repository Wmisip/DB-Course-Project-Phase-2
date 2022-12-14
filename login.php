<?php
    require "common.php";
    session_start();

?>

<style>
<?php include 'CSS/main.css'; ?>
</style>

<?php
    if(isset($_POST["Login"])){
        if(checkAuth($_POST["username"], $_POST["password"]) == 1){
            $_SESSION["username"] = $_POST["username"];
            if(isEnabled($_SESSION["username"]) == false){
                $_SESSION["enabled"] = "false";
                header("LOCATION:passwordreset.php");
                return;
            } else{
                $_SESSION["enabled"] = "true";
                header("LOCATION:main.php");
                return;
            }
        } else {
            echo '<div>';
            echo '<b style="color: red">incorrect username and password</b>';
            echo '</div>';
        }
    }
?>

<?php
echo '
<html>
    <head>

    </head>
    <body>';
echo '<div class=loginContainer>';
    if(isset($_POST["student"])){
        $_SESSION["role"] = "student";
        echo '<p>Welcome to the student login!</p>';
        echo '
        <form action="login.php" method="POST">
        <label class="textLabel" for="username">Username:</label> <br>
        <input type="text" name="username" class="textInput" placeholder="Username"> <br><br>
        <label class="textLabel" for="password">Password:</label><br>
        <input type="password" name="password" class="textInput" placeholder="Password"> 
        <br>
        <br>
        <input type="submit" name="Login" class="submitBtn" value="Login">
        </form>
        ';
    } elseif(isset($_POST["instructor"])) {
        $_SESSION["role"] = "instructor";
        echo '<p>Welcome to the instructor login!</p>';
        echo '
        <form action="login.php" method="POST">
        <label class="textLabel" for="username">Username:</label> <br>
        <input type="text" name="username" class="textInput" placeholder="Username"> <br><br>
            <label class="textLabel" for="password">Password:</label><br>
            <input type="password" name="password" class="textInput" placeholder="Password"> 
            <br>
            <br>
            <input type="submit" name="Login" class="submitBtn" value="Login">
        </form>
        ';
    }elseif((isset($_SESSION["role"]) && $_SESSION["role"] == "student") ){
        $_SESSION["role"] = "student";
        echo '<p>Welcome to the student login!</p>';
        echo '
        <form action="login.php" method="POST">
        <label class="textLabel" for="username">Username:</label> <br>
        <input type="text" name="username" class="textInput" placeholder="Username"> <br><br>
        <label class="textLabel" for="password">Password:</label><br>
        <input type="password" name="password" class="textInput" placeholder="Password"> 
        <br>
        <br>
        <input type="submit" name="Login" class="submitBtn" value="Login">
        </form>
        ';
    } else if( (isset($_SESSION["role"]) && $_SESSION["role"] == "instructor")){
        $_SESSION["role"] = "instructor";
        echo '<p>Welcome to the instructor login!</p>';
        echo '<form action="login.php" method="POST">
        <label class="textLabel" for="username">Username:</label> <br>
            <input type="text" name="username" class="textInput" placeholder="Username"> <br><br>
            <label for="password">Password:</label><br>
            <input type="password" name="password" class="textInput" placeholder="Password"> 
            <br>
            <br>
            <input type="submit" name="Login" class="submitBtn" value="Login">
        </form>
        ';
    }
    else{
    echo '<p>Welcome to the login portal, are you an instructor or a student?</p><br>';
    }
echo '       
        <form action="login.php" method="post">
        <input type="submit" name="instructor" class="submitBtn" value="Instructor">
        <input type="submit" name="student" class="submitBtn" value="Student">
    </form>
    </div>
    </body>
</html>
';

?>
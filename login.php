<?php
require "db.php";

echo '
<html>
    <head>

    </head>
    <body>';
    if(isset($_POST["student"])){
        $_SESSION["role"] = "student";
        echo '<p>Welcome to the student login!</.p>';
        echo '
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="usernameInput"> <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="passwordInput"> <br>
            <input type="submit" name="studentLogin" id="login" value="Login">
        </form>
        ';
    } else {
        $_SESSION["role"] = "instructor";
        echo '<p>Welcome to the instructor login!</.p>';
        echo '
        <form action="login.php" method="POST">
        <label for="username">Username:</label>
            <input type="text" name="username" id="usernameInput"> <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="passwordInput"> <br>
            <input type="submit" name="instructorLogin" id="login" value="Login">
        </form>
        ';
    }
echo '       
        <form action="login.php" method="post">
        <input type="submit" name="instructor" value="Instructor">
        <input type="submit" name="student" value="Student">
    </form>
    </body>
</html>
';

echo "<pre>";
print_r($_SESSION);
print_r($_POST);
echo "</pre>";

if(isset($_POST["instructorLogin"])){
    if(instructorAuth($_POST["username"], $_POST["password"]) == 1){
        $_SESSION["username"] = $_POST["username"];
        if(isInstructorEnabled($_SESSION["username"]) == false){
            $_SESSION["enabled"] = "false";
            header("LOCATION:passwordreset.php");
            return;
        } else{
            $_SESSION["enabled"] = "true";
            header("LOCATION:main.php");
            return;
        }
    } else {
        echo '<p style="color: red">incorrect username and password</p>';
    }
}

if(isset($_POST["studentLogin"])){
    if(studentAuth($_POST["username"], $_POST["password"]) == 1){
        $_SESSION["username"] = $_POST["username"];
        if(isStudentEnabled($_SESSION["username"]) == false){
            $_SESSION["enabled"] = "false";
            header("LOCATION:passwordreset.php");
            return;
        } else{
            $_SESSION["enabled"] = "true";
            header("LOCATION:main.php");
            return;
        }
    } else {
        echo '<p style="color: red">incorrect username and password</p>';
    }
}

?>
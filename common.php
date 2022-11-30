<?php
require "instructorFunc.php";
require "studentFunc.php";




echo '
<html>
    <head>

    </head>
    <body>';
    if(isset($_POST["student"])){
        echo '<p>Welcome to the student login!</.p>';
        echo '
        <form action="common.php" method="POST">
            <input type="text" name="username" id="usernameInput">
            <input type="password" name="password" id="passwordInput">
            <input type="submit" name="instructorLogin" id="login" value="Login">
        </form>
        ';
    } else {
        echo '<p>Welcome to the instructor login!</.p>';
        echo '
        <form action="common.php" method="POST">
            <input type="text" name="username" id="usernameInput">
            <input type="password" name="password" id="passwordInput">
            <input type="submit" name="instructorLogin" id="login" value="Login">
        </form>
        ';
    }
echo '       
        <form action="common.php" method="post">
        <input type="submit" name="instructor" value="Instructor">
        <input type="submit" name="student" value="Student">
    </form>
    </body>
</html>
';

if(isset($_POST["instructorLogin"])){
    if($_POST["username"] == "wmisip" && $_POST["password"] == "wmisip"){
        header("LOCATION:main.html");
        return;
    } else {
        echo '<p style="color: red">incorrect username and password</p>';
    }
}

if(isset($_POST["studentLogin"])){
    if($_POST["username"] == "eljones" && $_POST["password"] == "eljones"){
        header("LOCATION:main.html");
        return;
    } else {
        echo '<p style="color: red">incorrect username and password</p>';
    }
}

?>
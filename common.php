<?php
echo "<pre>";
print_r($_SESSION);
print_r($_POST);
echo "</pre>";
?>

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
            <input type="submit" name="studentLogin" id="login" value="Login">
        </form>
        ';
        $_SESSION["role"] = "student";
    } else {
        echo '<p>Welcome to the instructor login!</.p>';
        echo '
        <form action="common.php" method="POST">
            <input type="text" name="username" id="usernameInput">
            <input type="password" name="password" id="passwordInput">
            <input type="submit" name="instructorLogin" id="login" value="Login">
        </form>
        ';
        $_SESSION["role"] = "instructor";
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
    if(instructorAuth($_POST["username"], $_POST["password"]) == 1){
        $_SESSION["username"] = $_POST["username"];
        header("LOCATION:main.html");
        return;
    } else {
        echo '<p style="color: red">incorrect username and password</p>';
    }
}

if(isset($_POST["studentLogin"])){
    if(studentAuth($_POST["username"], $_POST["password"]) == 1){
        $_SESSION["username"] = $_POST["username"];
        header("LOCATION:main.html");
        return;
    } else {
        echo '<p style="color: red">incorrect username and password</p>';
    }
}

?>
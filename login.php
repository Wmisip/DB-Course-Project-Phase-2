<?php
    session_start();
?>

<?php
require "common.php";

echo '
<html>
    <head>

    </head>
    <body>';
    if(isset($_POST["student"]) || $_SESSION["role"] == "student"){
        $_SESSION["role"] = "student";
        echo '<p>Welcome to the student login!</p>';
        echo '
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="usernameInput"> <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="passwordInput"> <br>
            <input type="submit" name="Login" id="login" value="Login">
        </form>
        ';
    } elseif(isset($_POST["instructor"])  || $_SESSION["role"] == "instructor") {
        $_SESSION["role"] = "instructor";
        echo '<p>Welcome to the instructor login!</p>';
        echo '
        <form action="login.php" method="POST">
        <label for="username">Username:</label>
            <input type="text" name="username" id="usernameInput"> <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="passwordInput"> <br>
            <input type="submit" name="Login" id="login" value="Login">
        </form>
        ';
    } else{
    echo '<p>Welcome to the login portal, are you an instructor or a student?</p><br>';
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
        echo '<p style="color: red">incorrect username and password</p>';
    }
}

// if(isset($_POST["studentLogin"])){
//     if(studentAuth($_POST["username"], $_POST["password"]) == 1){
//         $_SESSION["username"] = $_POST["username"];
//         if(isStudentEnabled($_SESSION["username"]) == false){
//             $_SESSION["enabled"] = "false";
//             header("LOCATION:passwordreset.php");
//             return;
//         } else{
//             $_SESSION["enabled"] = "true";
//             header("LOCATION:main.php");
//             return;
//         }
//     } else {
//         echo '<p style="color: red">incorrect username and password</p>';
//     }
// }

?>
<?php
    require "db.php";
    function instructorLogin($username, $password){
        try{
            $dbh=connectDB();
        $statement = $dbh->prepare("SELECT count(*) FROM instructor "."WHERE username = :user AND password= sha2(:password,256) ");
        $statement -> bindParam(":user", $username);
        $statement -> bindParam(":password", $password);
        $result = $statement->execute();
        $row=$statement -> fetch();
        $dbh=null;

        return $row[0];
        } catch (PDOException $e){
            print "Error!" . $e->getMessage() . "<br>";
            die();
        } 
    }
?>
<?php
echo '
<html>
    <head>

    </head>
    <body>
        <form action="instructorFunc.php" method="POST">
            <input type="text" name="usernameInput" id="usernameInput">
            <input type="password" name="passwordInput" id="passwordInput">
            <input type="submit" name="instructorLogin" id="instructorLogin" value="Login">
        </form>
    </body>
</html>
'
?>

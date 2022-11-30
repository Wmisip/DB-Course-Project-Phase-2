<?php
session_start();
?>

<?php
echo "<pre>";
print_r($_SESSION);
print_r($_POST);
echo "</pre>";
?>

<?php
function connectDB(){
    $config = parse_ini_file("db.ini");
    $dbh = new PDO($config['dsn'], $config['username'], $config['password']);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

?>

<?php
    function instructorAuth($username, $password){
        try{
        $dbh=connectDB();
        $statement = $dbh->prepare("SELECT count(*) FROM instructor "."WHERE account_name = :user AND password= sha2(:password,256) ");
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

    function studentAuth($username, $password){
        try{
            $dbh=connectDB();
        $statement = $dbh->prepare("SELECT count(*) FROM student "."WHERE username = :user AND password= sha2(:password,256) ");
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
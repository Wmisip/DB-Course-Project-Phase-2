<?php
    require "db.php";
    function studentLogin($username, $password){
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
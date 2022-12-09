<!-- common functions
    Login(Possibly)
 -->
 <?php
 require "db.php";
 ?>
 <?php
        echo "<pre>";
        print_r($_SESSION);
        print_r($_POST);
        echo "</pre>";
?>

<?php
function checkAuth($username, $password)
{
    try {
        $dbh = connectDB();

        if($_SESSION["role"] == "instructor"){
            $statement = $dbh->prepare("SELECT count(*) FROM instructor " .
                "WHERE account_name = :username AND password = sha2(:password,256) ");

            $statement->bindParam(":username", $username);
            $statement->bindParam(":password", $password);
            $result = $statement->execute();
            $row = $statement->fetch();
        }
        elseif($_SESSION["role"] == "student"){
            $statement = $dbh->prepare("SELECT count(*) FROM student " .
                "WHERE account_name = :username AND password = sha2(:password,256) ");

            $statement->bindParam(":username", $username);
            $statement->bindParam(":password", $password);
            $result = $statement->execute();
            $row = $statement->fetch();
        }

        $dbh = null;

        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br>";
        die();
    }
}
?>


<!-- <?php
function studentAuth($username, $password)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT count(*) FROM student " .
            "WHERE account_name = :username AND password = sha2(:password,256) ");
        $statement->bindParam(":username", $username);
        $statement->bindParam(":password", $password);
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;

        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br>";
        die();
    }
}
?> 

<?php
    function isInstructorEnabled($username){
        try{
        $dbh = connectDB();

        $statement = $dbh->prepare("SELECT enabled FROM instructor " .
            "WHERE account_name = :username ");
        $statement->bindParam(":username", $username);
        $result = $statement->execute();
        $check = $statement->fetch();

        return $check[0];

        } catch (PDOException $e){
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }
?> -->

<?php
    function isEnabled($username){
        try{
        $dbh = connectDB();

        if($_SESSION["role"] == "instructor"){
            $statement = $dbh->prepare("SELECT enabled FROM instructor " .
            "WHERE account_name = :username ");

            $statement->bindParam(":username", $username);
            $result = $statement->execute();
            $check = $statement->fetch();
        }
        elseif($_SESSION["role"] == "student"){
            $statement = $dbh->prepare("SELECT enabled FROM student " .
            "WHERE account_name = :username ");

            $statement->bindParam(":username", $username);
            $result = $statement->execute();
            $check = $statement->fetch();
        }

        return $check[0];

        } catch (PDOException $e){
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }
?>

<?php
    function resetPassword($username, $password){
        try{
            $dbh = connectDB();
            $dbh->beginTransaction();
            
            if($_SESSION["role"] == "student"){

            $statement = $dbh->prepare("UPDATE student SET password = sha2(:password,256) " . 
                "WHERE account_name = :username ");
            $statement->bindParam(":username", $username);
            $statement->bindParam(":password", $password);
            $result = $statement->execute();

            $statement = $dbh->prepare("UPDATE student SET enabled = true WHERE account_name = :username AND password = sha2(:password,256) ");
            $statement->bindParam(":username", $username);
            $statement->bindParam(":password", $password);
            $result = $statement->execute();

            $dbh->commit();
        }   
        elseif($_SESSION["role"] == "instructor"){

            $statement = $dbh->prepare("UPDATE instructor " . "SET password = sha2(:password,256) " . 
                "WHERE account_name = :username ");
            $statement->bindParam(":username", $username);
            $statement->bindParam(":password", $password);
            $result = $statement->execute();

            $statement = $dbh->prepare("UPDATE instructor SET enabled = true WHERE account_name = :username AND password = sha2(:password,256) ");
            $statement->bindParam(":username", $username);
            $statement->bindParam(":password", $password);
            $result = $statement->execute();

            $dbh->commit();
        } else{
            $dbh->rollBack();
        }

        $dbh = null;
        return;
    
            } catch (PDOException $e){
                print "Error!" . $e->getMessage() . "<br>";
                die();
            }
    }
?>
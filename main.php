<?php
require "db.php";
session_start();
echo "<pre>";
print_r($_SESSION);
print_r($_POST);
echo "</pre>";
?>

<style>
    table,
    th,
    td {
        border: 1px solid black;
    }
</style>

<html>

<body>
    <?php
    if (!isset($_SESSION["username"]) || $_SESSION["enabled"] == "false") {
        header("LOCATION:login.php");
    } else {
        echo '<div>';
        if ($_SESSION["role"] == "instructor") {
            echo '<p style="text-align:left"> Welcome instructor, ' . $_SESSION["username"] . '.</p>';
        } else {
            echo '<p style="text-align:left"> Welcome student, ' . $_SESSION["username"] . '.</p>';
        }
        echo '</div>';
    }
    ?>
        <form action="main.php" method="post">
            <p style="text-align:left">
                <input type="submit" name="logout" id="logout" value="logout">
            </p>
        </form>
    <?php
    echo "<br/>";
    echo '<div>';
    if($_SESSION["role"] == "instructor"){
        $courses = getTaughtCourses($_SESSION["username"]);

        echo "<table>";
        echo "<tr>";
        echo "<th>CourseID</th>";
        echo "<th>Title</th>";
        echo "<th>Credits</th>";
        echo "<th>Exam Name</th>";
        echo "<th>Opened</th>";
        echo "<th>Closed</th>";
        echo "<th>Total Points</th>";
        echo "</tr>";
    
        foreach ($courses as $row) {
            echo "<tr>";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "<td>" . $row[3] . "</td>";
            echo "<td>" . $row[4] . "</td>";
            echo "<td>" . $row[5] . "</td>";
            echo "<td>" . $row[6] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo '</div>';    

        echo '
            <br>
            <br>
            <div>
            <p>Please enter the CourseID and Exam Name to view the scores of students.</p>
            
            <form action="instructorFunc.php" method="POST">
                <label for="courseID">Course: </label>
                <input type="text" name="courseID" id="textbox" value="courseID">
                <br>
                <label for="examName">Exam: </label>
                <input type="text" name="examName" id="textbox" value="examName">
                <br>
                <br>
                <input type="submit" name="checkScore" id="submitBtn" value="Check Score">
                <input type="submit" name="reviewExam" id="submitBtn" value="Review Exam">
                <input type="submit" name="createExam" id="submitBtn" value="Create Exam">
            </form>
            </div>
        ';
        
    }
    ?>

    <br>
</body>

</html>

<?php
    function getTaughtCourses($username){
        try {
            $dbh = connectDB();
    
        $statement = $dbh->prepare("SELECT 
        course.course_id, 
        course.course_name, 
        course.credits, 
        exam.exam_name, 
        exam.open, 
        exam.closed, 
        exam.total_points  
        FROM instructor 
        natural join teaches natural join course natural join exam " . 
        "WHERE instructor.account_name LIKE :username ");
    
        $statement->bindParam(":username", $_SESSION['username']);
        $statement->execute();

        $dbh = null;

        return $statement->fetchAll();

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }
?>

<?php
if (isset($_POST["logout"])) {
        
        // Initialize the session.
        // If you are using session_name("something"), don't forget it now!

        // Unset all of the session variables.
    session_unset();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();

    header("LOCATION:login.php");
    }
?>

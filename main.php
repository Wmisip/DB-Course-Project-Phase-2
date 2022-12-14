<?php
require "db.php";
session_start();

?>

<html>

<body>

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

    function getCoursesIn($username) {
        try {
            $dbh = connectDB();

        $statement = $dbh->prepare("        SELECT 
        course_info.course_id, 
        course_info.course_name, 
        course_info.credits, 
        course_info.name 
        FROM student 
        natural join register 
        inner join 
        (SELECT * 
        FROM course 
        natural join teaches 
        natural join instructor)course_info 
        on register.course_id = course_info.course_id
		WHERE student.account_name LIKE :username");

        $statement->bindParam(":username", $username);

        $statement->execute();

        $dbh = null;

            return $statement->fetchAll();

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

    function getExamsTaken($username) {
        try {
            $dbh = connectDB();

        $statement = $dbh->prepare("SELECT 
        course.course_id, 
        exam.exam_name,
        exam.open,
        exam.closed, 
        exam.total_points,
        takes.start_time, 
        takes.end_time, 
        takes.grade
        FROM student 
        natural join takes natural join course natural join exam
            WHERE student.account_name LIKE :username ORDER BY course.course_id ASC, exam_name ASC");

        $statement->bindParam(":username", $username);
        $statement->execute();

        $dbh = null;

        return $statement->fetchAll();

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

    function getCoursesNotIn($username) {
        try {
            $dbh = connectDB();

        $stmt = $dbh->prepare('SELECT student_id FROM student WHERE account_name LIKE :username ');
        $stmt -> bindParam(":username", $_SESSION['username']);
        $stmt -> execute();
        $studentID = $stmt -> fetch();

        $statement = $dbh->prepare("SELECT DISTINCT
        course_id,
        course_name,
        credits
    FROM
        course
    WHERE
        course.course_id NOT IN (SELECT 
                course.course_id
            FROM
                (SELECT 
                    student_id
                FROM
                    student
                WHERE
                    student_id LIKE :studentID) ls
                    NATURAL JOIN
                register
                    NATURAL JOIN
                course)
        "); // query broken
    // array to string conversion notice, doesn't seem to be a problem but shows up on web page
        $statement->bindParam(":studentID", $studentID[0]);
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

<style>
<?php include 'CSS/main.css'; ?>
</style>

<!-- <style>
    table,
    th,
    td {
        border: 1px solid black;
    }
</style> -->


    <?php
    if (!isset($_SESSION["username"]) || $_SESSION["enabled"] == "false") {
        header("LOCATION:login.php");
    } else {
        echo '<div class="logoutContainer">';
        if ($_SESSION["role"] == "instructor") {
            echo '<p style="text-align:left"> Welcome instructor, ' . $_SESSION["username"] . '</p>';
        } else {
            echo '<p style="text-align:left"> Welcome student, ' . $_SESSION["username"] . '</p>';
        }
    }
    ?>
        <form action="main.php" method="post">
                <input type="submit" name="logout" class="submitBtn" value="Logout">
        </form>
    </div>

    <?php
    echo '<br>';
    echo '<br>';
    if($_SESSION["role"] == "instructor"){
        $courses = getTaughtCourses($_SESSION["username"]);
        echo '<div class="tableContainer">';

        echo "<b>Here are the courses your're teaching and the exams you've created for them</b><br><br>";

        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>CourseID</th>";
        echo "<th>Title</th>";
        echo "<th>Credit</th>";
        echo "<th>Exam Name</th>";
        echo "<th>Opened</th>";
        echo "<th>Closed</th>";
        echo "<th>Total Points</th>";
        echo "</tr>";
        echo "</thead>";

        echo "<tbody>";
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
        echo "</tbody>";
        echo "</table>";
        echo '</div>';    

        echo '<br>';
        echo '<br>';

        echo '<br>
            <div class="formContainer">
            <p>Please enter the CourseID and Exam Name to view the scores of students.</p>

            <form action="instructorFunc.php" method="POST">
                <label class="textLabel" class="textLabel" for="courseID">Course: </label><br>
                <input type="text" name="courseID" class="textInput" placeholder="Course ID">
                <br><br>
                <label class="textLabel" for="examName">Exam: </label><br>
                <input type="text" name="examName" class="textInput" placeholder="Exam Name">
                <br>
                <br>
                <input type="submit" name="checkScore" class="submitBtn" value="Check Score">
                <input type="submit" name="reviewExam" class="submitBtn" value="Review Exam">
                <input type="submit" name="createExam" class="submitBtn" value="Create Exam">
            </form>
            </div>
        ';

    } else {
        $courses = getCoursesIn($_SESSION["username"]);

        echo '<div class="tableContainer">';
        echo "<b>Here are the classes you are taking.</b><br><br>";
        echo "<table>";
        echo '<thead>';
        echo "<tr>";
        echo "<th>CourseID</th>";
        echo "<th>Title</th>";
        echo "<th>Credits</th>";
        echo "<th>Instructor Name</th>";
        echo "</tr>";
        echo '</thead>';

        echo '<tbody>';
        foreach ($courses as $row) {
            echo "<tr>";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "<td>" . $row[3] . "</td>";
            echo "</tr>";
        }
        echo '</tbody>';
        echo "</table>";
        echo '</div>';

        echo '<br>';
        echo '<br>';

        $exams = getExamsTaken($_SESSION["username"]);
        echo '<div class="tableContainer">';

        echo "<b>Here are your exams in each course and your score</b><br><br>";

        echo "<table>";
        echo '<thead>';
        echo "<tr>";
        echo "<th>CourseID</th>";
        echo "<th>Exam Name</th>";
        echo "<th>Open</th>";
        echo "<th>Closed</th>";
        echo "<th>Total point</th>";
        echo "<th>Start time</th>";
        echo "<th>End time</th>";
        echo "<th>Grade</th>";
        echo "</tr>";
        echo '</thead>';

        echo '<tbody>';
        foreach ($exams as $row) {
            echo "<tr>";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "<td>" . $row[3] . "</td>";
            echo "<td>" . $row[4] . "</td>";
            echo "<td>" . $row[5] . "</td>";
            echo "<td>" . $row[6] . "</td>";
            echo "<td>" . $row[7] . "</td>";
            echo "</tr>";
        }
        echo '</tbody>';
        echo "</table>";
        echo '</div>';

        echo '<br>';
        echo '<br>';

        $coursesNotIn = getCoursesNotIn($_SESSION["username"]);

        echo '<div class="tableContainer">';
        echo "<b>Here is a list of classes you are not enrolled in</b><br><br>";
        echo "<table>";
        echo '<thead>';
        echo "<tr>";
        echo "<th>Course ID</th>";
        echo "<th>Title</th>";
        echo "<th>Credits</th>";
        echo "</tr>";
        echo '</thead>';

        echo '<tbody>';
        foreach ($coursesNotIn as $row) {
            echo "<tr>";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "</tr>";
        }
        echo '</tbody>';
        echo "</table>";
        echo '</div>';

        echo '<br>';
        echo '<br>
        <div class="formContainer">
        <p>To register new courses, type course id then click Register New Course</p>
        <p>To take an exam, type course id and exam name then click Take Exam</p>
        <p>To check an exam score, type course id then click Check Score</p>
        <form action="studentFunc.php" method="POST">
        <label class="textLabel" for="courseID">Course: </label><br>
            <input type="text" name="courseID" class="textInput" placeholder="Course ID">
            <br><br>
            <label class="textLabel" for="examName">Exam: </label><br>
            <input type="text" name="examName" class="textInput" placeholder="Exam Name">
            <br>
            <br>
            <input type="submit" name= "registerForCourse" class="submitBtn" value="Register New Course">
            <input type="submit" name= "takeExam" class="submitBtn" value="Take Exam">
            <input type="submit" name= "checkExam" class="submitBtn" value="Check Score">
        </form>
        </div>
    ';
    }
    ?>

    <br>
</body>

</html>




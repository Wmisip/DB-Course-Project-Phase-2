<?php
    session_start();
?>

<html>
<body>

<form action = "studentFunc.php" method = "get">
    <p> Course Id: <input type = "text" name = "courseId"/> </p>
    <p> Exam name: <input type = "text" name = "examName"/> </p>
    <p> <input type = "submit" name = regstrCourse value = "Register for course"/>
    <input type = "submit" name = takeExam value = "Take Exam"/>
    <input type = "submit" name = checkExam value = "Check Exam"/> </p>

</body>
</html>

<?php
    require "db.php";
    require
    $courseid = $_GET['courseId'];
    $examname = $_GET['examName'];        // need to check if things exist for each button




    if (isset($_GET['regstrCourse'])) {






    } else {
        // take an exam
    }

    ?>


<?php
require "db.php";
session_start();
?>

<style>
    table,
    th,
    td {
        border: 1px solid black;
    }
</style>

<html>
</html>


<?php
    function reviewExam($courseID, $examName) {
        try {
            $dbh = connectDB();

        $stmt = $dbh->prepare('SELECT
        open,
        closed,
        choice.question_num,
        question.description,
        answer,
        correct,
        question.points
        FROM chooses LEFT OUTER JOIN choice ON
            choice.course_id = chooses.course_id AND
            choice.exam_name = chooses.exam_name AND
            chooses.answer = choice.choice AND
            chooses.question_num = choice.question_num
        LEFT OUTER JOIN exam ON
            exam.course_id = choice.course_id AND
            exam.exam_name = choice.exam_name
        LEFT OUTER JOIN question ON
            question.course_id = exam.course_id AND
            question.exam_name = exam.exam_name
        WHERE account_name LIKE :username AND 
            choice.exam_name = :examName AND 
            choice.course_id = :courseId');
        $stmt -> bindParam(":username", $_SESSION['username']);
        $stmt -> bindParam(":examName", $examname);
        $stmt -> bindParam(":courseid", $courseid);
        $stmt -> execute([$courseid, $examname]);

        $dbh = null;

        return $stmt->fetchAll();

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

    function registerForCourse($username, $courseID) {
        try {
            $dbh = connectDB();
            $stmt = $dbh->prepare('SELECT student_id FROM student WHERE account_name LIKE :username ');
            $stmt -> bindParam(":username", $_SESSION['username']);
            $stmt -> execute([$username]);
            $studentID = $stmt -> fetchAll();

            $stmt = $dbh->prepare('INSERT INTO register (course_id, student_id) VALUES (:courseId, :)');
            $stmt -> bindParam(":course_id", $courseID);
            $stmt -> bindParam("student_id", $studentID);
            $stmt -> execute([$courseID, $studentID]);

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }


?>
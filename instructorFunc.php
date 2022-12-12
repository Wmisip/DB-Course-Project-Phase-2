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
        .tab1 {
            tab-size: 2;
        }
  
        .tab2 {
            tab-size: 4;
        }
  
        .tab4 {
            tab-size: 8;
        }
</style>

<html>
    <head>


    </head>
<body>
    <div>
    <?php
    if($_POST["courseID"] != "" && $_POST["examName"] != ""){
    if(isset($_POST["reviewExam"])){
        echo '<p>Here are questions for ' . $_POST["examName"];
            echo '<br>';
            echo '<br>';
            echo '<div>';
            $questions = getQuestions($_POST["courseID"], $_POST["examName"]);

            foreach ($questions as $question) {
                $options = getChoices($_POST["courseID"], $_POST["examName"], $question[0]);
                echo '<b style="font-size: larger;">' . $question[0] . ": " . $question[1] . " (" . $question[2] . " points)" . "</b>";
                foreach($options as $option){
                    // echo "<br>";
                    echo '<p style="padding-left: 2%;">' . $option[0] . ": " . $option[1];
                    if($option[2] == "1"){
                        echo ' (Correct)';
                    }
                    echo '</p>';
                }
            }
        echo '</div>';
    } elseif(isset($_POST["checkScore"])){
    echo '<br>';
    echo '<div>';
        $exams = getExams($_POST["courseID"], $_POST["examName"]);

        echo "<table>";
        echo "<tr>";
        echo "<th>CourseID</th>";
        echo "<th>Total</th>";
        echo "<th>Exam Name</th>";
        echo "<th>Completed</th>";
        echo "<th>Minimum</th>";
        echo "<th>Maximum</th>";
        echo "<th>Average</th>";
        echo "</tr>";
    
        foreach ($exams as $row) {
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
        
    echo '<br>';
    echo '<br>';
    echo '<div>';
        $scores = getScores($_POST["courseID"], $_POST["examName"]);

        echo "<table>";
        echo "<tr>";
        echo "<th>StudentID</th>";
        echo "<th>Name</th>";
        echo "<th>Start Time</th>";
        echo "<th>End Time</th>";
        echo "<th>Score</th>";
        echo "</tr>";
    
        foreach ($scores as $row) {
            echo "<tr>";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "<td>" . $row[3] . "</td>";
            echo "<td>" . $row[4] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo '</div>';   

    }elseif(isset($_POST["createExam"])){
    echo '<p>This function has not been implemented yet.';
    } else{
    header("LOCATION:main.php");
    }
}   else{
    header("LOCATION:main.php");
}
    echo '<br>';
    echo '
    <form action="instructorFunc.php" method="POST">
    <input type="submit" name="goBack" id="submitBtn" value="Go Back">
    </form>
    ';

?>
    </div>
</body>
</html>

<?php
    if(isset($_POST["goBack"])){
        header("LOCATION:main.php");
    }
?>



<?php
    function getExams($courseID, $examName){
        try {
            $dbh = connectDB();
    
        $statement = $dbh->prepare("SELECT
        course_id,
        reg.total,
        exam_name,
        count(takes.student_id) as completed,
        min(grade) as min,
        max(grade) as max,
        avg(grade) as average
        from
        course NATURAL JOIN 
        teaches NATURAL JOIN 
        instructor NATURAL JOIN
        (select count(student_id) as total 
        from register group by course_id)reg
        NATURAL JOIN takes
        WHERE account_name like :username AND course_id like :courseID AND exam_name like :examName
        GROUP BY course_id, reg.total, exam_name
        ORDER BY course_id, exam_name ASC");
    
        $statement->bindParam(":username", $_SESSION['username']);
        $statement->bindParam(":courseID", $courseID);
        $statement->bindParam(":examName", $examName);
        $statement->execute();

        $dbh = null;

        return $statement->fetchAll();

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

    function getScores($courseID, $examName){
        try {
            $dbh = connectDB();
    
        $statement = $dbh->prepare("SELECT 
        student_id, 
        student_name, 
        start_time, 
        end_time, 
        grade as score 
        FROM takes 
        NATURAL JOIN student
		NATURAL JOIN
		(select course_id FROM teaches NATURAL JOIN instructor
        WHERE account_name like :username)teach
        WHERE 
        course_id like :courseID AND exam_name like :examName
        ORDER BY student_id ASC");
    
        $statement->bindParam(":username", $_SESSION['username']);
        $statement->bindParam(":courseID", $courseID);
        $statement->bindParam(":examName", $examName);
        $statement->execute();

        $dbh = null;

        return $statement->fetchAll();

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

    function getQuestions($courseID, $examName){
        try {
            $dbh = connectDB();
    
        $statement = $dbh->prepare("SELECT 
        question_num, 
        description, 
        points 
        FROM question
        WHERE course_id = :courseID AND exam_name = :examName
        ORDER BY question_num ASC");
    
        $statement->bindParam(":courseID", $courseID);
        $statement->bindParam(":examName", $examName);
        $statement->execute();

        $dbh = null;

        return $statement->fetchAll();

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

    function getChoices($courseID, $examName, $questionNum){
        try {
            $dbh = connectDB();
    
        $statement = $dbh->prepare("SELECT 
        choice, 
        description, 
        correct 
        FROM choice
        WHERE course_id = :courseID AND exam_name = :examName AND question_num = :questionNum");
    
        $statement->bindParam(":courseID", $courseID);
        $statement->bindParam(":examName", $examName);
        $statement->bindParam("questionNum", $questionNum);
        $statement->execute();

        $dbh = null;

        return $statement->fetchAll();

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }
?>
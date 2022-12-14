<?php
require "db.php";
session_start();
?>

<html>
    <head>


    </head>
<body>

<style>
<?php include 'CSS/main.css'; ?>
</style>

<?php
    if(isset($_POST["goBack"])){
        header("LOCATION:main.php");
    }
?>



<?php
    function getExamsTaken($courseID, $examName){
        try {
            $dbh = connectDB();
    
        $statement = $dbh->prepare("SELECT
        course_id,
        reg.total,
        exam_name,
        (SELECT count(takes.student_id) 
        FROM takes WHERE course_id like :courseID 
        AND exam_name like :examName AND start_time is not null AND end_time is not null 
        GROUP BY course_id, exam_name) as completed ,
        min(grade) as min,
        max(grade) as max,
        avg(grade) as average
        from 
        instructor NATURAL JOIN
        (select count(student_id) as total 
        from register WHERE course_id like :courseID group by course_id )reg
        natural join takes
        WHERE course_id like :courseID AND exam_name like :examName 
        AND start_time is not null AND end_time is not null 
        GROUP BY course_id, reg.total, exam_name
        ORDER BY course_id, exam_name ASC");
    
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
        AND start_time is not null AND end_time is not null
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
    echo '<div class="tableContainer">';
    $exams = getExamsTaken($_POST["courseID"], $_POST["examName"]);

        echo "<table>";
        echo '<thead>';
        echo "<tr>";
        echo "<th>CourseID</th>";
        echo "<th>Total</th>";
        echo "<th>Exam Name</th>";
        echo "<th>Completed</th>";
        echo "<th>Minimum</th>";
        echo "<th>Maximum</th>";
        echo "<th>Average</th>";
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
            echo "</tr>";
        }
        echo '</tbody>';
        echo "</table>";
        echo '</div>'; 
        
    echo '<br>';
    echo '<br>';
    echo '<div class="tableContainer">';
    $scores = getScores($_POST["courseID"], $_POST["examName"]);

        echo "<table>";
        echo '<thead>';
        echo "<tr>";
        echo "<th>StudentID</th>";
        echo "<th>Name</th>";
        echo "<th>Start Time</th>";
        echo "<th>End Time</th>";
        echo "<th>Score</th>";
        echo "</tr>";
        echo '</thead>';
    
        echo '<tbody>';
        foreach ($scores as $row) {
            echo "<tr>";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "<td>" . $row[3] . "</td>";
            echo "<td>" . $row[4] . "</td>";
            echo "</tr>";
        }
        echo '</tbody>';
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
    <input type="submit" name="goBack" class="submitBtn" value="Go Back">
    </form>
    ';

?>
    </div>
</body>
</html>


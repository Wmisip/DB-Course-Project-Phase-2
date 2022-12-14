<?php
    require "db.php";
    session_start();
?>
<html>
<body>
<?php
    if(isset($_POST["goBack"])){
        header("LOCATION:main.php");
    }
?>

<?php
if (isset($_POST["submitExam"])) {

    try{
        $dbh = connectDB();

        $startTime = date('Y-m-d H:i:s',$_SESSION["startTime"]);
        $endTime = date('Y-m-d H:i:s');

        $dbh->beginTransaction();

        $studentID = getStudentID();

        $statement = $dbh->prepare("
        UPDATE takes SET start_time=:startTime, end_time=:endTime 
        WHERE course_id=:courseID AND exam_name=:examName AND student_id=:studentID
        ");
        echo '<br>';

        $statement->bindParam(":courseID", $_SESSION["courseID"] );
        $statement->bindParam(":examName", $_SESSION["examName"]);
        $statement->bindParam(":studentID", $studentID);
        $statement->bindParam(":startTime", $startTime);
        $statement->bindParam(":endTime", $endTime);
        $statement->execute();

        foreach (array_keys($_POST) as $x) {
            if ($x != 'submitExam') {
                $statement = $dbh->prepare("
            INSERT INTO 
            chooses(course_id, exam_name, question_num, answer, student_id)
            VALUES
            (:courseID, :examName, :questionNum, :answer, :studentID)
            ");

            $statement->bindParam(":courseID", $_SESSION["courseID"]);
            $statement->bindParam(":examName", $_SESSION["examName"]);
            $statement->bindParam(":questionNum", $x);
            $statement->bindParam(":answer", $_POST[$x]);
            $statement->bindParam(":studentID", $studentID);
            $statement->execute();
            }
        }
        $dbh->commit();
    }catch (PDOException $e){
        print "Error!" . $e->getMessage() . "<br>";
        die();
    }
    header("LOCATION:main.php");
}
?>


<?php
    function reviewExam($courseID, $examName) {
        try {
            $dbh = connectDB();

        $studentID = getStudentID();

        $stmt = $dbh->prepare('SELECT answers.question_num, 
        answers.description, 
        answers.answer, 
        if(choice.correct = true, answers.points, 0)
        FROM 
        (select question.course_id, 
        question.exam_name, 
        chooses.question_num, 
        question.description, 
        chooses.answer, 
        student_id, 
        points 
        from chooses 
        NATURAL JOIN question 
        WHERE student_id=:studentID 
        AND course_id like :courseID 
        AND exam_name LIKE :examName)answers 
        INNER JOIN choice 
        ON answers.course_id=choice.course_id 
        AND answers.exam_name=choice.exam_name 
        AND answers.question_num=choice.question_num 
        AND answers.answer=choice.choice');
        $stmt -> bindParam(":studentID", $studentID);
        $stmt -> bindParam(":examName", $examName);
        $stmt -> bindParam(":courseID", $courseID);
        $stmt -> execute();

        $dbh = null;

        return $stmt->fetchAll();

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

    function getAnswerKey($courseID, $examName, $questionNum){
        try {
            $dbh = connectDB();

        $stmt = $dbh->prepare('SELECT choice, 
        correct 
        FROM choice 
        WHERE course_id like :courseID
        AND exam_name like :examName 
        AND question_num like :questionNum
        HAVING correct=true');
        $stmt -> bindParam(":courseID", $courseID);
        $stmt -> bindParam(":examName", $examName);
        $stmt -> bindParam(":questionNum", $questionNum);
        $stmt -> execute();

        $dbh = null;

        $answer = $stmt->fetchAll();

        return $answer[0];

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

    function getStudentID(){
        try {
        $dbh = connectDB();
            $stmt = $dbh->prepare('SELECT student_id FROM student WHERE account_name LIKE :username ');
            $stmt -> bindParam(":username", $_SESSION['username']);
            $stmt -> execute();
            $studentID = $stmt -> fetch();

        return $studentID[0];

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

//    function submitExam($courseID, $examName){
//     try{
//         $dbh = connectDB();

//         $endTime = strtotime(date('Y-m-d H:i:s'));

//         $studentID = getStudentID($_SESSION["username"]);

//         $statement = $dbh->prepare("
//         INSERT INTO 
//         takes(course_id, exam_name, student_id, start_time, end_time)
//         VALUES
//         :courseID, :examName, :studentID, :startTime, :endTime
//         ");
//         $statement->bindParam(":courseID", $courseID);
//         $statement->bindParam(":examName", $examName);
//         $statement->bindParam(":studentID", $studentID);
//         $statement->bindParam(":startTime", $_SESSION["startTime"]);
//         $statement->bindParam(":endTime", $endTime);
//         foreach (array_keys($_POST) as $x) {
//             if ($x != 'submit') {
//                 echo $x . ":" . $_POST[$x] . "<br>";
//             }
//         }
//     }catch (PDOException $e){
//         print "Error!" . $e->getMessage() . "<br>";
//         die();
//     }
//    }

    // function registerForCourse($username, $courseID) {
    //     try {
    //         $dbh = connectDB();
    //         $stmt = $dbh->prepare('SELECT student_id FROM student WHERE account_name LIKE :username ');
    //         $stmt -> bindParam(":username", $_SESSION['username']);
    //         $stmt -> execute([$username]);
    //         $studentID = $stmt -> fetchAll();

    //         $stmt = $dbh->prepare('INSERT INTO register (course_id, student_id) VALUES (:courseId, :)');
    //         $stmt -> bindParam(":course_id", $courseID);
    //         $stmt -> bindParam("student_id", $studentID);
    //         $stmt -> execute([$courseID, $studentID]);

    //     } catch (PDOException $e) {
    //         print "Error!" . $e->getMessage() . "<br>";
    //         die();
    //     }
    // }

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

    function isExamOpen($courseID, $examName){
        try {

            $dbh = connectDB();
    
            $sqlstring = "select open, closed from exam where course_id=:courseID and exam_name=:examName";
    
            $statement = $dbh->prepare($sqlstring);
    
            $statement->bindParam(":courseID", $courseID);
    
            $statement->bindParam(":examName", $examName);
    
            $statement->execute();
    
            $x = $statement->fetch();
    
            $start_time = strtotime($x[0]);
    
            $end_time = strtotime($x[1]);
    
            $now = strtotime(date('Y-m-d H:i:s'));
    
            if ($now > $end_time) {
    
                return 2;
    
            } else if ($now < $start_time) {
    
                return 1;
    
            } else {
    
                return 0;
    
            }
    
        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br/>";
            die();
        }
    }

//     function reviewQuestions($courseID, $examName) {
//         try {
//             $dbh = connectDB();

//         $stmt = $dbh->prepare('SELECT student_id FROM student WHERE account_name LIKE :username ');
//         $stmt -> bindParam(":username", $_SESSION['username']);
//         $stmt -> execute();
//         $studentID = $stmt -> fetch();

// // need to make 'correct' into an "answer letter" instead of the boolean value it is (in below query)

//         $stmt = $dbh->prepare('SELECT
//         choice.question_num,
//         question.description,
//         answer,
//         correct,
//         question.points 
//         FROM chooses LEFT OUTER JOIN choice ON
//             choice.course_id = chooses.course_id AND
//             choice.exam_name = chooses.exam_name AND
//             chooses.answer = choice.choice AND
//             chooses.question_num = choice.question_num
//         LEFT OUTER JOIN exam ON
//             exam.course_id = choice.course_id AND
//             exam.exam_name = choice.exam_name
//         LEFT OUTER JOIN question ON
//             question.course_id = exam.course_id AND
//             question.exam_name = exam.exam_name
//         WHERE chooses.student_id = :studentid AND 
//             choice.exam_name = :examName AND 
//             choice.course_id = :courseID 
//         ORDER BY question_num ASC');
//         $stmt -> bindParam(":studentid", $studentID[0]);
//         $stmt -> bindParam(":examName", $examName);
//         $stmt -> bindParam(":courseID", $courseID);
//         $stmt -> execute();

//         $dbh = null;

//         return $stmt->fetchAll();

//         } catch (PDOException $e) {
//             print "Error!" . $e->getMessage() . "<br>";
//             die();
//         }
//     }

    function getExamInformation($courseID, $examName){
        try {
        $studentID = getStudentID();

            $dbh = connectDB();
            $stmt = $dbh->prepare('SELECT grade, 
            start_time, 
            end_time,
            end_time - start_time as duration_in_seconds  
            FROM takes 
            WHERE 
            course_id like :courseID
            AND exam_name like :examName
            AND student_id = :studentID');
            $stmt -> bindParam(":courseID", $courseID);
            $stmt -> bindParam(":examName", $examName);
            $stmt -> bindParam(":studentID", $studentID);
            $stmt -> execute();
            $examInfo = $stmt -> fetchall();

            $dbh = null;

        return $examInfo;

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
    }

    function registerForCourse($courseID) {
        try {
            $dbh = connectDB();
            $stmt = $dbh->prepare('SELECT student_id FROM student WHERE account_name LIKE :username ');
            $stmt -> bindParam(":username", $_SESSION['username']);
            $stmt -> execute();
            $studentID = $stmt -> fetch();

            $stmt = $dbh->prepare('INSERT INTO register VALUES (:courseid, :studentid)');
            $stmt -> bindParam(":courseid", $courseID);
            $stmt -> bindParam(":studentid", $studentID[0]);
            $stmt -> execute();

            $dbh = null;

        } catch (PDOException $e) {
            print "Error!" . $e->getMessage() . "<br>";
            die();
        }
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
    if (isset($_POST['registerForCourse'])) {
        registerForCourse($_POST["courseID"]);
    echo '<div class="formContainer">';
        echo '<p>You have signed up for, ' . $_POST["courseID"] . '</p>';
        echo '
        <form action="studentFunc.php" method="POST">
        <input type="submit" name="goBack" class="submitBtn" value="Go Back">
        </form>
        ';
    echo '</div>';

    }elseif(isset($_POST['takeExam'])){
        if(isExamOpen($_POST["courseID"], $_POST["examName"]) == 0){
            $_SESSION["startTime"] = strtotime(date('Y-m-d H:i:s'));
            $_SESSION["courseID"] = $_POST["courseID"];
            $_SESSION["examName"] = $_POST["examName"];
                
                    echo '<div class="examContainer">';
                    echo '<p>Here are questions for ' . $_POST["examName"] . ". Click Submit after you finish";
                    echo '<br>';
                    $questions = getQuestions($_POST["courseID"], $_POST["examName"]);
        
                    echo '<form action="studentFunc.php" method="POST">';
                    foreach ($questions as $question) {
                        $options = getChoices($_POST["courseID"], $_POST["examName"], $question[0]);
        echo '<br>';
                        echo '<b style="font-size: larger;">' . $question[0] . ": " . $question[1] . "</b>";
                        foreach($options as $option){
                            echo "<br>";
                            // echo '<p style="padding-left: 2%;">' . $option[0] . ": " . $option[1];
                            // if($option[2] == "1"){
                            //     echo ' (Correct)';
                            // }
                            // echo '</p>';
                            echo '<label style="padding-left: 2%;" for="' . $question[0].$option[0] . '">'. $option[0] . ': ' . $option[1] . '</label>';
                            echo '<input type="radio" class="' . $question[0].$option[0] . '" name="' . $question[0] . '" value="' . $option[0] . '">';
                echo '<br>';
                        }
                    }
    echo '<br>';
                    echo '<input type="submit" value="Submit" name="submitExam" class="submitBtn">';
                    echo '</form>';
                    echo '</div>';
            } else{
        echo '<b>EXAM CLOSED PLEASE CLICK GO BACK TO RETURN TO THE MAIN PAGE!</b>';
        echo '
            <form action="studentFunc.php" method="POST">
            <input type="submit" name="goBack" class="submitBtn" value="Go Back">
            </form>
            ';
            }
        }
    elseif(isset($_POST['checkExam'])){
        echo '<br>';
        echo '<div class="tableContainer">';
        $info = getExamInformation($_POST["courseID"], $_POST["examName"]);

        echo "<table>";
        echo '<thead>';
        echo "<tr>";
        echo "<th>Score</th>";
        echo "<th>Start Time</th>";
        echo "<th>End Time</th>";
        echo "<th>Duration in Seconds</th>";
        echo "</tr>";
        echo '</thead>';

        echo '<tbody>';
        foreach ($info as $row) {
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

        echo '</br>';

        echo '<br>';
        echo '<div class="tableContainer">';
        $exam = reviewExam($_POST["courseID"], $_POST["examName"]);

        echo "<table>";
        echo '<thead>';
        echo "<tr>";
        echo "<th>Question Number</th>";
        echo "<th>description</th>";
        echo "<th>Your Answer</th>";
        echo "<th>Points Received</th>";
        echo "<th>Correct Answer</th>";
        echo "</tr>";
        echo '</thead>';

        echo '<tbody>';
        foreach ($exam as $row) {
            echo "<tr>";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "<td>" . $row[3] . "</td>";
            $correct = getAnswerKey($_POST["courseID"], $_POST["examName"], $row[0]);
            echo "<td>" . $correct[0] . "</td>";
            echo "</tr>";
        }
        echo '</tbody>';
        echo "</table>";
        echo '</div>';

        echo '</br>';
        echo '<div>
            <form action="studentFunc.php" method="POST">
            <input type="submit" name="goBack" class="submitBtn" value="Go Back">
            </form>
            </div>
            ';
    }

    ?>

</body>
</html> 


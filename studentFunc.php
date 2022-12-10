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
    $courseid = $_GET['courseId'];
    $examname = $_GET['examName'];
    $dbh = connectDB();

    if (isset($_GET['regstrCourse'])) {
        printf("You have been added to %s \n",
            htmlspecialchars($_GET['courseId']));
        $stmt = $dbh->prepare('INSERT INTO register (course_id, student_id) VALUES (:courseId, :)');
        $stmt -> execute([$courseid, $examname]);    // how to grab student id?
    } elseif (isset($_GET['takeExam'])){
        // take an exam
    } else {
        // check an exam, with a students answers, the correct answer, and the point per question
    }
?>
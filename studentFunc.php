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
if (isset($_GET['regstrCourse'])) {
    printf("You have been added to %s \n",
        htmlspecialchars($_GET['courseId']));
    // add to course
} elseif (isset($_GET['takeExam'])){
    // take an exam
} else {
    // open up exam questions, answers, and score
}
?>
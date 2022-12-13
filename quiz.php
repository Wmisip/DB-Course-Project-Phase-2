<style>
	<?php include 'style.css'; ?>
</style>
<?php
		session_start();
		// echo "<pre>";
		// print_r($_SESSION);
		// print_r($_POST);
		// echo "</pre>";
		// ?> 
<?php
if (isset($_POST["submit"])) {
	echo "Your answers are: <br>";
	foreach (array_keys($_POST) as $x) {
		if ($x != 'submit') {
			echo $x . ":" . $_POST[$x] . "<br>";
		}
	}
	return;
}
?>


<html>

<head>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<form class="QnA" action="quiz.php" method="post">
		<div class="questionBlock">
			<label class="question">Q1: The pace of this course</label><br>

			<div class="option">
				<label for="Q1A">A: is too slow</label>
				<input type="radio" class="Q1A" name="Q1" value="A"><br>
			</div>

			<div class="option">
				<label for="Q1B">B: is just right</label>
				<input type="radio" class="Q1B" name="Q1" value="B"><br>
			</div>

			<div class="option">
				<label for="Q1C">C: is too fast</label>
				<input type="radio" class="Q1C" name="Q1" value="C"><br>
			</div>

			<div class="option">
				<label for="Q1D">D: I don't know</label>
				<input type="radio" class="Q1D" name="Q1" value="D">
			</div>

		</div>
		<!-- <br>
		<br> -->

		<div class="questionBlock">
			<label class="question">Q2: The feedback from homework assignment grading</label><br>

			<div class="option">
				<label for="Q2A">A: too few</label>
				<input type="radio" class="Q1A" name="Q2" value="A"><br>
			</div>
			<div class="option">
				<label for="Q2B">B: sufficient</label>
				<input type="radio" class="Q2B" name="Q2" value="B"><br>
			</div>
			<div class="option">

				<label for="Q2C">C: I don't know</label>
				<input type="radio" class="Q2C" name="Q2" value="C">
			</div>
		</div>
		<!-- <br>
		<br> -->

		<div class="questionBlock">
			<label class="question" for="Q3text">Q3: Any thing you like about the teaching of this course?</label><br>
			<textarea class="Q3text" name="Q3"></textarea>
		</div>

		<!-- <br>
		<br> -->

		<input type="submit" value="Submit" name="submit" class="submit">
	</form>


</body>

</html>
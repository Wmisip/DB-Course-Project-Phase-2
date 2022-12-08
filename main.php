<html>

<body>
    <?php
    if (!isset($_SESSION["username"]) || $_SESSION["enabled"] == "false") {
        header("LOCATION:common.php");
    } else {
        echo '<p style="text-align:left"> Welcome ' . $_SESSION["username"] . '</p>';

    ?>
        <form action="main.php" method="post">
            <p style="text-align:left">
                <input type="submit" name="logout" id="logout" value="logout">
            </p>
        </form>
    <?php
    }
    echo "<br/>";
    ?>
    <br>
    <p>
        Welcome to our online minibank!
        <br>
        <br>
        We can help you to transfer the money or display your accounts.
        <br>
        <br>
        What would you like to do? Please click one of the buttons
    </p>
    <br>
    <form action="bankoperation.php" method="post">
        <input type="submit" name="transfer" value="Transfer">
        <input type="submit" name="accounts" value="Accounts">
    </form>
</body>

</html>
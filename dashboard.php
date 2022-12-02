<?php
    session_start();
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 

    echo "Welcome, " . $_SESSION['username'] ;
    if (isset($_POST['Logout'])) {
        session_unset();
        session_destroy();
        header("Location: login.php?=loggedout");
    }
?>


<form action="dashboard.php" method="post">
    <input type = "submit" name = "Logout" value = "Logout">
</form>
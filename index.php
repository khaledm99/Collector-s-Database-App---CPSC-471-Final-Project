<?php
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } else {
        echo "connection successful";   
    }

    if (isset($_POST['Submit'])) {
        
        $username = htmlentities($_POST['username']);
        $email = htmlentities($_POST['email']);
        // $curdate = mysqli_query($connection, "SELECT curdate()");
        
        $query = "INSERT INTO CLIENT (Username, Date_Joined, Email) VALUES ('$username', curdate(), '$email')";
        $result = mysqli_query($connection, $query);
        echo $result;
        if (!$result) {
            exit("error occurred on insertion");
        } else {
            echo "data inserted";
        }
    }
?>


<h1> Welcome! </h1>
<form action="index.php" method="post">
    <label for="uname">Enter Username</label>
    <input type="text" id="uname" name="username"></br>
    <label for "email">Enter E-mail</label>
    <input type="text" id="email" name="email"></br>
    <input type = "submit" name = "Submit">
</form>
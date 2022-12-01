<?php
    session_start();
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 

    if (isset($_POST['Submit'])) {
        
        $username = htmlentities($_POST['username']);
        // $email = htmlentities($_POST['email']);
        // $curdate = mysqli_query($connection, "SELECT curdate()");
        
        $query = "SELECT Username FROM CLIENT WHERE CLIENT.Username = '$username'";
        $result = mysqli_query($connection, $query);
        if (!$result) {
            echo("error");
        } else {
            if (mysqli_num_rows($result) == 1){
                echo "logged in";
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
            } else {
                echo "error logging in";
            }
        }
    }
?>


<h1> Please login </h1>
<form action="login.php" method="post">
    <label for="uname">Enter Username</label>
    <input type="text" id="uname" name="username"></br>

    <input type = "submit" name = "Submit">
</form>

<form action="index.php">
    <input type="submit" value="Register" />
</form> 

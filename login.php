<style type = "text/css">
    body {
        background-color: lightgreen;
        margin: 100px;
        color: darkgreen; 

    }
    h1 {
        text-align: center;
        font-family: tahoma;
        margin: 50px;
    }
    form {
        text-align: center;
        font-family: tahoma;
    }
    div {
        text-align: center;
        font-family: tahoma;
    }
    input[type=text] {
        border: none;
    }
    input[type=button], input[type=submit] {
        border: none;
        border-radius: 2px;
        font-size: 18px;
        padding: 10px;
    } 
    input[type=submit]:hover {
        color: white;
    }

</style>
<body>
<h1> Please login </h1>
<form action="login.php" method="post">
    <label for="uname">Enter Username</label>
    <input type="text" id="uname" name="username"></br>
    </br>
    <input type = "submit" name = "Submit">
</form>

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
        
        $query = "SELECT Username FROM CLIENT WHERE CLIENT.Username = ?";
        if($prepared_query = mysqli_prepare($connection, $query)){
            mysqli_stmt_bind_param($prepared_query, 's', $username);
            mysqli_stmt_execute($prepared_query);
            mysqli_stmt_store_result($prepared_query);
            
            $result = mysqli_stmt_affected_rows($prepared_query);
        }
        
        if ($result == 1){
            echo "logged in";
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
        } else {
            echo "<div>error logging in</div></br>";
        }
        
    }
?>




<form action="index.php">
    <input type="submit" value="Register" />
</form> 
</body>
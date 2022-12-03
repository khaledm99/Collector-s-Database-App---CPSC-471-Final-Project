<?php
    session_start();
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 

    $username = $_SESSION['username'] ;
    if (isset($_POST['Logout'])) {
        session_unset();
        session_destroy();
        header("Location: login.php?=loggedout");
    }

    $query = "SELECT * FROM CLIENT";
    if($prepared_query = mysqli_prepare($connection, $query)){
        mysqli_stmt_bind_param($prepared_query, 's', $username);
        if(mysqli_stmt_execute($prepared_query)){
            $result = mysqli_stmt_get_result($prepared_query);
            while ($o = mysqli_fetch_object($result)) {
                $fstring = sprintf("User: %-10s | Joined: %-12s | E-mail: %-12s", $o->Username, $o->Date_Joined, $o->Email);
                echo ("<pre> $fstring </pre>");
            }
        } else {
            echo("Error executing SQL");
        }
    }
    
    
    
?>
</br>
<form action="admin_dashboard.php">
    <input type = "submit" name = "Dashboard" value = "Return to Dashboard">
</form>
<form action="view_users.php" method="post">
    <input type = "submit" name = "Logout" value = "Logout">
</form>
<?php
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 

    echo "Welcome, " . $_GET['username'] ;
    
?>


<h1> Logged in </h1>

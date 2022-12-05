<html>
<style type = "text/css">
    body {
        background-color: #37FF8B;
        /* background-image: url(bg3.png); */
        background-repeat: no-repeat;
        background-size: cover;
        margin: 100px;
        color: #457B9D; 

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
        color: #E63946;
    }
</style>
<body>
<?php
    session_start();
    $connection = mysqli_connect("localhost","root","","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 
    $username = $_SESSION['username'] ;
    echo "<h1>Welcome, administrator $username</h1>"  ;
    
    if (isset($_POST['Logout'])) {
        session_unset();
        session_destroy();
        header("Location: login.php?=loggedout");
    }
    if (isset($_POST['gen_report'])) {
       
        $time = date("Y-m-d H:i:s");
        $query = "INSERT INTO SYSTEM_REPORT (Timestamp) VALUES (?)";
        if($prepared_query = mysqli_prepare($connection, $query)){
            mysqli_stmt_bind_param($prepared_query, 's', $time);
            mysqli_stmt_execute($prepared_query);
            mysqli_stmt_store_result($prepared_query);
            
            $result = mysqli_stmt_affected_rows($prepared_query);
        }
        $query = "INSERT INTO GENERATES_SYS_REPORT (Admin_username, Report_timestamp, Super_collection_name) VALUES (?, ?, 'khaledm99''s collection')";
        
        if($prepared_query = mysqli_prepare($connection, $query)){
            mysqli_stmt_bind_param($prepared_query, 'ss', $username, $time);
            mysqli_stmt_execute($prepared_query);
            mysqli_stmt_store_result($prepared_query);
            
            $result = mysqli_stmt_affected_rows($prepared_query);
        }
        echo("<div></br> System Report Generated at $time!</div>");
        
    }
?>

<form action="admin_dashboard.php" method="post">
    <input type = "submit" name = "gen_report" value = "Generate System Report">
</form>

<form action="view_sys_reports.php">
    <input type = "submit" name = "view_reports" value = "View System Reports">
</form>

<form action="view_users.php">
    <input type = "submit" name = "view_users" value = "View User List">
</form>

<form action="admin_dashboard.php" method="post">
    <input type = "submit" name = "Logout" value = "Logout">
</form>
</body>
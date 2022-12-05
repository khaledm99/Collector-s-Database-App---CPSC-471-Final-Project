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
<?php
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 

?>
    
<h1> Welcome, enter a Username and Email to register! </h1>
<body>

<form action="index.php" method="post">
    <label for="uname">Enter Username</label>
    <input type="text" id="uname" name="username"></br>
    <label for ="email">Enter E-mail</label>
    <input type="text" id="email" name="email"></br></br>
    <input type = "submit" value="Register" name = "Submit">
</form>
<?php
if (isset($_POST['Submit'])) {
        
        $username = htmlentities($_POST['username']);
        $email = htmlentities($_POST['email']);
        $input_err = FALSE;
        if(empty(trim($username))) {
            echo("<div>Please enter a username </div></br>");
            $input_err = TRUE;
        }
        if(empty(trim($email))) {
            echo("<div>Please enter an email </div></br>");
            $input_err = TRUE;
        }

        if(!$input_err){
            $insert = "INSERT INTO CLIENT (Username, Date_Joined, Email) VALUES (?, curdate(), ?)";
            if($pinsert = mysqli_prepare($connection, $insert)){
                mysqli_stmt_bind_param($pinsert, 'ss', $username, $email);
                $insert_status = mysqli_stmt_execute($pinsert);                
            }
            // $result = mysqli_query($connection, $query);
            $insertion_err = FALSE;
            if (!$insert_status) {
                echo("<div>username already exists, please try again</div>");
                //echo mysqli_error($pinsert);
                $insertion_err = TRUE;
            } else {
                $supinsert = "INSERT INTO SUPER_COLLECTION (Name, Owner_username, no_of_subcollections) VALUES (?, ?, 1)";

                if($psupinsert = mysqli_prepare($connection, $supinsert)){
                    $col_name = $username.'s collection';
                    mysqli_stmt_bind_param($psupinsert, 'ss', $col_name, $username);
                    $result = mysqli_stmt_execute($psupinsert);

                }
                if (!$result) {
                    echo("sup_coll_err");
                    //echo mysqli_error($psupinsert);
                    $insertion_err = TRUE;
                } else {
                    echo("$result");
                }

                $subinsert = "INSERT INTO SUB_COLLECTION (Name, Super_collection_name) VALUES (?, ?)";
                
                if($psubinsert = mysqli_prepare($connection, $subinsert)){
                    $sub_col_name = $username.'s first collection';
                    $sup_col_name = $username.'s collection';
                    mysqli_stmt_bind_param($psubinsert, 'ss', $sub_col_name, $sup_col_name);
                    $result = mysqli_stmt_execute($psubinsert);

                }
                
                if (!$result) {
                    echo("sub_coll_err");
                    //echo mysqli_error($psubinsert);
                    $insertion_err = TRUE;
                } else {
                    echo("$result");
                }

                $wishinsert = "INSERT INTO WISHLIST (Owner_username) VALUES (?)";
                if($pwishinsert = mysqli_prepare($connection, $wishinsert)){
                    mysqli_stmt_bind_param($pwishinsert, 's', $username);
                    $result = mysqli_stmt_execute($pwishinsert);
                }
                if (!$result) {
                    echo("wishlist_err");
                    //echo mysqli_error($pwishinsert);
                    $insertion_err = TRUE;
                } else {
                    echo("$result");
                }

                $reportinsert = "INSERT INTO REPORT (Sub_collection_name, Super_collection_name) VALUES (?, ?)";
                if($preportinsert = mysqli_prepare($connection, $reportinsert)){
                    $sub_col_name = $username.'s first collection';
                    $sup_col_name = $username.'s collection';
                    mysqli_stmt_bind_param($preportinsert, 'ss', $sub_col_name, $sup_col_name);
                    $result = mysqli_stmt_execute($preportinsert);

                }
                if (!$result) {
                    echo("report_err");
                    //echo mysqli_error($preportinsert);
                    $insertion_err = TRUE;
                } else {
                    echo("$result");
                }
                
            }
            if(!$insertion_err){
                echo(("<div>Successfully registered '$username'!</div></br>"));
            }
        }

        
        

       
    }
?>
</br>
<form action="login.php">
    <input type="submit" value="Login" />
</form> 
</body>
</html>
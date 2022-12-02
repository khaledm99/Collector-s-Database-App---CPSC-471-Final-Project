<?php
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 
?>
    


<h1> Welcome, enter a Username and Email to register! </h1>
<form action="index.php" method="post">
    <label for="uname">Enter Username</label>
    <input type="text" id="uname" name="username"></br>
    <label for ="email">Enter E-mail</label>
    <input type="text" id="email" name="email"></br></br>
    <input type = "submit" name = "Submit">
</form>
<?php
if (isset($_POST['Submit'])) {
        
        $username = htmlentities($_POST['username']);
        $email = htmlentities($_POST['email']);
        $input_err = FALSE;
        if(empty(trim($username))) {
            echo("Please enter a username </br>");
            $input_err = TRUE;
        }
        if(empty(trim($email))) {
            echo("Please enter an email </br>");
            $input_err = TRUE;
        }

        if(!$input_err){
            $query = "INSERT INTO CLIENT (Username, Date_Joined, Email) VALUES (?, curdate(), ?)";
            if($prepared_query = mysqli_prepare($connection, $query)){
                mysqli_stmt_bind_param($prepared_query, 'ss', $username, $email);
                mysqli_stmt_execute($prepared_query);
                mysqli_stmt_store_result($prepared_query);
                
                $result = mysqli_stmt_affected_rows($prepared_query);
            }
            // $result = mysqli_query($connection, $query);
            $insertion_err = FALSE;
            if ($result != 1) {
                echo("username already exists, please try again");
            } else {
                $query = "INSERT INTO SUPER_COLLECTION (Name, Owner_username, no_of_subcollections) VALUES (?'s collection', ?, 1)";

                if($prepared_query = mysqli_prepare($connection, $query)){
                    mysqli_stmt_bind_param($prepared_query, 'ss', $username, $username);
                    mysqli_stmt_execute($prepared_query);
                    mysqli_stmt_store_result($prepared_query);
                    
                    $result = mysqli_stmt_affected_rows($prepared_query);
                }
                if ($result == 0) {
                    echo("sup_coll_err");
                    $insertion_err = TRUE;
                } 

                $query = "INSERT INTO SUB_COLLECTION (Name, Super_collection_name) VALUES (?'s first collection', ?'s collection')";
                
                if($prepared_query = mysqli_prepare($connection, $query)){
                    mysqli_stmt_bind_param($prepared_query, 'ss', $username, $username);
                    mysqli_stmt_execute($prepared_query);
                    mysqli_stmt_store_result($prepared_query);
                    
                    $result = mysqli_stmt_affected_rows($prepared_query);
                }
                
                if ($result==0) {
                    echo("sub_coll_err");
                    $insertion_err = TRUE;
                } 

                $query = "INSERT INTO WISHLIST (Owner_username) VALUES (?)";
                if($prepared_query = mysqli_prepare($connection, $query)){
                    mysqli_stmt_bind_param($prepared_query, 's', $username);
                    mysqli_stmt_execute($prepared_query);
                    mysqli_stmt_store_result($prepared_query);
                    
                    $result = mysqli_stmt_affected_rows($prepared_query);
                }
                if ($result == 0) {
                    echo("wishlist_err");
                    $insertion_err = TRUE;
                } 

                $query = "INSERT INTO REPORT (Sub_collection_name, Super_collection_name) VALUES (?'s first collection', ?'s collection')";
                if($prepared_query = mysqli_prepare($connection, $query)){
                    mysqli_stmt_bind_param($prepared_query, 'ss', $username, $username);
                    mysqli_stmt_execute($prepared_query);
                    mysqli_stmt_store_result($prepared_query);
                    
                    $result = mysqli_stmt_affected_rows($prepared_query);
                }
                if ($result==0) {
                    echo("report_err");
                    $insertion_err = TRUE;
                } 
                
            }
            if(!$insertion_err){
                echo(("Successfully registered '$username'!</br>"));
            }
        }

        
        

       
    }
?>
</br></br>
<form action="login.php">
    <input type="submit" value="Login" />
</form> 
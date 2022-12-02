<?php
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 

    if (isset($_POST['Submit'])) {
        
        $username = htmlentities($_POST['username']);
        $email = htmlentities($_POST['email']);
        // $curdate = mysqli_query($connection, "SELECT curdate()");
        
        $query = "INSERT INTO CLIENT (Username, Date_Joined, Email) VALUES ('$username', curdate(), '$email')";
        $result = mysqli_query($connection, $query);
        if (!$result) {
            echo("username already exists, please try again");
        } else {
            echo "data inserted";
            $query = "INSERT INTO SUPER_COLLECTION (Name, Owner_username, no_of_subcollections) VALUES ('$username''s collection', '$username', 1)";
            $result = mysqli_query($connection, $query);
            if (!$result) {
                echo("sup_coll_err");
            } else {
                echo "sup_col inserted";
            }

            $query = "INSERT INTO SUB_COLLECTION (Name, Super_collection_name) VALUES ('$username''s first collection', '$username''s collection')";
            $result = mysqli_query($connection, $query);
            if (!$result) {
                echo("sub_coll_err");
            } else {
                echo "sub_col inserted";
            }

            $query = "INSERT INTO WISHLIST (Owner_username) VALUES ('$username')";
            $result = mysqli_query($connection, $query);
            if (!$result) {
                echo("wishlist_err");
            } else {
                echo "wishlist inserted";
            }

            $query = "INSERT INTO REPORT (Sub_collection_name, Super_collection_name) VALUES ('$username''s first collection', '$username''s collection')";
            $result = mysqli_query($connection, $query);
            if (!$result) {
                echo("report_err");
            } else {
                echo "report inserted";
            }
        }

        

       
    }
?>


<h1> Welcome, enter a Username and Email to register! </h1>
<form action="index.php" method="post">
    <label for="uname">Enter Username</label>
    <input type="text" id="uname" name="username"></br>
    <label for ="email">Enter E-mail</label>
    <input type="text" id="email" name="email"></br>
    <input type = "submit" name = "Submit">
</form>
<form action="login.php">
    <input type="submit" value="Login" />
</form> 
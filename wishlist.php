<?php     session_start(); ?>   
<style type = "text/css">
    body {
        /* background-color: #37FF8B; */
        background-image: url(bg3.png);
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
    if (isset($_GET['delete'])){
        $to_del = strval($_GET['delete']);
        $query = "DELETE FROM ITEM where ITEM.Wishlist_name = ? AND ITEM.Item_ID = ?";
        if($prepared_query = mysqli_prepare($connection, $query)){
            
            mysqli_stmt_bind_param($prepared_query, 'ss', $username, $to_del);
            if(mysqli_stmt_execute($prepared_query)){
                $result = mysqli_stmt_get_result($prepared_query);
                echo('<div>Item deleted</div>');
                
            } else {
                echo("Error executing SQL");
            }
        }
    }

    $query = "SELECT Name,ITEM.ITEM_ID FROM ITEM, TITLE  WHERE TITLE.ITEM_ID = ITEM.ITEM_ID AND ITEM.Wishlist_name = ? 
              UNION select Name,ITEM.ITEM_ID  FROM ITEM, CONSOLE WHERE ITEM.ITEM_ID = CONSOLE.ITEM_ID AND ITEM.Wishlist_name = ?
              UNION select Name,ITEM.ITEM_ID  FROM ITEM, CONTROLLER WHERE ITEM.ITEM_ID = CONTROLLER.ITEM_ID AND ITEM.Wishlist_name = ?
              UNION select Name,ITEM.ITEM_ID  FROM ITEM, STORAGE_DEVICE WHERE ITEM.ITEM_ID = STORAGE_DEVICE.ITEM_ID AND ITEM.Wishlist_name = ?
              UNION select Name,ITEM.ITEM_ID  FROM ITEM, MISC_PERIPHERAL WHERE ITEM.ITEM_ID = MISC_PERIPHERAL.ITEM_ID AND ITEM.Wishlist_name = ?
              UNION select Name,ITEM.ITEM_ID  FROM ITEM, SUBSCRIPTION WHERE ITEM.ITEM_ID = SUBSCRIPTION.ITEM_ID AND ITEM.Wishlist_name = ?";
    if($prepared_query = mysqli_prepare($connection, $query)){
        mysqli_stmt_bind_param($prepared_query, 'ssssss', $username, $username, $username, $username, $username, $username);
        if(mysqli_stmt_execute($prepared_query)){
            $result = mysqli_stmt_get_result($prepared_query);
            echo('<div>');
            while ($o = mysqli_fetch_object($result)) {
                // printf("%s ", $o->Name);
                echo (strval($o->Name)."<form action=\"wishlist.php?delete=".strval($o->ITEM_ID)."\" method=\"post\"><input type = \"submit\" name = \"delete\" value = \"Delete\"></form></br>");
            }
            echo('</div>');

        } else {
            echo("Error executing SQL");
        }
    }
    
    
    
?>
</br>
<form action="dashboard.php">
    <input type = "submit" name = "Dashboard" value = "Return to Dashboard">
</form>
<form action="wishlist.php" method="post">
    <input type = "submit" name = "Logout" value = "Logout">
</form>
</body>
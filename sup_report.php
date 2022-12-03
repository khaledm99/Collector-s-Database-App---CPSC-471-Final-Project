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
    session_start();
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 
    $username = $_SESSION['username'] ;
    echo ("<h1>Viewing " . $_SESSION['username'] . "'s overall collection report</h1>" );

    $query = "SELECT TITLE.Name FROM ITEM, TITLE, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION   WHERE TITLE.ITEM_ID = ITEM.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
                AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = ? 
              UNION select CONSOLE.Name FROM ITEM, CONSOLE, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION WHERE ITEM.ITEM_ID = CONSOLE.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
                AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = ? 
              UNION select CONTROLLER.Name FROM ITEM, CONTROLLER, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION WHERE ITEM.ITEM_ID = CONTROLLER.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
                AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = ? 
              UNION select STORAGE_DEVICE.Name FROM ITEM, STORAGE_DEVICE, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION WHERE ITEM.ITEM_ID = STORAGE_DEVICE.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
                AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = ? 
              UNION select MISC_PERIPHERAL.Name FROM ITEM, MISC_PERIPHERAL, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION WHERE ITEM.ITEM_ID = MISC_PERIPHERAL.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
                AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = ? 
              UNION select SUBSCRIPTION.Name FROM ITEM, SUBSCRIPTION, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION WHERE ITEM.ITEM_ID = SUBSCRIPTION.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
                AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = ? ";
    if($prepared_query = mysqli_prepare($connection, $query)){
        mysqli_stmt_bind_param($prepared_query, 'ssssss', $username, $username, $username, $username, $username, $username);
        if(mysqli_stmt_execute($prepared_query)){
            $result = mysqli_stmt_get_result($prepared_query);
            echo("<div>Number of items owned: $result->num_rows</div>");
        } else {
            echo("Error executing SQL");
        }
    }
    echo("</br>");
    $query = "SELECT Name FROM ITEM, TITLE  WHERE TITLE.ITEM_ID = ITEM.ITEM_ID AND ITEM.Wishlist_name = ? 
              UNION select Name FROM ITEM, CONSOLE WHERE ITEM.ITEM_ID = CONSOLE.ITEM_ID AND ITEM.Wishlist_name = ?
              UNION select Name FROM ITEM, CONTROLLER WHERE ITEM.ITEM_ID = CONTROLLER.ITEM_ID AND ITEM.Wishlist_name = ?
              UNION select Name FROM ITEM, STORAGE_DEVICE WHERE ITEM.ITEM_ID = STORAGE_DEVICE.ITEM_ID AND ITEM.Wishlist_name = ?
              UNION select Name FROM ITEM, MISC_PERIPHERAL WHERE ITEM.ITEM_ID = MISC_PERIPHERAL.ITEM_ID AND ITEM.Wishlist_name = ?
              UNION select Name FROM ITEM, SUBSCRIPTION WHERE ITEM.ITEM_ID = SUBSCRIPTION.ITEM_ID AND ITEM.Wishlist_name = ?";
    if($prepared_query = mysqli_prepare($connection, $query)){
        mysqli_stmt_bind_param($prepared_query, 'ssssss', $username, $username, $username, $username, $username, $username);
        if(mysqli_stmt_execute($prepared_query)){
            $result = mysqli_stmt_get_result($prepared_query);
            echo("<div>Number of items in wishlist: $result->num_rows</div>");
            // echo("'$result' Items owned");
        } else {
            echo("Error executing SQL");
        }
    }
    echo("</br>");

    $query = "SELECT Sub_collection.Name from Sub_collection, Super_collection where Sub_collection.Super_collection_name = Super_collection.Name and Super_collection.Owner_username = ?";
    if($prepared_query = mysqli_prepare($connection, $query)){
        mysqli_stmt_bind_param($prepared_query, 's', $username);
        if(mysqli_stmt_execute($prepared_query)){
            $result = mysqli_stmt_get_result($prepared_query);
            echo("<div>Number of sub-collections: $result->num_rows</div>");
            // echo("'$result' Items owned");
        } else {
            echo("Error executing SQL");
        }
    }

    if (isset($_POST['Logout'])) {
        session_unset();
        session_destroy();
        header("Location: login.php?=loggedout");
    }
?>




</br>
<form action="dashboard.php">
    <input type = "submit" name = "Dashboard" value = "Return to Dashboard">
</form>
<form action="dashboard.php" method="post">
    <input type = "submit" name = "Logout" value = "Logout">
</form>
</body>
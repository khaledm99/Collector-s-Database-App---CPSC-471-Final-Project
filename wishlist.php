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

    $query = "SELECT Name FROM ITEM, TITLE  WHERE TITLE.ITEM_ID = ITEM.ITEM_ID AND ITEM.Wishlist_name = '$username' UNION select Name FROM ITEM, CONSOLE WHERE ITEM.ITEM_ID = CONSOLE.ITEM_ID AND ITEM.Wishlist_name = '$username'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        echo("sup_coll_err");
    } else {
        while ($o = mysqli_fetch_object($result)) {
            printf("%s ", $o->Name);
            printf("\n");
        }
    }
?>
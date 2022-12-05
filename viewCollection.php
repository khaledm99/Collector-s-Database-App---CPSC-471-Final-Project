<?php session_start(); ?>
<!DOCTYPE html>
<html>
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
    table {
            margin: 0 auto;
            font-size: large;
            border: 1px solid black;
    }

    td {
            border: 1px solid black;
    }
 
    th,
    td {
        font-weight: bold;
        border: 1px solid black;
        padding: 10px;
        text-align: center;
    }

    td {
        font-weight: lighter;
    }

    .center {
        margin-left:auto;
        margin-right:auto;
    }

</style>

<body>

<?php 
$conn = mysqli_connect("localhost","root","password","main");
if(!$conn) {
    exit("there was an error".mysqli_connect_errno());
} 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$username = $_SESSION['username'];

$collection = $_POST['collectionID'];

if(isset($_POST['itemToAdd'])){
    $item_id = $_POST['itemID'];
    $sql = "INSERT INTO IN_COLLECTION (Item_ID, Collection_name) VALUES (?, ?)";
    $pquery = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($pquery, 'is', $item_id, $collection);
    mysqli_stmt_execute($pquery);
}

if(isset($_POST['addTitle'])){
    $item_id = addItem($conn);
    $title = $_POST['title'];
    $play_status = $_POST['play_status'];
    $release_year = $_POST['release_year'];
    $play_time = $_POST['play_time'];
    $game_type = $_POST['game_type'];
    $edition = $_POST['edition'];
    $rating = $_POST['rating'];
    $sql = "INSERT INTO TITLE (Name, Play_status, Release_year, Playtime, Game_type, Edition, Rating, Item_ID) VALUES (?,?,?,?,?,?,?,?)";
    $pquery = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($pquery, 'ssidssdi', $title, $play_status, $release_year, $play_time, $game_type, $edition, $rating, $item_id);
    mysqli_stmt_execute($pquery);
    $sql = "INSERT INTO IN_COLLECTION (Item_ID, Collection_name) VALUES (?, ?)";
    $pquery = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($pquery, 'is', $item_id, $collection);
    mysqli_stmt_execute($pquery);
}

if(isset($_POST['addConsole'])){
    $item_id = addItem($conn);
    $title = $_POST['title'];
    $serial = $_POST['serial'];
    $storage = $_POST['storage'];
    $ctype = $_POST['ctype'];
    $edition = $_POST['edition'];
    $sql = "INSERT INTO CONSOLE (Name, Serial_no, Internal_storage_capacity, Type, Edition, Item_ID) VALUES (?,?,?,?,?,?)";
    $pquery = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($pquery, 'ssdssi', $title, $serial, $storage, $ctype, $edition, $item_id);
    mysqli_stmt_execute($pquery);
    $sql = "INSERT INTO IN_COLLECTION (Item_ID, Collection_name) VALUES (?, ?)";
    $pquery = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($pquery, 'is', $item_id, $collection);
    mysqli_stmt_execute($pquery);
}

$sql = "SELECT in_col.item_id AS ID, t.name AS Name, 'title' AS Type FROM IN_COLLECTION AS in_col, TITLE AS t WHERE in_col.Collection_name = ? AND in_col.Item_ID = t.Item_ID
        UNION
        SELECT in_col.item_id AS ID, c.name AS Name, 'console' as Type FROM IN_COLLECTION AS in_col, CONSOLE AS c WHERE in_col.Collection_name = ? AND in_col.Item_ID = c.Item_ID";

$pquery = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($pquery, 'ss', $collection, $collection);
mysqli_stmt_execute($pquery);
mysqli_stmt_store_result($pquery);
$result = mysqli_stmt_affected_rows($pquery);
mysqli_stmt_bind_result($pquery, $item_id, $item_name, $type);

    ?>
    <h1>Your Items</h1>
        <!-- TABLE HEADER CONSTRUCTION -->
        <div><table class="center">
            <tr>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Type</th>
            </tr>
    <?php    
        // output data of each row
        // NEED TO FIX THIS - GET THE RESULT SET FROM THE STORED STATEMENT
        while(mysqli_stmt_fetch($pquery)) { 
    ?>
        <tr>
            <td><?php echo $item_id; ?> </td>
            <td><?php echo ("<a href=\"view_item.php?itemid=$item_id&type=$type\">$item_name</a>"); ?> </td>
            <td><?php echo $type; ?> </td>
        </tr>
    <?php 
            }
            echo "</table></div>";
    ?>
<form action="addItem.php" method="post">
  <label for="type">Add to collection:</label>
  <select id="type" name="type">
    <option value="title">Title</option>
    <option value="console">Console</option>
  </select>
  <input type="hidden" id="collectionID" name="collectionID" value="<?php echo $collection; ?>">
  <input type="submit" value="Add Item" name="Add_Item">
</form>
<form action="collections.php">
    <input type = "submit" name = "collections" value = "View Collections">
</form>

</body>
</html>

<?php
function addItem($conn){
    $sql = "SELECT MAX(Item_ID) FROM ITEM";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_row($result);
    $itemID = $row[0] + 1;
    $sql = "INSERT INTO ITEM (Item_ID, Is_owned) VALUES (".$itemID.",TRUE)";
    $result = mysqli_query($conn, $sql);
    return $itemID;
}
?>
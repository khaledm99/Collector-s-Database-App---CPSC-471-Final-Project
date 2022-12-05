<?php session_start();?>
<!DOCTYPE html>
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
$connection = mysqli_connect("localhost","root","password","main");
if(!$connection) {
    exit("there was an error".mysqli_connect_errno());
} 
$username = $_SESSION['username'];

// Prepare the query against table super_collection
$sql = "SELECT SUB_COLLECTION.Name FROM SUB_COLLECTION, SUPER_COLLECTION WHERE SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND Owner_username = ?";
$search_set = FALSE;

if(isset($_POST['Add'])){
    $newName = trim(htmlentities($_POST['collection']));
    if (empty($newName)){
        echo "<div>You must enter a collection name in order to add something new</div></br>";
    }
    else{
        $insert = "INSERT INTO SUB_COLLECTION (Name, Super_collection_name) VALUES (?, ?)";
        if($prepared_query = mysqli_prepare($connection, $insert)){
            $subName = $username.'s collection';
            mysqli_stmt_bind_param($prepared_query, 'ss', $newName, $subName);
            mysqli_stmt_execute($prepared_query);
        }
        else{
            die ("Statement error: ".mysqli_stmt_error($prepared_query));
        }
    }
}

if(isset($_POST['Search'])){
    $search = "%" . trim(htmlentities($_POST['searchFor'])) . "%";
    $search_set = TRUE;
    $sql = $sql . " AND SUB_COLLECTION.Name like ?";
}

if( $pquery = mysqli_prepare($connection, $sql) ){
    if (!$search_set) {
        mysqli_stmt_bind_param($pquery, 's', $username);
    }
    else{
        mysqli_stmt_bind_param($pquery, 'ss', $username, $search);
    }
    mysqli_stmt_execute($pquery);
    mysqli_stmt_store_result($pquery);
    mysqli_stmt_bind_result($pquery, $cname);
    
    $result = mysqli_stmt_affected_rows($pquery);
}
if ( $result > 0 ) {
    ?>
            <h1>Your Collections</h1>
                <form action="collections.php" method="post">
                    <label for="cname">Search for:</label>
                    <input type="text" id="searchFor" name="searchFor">
                    <input type="submit" value="Search" name="Search">
                </form>
                <!-- TABLE HEADER CONSTRUCTION -->
                <div><table class="center">
                    <tr>
                        <th>Collection Name</th>
                        <th></th>
                    </tr>
    <?php    
        // output data of each row
        // NEED TO FIX THIS - GET THE RESULT SET FROM THE STORED STATEMENT
        while(mysqli_stmt_fetch($pquery)) { 
    ?>
                    <tr>
                        <td><?php echo $cname; ?> </td>
                        <td>
                            <form action="viewCollection.php" method="post">
                                <input type="hidden" id="collectionID" name="collectionID" value="<?php echo $cname; ?>">
                                <input type="submit" name="details" value="View Details">
                            </form>
                        </td>
                    </tr>
    <?php 
            }
            echo "</table></div>";
    } else {
        if ( $search_set ) {
            echo "<div>Your search for '".$search."' didn't find anything</div></br>";
        }
        else {
            echo "<div>You don't have any collections</div></br>";
        }
    }
    //echo print_r($_POST, TRUE);
?>

<form action="collections.php" method="post">
    <label for="add">Add new collection:</label>
    <input type="text" id="add" name="collection">
    <input type="submit" value="Add" name="Add"></br>
</form>

</body>

</html>


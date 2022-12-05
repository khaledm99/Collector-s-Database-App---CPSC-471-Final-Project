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
    $conn = mysqli_connect("localhost","root","password","main");
    if(!$conn) {
        exit("there was an error".mysqli_connect_errno());
    } 
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $username = $_SESSION['username'];
    $collection = $_POST['collectionID'];
    $type = $_POST['type'];

    if($type == "Title"){
        $sql = "SELECT Name, Item_ID FROM TITLE";
    }
    else{
        $sql = "SELECT Name, Item_ID FROM CONSOLE";
    }
    $result = mysqli_query($conn, $sql);

    ?>
    <h1>Select a <?php echo $type; ?> to add to collection <?php echo $collection; ?></h1>
        <!-- TABLE HEADER CONSTRUCTION -->
        <div><table class="center">
            <tr>
                <th>Name</th>
                <th></th>
            </tr>
    <?php    
        // output data of each row
        // NEED TO FIX THIS - GET THE RESULT SET FROM THE STORED STATEMENT
        while($row = mysqli_fetch_row($result)) { 
    ?>
        <tr>
            <td><?php echo $row[0]; ?> </td>
            <td>
                <form action="viewCollection.php" method="post">
                    <input type="hidden" id="itemID" name="itemID" value="<?php echo $row[1]; ?>">
                    <input type="hidden" id="collectionID" name="collectionID" value="<?php echo $collection; ?>">
                    <input type="submit" name="itemToAdd" value="Select">
                </form>
            </td>
        </tr>
    <?php 
            }
            echo "</table></div>";
            echo "<div>Or add a new one:</div> </br>";
            if($type == "Title"){
                ?>
                <table>
                <form action="viewCollection.php" method="post">
                    <tr>
                        <td><label for="title">Name:</label></td>
                        <td><input type="text" id="title" name="title"></td>
                    </tr>
                    <tr>
                        <td><label for="play_status">Status:</label></td>
                        <td><input type="text" id="play_status" name="play_status"></td>
                    </tr>
                    <tr>
                        <td><label for="release_year">Release Year:</label></td>
                        <td><input type="text" id="release_year" name="release_year"></td>
                    </tr>
                    <tr>
                        <td><label for="play_time">Play Time:</label></td>
                        <td><input type="text" id="play_time" name="play_time"></td>
                    </tr>
                    <tr>
                        <td><label for="game_type">Game Type:</label></td>
                        <td><input type="text" id="game_type" name="game_type"></td>
                    </tr>
                    <tr>
                        <td><label for="edition">Edition:</label></td>
                        <td><input type="text" id="edition" name="edition"></td>
                    </tr>
                    <tr>
                        <td><label for="rating">Rating:</label></td>
                        <td><input type="text" id="rating" name="rating"></td>
                    </tr>        
                    <tr><td colspan="2"><input type="submit" value="Add Title" name="addTitle"></td></tr>
                    <input type="hidden" id="collectionID" name="collectionID" value="<?php echo $collection; ?>">
                </form>
                </table>
                <?php
            }
            else{
                ?>
                <table>
                <form action="viewCollection.php" method="post">
                    <tr>
                        <td><label for="title">Name:</label></td>
                        <td><input type="text" id="title" name="title"></td>
                    </tr>
                    <tr>
                        <td><label for="serial">Serial number:</label></td>
                        <td><input type="text" id="serial" name="serial"></td>
                    </tr>
                    <tr>
                        <td><label for="storage">Internal Storage Capacity:</label></td>
                        <td><input type="text" id="storage" name="storage"></td>
                    </tr>
                    <tr>
                        <td><label for="ctype">Type:</label></td>
                        <td><input type="text" id="ctype" name="ctype"></td>
                    </tr>
                    <tr>
                        <td><label for="edition">Edition:</label></td>
                        <td><input type="text" id="edition" name="edition"></td>
                    </tr>        
                    <tr><td colspan="2"><input type="submit" value="Add Console" name="addConsole"></td></tr>
                    <input type="hidden" id="collectionID" name="collectionID" value="<?php echo $collection; ?>">
                </form>
                </table>
                <?php
            }
    ?>
    
</body>

</html>
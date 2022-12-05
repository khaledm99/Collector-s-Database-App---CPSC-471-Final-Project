<?php     session_start(); ?>   
<style type = "text/css">
    body {
        background-color: #37FF8B;
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
        width: 100%;
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
    table, th, td {
        border: 3px solid;
        padding: 10px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        height: 100px;
        text-align: center;
        font-family: tahoma;
        background-color: white;
    }
    th {
        height: 75px;
    }

</style>
<body>
<?php
    $connection = mysqli_connect("localhost","root","password","main");
    if(!$connection) {
        exit("there was an error".mysqli_connect_errno());
    } 

    $username = $_SESSION['username'];
    if(isset($_GET['itemid'])) {
        $itemid = $_GET['itemid'];
    } else {
        echo("<div>Error: No Item ID passed</div>");
    }
    if(isset($_GET['type'])) {
        $itemtype = $_GET['type'];
    } else {
        echo("<div>Error: No Item type passed</div>");
    }
    if (isset($_POST['Logout'])) {
        session_unset();
        session_destroy();
        header("Location: login.php?=loggedout");
    }

    if(isset($_POST['save'])){
        if($itemtype == 'console'){
            $name = $_POST['Name'];
            $sno =  $_POST['Serial_no'];
            $cond = $_POST['Condition'];
            $storage = (float)$_POST['Internal_storage_capacity'];
            $consoletype = $_POST['Type'];
            $edition = $_POST['Edition'];
            $quantity = (int)$_POST['Quantity'];
            $date = $_POST['Date_acquired'];
            $madeby = $_POST['Company_made_by'];
            $itemid = (int) $itemid;
            echo "$madeby";
            $query = "UPDATE CONSOLE  SET  Name = ?, Serial_no = ? , CONSOLE.Condition = ?, Internal_storage_capacity = ?,Type = ? ,
            Edition = ? ,Quantity = ?,Date_acquired = ? , Company_made_by = ? WHERE Item_ID = ?";

            // $query = "UPDATE CONSOLE  SET  Name = ? , Serial_no = ? , CONSOLE.Condition = ? , Internal_storage_capacity = ? ,Type = ? ,Edition = ? ,Quantity = ? ,Date_acquired = ? , Company_made_by = ? WHERE Item_ID = ?";
            $sql = "UPDATE CONSOLE  SET  Name = \'ss\', Serial_no = \'ddf\' , CONSOLE.Condition = \'good\' , Internal_storage_capacity = \'4\' ,Type = \'ps\' ,Edition = \'dayone\' ,Quantity = 2 ,Date_acquired = NULL , Company_made_by = NULL WHERE Item_ID = 3";

            if($prepared_query = mysqli_prepare($connection, $query)){
                echo 'test';
                // mysqli_stmt_bind_param($prepared_query, 'sssdssissi', $name,$sno,$cond,$storage,$consoletype,$edition,$quantity,$date,$made_by, $itemid);
                mysqli_stmt_bind_param($prepared_query, 'sssdssissi',$name,$sno,$cond,$storage,$consoletype,$edition,$quantity,$date,$madeby,$itemid);
                if(mysqli_stmt_execute($prepared_query)){
                    $result = mysqli_stmt_get_result($prepared_query);
                    echo('<div>Item saved</div>');
                    
                } else {
                    echo("Error executing SQL");
                }
            }



        }
    }
















    if (isset($_POST['delete'])){
        $query = "DELETE FROM ITEM where ITEM.Item_ID = ?";
        if($prepared_query = mysqli_prepare($connection, $query)){
            
            mysqli_stmt_bind_param($prepared_query, 's', $itemid);
            if(mysqli_stmt_execute($prepared_query)){
                $result = mysqli_stmt_get_result($prepared_query);
                echo('<div>Item deleted</div>');
                
            } else {
                echo("Error executing SQL");
            }
        }
    } else {
    $sql = "SELECT * FROM {$itemtype} WHERE {$itemtype}.ITEM_ID = ?";
    
    if($prepared_query = mysqli_prepare($connection, $sql)){
        mysqli_stmt_bind_param($prepared_query, 's', $itemid);
        
        if(mysqli_stmt_execute($prepared_query)){
            
            
            $result = mysqli_stmt_get_result($prepared_query);
            $query = array();
            while($query[] = mysqli_fetch_assoc($result));
            array_pop($query);

            // Output a dynamic table of the results with column headings. Code to do so has been retrieved from: https://www.antropy.co.uk/blog/handy-php-snippets/
            echo '<table border="1">';
            echo '<tr>';
            foreach($query[0] as $key => $value) {
                echo '<td>';
                echo $key;
                echo '</td>';
            }
            echo '</tr>';
            foreach($query as $row) {
                echo '<tr>';
                foreach($row as $column) {
                    echo '<td>';
                    echo $column;
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo("Error executing SQL");
        }
        }
    }
    echo("</br>");
    echo("<div style=\"float:left;display:inline-block;\"><form action=\"edit_item.php?itemid=$itemid&type=$itemtype\" method=\"post\">
        <input type = \"submit\" name = \"edit\" value = \"Edit Item\">
    </form></div> ");
    echo("<div style=\"float:right;display:inline-block;\"><form action=\"view_item.php?itemid=$itemid&type=$itemtype\" method=\"post\">
        <input type = \"submit\" name = \"delete\" value = \"Delete Item\">
    </form></div> ");
?>
</br>

<form action="dashboard.php">
    <input type = "submit" name = "Dashboard" value = "Return to Dashboard">
</form>
<form action="view_item.php" method="post">
    <input type = "submit" name = "Logout" value = "Logout">
</form>
</body>





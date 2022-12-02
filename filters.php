<?php

session_start(); //Start Session

$connection = mysqli_connect("localhost","root","","main"); //Connecting to server
if(!$connection) {
    exit("there was an error".mysqli_connect_errno());
}

//USERNAME
$username = htmlentities($_POST['username']);

//Collection stuff
$query = "SELECT TITLE.* FROM TITLE, IN_COLLECTION
WHERE TITLE.Item_ID = IN_COLLECTION.Item_ID AND 
IN_COLLECTION.Collection_name = @sub_col_name
UNION
SELECT CONSOLE.* FROM CONSOLE, IN_COLLECTION
WHERE CONSOLE.Item_ID = IN_COLLECTION.Item_ID AND 
IN_COLLECTION.Collection_name = @sub_col_name
UNION
SELECT SUBSCRIPTION.* FROM SUBSCRIPTION, IN_COLLECTION
WHERE SUBSCRIPTION.Item_ID = IN_COLLECTION.Item_ID AND 
IN_COLLECTION.Collection_name = @sub_col_name
UNION
SELECT CONTROLLER.* FROM CONTROLLER, IN_COLLECTION
WHERE CONTROLLER.Item_ID = IN_COLLECTION.Item_ID AND 
IN_COLLECTION.Collection_name = @sub_col_name
UNION
SELECT MISC_PERIPHERAL.* FROM  MISC_PERIPHERAL, IN_COLLECTION
WHERE  MISC_PERIPHERAL.Item_ID = IN_COLLECTION.Item_ID AND 
IN_COLLECTION.Collection_name = @sub_col_name
UNION
SELECT STORAGE_DEVICE.* FROM STORAGE_DEVICE, IN_COLLECTION
WHERE STORAGE_DEVICE.Item_ID = IN_COLLECTION.Item_ID AND 
IN_COLLECTION.Collection_name = @sub_col_name
";

$filter = "default";
if (isset($_POST['Submit'])) {
        
  $filter = htmlentities($_POST['filter']);
  
}



?>

<!--HTML LOOK STUFF-->
<!DOCTYPE html>
<html>
<body>

<h1>Filter Selection</h1>
<h2>Username: Zohaib</h2>


<p>Please choose a filter for your collection from the dropdown menu below.</p>

<form action="filters.php" method = "post">
  <label for="filter">Choose a filter:</label>
  <select name="filter" id="filter">
    <option <?=$filter=="default"?'selected="selected"':'';?> value="default" > -- no filter -- </option>
    <option <?=$filter=="titles"?'selected="selected"':'';?>value="titles">Titles</option>
    <option <?=$filter=="consoles"?'selected="selected"':'';?>value="consoles">Consoles</option>
    <option <?=$filter=="subscriptions"?'selected="selected"':'';?>value="subscriptions">Subscriptions</option>
    <option <?=$filter=="accessories"?'selected="selected"':'';?>value="accessories">Accessories</option>
    <option <?=$filter=="company"?'selected="selected"':'';?>value="company">Company</option>
  </select>
  <br><br>
  <input type="submit" value="Submit" name = "Submit">
</form>

<p>Click the "Submit" button and the form-data will be sent to a page on the 
server called "filters.php".</p>

</body>
</html>

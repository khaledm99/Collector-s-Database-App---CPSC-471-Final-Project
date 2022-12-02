<?php

session_start();
$connection = mysqli_connect("localhost","root","","main");
if(!$connection) {
    exit("there was an error".mysqli_connect_errno());
}

//USERNAME
$username = htmlentities($_POST['username']);

//TITLE FILTER POSSIBLY
$query = "SELECT TITLE.* FROM TITLE, IN_COLLECTION WHERE TITLE.Item_ID = IN_COLLECTION.Item_ID AND  IN_COLLECTION.Collection_name";



?>

<!--HTML LOOK STUFF-->
<!DOCTYPE html>
<html>
<body>

<h1>Filter Selection</h1>
<h2>Username: Zohaib</h2>


<p>Please choose a filter for your collection from the dropdown menu below.</p>

<form action="filters.php">
  <label for="filters">Choose a filter:</label>
  <select name="filter" id="filter">
    <option disabled selected value> -- select an option -- </option>
    <option value="titles">Titles</option>
    <option value="consoles">Consoles</option>
    <option value="subscriptions">Subscriptions</option>
    <option value="accessories">Accessories</option>
    <option value="company">Company</option>
  </select>
  <br><br>
  <input type="submit" value="Submit">
</form>

<p>Click the "Submit" button and the form-data will be sent to a page on the 
server called "filters.php".</p>

</body>
</html>

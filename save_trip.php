<?php
$conn = new mysqli('localhost', 'root', '', 't');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

date_default_timezone_set('Asia/Manila');
$date = date("Y-m-d");
$time = date("H:i:s");

$routeName = $_POST['routeName'];
$pickup = $_POST['pickup'];
$dropoff = $_POST['dropoff'];
$passengerType = $_POST['passengerType'];
$fare = $_POST['fare'];
$estTime = $_POST['estTime'];

$stmt = $conn->prepare("
    INSERT INTO savedtrips (RouteName, PickupPoint, DropoffPoint, PassengerType, Fare, EstimatedTime, DateCreated, TimeCreated)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("sssssdss", $routeName, $pickup, $dropoff, $passengerType, $fare, $estTime, $date, $time);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<h2>Route saved successfully!</h2><a href='view_archive.php'>View Archive</a>";
} else {
    echo "<h2>Failed to save route.</h2>";
}

$conn->close();
?>

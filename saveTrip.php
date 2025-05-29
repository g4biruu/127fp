<?php
include 'DBConnector.php';
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $TripID = $_POST['TripID'];
    $JeepName1 = $_POST['JeepName1'];
    $JeepName2 = $_POST['JeepName2'] ?: NULL;
    $PickupPoint = $_POST['PickupPoint'];
    $DropoffPoint = $_POST['DropoffPoint'] ?: NULL;
    $DestinationPoint = $_POST['DestinationPoint'];
    $PassengerType = $_POST['PassengerType'];
    $ACType = $_POST['ACType'];
    $Fare = $_POST['Fare'];

    // EstimatedTime, DateCreated, TimeCreated - you can set defaults or skip for now
    $DateCreated = date('Y-m-d');
    $TimeCreated = date('H:i:s');

    $stmt = $conn->prepare("INSERT INTO SavedTrips (TripID, JeepName1, JeepName2, PickupPoint, DropoffPoint, DestinationPoint, PassengerType, ACType, Fare, DateCreated, TimeCreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssss", $TripID, $JeepName1, $JeepName2, $PickupPoint, $DropoffPoint, $DestinationPoint, $PassengerType, $ACType, $Fare, $DateCreated, $TimeCreated);

    if ($stmt->execute()) {
        echo "Trip saved successfully! <a href='routeFinder.php'>Back to Route Finder</a>";
    } else {
        echo "Error saving trip: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>

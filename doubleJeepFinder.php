<?php
    include 'DBConnector.php';

    $originID = $_POST['origin'];
    $destinationID = $_POST['destination'];
    $dropOff = $_POST['dropOff'];
    $PassengerType = $_POST['PassengerType'];
    $ACType = $_POST['ACType'];
    $originDistance = $_POST['originDistance'];
    $destDistance = $_POST['destDistance'];
    $Fare = 0;

    $sql = "SELECT * FROM Landmarks WHERE LandmarkName = '$dropOff'";
    $dropOffID = $conn->query($sql)->fetch_assoc()['LandmarkID'];
    $dropOffDistance = $conn->query($sql)->fetch_assoc()['Distance'];

    $jeepID1;
    $jeepID2;

    // $sql = "INSERT INTO Trips (`TripID`,`JeepID1`, `JeepID2`, `OriginID`, `DropOffID`, `DestinationID`, `ACType`, `PassengerType`, `Fare`) VALUES 
    // (NULL,'$mostID',NULL,'$originID',NULL,'$destinationID','$ACType','$PassengerType','$Fare');";
    // $conn->query($sql);

    // $sql = "DELETE FROM Trips WHERE TripID = (SELECT MIN(TripID) FROM Trips);";
    // $conn->query($sql);

    
    $sql = "SELECT J.JeepID, J.JeepName
    FROM Jeep as J
    INNER JOIN jeeplandmarks as JL ON J.JeepID = JL.JeepID
    WHERE JL.LandmarkID = '$originID'

    INTERSECT

    SELECT J.JeepID, J.JeepName
    FROM Jeep as J
    INNER JOIN jeeplandmarks as JL ON J.JeepID = JL.JeepID
    WHERE JL.LandmarkID = '$dropOffID';";

    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        // echo $row["JeepName"]."<br>";
        $jeepID1 = $row["JeepID"];
    }

    $sqlFare = "SELECT F.FarePerKM, F.MinimumFare
                FROM jeepFare JF
                INNER JOIN Fare F ON JF.FareID = F.FareID
                WHERE JF.JeepID = $jeepID1 AND F.PassengerType = '$PassengerType';
    ";
    $resultFare = $conn->query($sqlFare);
    $rowFare = $resultFare -> fetch_assoc();
    
    if(abs($dropOffDistance - $originDistance)<=4){
        $Fare += $rowFare["MinimumFare"];
    }else{
        $Fare += ((abs($originDistance - $dropOffDistance) - 4) * $rowFare["FarePerKM"]) + $rowFare["MinimumFare"];
    }
    
    $sql = "SELECT J.JeepID, J.JeepName
    FROM Jeep as J
    INNER JOIN JeepLandmarks as JL ON J.JeepID = JL.JeepID
    WHERE JL.LandmarkID = '$dropOffID'

    INTERSECT

    SELECT J.JeepID, J.JeepName
    FROM Jeep as J
    INNER JOIN JeepLandmarks as JL ON J.JeepID = JL.JeepID
    WHERE JL.LandmarkID = '$destinationID';";

    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        // echo $row["JeepName"]."<br>";
        $jeepID2 = $row["JeepID"];
    }

    $sqlFare = "SELECT F.FarePerKM, F.MinimumFare
                FROM jeepFare JF
                INNER JOIN Fare F ON JF.FareID = F.FareID
                WHERE JF.JeepID = $jeepID2 AND F.PassengerType = '$PassengerType';
    ";
    $resultFare = $conn->query($sqlFare);
    $rowFare = $resultFare -> fetch_assoc();
    
    if(abs($destDistance - $dropOffDistance)<=4){
        $Fare += $rowFare["MinimumFare"];
    }else{
        $Fare += ((abs($dropOffDistance - $destDistance) - 4) * $rowFare["FarePerKM"]) + $rowFare["MinimumFare"];
    }

    // $result = $conn->query("SELECT MAX(TripID) AS highestID FROM Trips");
    // $row = $result->fetch_assoc();
    // $lastID = $row['highestID'] + 1;

    $sql = "INSERT INTO Trips(`TripID`,`JeepID1`,`JeepID2`,`OriginID`,`DropOffID`,`DestinationID`, `ACType`, `PassengerType`, `Fare`) VALUES
    (Null,'$jeepID1','$jeepID2','$originID','$dropOffID','$destinationID','$ACType','$PassengerType','$Fare')";
    $conn->query($sql);
    
    header("Location: http://localhost/cmsc127/routeFinder.php");
?>
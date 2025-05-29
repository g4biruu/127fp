<?php
include 'DBConnector.php';
date_default_timezone_set('Asia/Manila');

// Handle delete request / delet elogic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_trip_id'])) {
    $deleteTripID = intval($_POST['delete_trip_id']);

    // First delete from SavedTrips
    $stmtSaved = $conn->prepare("DELETE FROM SavedTrips WHERE TripID = ?");
    $stmtSaved->bind_param("i", $deleteTripID);
    $stmtSaved->execute();
    $stmtSaved->close();

    // Redirect to refresh page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Saved Trips - Sakay na Iloilo!</title>
<link rel="stylesheet" href="style.css">
<style>
    /* Basic improvements for table layout and responsiveness */
    body {
        font-family: Arial, sans-serif;
        background: #f9f9f9;
        margin: 0; padding: 0;
    }
    nav.main-nav {
        background-color: #004d40;
        padding: 0.5rem;
        text-align: center;
    }
    nav.main-nav button {
        background: white;
        border: none;
        color: #004d40;
        padding: 0.5rem 1rem;
        margin: 0 0.3rem;
        cursor: pointer;
        border-radius: 4px;
        font-weight: bold;
    }
    nav.main-nav button:hover {
        background: #60a5fa;
        color: white;
    }
    .table-container {
    max-width: 1200px;  /* increased from 1000px */
    margin: 2rem auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow-x: auto;
    padding: 1rem;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1100px;  /* increased from 800px */
    }
    
    th, td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #ddd;
        text-align: center;
    }
    th {
        background-color: #2563eb; /* blue */
        color: white;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    tr:hover {
        background-color: #e0e7ff;
    }
    .delete-btn {
        background-color: #dc2626; /* red */
        color: white;
        border: none;
        padding: 0.4rem 0.8rem;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background-color 0.3s ease;
    }
    .delete-btn:hover {
        background-color: #b91c1c;
    }
    @media (max-width: 768px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }
        thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        tr {
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 1rem;
        }
        td {
            border: none;
            position: relative;
            padding-left: 50%;
            text-align: left;
        }
        td::before {
            position: absolute;
            top: 0.75rem;
            left: 1rem;
            width: 45%;
            white-space: nowrap;
            font-weight: bold;
            color: #2563eb;
        }
        td[data-label="Trip ID"]::before { content: "Trip ID"; }
        td[data-label="Jeep 1"]::before { content: "Jeep 1"; }
        td[data-label="Jeep 2"]::before { content: "Jeep 2"; }
        td[data-label="Origin"]::before { content: "Origin"; }
        td[data-label="Drop-Off"]::before { content: "Drop-Off"; }
        td[data-label="Destination"]::before { content: "Destination"; }
        td[data-label="AC Type"]::before { content: "AC Type"; }
        td[data-label="Passenger Type"]::before { content: "Passenger Type"; }
        td[data-label="Fare"]::before { content: "Fare"; }
        td[data-label="Date Created"]::before { content: "Date Created"; }
        td[data-label="Time Created"]::before { content: "Time Created"; }
        td[data-label="Action"]::before { content: "Action"; }
    }
</style>
</head>
<body>

<header>
    <div class="logo-title">
        <img src="123.png" alt="Logo" class="logo" />
        <h1>Sakay na Iloilo! - Saved Trips</h1>
    </div>
</header>

<nav class="main-nav">
    <button onclick="location.href='index.php'">Home</button>
    <button onclick="location.href='routeFinder.php'">Route Finder</button>
</nav>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <!-- <th>Trip ID</th> -->
                 <th>Pickup Point</th>
                <th>Jeep 1</th>
                <th>Drop-Off</th>
                <th>Jeep 2</th>
                <th>Destination</th>
                <th>AC Type</th>
                <th>Passenger Type</th>
                <th>Fare (₱)</th>
                <!-- <th>Estimated Time</th> -->
                <th>Date Created</th>
                <th>Time Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM SavedTrips ORDER BY TripID ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($trip = $result->fetch_assoc()):
            ?>
                <tr>
                    <!-- <td data-label="Trip ID"><?= htmlspecialchars($trip['TripID']) ?></td> -->
                     <td data-label="Pickup Point"><?= htmlspecialchars($trip['PickupPoint'] ?: 'N/A') ?></td>
                    <td data-label="Jeep 1"><?= htmlspecialchars($trip['JeepName1'] ?: 'N/A') ?></td>
                    <td data-label="Drop-Off"><?= htmlspecialchars($trip['DropoffPoint'] ?: 'N/A') ?></td>
                    <td data-label="Jeep 2"><?= htmlspecialchars($trip['JeepName2'] ?: 'N/A') ?></td>
                    <td data-label="Destination"><?= htmlspecialchars($trip['DestinationPoint'] ?: 'N/A') ?></td>
                    <td data-label="AC Type"><?= htmlspecialchars($trip['ACType'] ?: 'N/A') ?></td>
                    <td data-label="Passenger Type"><?= htmlspecialchars($trip['PassengerType'] ?: 'N/A') ?></td>
                    <td data-label="Fare (₱)"><?= number_format((float)$trip['Fare'], 2) ?></td>
                    <!-- <td data-label="Estimated Time"><?//= htmlspecialchars($trip['EstimatedTime'] ?: 'N/A') ?></td> -->
                    <td data-label="Date Created"><?= htmlspecialchars($trip['DateCreated'] ?: 'N/A') ?></td>
                    <td data-label="Time Created"><?= htmlspecialchars($trip['TimeCreated'] ?: 'N/A') ?></td>
                    <td data-label="Action">
                        <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete Trip ID <?= htmlspecialchars($trip['TripID']) ?>?');">
                            <input type="hidden" name="delete_trip_id" value="<?= htmlspecialchars($trip['TripID']) ?>" />
                            <button type="submit" class="delete-btn" title="Delete Trip">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php
                endwhile;
            else:
            ?>
                <tr><td colspan="13" style="text-align:center; padding: 1rem;">No trips found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>

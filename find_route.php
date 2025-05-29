<?php
include 'DBConnector.php';

$landmarks = $conn->query("SELECT LandmarkName FROM landmarks ORDER BY LandmarkName ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Route Finder - Sakay na Iloilo!</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <div class="logo-title">
        <img src="logo.png" alt="Logo" class="logo">
        <h1>Sakay na Iloilo!</h1>
    </div>
</header>

<nav class="main-nav">
    <button onclick="location.href='index.php'">Terminal</button>
    <button onclick="location.href='find_route.php'">Route Finder</button>
    <button onclick="location.href='view_archive.php'">View Saved Routes</button>
    <div class="login-link"><a href="admin_login.html">Admin Login</a></div>
</nav>

<!-- FORM SECTION -->
<form action="display_routes.php" method="POST">
    <div class="route-form">
        <label for="from">From:</label>
        <select id="from" name="pickup" required>
            <option value="">Select Origin</option>
            <?php while ($row = $landmarks->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['LandmarkName']) ?>"><?= htmlspecialchars($row['LandmarkName']) ?></option>
            <?php endwhile; ?>
        </select>

        <?php $landmarks->data_seek(0); // rewind for second dropdown ?>

        <label for="to">To:</label>
        <select id="to" name="dropoff" required>
            <option value="">Select Destination</option>
            <?php while ($row = $landmarks->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['LandmarkName']) ?>"><?= htmlspecialchars($row['LandmarkName']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="passengerType">Passenger Type:</label>
        <select id="passengerType" name="passengerType" required>
            <option value="Regular">Regular</option>
            <option value="Student">Student</option>
            <option value="Elderly">Elderly</option>
            <option value="Disabled">Disabled</option>
        </select>

        <button type="submit">Find Route</button>
    </div>
</form>

<!-- MAP SECTION -->
<div class="map-section">
    <h2>Transport Map (Static)</h2>
    <div class="map">
        <div class="map-label" style="top: 30px; left: 20px;">Landmark 1</div>
        <div class="map-label" style="top: 20px; right: 20px;">Terminal 1</div>
        <div class="map-label" style="bottom: 20px; left: 50%;">Terminal 2</div>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>

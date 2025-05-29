<!DOCTYPE html>
<?php
    include 'DBConnector.php';
?>
<html>
<head>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo-title">
        <img src="123.png" alt="Logo" class="logo">
        <h1>Sakay na Iloilo!</h1>
    </div>
</header>

<nav class="main-nav">
    <button onclick="location.href='index.php'">Home</button>
    <button onclick="location.href='routeFinder.php'">Route Finder</button>
    <button onclick="location.href='view_archive.php'">Saved Trips</button>
    <div class="login-link"><a href="admin_login.html">Admin Login</a></div>
</nav>

<form action="singleJeepFinder.php" method="post">
    <select class="expand" name="origin" required>
        <?php
        $landmarks = $conn->query("SELECT LandmarkName FROM Landmarks ORDER BY Distance ASC");
        while ($row = $landmarks->fetch_assoc()){
            echo "<option value='".$row['LandmarkName']."'>".$row['LandmarkName']."</option>";
        }
        ?>
    </select>

    <select class="expand" name="destination" required>
        <?php
        $landmarks = $conn->query("SELECT LandmarkName FROM Landmarks ORDER BY Distance ASC");
        while ($row = $landmarks->fetch_assoc()){
            echo "<option value='".$row['LandmarkName']."'>".$row['LandmarkName']."</option>";
        }
        ?>
    </select>
    
    <br>

    <label for="passengerType">Passenger Type:</label>
    <select id="passengerType" name="passengerType" required>
        <option value="Regular">Regular</option>
        <option value="Student">Student</option>
        <option value="Elderly">Elderly</option>
        <option value="Disabled">Disabled</option>
    </select>
    
    <br>

    <label for="ACType">AC Type:</label>
    <input type="radio" name="ACType" value="AC"> AC
    <input type="radio" name="ACType" value="NonAC"> NonAC <br><br>

    <input type="submit" value="Submit">
</form>

<!-- DISPLAY GOES HERE -->
<div class="results">
    <?php include 'displayTrip.php'; ?>
</div>

</body>
</html>

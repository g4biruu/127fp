<?php
require_once 'DBConnector.php';

$message = "";

// Handle Add
if (isset($_POST['add'])) {
    $passengerType = $_POST['PassengerType'];
    $farePerKM = $_POST['FarePerKM'];
    $minimumFare = $_POST['MinimumFare'];

    $stmt = $conn->prepare("INSERT INTO Fare (PassengerType, FarePerKM, MinimumFare) VALUES (?, ?, ?)");
    $stmt->bind_param("sdd", $passengerType, $farePerKM, $minimumFare);
    $stmt->execute();
    $stmt->close();
    $message = "Fare added successfully!";
}

// Handle Edit
if (isset($_POST['edit'])) {
    $fareID = $_POST['FareID'];
    $passengerType = $_POST['PassengerType'];
    $farePerKM = $_POST['FarePerKM'];
    $minimumFare = $_POST['MinimumFare'];

    $stmt = $conn->prepare("UPDATE Fare SET PassengerType=?, FarePerKM=?, MinimumFare=? WHERE FareID=?");
    $stmt->bind_param("sddi", $passengerType, $farePerKM, $minimumFare, $fareID);
    $stmt->execute();
    $stmt->close();
    $message = "Fare updated successfully!";
}

// Handle Delete
if (isset($_POST['delete'])) {
    $fareID = $_POST['FareID'];

    $conn->query("DELETE FROM jeepFare WHERE FareID=$fareID");
    $conn->query("DELETE FROM Fare WHERE FareID=$fareID");

    $message = "Fare deleted successfully!";
}

$fares = $conn->query("SELECT * FROM Fare ORDER BY FareID ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jeepney Fare Management</title>
    <link href="style.css" rel="stylesheet">
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
</nav>
<div class="container mt-5">
    <h2 class="mb-4 text-primary">🛺 Jeepney Fare Management</h2>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Add Fare</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="PassengerType" class="form-control" placeholder="Passenger Type" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="FarePerKM" step="0.01" class="form-control" placeholder="Fare per KM" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="MinimumFare" step="0.01" class="form-control" placeholder="Minimum Fare" required>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" name="add" class="btn btn-success">Add Fare</button>
                </div>
            </form>
        </div>
    </div>

    <h4 class="mb-3">Current Fares</h4>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Fare ID</th>
                    <th>Passenger Type</th>
                    <th>Fare per KM</th>
                    <th>Minimum Fare</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $fares->fetch_assoc()): ?>
                <tr>
                    <form method="POST" class="row g-1">
                        <td class="col-1"><?= $row['FareID'] ?><input type="hidden" name="FareID" value="<?= $row['FareID'] ?>"></td>
                        <td class="col-3"><input type="text" name="PassengerType" value="<?= $row['PassengerType'] ?>" class="form-control"></td>
                        <td class="col-2"><input type="number" step="0.01" name="FarePerKM" value="<?= $row['FarePerKM'] ?>" class="form-control"></td>
                        <td class="col-2"><input type="number" step="0.01" name="MinimumFare" value="<?= $row['MinimumFare'] ?>" class="form-control"></td>
                        <td class="col-4 text-center">
                            <button type="submit" name="edit" class="btn btn-primary btn-sm me-1">Save</button>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </td>
                    </form>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

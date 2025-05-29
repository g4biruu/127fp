<?php
require_once 'DBConnector.php';

$message = "";

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeepney Fare Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        header {
            background-color: #06b0f0;
            padding: 1rem 2rem;
            color: white;
        }
        .logo {
            height: 50px;
            margin-right: 1rem;
        }
        .logo-title {
            display: flex;
            align-items: center;
        }
        .main-nav {
            background-color: #ffffff;
            padding: 0.75rem 2rem;
            border-bottom: 1px solid #dee2e6;
        }
        .main-nav button {
            margin-right: 10px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<header class="shadow-sm">
    <div class="logo-title">
        <img src="123.png" alt="Logo" class="logo">
        <h1 class="h4 mb-0">Sakay na Iloilo!</h1>
    </div>
</header>

<nav class="main-nav d-flex justify-content-start shadow-sm">
    <button onclick="location.href='index.php'" class="btn btn-outline-primary btn-sm">Home</button>
    <button onclick="location.href='routeFinder.php'" class="btn btn-outline-primary btn-sm">Route Finder</button>
    <button onclick="location.href='view_archive.php'" class="btn btn-outline-primary btn-sm">Saved Trips</button>
</nav>

<div class="container my-5">
    <h2 class="mb-4 text-primary">🛺 Jeepney Fare Management</h2>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Current Fares</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Fare ID</th>
                            <th scope="col">Passenger Type</th>
                            <th scope="col">Fare per KM</th>
                            <th scope="col">Minimum Fare</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $fares->fetch_assoc()): ?>
                        <tr>
                            <form method="POST" class="d-flex gap-2 flex-wrap">
                                <td><?= $row['FareID'] ?><input type="hidden" name="FareID" value="<?= $row['FareID'] ?>"></td>
                                <td><input type="text" name="PassengerType" value="<?= $row['PassengerType'] ?>" class="form-control form-control-sm"></td>
                                <td><input type="number" step="0.01" name="FarePerKM" value="<?= $row['FarePerKM'] ?>" class="form-control form-control-sm"></td>
                                <td><input type="number" step="0.01" name="MinimumFare" value="<?= $row['MinimumFare'] ?>" class="form-control form-control-sm"></td>
                                <td class="text-center">
                                    <button type="submit" name="edit" class="btn btn-sm btn-primary me-1">Save</button>
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
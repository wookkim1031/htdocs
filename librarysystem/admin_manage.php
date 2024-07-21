<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

$errors = [];
$messages = [];

if(isset($_POST['add_status'])) {
    $status = $_POST['status'];
    if (!empty($status)) {
        $stmt = $mysqli->prepare("INSERT INTO status (status) VALUES (?)");
        $stmt->bind_param("s", $status);
        if ($stmt->execute()) {
            $messages[] = "Status added successfully.";
        } else {
            $errors[] = "Failed to add status: " . $mysqli->error;
        }
        $stmt->close();
    } else {
        $errors[] = "Status cannot be empty.";
    }
}


// Handle deleting status
if (isset($_POST['delete_status'])) {
    $status_id = $_POST['status_id'];
    $stmt = $mysqli->prepare("DELETE FROM status WHERE id = ?");
    $stmt->bind_param("i", $status_id);
    if ($stmt->execute()) {
        $messages[] = "Status deleted successfully.";
    } else {
        $errors[] = "Failed to delete status: " . $mysqli->error;
    }
    $stmt->close();
}

// Handle adding location
if (isset($_POST['add_location'])) {
    $name = $_POST['name'];
    $room = $_POST['room'];
    if (!empty($name) && !empty($room)) {
        $stmt = $mysqli->prepare("INSERT INTO location (name, room) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $room);
        if ($stmt->execute()) {
            $messages[] = "Location added successfully.";
        } else {
            $errors[] = "Failed to add location: " . $mysqli->error;
        }
        $stmt->close();
    } else {
        $errors[] = "Name and Room cannot be empty.";
    }
}

// Handle deleting location
if (isset($_POST['delete_location'])) {
    $location_id = $_POST['location_id'];
    $stmt = $mysqli->prepare("DELETE FROM location WHERE id = ?");
    $stmt->bind_param("i", $location_id);
    if ($stmt->execute()) {
        $messages[] = "Location deleted successfully.";
    } else {
        $errors[] = "Failed to delete location: " . $mysqli->error;
    }
    $stmt->close();
}

// Fetch all statuses
$statuses = [];
$status_result = $mysqli->query("SELECT id, status FROM status");
if ($status_result) {
    $statuses = $status_result->fetch_all(MYSQLI_ASSOC);
}

// Fetch all locations
$locations = [];
$location_result = $mysqli->query("SELECT id, name, room FROM location");
if ($location_result) {
    $locations = $location_result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Management</title>
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital@1&family=PT+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style/admin_manage.css">
    <link rel="stylesheet" type="text/css" href="style/footnotes.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
    <h1>Datenbank Management</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($messages)): ?>
        <div class="messages">
            <?php foreach ($messages as $message): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="flex-container">
        <div class="status-container">
            <h2>Manage Status</h2>
            <form action="admin_manage.php" method="post">
                <label for="status">Add Status:</label>
                <input type="text" id="status" name="status">
                <button type="submit" name="add_status">Add</button>
            </form>

            <h3>Existing Statuses</h3>
            <ul>
                <?php foreach ($statuses as $status): ?>
                    <li>
                        <?php echo htmlspecialchars($status['status']); ?>
                        <form action="admin_manage.php" method="post" style="display:inline;">
                            <input type="hidden" name="status_id" value="<?php echo $status['id']; ?>">
                            <button type="submit" name="delete_status">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="location-container">
            <h2>Manage Locations</h2>
            <form action="admin_manage.php" method="post">
                <label for="name">Location Name:</label>
                <input type="text" id="name" name="name">
                <label for="room">Room:</label>
                <input type="text" id="room" name="room">
                <button type="submit" name="add_location">Add</button>
            </form>

            <h3>Location Name, Location Room</h3>
            <ul>
                <?php foreach ($locations as $location): ?>
                    <li>
                        <?php echo htmlspecialchars($location['name']) . " - Room: " . htmlspecialchars($location['room']); ?>
                        <form action="admin_manage.php" method="post" style="display:inline;">
                            <input type="hidden" name="location_id" value="<?php echo $location['id']; ?>">
                            <button type="submit" name="delete_location">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

</body>
</html>
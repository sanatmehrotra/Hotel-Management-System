<html>
<?php
$conn = new mysqli("localhost", "root", "", "iwp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$room = $_POST["rooms"];
$checkin = $_POST["checkin"];
$checkout = $_POST["checkout"];

// ✅ Prevent past dates
$today = date('Y-m-d');
if ($checkin < $today || $checkout < $today) {
    die("❌ Error: Check-in and check-out must be today or in the future.");
}

// ✅ Check if checkout is after checkin
if (strtotime($checkout) <= strtotime($checkin)) {
    die("❌ Error: Check-out date must be after check-in date.");
}

$ac = isset($_POST["ac"]) ? "true" : "false";
$breakfast = isset($_POST["breakfast"]) ? "true" : "false";
$lunch = isset($_POST["lunch"]) ? "true" : "false";
$snacks = isset($_POST["snacks"]) ? "true" : "false";
$dinner = isset($_POST["dinner"]) ? "true" : "false";
$swimming = isset($_POST["swimming"]) ? "true" : "false";

// ✅ Calculate total number of days
$in = strtotime($checkin);
$out = strtotime($checkout);
$diff = abs($out - $in); 
$days = floor($diff / (60 * 60 * 24));  // directly use total days

// ✅ Retrieve session data
$sql = "SELECT * FROM temp_session";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_row($result);
$phone = $row[0];
$name = $row[2];
$id = $row[4];
$status = "Waiting";

// ✅ Base price by room type
switch ($room) {
    case "Single bed":
        $price = 1000;
        break;
    case "Double bed":
        $price = 1800;
        break;
    case "Four bed":
        $price = 3000;
        break;
    default:
        $price = 0;
}

// ✅ Add-on pricing
$additional = 0;
if ($ac == "true") $additional += 300;
if ($breakfast == "true") $additional += 150;
if ($lunch == "true") $additional += 300;
if ($snacks == "true") $additional += 120;
if ($dinner == "true") $additional += 250;
if ($swimming == "true") $additional += 300;

// ✅ Calculate total price
$price = ($price + $additional) * $days;

// ✅ Get and update booking ID
$sqlt = "SELECT * from book_id";
$result = mysqli_query($conn, $sqlt);
$row = mysqli_fetch_row($result);
$t = $row[0];

$sql = "INSERT INTO user_room_book 
        VALUES ('$phone', '$name', '$id', '$room', '$checkin', '$checkout', '$days', 
        '$ac', '$breakfast', '$lunch', '$snacks', '$dinner', '$swimming', 
        '$status', '$price', '$t')";
mysqli_query($conn, $sql);

$t = $t + 1;
mysqli_query($conn, "DELETE from book_id");
mysqli_query($conn, "INSERT INTO book_id VALUES ('$t')");

header("Location: bookroom2.php");
?>
</html>

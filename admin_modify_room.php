<html>
<?php
$conn = new mysqli("localhost", "root", "", "iwp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$bid = $_POST["book_id"];
$checkout = $_POST["checkout"];

// Get today’s date
$today = date("Y-m-d");

// ✅ Block past checkout dates
if ($checkout < $today) {
    die("❌ Error: Checkout date cannot be in the past.");
}

// Get existing booking info
$sql = "SELECT * FROM confirmed_booking WHERE book_id='$bid'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("❌ Error: Booking ID not found.");
}

$row = mysqli_fetch_row($result);
$checkin = $row[4]; // Assuming index 4 = checkin
$prev_days = $row[6]; // Assuming index 6 = previous days
$old_price = $row[13]; // Assuming index 13 = total price

// ✅ Block if checkout is before or same as checkin
if (strtotime($checkout) <= strtotime($checkin)) {
    die("❌ Error: Checkout must be after check-in.");
}

// ✅ Calculate new duration
$in = strtotime($checkin);
$out = strtotime($checkout);
$days = floor(($out - $in) / (60 * 60 * 24));

// ✅ Room type and base price
$room_type = $row[3]; // Assuming index 3 = room type
switch ($room_type) {
    case "Single bed":
        $base_price = 1000;
        break;
    case "Double bed":
        $base_price = 1800;
        break;
    case "Four bed":
        $base_price = 3000;
        break;
    default:
        $base_price = 0;
}

// ✅ Adjust price based on new days
$new_price = $old_price - ($base_price * $prev_days) + ($base_price * $days);

// ✅ Update the booking record
$sql = "UPDATE confirmed_booking 
        SET checkout='$checkout', days='$days', price='$new_price' 
        WHERE book_id='$bid'";
mysqli_query($conn, $sql);

// ✅ Redirect to admin page
header("Location: admin_modify_room1.php");
?>
</html>

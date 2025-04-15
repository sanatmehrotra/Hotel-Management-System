<html>
<?php
$conn = new mysqli("localhost", "root", "", "iwp");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$num = $_POST["noofrooms"];
$r = $_POST["rooms"];

// ✅ Basic validation
if (!is_numeric($num) || $num <= 0) {
    die("❌ Invalid number of rooms. Please enter a positive value.");
}

// ✅ Check if room type exists
$check_sql = "SELECT * FROM rooms_count WHERE room_type='$r'";
$result = mysqli_query($conn, $check_sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die("❌ Error: Room type '$r' does not exist.");
}

// ✅ Update room count
$update_sql = "UPDATE rooms_count SET available_rooms = available_rooms + $num WHERE room_type='$r'";
if (mysqli_query($conn, $update_sql)) {
    header("Location: admin_room_added1.php");
    exit;
} else {
    echo "❌ Error updating room count: " . mysqli_error($conn);
}
?>
</html>

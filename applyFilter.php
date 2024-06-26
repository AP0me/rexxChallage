<?php
include("db_connect.php");

function validateDate($date, $format = 'Y-m-d') {
  $d = DateTime::createFromFormat($format, $date);
  return $d && $d->format($format) === $date;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve and sanitize POST data
  $employeeName = $_POST['employee_name'] ?? '';
  $eventName = $_POST['event_name'] ?? '';
  $dateRange = $_POST['date_range'] ?? '';
  
  // Split date range into start and end dates
  $dates = explode(" to ", $dateRange);
  $startDate = $dates[0] ?? '';
  $endDate = $dates[1] ?? '';
  if(!validateDate($startDate, 'Y-m-d') || !validateDate($endDate, 'Y-m-d')){
    $startDate = ''; $endDate = '';
  }
  
  // Prepare SQL query
  $query = "
    SELECT event.name as event_name, employee.name as employee_name, fee_cents, mail, date 
    FROM event 
    JOIN ticket ON event.event_id = ticket.event_id
    JOIN employee ON ticket.emp_id = employee.emp_id 
    WHERE event.name LIKE ? AND employee.name LIKE ? AND date BETWEEN ? AND ?;
  ";

  // Execute the prepared statement
  if ($stmt = $conn->prepare($query)) {
    $likeEmployeeName = "%$employeeName%";
    $likeEventName = "%$eventName%";
    $stmt->bind_param('ssss', $likeEventName, $likeEmployeeName, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $results = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    include('column_names.php');
    // Loop through the results and include filterRow.php for each row
    foreach ($results as $index => $row) {
      $_GET["index"] = $index;
      $_GET["name"] = $row["employee_name"];
      $_GET["event_name"] = $row["event_name"];
      $_GET["mail"] = $row["mail"];
      $_GET["fee"] = $row["fee_cents"];
      $_GET["date"] = $row["date"];
      include("filterRow.php");
    }
  } else {
    // Handle query preparation error
    echo "Error preparing statement: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8');
  }
}
$conn->close();

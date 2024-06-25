<?php
include("db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve the query parameters from the request
  $employeeName = $_POST['employee_name'] ?? '';
  $eventName = $_POST['event_name'] ?? '';
  $startDate = $_POST['start_date'] ?? '';

  $query = "
    SELECT employee.name AS employee_name, event.name AS event_name, event.fee_cents AS fee, event.date
    FROM employee
    JOIN event ON employee.event_id = event.event_id
    WHERE employee.name LIKE ? AND event.name LIKE ? AND event.date BETWEEN ? AND NOW();
  ";

  // Prepare the statement
  if ($stmt = $conn->prepare($query)) {
    // Bind parameters
    $likeEmployeeName = "%$employeeName%";
    $likeEventName = "%$eventName%";
    $stmt->bind_param('sss', $likeEmployeeName, $likeEventName, $startDate);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch all results
    $results = $result->fetch_all(MYSQLI_ASSOC);

    // Close statement
    $stmt->close();
    
    
    for ($i=0; $i < count($results); $i++) {
      $_GET["index"] = $i;
      $_GET["name"] = $results[$i]["employee_name"];
      $_GET["event_name"] = $results[$i]["event_name"];
      $_GET["fee"] = $results[$i]["fee"];
      $_GET["date"] = $results[$i]["date"];
      include("filterRow.php");
    }
    
  } else {
    echo "Error preparing statement: " . $conn->error;
  }
}

// Close connection
$conn->close();


<?php
include("db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve the query parameters from the request
  $employeeName = $_POST['employee_name'] ?? '';
  $eventName = $_POST['event_name'] ?? '';
  $startDate = $_POST['start_date'] ?? '';

  $query = "
    SELECT event.name as event_name, employee.name as employee_name, fee_cents, mail, date 
    FROM event 
    JOIN ticket ON event.event_id = ticket.event_id
    JOIN employee ON ticket.emp_id = employee.emp_id WHERE event.name LIKE ? and employee.name LIKE ? and date BETWEEN ? AND NOW(); 
  ";

  // Prepare the statement
  if ($stmt = $conn->prepare($query)) {
    // Bind parameters
    $likeEmployeeName = "%$employeeName%";
    $likeEventName = "%$eventName%";
    $stmt->bind_param('sss', $likeEventName, $likeEmployeeName, $startDate);

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
      $_GET["mail"] = $results[$i]["mail"];
      $_GET["fee"] = $results[$i]["fee_cents"];
      $_GET["date"] = $results[$i]["date"];
      include("filterRow.php");
    }
    
  } else {
    echo "Error preparing statement: " . $conn->error;
  }
}

// Close connection
$conn->close();


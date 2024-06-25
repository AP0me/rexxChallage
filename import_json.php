<?php
include("db_connect.php");

// Read and decode JSON file
$jsonData = file_get_contents('data.json');
$data = json_decode($jsonData, true);

// Prepare insert statements
$insertEvent = $conn->prepare("INSERT INTO `event` (`name`, `fee_cents`, `date`) VALUES (?, ?, ?)");
$insertEmployee = $conn->prepare("INSERT INTO employee (`name`, `mail`) VALUES (?, ?)");
$insertTicket = $conn->prepare("INSERT INTO ticket (emp_id, event_id) VALUES (
  (SELECT emp_id FROM employee WHERE `mail`=?),
  (SELECT event_id FROM `event` WHERE `name`=?));");

// Insert data into tables
foreach ($data as $item) {
  $participationFeeCents = (int)($item['participation_fee'] * 100);
  $eventDate = isset($item['event_date']) ? $item['event_date'] : NULL;

  // Check if the event already exists and get the event_id
  $eventStmt = $conn->prepare("SELECT count(event_id) FROM `event` WHERE `name` = ?");
  $eventStmt->bind_param("s", $item['event_name']);
  $eventStmt->execute();
  $eventStmt->bind_result($event_count);
  $eventStmt->fetch();
  $eventStmt->close();

  // Insert into events table if it doesn't exist
  echo $event_count.'<br>';
  if ($event_count==0) {
    $insertEvent->bind_param("sis", $item['event_name'], $participationFeeCents, $eventDate);
    $insertEvent->execute();
    $event_id = $insertEvent->insert_id;
  }

  // Check if the employee already exists and get the employee_id
  $employeeStmt = $conn->prepare("SELECT count(emp_id) FROM employee WHERE mail = ?");
  $employeeStmt->bind_param("s", $item['employee_mail']);
  $employeeStmt->execute();
  $employeeStmt->bind_result($employee_count);
  $employeeStmt->fetch();
  $employeeStmt->close();

  // Insert into employees table if they don't exist
  if ($employee_count==0) {
    $insertEmployee->bind_param("ss", $item['employee_name'], $item['employee_mail']);
    $insertEmployee->execute();
    $employee_id = $insertEmployee->insert_id;
  }

  // Insert into tickets table
  $insertTicket->bind_param("ss", $item['employee_mail'], $item['event_name']);
  $insertTicket->execute();
}

echo "Data imported successfully.";

$conn->close();
header('Location: upload.php');
exit();


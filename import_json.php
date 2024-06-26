<?php
include("db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_FILES['jsonDataFile']) && $_FILES['jsonDataFile']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['jsonDataFile']['tmp_name'];
    $fileName = $_FILES['jsonDataFile']['name'];
    $fileSize = $_FILES['jsonDataFile']['size'];
    $fileType = $_FILES['jsonDataFile']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = array('json');
    if (in_array($fileExtension, $allowedfileExtensions)) {
      $jsonData = file_get_contents($fileTmpPath);
      $data = json_decode($jsonData, true);

      $insertEvent = $conn->prepare("INSERT INTO `event` (`name`, `fee_cents`, `date`) VALUES (?, ?, ?)");
      $insertEmployee = $conn->prepare("INSERT INTO employee (`name`, `mail`) VALUES (?, ?)");
      $insertTicket = $conn->prepare("INSERT INTO ticket (emp_id, event_id) VALUES (
        (SELECT emp_id FROM employee WHERE `mail`=?),
        (SELECT event_id FROM `event` WHERE `name`=?));");

      foreach ($data as $item) {
        $participationFeeCents = (int)($item['participation_fee'] * 100);
        $eventDate = isset($item['event_date']) ? $item['event_date'] : NULL;

        $eventStmt = $conn->prepare("SELECT count(event_id) FROM `event` WHERE `name` = ?");
        $eventStmt->bind_param("s", $item['event_name']);
        $eventStmt->execute();
        $eventStmt->bind_result($event_count);
        $eventStmt->fetch();
        $eventStmt->close();

        if ($event_count == 0) {
          $insertEvent->bind_param("sis", $item['event_name'], $participationFeeCents, $eventDate);
          $insertEvent->execute();
        }

        $employeeStmt = $conn->prepare("SELECT count(emp_id) FROM employee WHERE mail = ?");
        $employeeStmt->bind_param("s", $item['employee_mail']);
        $employeeStmt->execute();
        $employeeStmt->bind_result($employee_count);
        $employeeStmt->fetch();
        $employeeStmt->close();

        if ($employee_count == 0) {
          $insertEmployee->bind_param("ss", $item['employee_name'], $item['employee_mail']);
          $insertEmployee->execute();
        }

        $ticketStmt = $conn->prepare("SELECT count(ticket_id) FROM ticket WHERE emp_id = (SELECT emp_id FROM employee WHERE `mail`=?) AND event_id = (SELECT event_id FROM `event` WHERE `name`=?)");
        $ticketStmt->bind_param("ss", $item['employee_mail'], $item['event_name']);
        $ticketStmt->execute();
        $ticketStmt->bind_result($ticket_count);
        $ticketStmt->fetch();
        $ticketStmt->close();
        
        if ($ticket_count == 0) {
          $insertTicket->bind_param("ss", $item['employee_mail'], $item['event_name']);
          $insertTicket->execute();
        }
      }
      echo "Data imported successfully.";
    }
    else {
      echo "Upload failed. Allowed file types: .json";
    }
  }
  else {
    echo "There was an error uploading the file.";
  }
}

$conn->close();
header('Location: upload.php');
exit();

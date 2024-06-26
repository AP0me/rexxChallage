<?php
include("db_connect.php");

function zeroCopies($checkforCopyStmt){
  $copy_count = null;
  $checkforCopyStmt->execute();
  $checkforCopyStmt->bind_result($copy_count);
  $checkforCopyStmt->fetch();
  $checkforCopyStmt->close();
  return $copy_count == 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_FILES['jsonDataFile']) && $_FILES['jsonDataFile']['error'] === UPLOAD_ERR_OK) {
    
    $fileNameCmps = explode(".", $_FILES['jsonDataFile']['name']);
    $fileExtension = strtolower(end($fileNameCmps));
    $allowedfileExtensions = array('json');
    if (in_array($fileExtension, $allowedfileExtensions)) {
      $jsonData = file_get_contents($_FILES['jsonDataFile']['tmp_name']);
      $data = json_decode($jsonData, true);

      $conn->begin_transaction();
      try {
        $insertEvent = $conn->prepare("INSERT INTO `event` (`name`, `fee_cents`, `date`) VALUES (?, ?, ?)");
        $insertEmployee = $conn->prepare("INSERT INTO employee (`name`, `mail`) VALUES (?, ?)");
        $insertTicket = $conn->prepare("INSERT INTO ticket (emp_id, event_id) VALUES ((SELECT emp_id FROM employee WHERE `mail`=?), (SELECT event_id FROM `event` WHERE `name`=?));");

        foreach ($data as $item) {
          $participationFeeCents = (int)($item['participation_fee'] * 100);
          $eventDate = isset($item['event_date']) ? $item['event_date'] : NULL;

          $eventStmt = $conn->prepare("SELECT count(event_id) FROM `event` WHERE `name` = ?");
          $eventStmt->bind_param("s", $item['event_name']);
          if (zeroCopies($eventStmt)) {
            $insertEvent->bind_param("sis", $item['event_name'], $participationFeeCents, $eventDate);
            $insertEvent->execute();
          }

          $employeeStmt = $conn->prepare("SELECT count(emp_id) FROM employee WHERE mail = ?");
          $employeeStmt->bind_param("s", $item['employee_mail']);
          if (zeroCopies($employeeStmt)) {
            $insertEmployee->bind_param("ss", $item['employee_name'], $item['employee_mail']);
            $insertEmployee->execute();
          }

          $ticketStmt = $conn->prepare("SELECT count(ticket_id) FROM ticket WHERE emp_id = (SELECT emp_id FROM employee WHERE `mail`=?) AND event_id = (SELECT event_id FROM `event` WHERE `name`=?)");
          $ticketStmt->bind_param("ss", $item['employee_mail'], $item['event_name']);        
          if (zeroCopies($ticketStmt)) {
            $insertTicket->bind_param("ss", $item['employee_mail'], $item['event_name']);
            $insertTicket->execute();
          }
        }

        $conn->commit();
        echo "Data imported successfully.";
        $conn->close();
        header('Location: upload.php?upload=success');
        exit();
      } catch (Exception $e) {
        $conn->rollback();
        echo "Error importing data: " . $e->getMessage();
        $conn->close();
        header('Location: upload.php?upload=error');
        exit();
      }
    }
    else {
      echo "Upload failed. Allowed file types: .json";
      $conn->close();
      header('Location: upload.php?upload=error');
      exit();
    }
  }
  else {
    echo "There was an error uploading the file.";
    $conn->close();
    header('Location: upload.php?upload=error');
    exit();
  }
}

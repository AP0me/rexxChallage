<?php
include("db_connect.php");
$newURL = "upload.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
  $file = $_FILES['file'];

  if ($file['error'] === UPLOAD_ERR_OK) {
    $jsonData = file_get_contents($file['tmp_name']);
    $data = json_decode($jsonData, true);

    $insertEvent = $conn->prepare("INSERT INTO `event` (`name`, `date`, `fee_cents`) VALUES (?, ?, ?)");
    $insertEmployee = $conn->prepare("INSERT INTO `employee` (`name`, `mail`, `event_id`) VALUES (?, ?, ?)");

    foreach ($data as $item) {
      $participationFeeCents = (int)($item['participation_fee'] * 100);
      $eventDate = isset($item['event_date']) ? $item['event_date'] : NULL;

      $insertEvent->bind_param('ssi', $item['event_name'], $eventDate, $participationFeeCents);
      $insertEvent->execute();

      $eventId = $conn->insert_id;
      $insertEmployee->bind_param('ssi', $item['employee_name'], $item['employee_mail'], $eventId);
      $insertEmployee->execute();
    }

    echo "Data imported successfully.";
    header('Location: '.$newURL);
    exit();
  } else {
    echo "Error uploading file: " . $file['error'];
    header('Location: '.$newURL);
    exit();
  }
} else {
  echo "No file uploaded or invalid request method.";
  header('Location: '.$newURL);
  exit();
}
?>

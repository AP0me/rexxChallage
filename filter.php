<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Filter Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f4f4f4;
    }
    .filter-form {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr;
      background: #fff;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      gap: 15px;
    }
    .filter-form div {
      margin-bottom: 10px;
    }
    .filter-form label {
      margin-right: 10px;
    }
    .filter-form input[type="text"],
    .filter-form input[type="date"] {
      padding: 8px;
      width: calc(100% - 20px);
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .filter-form button {
      width: 100%;
      height: 33px;
      padding: 10px 20px;
      border: none;
      background: #007BFF;
      color: #fff;
      border-radius: 4px;
      cursor: pointer;
      justify-self: center;
      align-self: center;
    }
    .filter-form button:hover {
      background: #0056b3;
    }
    .display-result {
      display: grid;
      max-height: calc(100vh - 260px);
      overflow-y: auto;
      grid-template-columns: 80px auto auto auto min-content;
      gap: 10px;
      background: #fff;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .display-result .event-row {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      background: #f9f9f9;
    }
    .display-result .event-index {
      text-align: center;
      font-weight: bold;
    }
    .display-result .event-emp-name,
    .display-result .event-name,
    .display-result .event-date,
    .display-result .apply-filter {
      padding-left: 10px;
    }

    .filter-segmenter{
      display: grid;
      grid-template-rows: min-content min-content auto;
      max-height: calc(100vh - 40px);
    }
    .break17{
      height: 7px;
    }
  </style>
</head>
<body>
  <div class="filter-segmenter">
    <?php include("navbar.php") ?>
    <div class="filter-form">
      <div class="employee-name">
        <label for="employeeName">Employee Name:</label>
        <input type="text" id="employeeName" name="employeeName" placeholder="Name Surname">
      </div>
      <div class="event-name">
        <label for="eventName">Event Name:</label>
        <input type="text" id="eventName" name="eventName" placeholder="Event">
      </div>
      <div class="date">
        <label for="eventDate">Event Date:</label>
        <input type="date" id="eventDate" name="eventDate">
      </div>
      <div class="apply-filter">
        <div class="break17"></div>
        <button id="applyFilter" name="applyFilter" onclick="applyFilters()">Apply</button>
      </div>
    </div>
    <div class="display-result"></div>
  </div>

  <script defer="true">
    function applyFilters() {
      let employeeName = document.querySelector('#employeeName').value;
      let eventName = document.querySelector('#eventName').value;
      let eventDate = document.querySelector('#eventDate').value;
      let displayResult = document.querySelector('.display-result');

      let formData = new FormData();
      formData.append('employee_name', employeeName);
      formData.append('event_name', eventName);
      formData.append('start_date', eventDate);

      fetch('applyFilter.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        let titleAndData = `
        <div class="event-row event-index">Index</div>
        <div class="event-row event-emp-name">Employee</div>
        <div class="event-row event-name">Event</div>
        <div class="event-row event-fee">Fee</div>
        <div class="event-row event-date">Date</div>` + data;
        if(displayResult.innerHTML.length!==titleAndData.length){
          displayResult.innerHTML=titleAndData;
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }

    applyFilters();
  </script>
</body>
</html>

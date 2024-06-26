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
      background: #fff;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      grid-template-columns: 1fr 1fr 1fr 1fr;
      gap: 15px;
    }
    @media (max-width:560px) { 
      .filter-form {
        grid-template-columns: 1fr;
        grid-template-rows: min-content min-content min-content min-content;
        gap: unset;
      }
    }
    .filter-form label {
      margin-right: 10px;
      white-space: nowrap;
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
      max-height: 100%;
      height: fit-content;
      overflow-y: auto;
      grid-template-columns: 80px auto auto auto auto min-content;
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
      white-space: nowrap;
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

    .title-row{
      grid-column: 1 / span 4;
    }

    .filter-segmenter{
      display: grid;
      grid-template-rows: min-content calc(100vh - 248px);
      max-height: calc(100vh - 40px);
    }
    @media (max-width:560px) { 
      .filter-segmenter{
        grid-template-rows: min-content calc(100vh - 402px);
      }
    }
    .break17{
      height: 17px;
    }
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
  <?php include("navbar.php") ?>
  <div class="filter-segmenter">
    <div class="filter-form">
      <div class="employee-name">
        <label for="employeeName">Employee Name:</label>
        <input type="text" id="employeeName" name="employeeName" placeholder="Name Surname">
      </div>
      <div class="event-name">
        <label for="eventName">Event Name:</label>
        <input type="text" id="eventName" name="eventName" placeholder="Event">
      </div>
      <div class="container">
        <label for="dateRange">Stay duration</label>
        <input type="text" id="dateRange" name="dateRange" class="max-w-xs" />
      </div>
      <div class="apply-filter">
        <div class="break17"></div>
        <button id="applyFilter" name="applyFilter" onclick="applyFilters()">Apply</button>
      </div>
    </div>
    <div class="display-result"></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dateRangePickerElement = document.getElementById('dateRange');
      const earliestDate = new Date(0);
      const todayDate = new Date();

      flatpickr(dateRangePickerElement, {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: [earliestDate, todayDate],
        onChange: function(selectedDates, dateStr, instance) {
          console.log("Selected dates: " + dateStr);
        }
      });

      applyFilters();
    });

    function applyFilters() {
      let employeeName = document.querySelector('#employeeName').value;
      let eventName = document.querySelector('#eventName').value;
      let eventDate = document.querySelector('#dateRange').value;
      let displayResult = document.querySelector('.display-result');

      let formData = new FormData();
      formData.append('employee_name', employeeName);
      formData.append('event_name', eventName);
      formData.append('date_range', eventDate);

      fetch('applyFilter.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(titleAndData => {
        if(displayResult.innerHTML.length !== titleAndData.length){
          displayResult.innerHTML = titleAndData;
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }
  </script>
</body>
</html>

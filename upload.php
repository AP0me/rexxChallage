<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload JSON File</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f4f4f4;
    }
    h1 {
      color: #333;
    }
    form {
      background: #fff;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
    }
    input[type="file"] {
      margin-bottom: 20px;
    }
    button {
      padding: 10px 20px;
      border: none;
      background: #007BFF;
      color: #fff;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <?php include("navbar.php") ?>
  <h1>Upload JSON File</h1>
  <form action="import_json.php" method="post" enctype="multipart/form-data">
    <label for="jsonDataFile">Choose JSON file to upload:</label>
    <input type="file" name="jsonDataFile" id="file" accept=".json" required>
    <input type="submit" value="Upload">
  </form>
</body>
</html>

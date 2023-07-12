<?php
// Define path to CSV file
$csv_path = "dummy.csv";

// Define which columns to exclude from the search results
$exclude_columns = array(array_search('url', fgetcsv(fopen($csv_path, 'r'))), array_search('hide2', fgetcsv(fopen($csv_path, 'r'))) !== false ? array_search('hide2', fgetcsv(fopen($csv_path, 'r'))) : null, array_search('hide3', fgetcsv(fopen($csv_path, 'r'))) !== false ? array_search('hide3', fgetcsv(fopen($csv_path, 'r'))) : null); // Excludes 'url', 'hide2', and 'hide3' columns if they exist in the CSV file

// Check if search has been submitted
if (isset($_GET['search'])) {
  // Get search query
  $search_query = $_GET['search'];

  // Open CSV file and read data
  $csv_file = fopen($csv_path, 'r');
  $csv_data = array();
  while (($row = fgetcsv($csv_file)) !== false) {
    $csv_data[] = $row;
  }
  fclose($csv_file);

  // Get header row from CSV data
  $header_row = $csv_data[0];

  // Remove header row from CSV data
  $csv_data = array_slice($csv_data, 1);

  // Search CSV data for matching rows
  $matching_rows = array();
  foreach ($csv_data as $row) {
    foreach ($row as $index => $value) {
      if (!in_array($index, $exclude_columns) && stripos($value, $search_query) !== false) {
        $matching_rows[] = $row;
        break;
      }
    }
  }

  // Display matching rows with header row
  echo "<h2 style='font-family: \"Open Sans\", sans-serif;'>Search Results</h2>";
  if (count($matching_rows) > 0) {
    echo "<table style='font-family: \"Roboto\", sans-serif;'>";
    echo "<tr style='background-color: #eee;'>";
    foreach ($header_row as $index => $value) {
      if ($index == array_search('url', $header_row)) {
        // Skip 'url' column
        continue;
      } else if ($index == 0) {
        // Include 'provider' column
        echo "<th style='padding: 10px;'>" . htmlspecialchars($value) . "</th>";
      } else if (!in_array($index, $exclude_columns)) {
        // Include other columns
        echo "<th style='padding: 10px;'>" . htmlspecialchars($value) . "</th>";
      }
    }
    echo "<th></th>"; // Include empty header for button column
    echo "</tr>";
    foreach ($matching_rows as $row) {
      echo "<tr>";
      foreach ($row as $index => $value) {
        if ($index == array_search('url', $header_row)) {
          // Skip 'url' column
          continue;
        } else if ($index == 0) {
          // Include 'provider' column
          echo "<td style='padding: 10px;'>" . htmlspecialchars($value) . "</td>";
        } else if (!in_array($index, $exclude_columns)) {
          // Include other columns
          echo "<td style='padding: 10px;'>" . htmlspecialchars($value) . "</td>";
        }
      }
      echo "<td><a href='" . htmlspecialchars($row[array_search('url', $header_row)]) . "' target='_blank'><button style='background-color: #007bff; color: #fff; border: none; border-radius: 5px; padding: 10px;'>Go to Website</button></a></td>";
echo "</tr>";
}
echo "</table>";
} else {
echo "<p>No matching results found.</p>";
}
}

// Display search form
echo "<form method='get'>";
echo "<label for='search'>Search:</label>";
echo "<input type='text' name='search' id='search'>";
echo "<input type='submit' value='Search' style='background-color: #007bff; color: #fff; border: none; border-radius: 5px; padding: 10px;'>";
echo "</form>";
?>
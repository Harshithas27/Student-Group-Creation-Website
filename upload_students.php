<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$servername = "localhost";
$username = "root";  // Change this to your MySQL username
$password = "";  // Change this to your MySQL password
$dbname = "user_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$spreadsheet = IOFactory::load('students.xlsx');
$worksheet = $spreadsheet->getActiveSheet();

foreach ($worksheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);

    $data = [];
    foreach ($cellIterator as $cell) {
        $data[] = $cell->getValue();
    }

    $name = $data[0];
    $usn = $data[1];
    $email = $data[2];
    $section = $data[3];

    $stmt = $conn->prepare("INSERT INTO students (name, usn, email, section) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $usn, $email, $section);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>

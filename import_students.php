<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$servername = "localhost";
$username = "root";  // Change this to your MySQL username
$password = "Harsh123";  // Change this to your MySQL password
$dbname = "registration";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$spreadsheet = IOFactory::load('all students.csv');
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

    $stmt = $conn->prepare("INSERT INTO students (ID,Enrollmentno,Studentname,Divisioncode,EmailID) VALUES (?, ?, ?, ?,?)");
    $stmt->bind_param("sssss", $id,$enrollmentno,$studentname,$divisioncode,$emailID);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>

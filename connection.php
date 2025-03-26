<?php
$serverName = "localhost";
$userName = "root";
$password = "";

// Establish database connection
$conn = mysqli_connect($serverName, $userName, $password);
if (!$conn) {
    die("Failed to connect: " . mysqli_connect_error());
}

$createDatabase = "CREATE DATABASE IF NOT EXISTS prototype3";
if (!mysqli_query($conn, $createDatabase)) {
    die("Failed to create database: " . mysqli_error($conn));
}


mysqli_select_db($conn, 'prototype3');


$createTable = "CREATE TABLE IF NOT EXISTS weather (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(255) NOT NULL,
    temperature FLOAT NOT NULL,
    humidity FLOAT NOT NULL,
    wind_speed FLOAT NOT NULL,
    wind_direction INT NOT NULL,
    pressure FLOAT NOT NULL,
    description VARCHAR(255) NOT NULL,
    icon VARCHAR(255),
    fetched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!mysqli_query($conn, $createTable)) {
    die("Failed to create table: " . mysqli_error($conn));
}

$cityName = isset($_GET['q']) ? $_GET['q'] : "Pokhara";

// Sanitize city name
$cityName = mysqli_real_escape_string($conn, $cityName);

// Fetch data from OpenWeather API
$apiKey = "a9aae5d26f5f1a3c2c462695a237efba"; 
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$cityName&units=metric&appid=$apiKey";
$response = file_get_contents($apiUrl);

if ($response === false) {
    die("Failed to fetch data from the API.");
}

$data = json_decode($response, true);

// Check if the API returned valid data
if (empty($data) || isset($data['cod']) && $data['cod'] !== 200) {
    die("Invalid data received from the API.");
}

$temperature = $data['main']['temp'];
$humidity = $data['main']['humidity'];
$windSpeed = $data['wind']['speed'];
$windDirection = $data['wind']['deg'];
$pressure = $data['main']['pressure'];
$description = $data['weather'][0]['description'];
$icon = $data['weather'][0]['icon'];


$selectAllData = "SELECT * FROM weather WHERE city = '$cityName'";
$result = mysqli_query($conn, $selectAllData);

if (mysqli_num_rows($result) == 0) {
    
    $insertData = "INSERT INTO weather (city, temperature, humidity, wind_speed, wind_direction, pressure, description, icon)
                   VALUES ('$cityName', '$temperature', '$humidity', '$windSpeed', '$windDirection', '$pressure', '$description', '$icon')";
    if (!mysqli_query($conn, $insertData)) {
        die("Error inserting data: " . mysqli_error($conn));
    }
} else {
   
    $updateData = "UPDATE weather SET 
                   temperature = '$temperature', humidity = '$humidity', wind_speed = '$windSpeed', 
                   wind_direction = '$windDirection', pressure = '$pressure', description = '$description', 
                   icon = '$icon' WHERE city = '$cityName'";
    if (!mysqli_query($conn, $updateData)) {
        die("Error updating data: " . mysqli_error($conn));
    }
}

echo "City Name: $cityName\n";
echo "API Response: $response\n";


echo "Database Query: $updateData\n";


mysqli_close($conn);
?>
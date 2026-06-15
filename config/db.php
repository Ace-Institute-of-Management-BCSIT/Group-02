<?php

$conn = mysqli_connect("localhost", "root", "", "expensetracker");

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}

?>
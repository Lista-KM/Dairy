<?php
session_start();

// Database connection parameters
$db_host = "localhost"; // Change this to your database host if different
$db_user = "root";
$db_pass = "";
$db_name = "dfsms";

// Attempt to establish a connection to the database
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check the connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

function authenticateUser($username, $password)
{
    global $con;

    $username = mysqli_real_escape_string($con, $username);
    $password = mysqli_real_escape_string($con, $password);

    // Check if the user is an admin
    $admin_query = "SELECT * FROM tbladmin WHERE UserName='$username' AND Password='$password'";
    $admin_result = mysqli_query($con, $admin_query);

    if ($admin_result && mysqli_num_rows($admin_result) > 0) {
        $_SESSION['user_type'] = 'admin';
        return true;
    }

    // Check if the user is a regular user
    $user_query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $user_result = mysqli_query($con, $user_query);

    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $_SESSION['user_type'] = 'user';
        return true;
    }

    return "Invalid username or password";
}

function authorizeAdmin()
{
    if ($_SESSION['user_type'] !== 'admin') {
        header('Location: unauthorized.php');
        exit();
    }
}

function authorizeUser()
{
    if ($_SESSION['user_type'] !== 'user') {
        header('Location: unauthorized.php');
        exit();
    }
}

function getFarmsForUser($user_id)
{
    global $con;

    $user_id = mysqli_real_escape_string($con, $user_id);

    // Retrieve farms associated with the user
    $farm_query = "SELECT * FROM farms WHERE user_id='$user_id'";
    $farm_result = mysqli_query($con, $farm_query);

    if ($farm_result && mysqli_num_rows($farm_result) > 0) {
        return mysqli_fetch_all($farm_result, MYSQLI_ASSOC);
    } else {
        return "No farms found for the user";
    }
}
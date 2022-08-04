<?php
header('Content-Type:application/json');
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Methods: *");
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
require('../includes/db.php');
require_once('../includes/functions.php');
$data = json_decode(file_get_contents("php://input"), true);
// echo json_encode($data);
$pass = safe_input($data['password']);
$password=md5($pass);
$employeeCode = safe_input($data['employeeCode']);
// echo json_encode($employeeCode) ;
$sql = "SELECT * FROM users WHERE employeeCode='$employeeCode' && status='1' && password='$password'";
$res = mysqli_query($conn, $sql) or die("Query Failed");
if (mysqli_num_rows($res) > 0) {
    $output = mysqli_fetch_assoc($res);
    $_SESSION['USER']=$output['roles'];
    $_SESSION['NAME']=$output['firstName'];
    $_SESSION['TOKEN']=$output['token'];
    $_SESSION['CODE']=$output['employeeCode'];
   
    $code = $output['employeeCode'];
    $pass = $output['password'];
    // $dtoken = $output['token'];
    // $fname = $output['firstName'];
    // $role = $output['roles'];

    if ($employeeCode == $code && $password == $pass) {
        echo json_encode(['message'=>'Login Successful', 'status' => true]);
    } else {
        echo json_encode(['message' => 'Invalid User', 'status' => false]);
    }
} else {
    echo json_encode(['message' => 'Invalid User', 'status' => false]);
}

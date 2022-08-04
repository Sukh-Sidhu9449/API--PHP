<?php
header('Content-Type:application/json');
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Methods:POST");
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
require_once('../includes/functions.php');
require_once('../includes/db.php');
$data = json_decode(file_get_contents("php://input"), true);

$userEmail = safe_input($data['email']);
$Pass = safe_input($data['password']);
$userPass=md5($Pass);
$userCode = safe_input($data['employeeCode']);
$userFirstName = safe_input($data['firstName']);
$userLastName = safe_input($data['lastName']);
$userCreatedDate = date('d/m/Y');
$token = encode($userCode, $Pass);
//Insert Record
if ($userEmail != "" && $userPass != "" && $userCode != "" && $userFirstName != "" && $userLastName != "" && $userCreatedDate != "" && $token != "") {
    if($userCode>='1000' && $userCode<='2000'){
        $sql = "INSERT INTO users(email,password,employeeCode,firstName,lastName,createdOn,status,token,roles) VALUES ('$userEmail','$userPass', '$userCode','$userFirstName','$userLastName','$userCreatedDate','1','$token','1')";
    }elseif($userCode>='3000'){
        $sql = "INSERT INTO users(email,password,employeeCode,firstName,lastName,createdOn,status,token,roles) VALUES ('$userEmail','$userPass', '$userCode','$userFirstName','$userLastName','$userCreatedDate','1','$token','0')";
    }else{
        echo json_encode(['message' => 'Not Valid Employee Code', 'status' => false]);
    }
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['message' => 'Data Inserted Successfully', 'status' => true]);
    } else {
        echo json_encode(['message' => 'No Record inserted', 'status' => false]);
    }
}else {
    echo json_encode(['message' => 'PLease provide all details', 'status' => false]);
}
?>
<?php
header('Content-Type:application/json');
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Methods:*");
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
require_once('../includes/functions.php');
require_once('../includes/db.php');


if (!empty($_SESSION['TOKEN']) && !empty($_SESSION['CODE'])) {
    $admincode = $_SESSION['CODE'];
    $token = $_SESSION['TOKEN'];
}
$data = json_decode(file_get_contents("php://input"), true);

$employeeCode = safe_input($data['employeeCode']);
// $token = safe_input($data['token']);

$tokensql = "SELECT token,roles FROM users WHERE employeeCode='$admincode'";
$tokenres = mysqli_query($conn, $tokensql);
if (mysqli_num_rows($tokenres)) {
    $output = mysqli_fetch_assoc($tokenres);
    $dtoken = $output['token'];
    $role = $output['roles'];
}
if ($token === $dtoken) {

    $method = $_SERVER['REQUEST_METHOD'];
    if ($role === '1') {

        switch ($method) {
            case "GET":

                //Active Users
                if (isset($_GET['status']) && $_GET['status'] == 1) {
                    // if ($userStatus == "1") {
                    $sql = "SELECT email,employeeCode,firstName,lastName FROM users WHERE status='1' && roles='0'";
                    $res = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        echo json_encode($output);
                        break;
                    } else {
                        echo json_encode(['message' => 'No Record Found', 'status' => false]);
                    }
                }

                //Inactive Users
                if (isset($_GET['status']) && $_GET['status'] == 0) {
                    $sql = "SELECT email,employeeCode,firstName,lastName FROM users WHERE status='0'";
                    $res = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);

                        echo json_encode($output);
                    } else {
                        echo json_encode(['message' => 'No Record Found', 'status' => false]);
                    }
                }

                //Single User Detail
                if (isset($_GET['employeeCode']) && !empty($_GET['employeeCode'])) {
                    $userCode = $_GET['employeeCode'];
                    $sql = "SELECT users.employeeCode,users.firstName,users.lastName,attendence.signIn,attendence.signOut,attendence.totalHrs,attendence.signInDate FROM users JOIN attendence ON users.employeeCode=attendence.employeeCode WHERE users.employeeCode='{$userCode}' && users.status='1' && users.roles='0'";
                    $res = mysqli_query($conn, $sql) or die("Query Failed");
                    if (mysqli_num_rows($res)) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        echo json_encode($output);
                    } else {
                        echo json_encode(['message' => 'No Record Found', 'status' => false]);
                    }
                }

                // Present Users 
                $userDate = safe_input($_GET['date']);

                if (!empty($userDate) && $userDate=='1') {
                    $Date = date('m/d/Y');

                    $sql = "SELECT employeeCode,signIn,signOut,totalHrs FROM attendence  WHERE signInDate='$Date'";
                    $res = mysqli_query($conn, $sql) or die("Query Failed");
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        echo json_encode($output);
                    } else {
                        echo json_encode(['message' => 'No Rcord Found.', 'status' => false]);
                    }
                }

                //Absent Users
                if (!empty($userDate) && $userDate=='2') {
                    $Date = date('m/d/Y');
                    $sql="SELECT employeeCode,firstName,lastName,email FROM users WHERE roles='0' && status='1' && NOT EXISTS(SELECT signIn FROM attendence WHERE users.employeeCode=attendence.employeeCode && signInDate='$Date')";
                  
                    $res = mysqli_query($conn, $sql) or die("Query Failed");
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        echo json_encode($output);
                    } else {
                        echo json_encode(['message' => 'No Rcord Found.', 'status' => false]);
                    }
                }

                break;
            case "POST":
                $data = json_decode(file_get_contents("php://input"), true);
                $userCode = safe_input($data['employeeCode']);
                $userProfile=safe_input($data['profile']);

                $search_value = safe_input($data['search']);

                // Search users by Name
                if (!empty($search_value)) {
                    $sql = "SELECT email,employeeCode,firstName,lastName FROM users WHERE firstName LIKE '%{$search_value}%'  && status='1' && roles='0'";
                    $res = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        echo json_encode($output);
                    } else {
                        echo json_encode(['message' => 'No Search Found', 'status' => false]);
                    }
                }
                //Fetch Profile
                
                if(!empty($userCode) && !empty($userProfile)){
                    $sql = "SELECT email,employeeCode,firstName,lastName FROM users WHERE employeeCode='{$userCode}'";
                    $res = mysqli_query($conn, $sql) or die("Query Failed");
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        echo json_encode($output);
                    } else {
                        echo json_encode(['message' => 'User deactivated', 'status' => false]);
                    }
                }

                //Fetch deactivate users
                // if ($userStatus == "0") {
                //     $sql = "SELECT email,employeeCode,firstName,lastName FROM users WHERE status='0'";
                //     $res = mysqli_query($conn, $sql);
                //     if (mysqli_num_rows($res) > 0) {
                //         $output = mysqli_fetch_all($res, MYSQLI_ASSOC);

                //         echo json_encode($output);
                //     } else {
                //         echo json_encode(['message' => 'No Record Found', 'status' => false]);
                //     }
                // }

                // Fetch all active records
                // if ($userStatus == "1") {
                //     $sql = "SELECT email,employeeCode,firstName,lastName FROM users WHERE status='1' && roles='0'";
                //     $res = mysqli_query($conn, $sql);
                //     if (mysqli_num_rows($res) > 0) {
                //         $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                //         echo json_encode($output);
                //         break;
                //     } else {
                //         echo json_encode(['message' => 'No Record Found', 'status' => false]);
                //     }
                // }
                // Fetch Single record


                break;
            case "PUT":
                // Update Admin Record
                $data = json_decode(file_get_contents("php://input"), true);

                $email = safe_input($data['email']);
                $password = md5(safe_input($data['password']));
                $employeeCode = safe_input($data['employeeCode']);
                $firstName = safe_input($data['firstName']);
                $lastName = safe_input($data['lastName']);

                if (!empty($employeeCode) && !empty($password) && !empty($email) && !empty($firstName) && !empty($lastName)) {

                    $sql = "UPDATE users SET email='$email',password='$password',firstName='$firstName',lastName='$lastName'  WHERE employeeCode='$employeeCode'";

                    if (mysqli_query($conn, $sql)) {
                        echo json_encode(['message' => 'Data Updated Successfully', 'status' => true]);
                    } else {
                        echo json_encode(['message' => 'No Record Updated', 'status' => false]);
                    }
                }

                //Update User Status
                $userCode = safe_input($data['employeeCode']);
                $userStatus = safe_input($data['status']);
                if ($userCode != "" && $userStatus != "") {
                    if ($userStatus == "0") {

                        $sql = "UPDATE users SET status='1' WHERE employeeCode='$userCode'";

                        if (mysqli_query($conn, $sql)) {
                            echo json_encode(['message' => 'Data Updated Successfully', 'status' => true]);
                        } else {
                            echo json_encode(['message' => 'No Record Updated', 'status' => false]);
                        }
                    } else {
                        echo json_encode(['message' => 'User already activated', 'status' => false]);
                    }
                }

                break;

            case "DELETE":
                $data = json_decode(file_get_contents("php://input"), true);

                $userCode = safe_input($data['employeeCode']);
                $userStatus = safe_input($data['status']);

                if ($userCode != "" && $userStatus != "") {
                    if ($userStatus == "1") {
                        $del = "UPDATE users SET status='0' WHERE employeeCode='$userCode'";
                        if (mysqli_query($conn, $del)) {
                            echo json_encode(['message' => 'Record Deleted', 'status' => true]);
                        } else {
                            echo json_encode(['message' => 'No Record Deleted', 'status' => false]);
                        }
                    } else {
                        echo json_encode(['message' => 'Record already Deleted', 'status' => false]);
                    }
                }
                break;

            default:
                echo json_encode(['message' => 'Not Valid Method', 'status' => false]);
                break;
        }
    } elseif($role === '0') {

        switch ($method) {
            case "POST":

                $data = json_decode(file_get_contents("php://input"), true);

                $userCode = safe_input($data['employeeCode']);
                $userSignIn = safe_input($data['signIn']);
                $userDate = safe_input($data['date']);
                $userProfile=safe_input($data['profile']);

                // Attendence Insert

                if (!empty($userCode) && !empty($userSignIn) && empty($userDate)) {
                    $signIn = date('H:i:s');
                    $date = date('m/d/Y');
                    $usql = "SELECT * FROM attendence WHERE employeeCode='$userCode' && signInDate='$date'";
                    $ures = mysqli_query($conn, $usql) or die("Query Failed");
                    if (mysqli_num_rows($ures) > 0) {
                        echo json_encode(['message' => 'Data Inserted Successfully', 'status' => true]);
                        break;
                    } else {

                        $ins = "INSERT INTO attendence(`employeeCode`, `signIn`, `signOut`, `totalHrs`, `signInDate`) VALUES ('{$userCode}','{$signIn}','','','{$date}')";
                        $res = mysqli_query($conn, $ins);
                        if ($res) {
                            echo json_encode(['message' => 'Data Inserted Successfully', 'status' => true]);
                            break;
                        } else {
                            echo json_encode(['message' => 'No Record inserted', 'status' => false]);
                        }
                    }
                }

                // Fetch Single User Record of Current Date
                if (!empty($userCode) && empty($userSignIn) && !empty($userDate)) {
                    $date = date('m/d/Y');

                    $sql = "SELECT signIn,signOut,totalHrs FROM attendence  WHERE employeeCode='$userCode' && signInDate='$date'";
                    $res = mysqli_query($conn, $sql) or die("Query Failed");
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        echo json_encode($output);
                    } else {
                        echo json_encode(['message' => 'No Rcord Found.', 'status' => false]);
                    }
                }
                //Fetch Profile
                if(!empty($userCode) && !empty($userProfile)){
                    $sql = "SELECT email,employeeCode,firstName,lastName FROM users WHERE employeeCode='{$userCode}'";
                    $res = mysqli_query($conn, $sql) or die("Query Failed");
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        echo json_encode($output);
                    } else {
                        echo json_encode(['message' => 'User deactivated', 'status' => false]);
                    }
                }

                //Fetch Single User Record 

                if (!empty($userCode) && empty($userSignIn) && empty($userDate) && empty($userProfile)) {

                    $sql = "SELECT users.employeeCode,users.firstName,users.lastName,attendence.signIn,attendence.signOut,attendence.totalHrs,attendence.signInDate FROM users JOIN attendence ON users.employeeCode=attendence.employeeCode WHERE users.employeeCode='{$userCode}'";
                    $res = mysqli_query($conn, $sql) or die("Query Failed");
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        echo json_encode($output);
                    } else {
                        echo json_encode(['message' => 'User deactivated', 'status' => false]);
                    }
                }


                break;
            case "PUT":

                // Update User Record
                $data = json_decode(file_get_contents("php://input"), true);

                $userEmail = safe_input($data['email']);
                $userPass = md5(safe_input($data['password']));
                $userCode = safe_input($data['employeeCode']);
                $userFirstName = safe_input($data['firstName']);
                $userLastName = safe_input($data['lastName']);

                $userSignOut = safe_input($data['signOut']);

                if (!empty($userCode) && !empty($userPass) && !empty($userEmail) && !empty($userFirstName) && !empty($userLastName))  {
                    $rsql = "SELECT * FROM users WHERE employeeCode='$userCode'";
                    $read = mysqli_query($conn, $rsql);
                    if (mysqli_num_rows($read) > 0) {
                        $output = mysqli_fetch_all($read, MYSQLI_ASSOC);
                        $empcode = $output[0]['employeeCode'];
                        $sql = "UPDATE users SET email='$userEmail',password='$userPass',firstName='$userFirstName',lastName='$userLastName' WHERE employeeCode='$empcode'";

                        if (mysqli_query($conn, $sql)) {
                            echo json_encode(['message' => 'Data Updated Successfully', 'status' => true]);
                        } else {
                            echo json_encode(['message' => 'No Record Updated', 'status' => false]);
                        }
                    } else {
                        echo json_encode(['message' => 'No Record Found', 'status' => false]);
                    }
                }

                //Update Attendence Table

                if (!empty($userCode) && !empty($userSignOut)) {
                    $signOut = date('H:i:s');
                    $date = date('m/d/Y');
                    $sel = "SELECT signIn FROM attendence WHERE employeeCode='$userCode' && signInDate='$date'";
                    $res = mysqli_query($conn, $sel);
                    if (mysqli_num_rows($res) > 0) {
                        $output = mysqli_fetch_assoc($res);
                        $signInTime = $output['signIn'];
                    }
                    $totalHrs = totalTime($signInTime, $signOut);

                    $up = "UPDATE attendence SET signOut='$signOut',totalHrs='$totalHrs' WHERE employeeCode='$userCode' && signInDate='$date'";
                    if (mysqli_query($conn, $up)) {
                        echo json_encode(['message' => 'Data Updated Successfully', 'status' => true]);
                    } else {
                        echo json_encode(['message' => 'No Record inserted', 'status' => false]);
                    }
                }

                break;
            default:
                echo json_encode(['message' => 'Not Valid Method', 'status' => false]);
                break;
        }
    }
} else {
    echo json_encode(['message' => 'Not Valid Token', 'status' => false]);
}

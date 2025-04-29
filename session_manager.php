<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function checkRoleAccess($requiredRole) {


    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $requiredRole) {
        
        switch ($requiredRole) {
            case 'employee':
                header("Location: ../employee/emp-Login.php");
                break;
            case 'manager':
                header("Location: ../leaders/manager-Login.php");
                break;
            case 'company':
                header("Location: ../companies/company-Login.php");
                break;
            default:
                header("Location: ../login-pages/login.html");
        }
        exit();
    }
    
}


function getUserProfileData() {
    global $con; 
    
    $profileData = [
        'name' => 'Guest',
        'role' => 'User',
        'profile_pic' => 'images/Profile/guest.png'
    ];
    
    if (isset($_SESSION['user_role']) && isset($_SESSION['user_id'])) {
        $role = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];
        $userId = mysqli_real_escape_string($con, $userId); 
        
        switch ($role) {

            case 'employee':
                $query = "SELECT * FROM employee WHERE emp_id = '$userId'";
                $result = mysqli_query($con, $query);
                
                if ($row = mysqli_fetch_assoc($result)) {
                    $profileData = [
                        'name' => $row['emp_name'],
                        'role' => $row['emp_role'],
                        'profile_pic' => !empty($row['emp_profile']) ? $row['emp_profile'] : 'images/Profile/employee.png'
                    ];
                }
                break;
                
            case 'manager':
                $query = "SELECT mag_name, mag_role, mag_profile FROM manager WHERE mag_id = '$userId'";
                $result = mysqli_query($con, $query);
                
                if ($row = mysqli_fetch_assoc($result)) {
                    $profileData = [
                        'name' => $row['mag_name'],
                        'role' => $row['mag_role'],
                        'profile_pic' => !empty($row['mag_profile']) ? $row['mag_profile'] : 'images/Profile/manager.png'
                    ];
                }
                break;
                
            case 'company':
                $query = "SELECT cmp_name, cmp_logo FROM company WHERE cmp_id = '$userId'";
                $result = mysqli_query($con, $query);
                
                if ($row = mysqli_fetch_assoc($result)) {
                    $profileData = [
                        'name' => $row['cmp_name'],
                        'role' => 'Company', // No designation for company
                        'profile_pic' => !empty($row['cmp_logo']) ? $row['cmp_logo'] : 'images/Profile/company.png'
                    ];
                }
                break;
        }
    }
    
    return $profileData;
}
?>
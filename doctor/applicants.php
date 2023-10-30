<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Dashboard</title>
    <style>
        .dashbord-tables,.doctor-heade{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table,#anim{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .doctor-heade{
            animation: transitionIn-Y-over 0.5s;
        }
    </style>
    
    
</head>
<body>
    <?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $userrow = $database->query("select * from doctor where docemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["docid"];
    $username=$userfetch["docname"];


    //echo $userid;
    //echo $username;

        
    function getSessionTitleById($sessionID) {
    global $database; // Make sure you have a valid database connection

    // SQL query to select the session title based on the provided ID
    $sql = "SELECT title FROM schedule WHERE scheduleid = ?";

    // Prepare the SQL statement
    $stmt = $database->prepare($sql);

    if ($stmt === false) {
        // Handle the SQL preparation error, if any
        return false;
    }

    // Bind the session ID as a parameter
    $stmt->bind_param("i", $sessionID);

    // Execute the SQL statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if a row was returned
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['title'];
    } else {
        // Return an empty string or an error message if the session ID is not found
        return 'Session not found';
    }
}
    
    ?>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active" >
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">My Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
       <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
            <?php

$sql = "SELECT * FROM appointment_status";

$result = $database->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointment_status_data[] = $row;
    }
}

/// 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept']) && isset($_POST['statusid'])) {
        // Handle Accept button
        $statusId = $_POST['statusid'];

        // Fetch data from appointment_status
        $statusSql = "SELECT * FROM appointment_status WHERE statusid = ?";
        $statusStmt = $database->prepare($statusSql);
        $statusStmt->bind_param("i", $statusId);
        $statusStmt->execute();
        $statusResult = $statusStmt->get_result();
        
        if ($statusResult->num_rows > 0) {
            $statusRow = $statusResult->fetch_assoc();

            // Extract relevant data
            $scheduleid = $statusRow['scheduleid'];
            $pid = $statusRow['pid'];
            $apponums = $statusRow['apponum'];

            // Create a new row in the 'appointment' table
            $insertAppointmentSql = "INSERT INTO appointment (pid, apponum, scheduleid, appodate) VALUES (?, ?, ?, ?)";
            $insertStmt = $database->prepare($insertAppointmentSql);
            $pid = $pid;
            $apponum = $apponums;
            $appodate = date('Y-m-d'); // The current date
            $insertStmt->bind_param("iiis", $pid, $apponum, $scheduleid, $appodate);
            $insertStmt->execute();
            $insertStmt->close();

            // Update the appointment_status to 'accepted'
            $updateStatusSql = "UPDATE appointment_status SET status = 'accepted' WHERE statusid = ?";
            $updateStmt = $database->prepare($updateStatusSql);
            $updateStmt->bind_param("i", $statusId);
            $updateStmt->execute();
            $updateStmt->close();
        }

        $statusStmt->close();
    } elseif (isset($_POST['reject']) && isset($_POST['statusid'])) {
        // Handle Reject button (You can implement rejection logic here)
        // ...
          // Handle Reject button
        $statusId = $_POST['statusid'];

        // Update the appointment_status to 'rejected'
        $updateStatusSql = "UPDATE appointment_status SET status = 'rejected' WHERE statusid = ?";
        $updateStmt = $database->prepare($updateStatusSql);
        $updateStmt->bind_param("i", $statusId);
        $updateStmt->execute();
        $updateStmt->close();
    }

}


?>
                <tr >
                    <td width="13%">
                        <a href="doctors.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        
   
                        
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                        date_default_timezone_set('Asia/Kolkata');

                        $date = date('Y-m-d');
                        echo $date;
                        ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>


                </tr>
               
                
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">
                                Accept/Reject Bookings 
                    </p>
                    </td>
                    
                </tr>
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                        <tr>
                                <th class="table-headin">
                                    
                                
                                S/N
                                
                                </th>
                                <th class="table-headin">
                                    
                                    Sheduled Session
                                    
                                </th>
                                <th class="table-headin">
                                    Status
                                </th>
                                <th class="table-headin">
                                    Action
                                </th>
                                
                                
                        </thead>
   <tbody>
        <?php
        if (!empty($appointment_status_data)) {
            foreach ($appointment_status_data as $key => $status) {
                echo '<tr>';
                echo '<td>' . ($key + 1) . '</td>';
                echo '<td>' . getSessionTitleById($status['scheduleid']) . '</td>';
                echo '<td>' . ucwords($status['status']) . '</td>';
// Inside the loop that displays appointment statuses
echo '<td><form method="post"><input type="hidden" name="statusid" value="' . $status['statusid'] . '">';
echo '<button type="submit" name="accept" class="btn btn-success">Accept</button>';
echo '<button type="submit" name="reject" class="btn btn-danger">Reject</button></form></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3">No appointment status data found.</td></tr>';
        }
        ?>
    </tbody>

                        </table>
                        </div>
                        </center>
                   </td> 
                </tr>
                       
                        
                        
            </table>
        </div>
    </div>


</body>
</html>
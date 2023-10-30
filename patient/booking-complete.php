<?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];

if($_POST){
    if(isset($_POST["booknow"])){
        $apponum = $_POST["apponum"];
        $scheduleid = $_POST["scheduleid"];
        $apponum=$_POST["apponum"];

        // You need to insert data into the appointment_status table here
        $sql2 = "INSERT INTO appointment_status(pid, scheduleid, apponum) VALUES (?, ?, ?)";
        $stmt = $database->prepare($sql2);
        $stmt->bind_param("isi",  $userid, $scheduleid, $apponum);
        $stmt->execute();

        // Assuming you want to retrieve the statusid of the newly inserted row
        $statusid = $stmt->insert_id;

        // Message to indicate that the appointment is pending approval
        $message = "Your appointment has been booked and is pending approval. Please wait for confirmation.";

        // Redirect with the statusid and message
        header("location: appointment.php?action=booking-added&id=" . $statusid . "&titleget=none&message=" . urlencode($message));
    }
}
    // if($_POST){
    //     if(isset($_POST["booknow"])){
    //         $apponum=$_POST["apponum"];
    //         $scheduleid=$_POST["scheduleid"];
    //         $date=$_POST["date"];
    //         $scheduleid=$_POST["scheduleid"];
    //         $sql2="insert into appointment(pid,apponum,scheduleid,appodate) values ($userid,$apponum,$scheduleid,'$date')";
    //         $result= $database->query($sql2);
    //         //echo $apponom;
    //         header("location: appointment.php?action=booking-added&id=".$apponum."&titleget=none");

    //     }
    // }
 ?>
<?php

    session_start(); 

    use PHPMailer\PHPMailer\PHPMailer; 
    use PHPMailer\PHPMailer\Exception; 
    require './PHPMailer/src/Exception.php'; 
    require './PHPMailer/src/PHPMailer.php'; 
    require './PHPMailer/src/SMTP.php';

    if (!isset($_SESSION['username'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }
        if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION['username']);
        header("location: login.php");
    }

    

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = '465';
    $mail->isHTML();
    $mail->Username = 'sahonlinecuphp@gmail.com';
    $mail->Password = 'EtFaTsCeDt27';
    $mail->SetFrom('no-reply@sahonlinecuphp.com');
    $mail->Subject = 'Thank you for your registration';
    
    $mail->AddAddress($_SESSION['email']);

    $mail->Body = nl2br('Thanks for signing up '.$_SESSION['username'].'!
    Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
    
    Please click this link to activate your account:
    http://www.SahOnline.com/verify.php?email='.$_SESSION['username'].'&hash='.$_SESSION['password']);
    $mail->Send();

    header('location: main.php');
?>
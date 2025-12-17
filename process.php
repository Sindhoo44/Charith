<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

include("db.php");

if (isset($_POST["send"])) {

    try {

        // ✅ CLEAN INPUTS
        $f_name    = trim($_POST['f_name']);
        $l_name    = trim($_POST['l_name']);
        $userEmail = trim($_POST['email']);
        $country   = trim($_POST['country']);
        $mobile    = trim($_POST['mobile']);
        $adults    = trim($_POST['adults']);
        $childrens = trim($_POST['childrens'] ?? 0);
        $fullName  = $f_name . " " . $l_name;

        // ✅ REQUIRED FIELDS
        if (!$f_name || !$l_name || !$userEmail || !$country || !$mobile || !$adults) {
            throw new Exception("❌ Please fill all required fields.");
        }

        // ✅ NAME VALIDATION (ONLY LETTERS)
        if (!preg_match("/^[a-zA-Z ]+$/", $f_name)) {
            throw new Exception("❌ First name must contain only letters.");
        }

        if (!preg_match("/^[a-zA-Z ]+$/", $l_name)) {
            throw new Exception("❌ Last name must contain only letters.");
        }

        // ✅ EMAIL
        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("❌ Invalid email format.");
        }

        // ✅ MOBILE VALIDATION (10 DIGITS)
        if (!preg_match('/^[0-9]{10}$/', $mobile)) {
            throw new Exception("❌ Mobile number must be exactly 10 digits.");
        }

        // ✅ ADULTS & CHILDREN
        if (!is_numeric($adults) || $adults < 1) {
            throw new Exception("❌ Adults must be at least 1.");
        }

        if (!is_numeric($childrens)) {
            throw new Exception("❌ Children must be numeric.");
        }

        // ✅ LENGTH CHECK
        if (strlen($f_name) > 50 || strlen($l_name) > 50 || strlen($country) > 50) {
            throw new Exception("❌ Input too long.");
        }

        // ✅ SQL INSERT WITH EXCEPTION HANDLING
        $stmt = $conn->prepare(
            "INSERT INTO booking (f_name, l_name, email, country, mobile, adults, childrens)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            throw new Exception("❌ Database prepare failed.");
        }

        $stmt->bind_param("sssssss", $f_name, $l_name, $userEmail, $country, $mobile, $adults, $childrens);

        if (!$stmt->execute()) {
            throw new Exception("❌ Database insert failed.");
        }

        $stmt->close();

        // ✅ EMAIL SENDING
        $adminEmail = "charith@gmail.com";
        $adminName  = "Charith";

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'sindhoogta@gmail.com';
            $mail->Password   = 'tikcexmwrlwaifgc';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('sindhoogta@gmail.com', 'Charith Tours Sri Lanka');

            $mail->addAddress($userEmail, $fullName);
            $mail->addReplyTo($adminEmail, $adminName);

            $mail->isHTML(true);
            $mail->Subject = "Booking Confirmation";

            $mail->Body = "
                <html>
        <head>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    background: #f4f4f4;
                    font-family: Arial, sans-serif;
                }
                .container {
                    max-width: 600px;
                    margin: auto;
                    background: #2ea9e7;
                    color: #fff;
                    padding: 0;
                }
                .header img {
                    width: 100%;
                    display: block;
                }
                .content {
                    padding: 30px;
                }
                h1 {
                    margin-top: 0;
                }
                .details p {
                    font-size: 15px;
                    line-height: 1.6;
                    margin: 6px 0;
                }
                .btn {
                    display: inline-block;
                    margin-top: 25px;
                    padding: 12px 25px;
                    background: #fff;
                    color: #000;
                    text-decoration: none;
                    font-weight: bold;
                    border-radius: 6px;
                }
                .footer {
                    font-size: 12px;
                    color: #000000ff;
                    text-align: center;
                    padding: 20px;
                }
            </style>
        </head>

        <body>
            <div class='container'>

                <!-- ✅ TOP IMAGE -->
                <div class='header'>
                    <img src='https://img.freepik.com/premium-vector/background-rocky-beach-artistic-illustration-showcasing-rugged-beauty-rocky-beach-landscape-with-wild-nature-vector-illustration_198565-8002.jpg?semt=ais_hybrid&w=740&q=80' alt='Travel'>
                </div>

                <!-- ✅ BODY CONTENT -->
                <div class='content'>
                    <h1>Hi $fullName,</h1>

                    <p style='font-size: 15px;'>We're happy to confirm your booking with <b>Charith Tours Sri Lanka</b> 🇱🇰</p>

                    <div class='details'>
          <p>
            Thank you for choosing <b>Charith Tours Sri Lanka</b>
            for your travel experience! 🌴 We’re excited to confirm that your
            booking has been successfully received. <br><br>
            Our team will contact you shortly with further details. If you have any questions in the meantime, feel free to reply to this email.
            <br><br>
            <p><b>We look forward to welcoming you to Sri Lanka!</b>
                <br> <b>Warm regards,</b>
                <br> <b>Charith Tours Sri Lanka Team </b>
                <br> <b>Your Trusted Travel Partner</b>
            </p>
          </p>
        </div>

                    <a href='https://charithtours.com' class='btn'>Visit Our Website</a>

                    <p style='margin-top: 30px;'>Thank you for choosing us. We’ll contact you shortly! ✨</p>
                </div>

                <!-- ✅ FOOTER -->
                <div class='footer'>
                    © 2025 Charith Tours Sri Lanka<br>
                    This is an automated email. Please do not reply.
                </div>

            </div>
        </body>
        </html>
        ";

            $mail->send();

        } catch (Exception $e) {
            throw new Exception("❌ Email could not be sent. Booking saved successfully.");
        }

        echo "<script>
            alert('✅ Booking saved and email sent successfully!');
            window.location.href = 'index.html';
        </script>";

    } catch (Exception $e) {

        echo "<script>
            alert('" . $e->getMessage() . "');
            history.back();
        </script>";
    }

    mysqli_close($conn);
}
?>

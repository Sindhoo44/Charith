<?php

include("db.php");
$f_name = $_POST['f_name'];
$l_name = $_POST['l_name'];
$email = $_POST['email'];
$country = $_POST['country'];
$mobile = $_POST['mobile'];
$adults = $_POST['adults'];
$childrens = $_POST['childrens'];

$fullName = $f_name . " " . $l_name;

$sql = "INSERT INTO booking(f_name, l_name, email, country, mobile, adults, childrens)
VALUES ('$f_name', '$l_name', '$email', '$country', '$mobile', '$adults', '$childrens')";

echo "Data Added";

mysqli_query($conn, $sql);
mysqli_close($conn);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

$charith = "Charith";
$email = "charith@gmail.com";

if (isset($_POST["send"])) {

    $mail = new PHPMailer(true);

    try {

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sindhoogta@gmail.com';
        $mail->Password   = 'tikcexmwrlwaifgc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        // Set the 'From' email to your Gmail address
        $mail->setFrom('sindhoogta@gmail.com', 'Charith Toures Sri Lanka');

        // Set the recipient to the email provided in the form
        $mail->addAddress($_POST["email"], $_POST["f_name"]);

        // Optionally, you can add a reply-to address (the sender's email)
        $mail->addReplyTo($email, $charith);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $_POST["subject"] ?? "New Contact Form Message";
        $mail->Body    = "Hiiiiii";

        // Send the email
        $mail->send();

        // Success message
        echo "<script>
            alert('Message was sent successfully!');
            window.location.href = 'index.php';
        </script>";

    } catch (Exception $e) {
        // Error handling
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}

?>

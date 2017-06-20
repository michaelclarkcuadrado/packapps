<?php
class EmergencyAlert
{
    function prepareMail()
    {
        include '../../config.php';

        $email_list = mysqli_query($mysqli, "SELECT FullName, EmailAddress FROM quality_AlertEmails");
        require_once("class.phpmailer.php");
        require_once("class.smtp.php");

        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = 'true';
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPassword;
        $mail->Port = $smtpPort;
        $mail->isHTML();
        $mail->setFrom($smtpSendAs, 'Packer Cloud');
        while ($emaildata = mysqli_fetch_assoc($email_list))
        {
            $mail->AddAddress($emaildata['EmailAddress'], $emaildata['FullName']);
        }

        return $mail;
    }

    function sendMail($mail)
    {
        if (!$mail->send()) {
            error_log($mail->ErrorInfo);
        }
    }

    function setSubject($mail, $subject)
    {
        $mail->Subject = $subject;
    }

    function setBody($mail, $body)
    {
        $mail->Body = $body;
    }

    function addAttachment($mail, $attachmentPath)
    {
        $mail->addAttachment($attachmentPath);
    }
}
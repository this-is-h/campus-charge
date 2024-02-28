<?php
header('content-type: text/plain');

// 这里执行一些服务器检测及测试函数
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once('vendor/autoload.php');

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Asia/Shanghai');

//Create a new PHPMailer instance
$mail = new PHPMailer();
try {
    //Server settings
    //Enable verbose debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    //Send using SMTP
    $mail->isSMTP();
    //Set the SMTP server to send through，这里我用的QQ邮箱的，其他邮箱同理
    $mail->Host       = 'smtp.qq.com';
    //Enable SMTP authentication
    $mail->SMTPAuth   = true;
    //SMTP username
    $mail->Username   = 'thisish@foxmail.com';
    //SMTP password 
    $mail->Password   = 'tfmvhtvkzvhwdgbd';
    //Enable implicit TLS encryption
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    //这里的端口可以去QQ邮箱查一下
    $mail->Port       = 465;
 
    //Recipients
    $mail->setFrom('thisish@foxmail.com', '11');
    //Add a recipient
    $mail->addAddress('3259460561@qq.com');
    //Name is optional
    // $mail->addAddress('多个收件人邮箱');
    // $mail->addReplyTo('用于接受回复的邮箱', 'Information');
    // $mail->addCC('抄送邮箱');
    // $mail->addBCC('收件人邮箱（能收到邮件但是不会显示邮箱）');
 
    //Attachments
    //Add attachments
    // $mail->addAttachment('这里填要发送的附件的【路径/附件】');
    //Optional name
    // $mail->addAttachment('这里填要发送的附件的【路径/附件】', '新名称');
 
    //Content
    //Set email format to HTML
    $mail->isHTML(true);
    //这里是邮件的主题
    $mail->Subject = 'This is a emailTest of php';
    //这里是邮件的正文内容
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    //这是非html邮件客户端的纯文本正文
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
 
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
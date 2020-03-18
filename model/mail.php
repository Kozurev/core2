<?php

namespace Model;

use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    /**
     * @return PHPMailer
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function factory()
    {
        global $CFG;
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';

        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;

        $mail->Host = $CFG->smtp->host;
        $mail->Port = $CFG->smtp->port;
        $mail->Username = $CFG->smtp->username;
        $mail->Password = $CFG->smtp->password;

        $mail->setFrom('no-reply@musicmetod.ru', 'Musicmetod');
        return $mail;
    }
}
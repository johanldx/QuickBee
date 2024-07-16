<!-- require_once ROOT_PATH . '/php/env.php';

require ROOT_PATH . '/vendor/PHPMailer/PHPMailer.php';
require ROOT_PATH . '/vendor/PHPMailer/Exception.php';
require ROOT_PATH . '/vendor/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; -->

<?php

require_once ROOT_PATH . '/php/env.php';

require ROOT_PATH . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->setup();
        $this->mailer->CharSet = 'UTF-8';
    }

    private function setup() {
        $this->mailer->isSMTP();
        $this->mailer->Host = getenv("HOST");
        $this->mailer->SMTPAuth = getenv("SMTP_AUTH");
        $this->mailer->Username = getenv("USERNAME");
        $this->mailer->Password = getenv("PASSWORD");
        $this->mailer->SMTPSecure = getenv("SMTP_SECURE");
        $this->mailer->Port = getenv("PORT");
        
        $this->mailer->setFrom('quickbee@rootage.fr', 'Notifications QuickBee');
    }

    public function send($to, $toName, $subject, $body, $altBody = '') {
        try {
            $this->mailer->addAddress($to, $toName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = $altBody;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return $e;
        }
    }
}

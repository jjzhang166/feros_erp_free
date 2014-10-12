<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace tool;

/**
 * email类
 * @author sanliang
 */
class email {

    public function send($toemail, $subject, $message, $from = '', $sitename = '') {
        return $this->smtp($toemail, $subject, $message, $from, $sitename);
    }

    function smtp($toemail, $subject, $message, $from = '', $sitename = '') {
        if ($sitename == '') {
            $siteid = get_siteid();
            $siteinfo = siteinfo($siteid);
            $sitename = $siteinfo['site_title'];
        }

        if ($cfg && is_array($cfg)) {
            $from = $cfg['from'];
            $mail = $cfg;
            $mail_type = $cfg['mail_type']; //邮件发送模式
        } else {
            $cfg = getcache('common', 'commons');
            $from = $cfg['mail_from'];
            $mail_type = $cfg['mail_type']; //邮件发送模式
            if ($cfg['mail_user'] == '' || $cfg['mail_password'] == '') {
                return false;
            }
            $mail = Array(
                'mailsend' => 2,
                'maildelimiter' => 1,
                'mailusername' => 1,
                'server' => $cfg['mail_server'],
                'port' => $cfg['mail_port'],
                'auth' => $cfg['mail_auth'],
                'from' => $cfg['mail_from'],
                'auth_username' => $cfg['mail_user'],
                'auth_password' => $cfg['mail_password']
            );
        }
        //mail 发送模式
        if ($mail_type == 0) {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=' . FEROS_ENCODING . '' . "\r\n";
            $headers .= 'From: ' . $sitename . ' <' . $from . '>' . "\r\n";
            mail($toemail, $subject, $message, $headers);
            return true;
        }
        //邮件头的分隔符
        $maildelimiter = $mail['maildelimiter'] == 1 ? "\r\n" : ($mail['maildelimiter'] == 2 ? "\r" : "\n");
        //收件人地址中包含用户名
        $mailusername = isset($mail['mailusername']) ? $mail['mailusername'] : 1;
        //端口
        $mail['port'] = $mail['port'] ? $mail['port'] : 25;
        $mail['mailsend'] = $mail['mailsend'] ? $mail['mailsend'] : 1;

        //发信者
        $email_from = $from == '' ? '=?' . FEROS_ENCODING . '?B?' . base64_encode($sitename) . "?= <" . $from . ">" : (preg_match('/^(.+?) \<(.+?)\>$/', $from, $mats) ? '=?' . FEROS_ENCODING . '?B?' . base64_encode($mats[1]) . "?= <$mats[2]>" : $from);

        $email_to = preg_match('/^(.+?) \<(.+?)\>$/', $toemail, $mats) ? ($mailusername ? '=?' . FEROS_ENCODING . '?B?' . base64_encode($mats[1]) . "?= <$mats[2]>" : $mats[2]) : $toemail;
        ;

        $email_subject = '=?' . FEROS_ENCODING . '?B?' . base64_encode(preg_replace("/[\r|\n]/", '', '[' . $sitename . '] ' . $subject)) . '?=';
        $email_message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));

        $headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: FEROS-V2 {$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/html; charset=" . FEROS_ENCODING . "{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";

        if (!$fp = fsockopen($mail['server'], $mail['port'], $errno, $errstr, 30)) {
            runlog('SMTP', "($mail[server]:$mail[port]) CONNECT - Unable to connect to the SMTP server", 0);
            return false;
        }
        stream_set_blocking($fp, true);

        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != '220') {
            runlog('SMTP', "$mail[server]:$mail[port] CONNECT - $lastmessage", 0);
            return false;
        }

        fputs($fp, ($mail['auth'] ? 'EHLO' : 'HELO') . " feros\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
            runlog('SMTP', "($mail[server]:$mail[port]) HELO/EHLO - $lastmessage", 0);
            return false;
        }

        while (1) {
            if (substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
                break;
            }
            $lastmessage = fgets($fp, 512);
        }

        if ($mail['auth']) {
            fputs($fp, "AUTH LOGIN\r\n");
            $lastmessage = fgets($fp, 512);
            if (substr($lastmessage, 0, 3) != 334) {
                runlog('SMTP', "($mail[server]:$mail[port]) AUTH LOGIN - $lastmessage", 0);
                return false;
            }

            fputs($fp, base64_encode($mail['auth_username']) . "\r\n");
            $lastmessage = fgets($fp, 512);
            if (substr($lastmessage, 0, 3) != 334) {
                runlog('SMTP', "($mail[server]:$mail[port]) USERNAME - $lastmessage", 0);
                return false;
            }

            fputs($fp, base64_encode($mail['auth_password']) . "\r\n");
            $lastmessage = fgets($fp, 512);
            if (substr($lastmessage, 0, 3) != 235) {
                runlog('SMTP', "($mail[server]:$mail[port]) PASSWORD - $lastmessage", 0);
                return false;
            }

            $email_from = $mail['from'];
        }

        fputs($fp, "MAIL FROM: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from) . ">\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 250) {
            fputs($fp, "MAIL FROM: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from) . ">\r\n");
            $lastmessage = fgets($fp, 512);
            if (substr($lastmessage, 0, 3) != 250) {
                runlog('SMTP', "($mail[server]:$mail[port]) MAIL FROM - $lastmessage", 0);
                return false;
            }
        }

        fputs($fp, "RCPT TO: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail) . ">\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 250) {
            fputs($fp, "RCPT TO: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail) . ">\r\n");
            $lastmessage = fgets($fp, 512);
            runlog('SMTP', "($mail[server]:$mail[port]) RCPT TO - $lastmessage", 0);
            return false;
        }

        fputs($fp, "DATA\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 354) {
            runlog('SMTP', "($mail[server]:$mail[port]) DATA - $lastmessage", 0);
            return false;
        }

        $headers .= 'Message-ID: <' . gmdate('YmdHs') . '.' . substr(md5($email_message . microtime()), 0, 6) . rand(100000, 999999) . '@' . $_SERVER['HTTP_HOST'] . ">{$maildelimiter}";

        fputs($fp, "Date: " . gmdate('r') . "\r\n");
        fputs($fp, "To: " . $email_to . "\r\n");
        fputs($fp, "Subject: " . $email_subject . "\r\n");
        fputs($fp, $headers . "\r\n");
        fputs($fp, "\r\n\r\n");
        fputs($fp, "$email_message\r\n.\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 250) {
            runlog('SMTP', "($mail[server]:$mail[port]) END - $lastmessage", 0);
        }
        fputs($fp, "QUIT\r\n");
        return true;
    }

}

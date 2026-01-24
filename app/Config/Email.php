<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    // Original production credentials
    public $fromEmail  = 'sudhakarpoul@vedantlights.com';
    public $fromName   = 'Vedant Lights';

    // Use Gmail SMTP with original credentials
    public $protocol = 'smtp';
    public $SMTPHost = 'smtp.gmail.com';
    public $SMTPUser = 'sudhakarpoul@vedantlights.com';
    public $SMTPPass = 'ayjhogduwndjdgnb';
    public $SMTPPort = 587;
    public $SMTPCrypto = 'tls';
    public $SMTPTimeout = 120;
    public $SMTPKeepAlive = false;
    public $validate = true;
    public $priority = 3;
    
    public $mailType = 'html';
    public $charset  = 'utf-8';
    public $wordWrap = true;
    public $CRLF    = "\r\n";
    public $newline = "\r\n";
    public $BCCBatchMode = false;
    public $BCCBatchSize = 200;
}

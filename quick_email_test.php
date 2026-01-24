<?php
// Quick email test with improved formatting
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', FCPATH . 'system' . DIRECTORY_SEPARATOR);
define('APPPATH', FCPATH . 'app' . DIRECTORY_SEPARATOR);
define('ROOTPATH', FCPATH);
define('WRITEPATH', FCPATH . 'writable' . DIRECTORY_SEPARATOR);

require_once(SYSTEMPATH . 'bootstrap.php');

echo "<h1>Email Send Test</h1>";

try {
    $email = \Config\Services::email();
    
    $body = '<html><body style="font-family: Arial, sans-serif;">' . PHP_EOL;
    $body .= '<h3 style="color: #333;">Test Contact Form Inquiry</h3>' . PHP_EOL;
    $body .= '<table style="border-collapse: collapse; width: 100%;">' . PHP_EOL;
    $body .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Name:</td><td style="padding: 8px; border: 1px solid #ddd;">Test User</td></tr>' . PHP_EOL;
    $body .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Email:</td><td style="padding: 8px; border: 1px solid #ddd;">test@example.com</td></tr>' . PHP_EOL;
    $body .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Subject:</td><td style="padding: 8px; border: 1px solid #ddd;">Email Test</td></tr>' . PHP_EOL;
    $body .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Message:</td><td style="padding: 8px; border: 1px solid #ddd;">This is a test to verify email functionality works properly.</td></tr>' . PHP_EOL;
    $body .= '</table>' . PHP_EOL;
    $body .= '</body></html>' . PHP_EOL;

    $email->setTo('sudhakarpoul@vedantlights.com');
    $email->setFrom('sudhakarpoul@vedantlights.com', 'Vedant Lights');
    $email->setSubject('Contact Form Test - Fixed Formatting');
    $email->setMessage($body);

    echo "<p>Attempting to send email...</p>";

    if ($email->send()) {
        echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS! Email sent successfully!</p>";
        echo "<p>Check: sudhakarpoul@vedantlights.com</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ FAILED!</p>";
        echo "<h4>Debug Info:</h4>";
        echo "<pre>" . htmlspecialchars($email->printDebugger()) . "</pre>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}
?>
<?php

use Illuminate\Support\Facades\Mail;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');

try {
    echo "Attempting to send test email to yordyalejandro13@gmail.com...\n";

    Mail::raw('Test email from Maikel Cars Internal Diagnostic Script', function ($message) {
        $message->to('yordyalejandro13@gmail.com')
            ->subject('Diagnostic SMTP Test');
    });

    echo "SUCCESS: Email sent successfully via SMTP.\n";
} catch (\Exception $e) {
    echo "ERROR: Failed to send email.\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

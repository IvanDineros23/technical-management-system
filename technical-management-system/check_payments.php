<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use App\Models\Certificate;

echo "=== PAYMENTS TABLE ===\n";
$payments = Payment::with('jobOrder')->get();
echo "Total payments: " . count($payments) . "\n\n";

foreach($payments as $p) {
    echo "ID: {$p->id}\n";
    echo "  Job Order: " . ($p->jobOrder ? $p->jobOrder->job_order_number : 'N/A') . "\n";
    echo "  Payment Code: {$p->payment_code}\n";
    echo "  Amount: {$p->amount}\n";
    echo "  Amount Paid: {$p->amount_paid}\n";
    echo "  Status: {$p->status}\n";
    echo "  Verified At: {$p->verified_at}\n";
    echo "  Verified By: {$p->verified_by}\n";
    echo "---\n";
}

echo "\n=== CERTIFICATES TABLE ===\n";
$certificates = Certificate::with('jobOrder')->get();
echo "Total certificates: " . count($certificates) . "\n\n";

foreach($certificates as $c) {
    echo "ID: {$c->id}\n";
    echo "  Certificate #: {$c->certificate_number}\n";
    echo "  Status: {$c->status}\n";
    echo "  Released At: {$c->released_at}\n";
    echo "  Released To: {$c->released_to}\n";
    echo "  Delivery Method: {$c->delivery_method}\n";
    echo "  Is On Hold: " . ($c->is_on_hold ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

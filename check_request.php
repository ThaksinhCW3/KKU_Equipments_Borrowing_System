<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$req = App\Models\BorrowRequest::where('req_id', 'REQYIKJL0QRQZ')->first();

if ($req) {
    echo "Request ID: " . $req->req_id . "\n";
    echo "Status: " . $req->status . "\n";
    echo "Pickup Deadline: " . ($req->pickup_deadline ? $req->pickup_deadline : 'NULL') . "\n";
    echo "Created At: " . $req->created_at . "\n";
    echo "Updated At: " . $req->updated_at . "\n";
} else {
    echo "Request not found\n";
}

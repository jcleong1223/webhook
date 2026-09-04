<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

#[Signature('app:my-testing-command')]
#[Description('Command description')]
class MyTestingCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $timestamp = '1788493581';
        $rawRequestBody = <<<'JSON'
{
    "emails": [
        "integration@example.com"
    ],
    "notify_on_recovery": true,
    "notify_after_attempt": 3,
    "notify_on_permanent_failure": true
}
JSON;
        dd(json_decode($rawRequestBody));
        $secret = Crypt::decryptString('eyJpdiI6IkdyYzBmdnlNNE11d3pqbERlbkhFb1E9PSIsInZhbHVlIjoibkJ6V2s0Rm5PZW5qeFNWSjdwTkUrK1lDRE1JRWxhSmVsOVlXbCtzMEJKTWRrUFB1V1RCeDFyd2ZINHRqTmlleiIsIm1hYyI6IjE2YWY3ZjRlZTcyMjQ3MDE1MTE1ZWY0MjY2NWJmMDZhOGU2NDNmZTRjYjNlZWJmYmQ4YmY0MjExYTRlZmIwYjUiLCJ0YWciOiIifQ==');
        $signedInput = $timestamp . '.' . $rawRequestBody;
        $sign = 'v1=' . hash_hmac('sha256', $signedInput, $secret);
        dd($rawRequestBody, time(), $sign);
    }
}

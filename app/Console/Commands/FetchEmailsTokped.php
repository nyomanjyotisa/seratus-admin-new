<?php

namespace App\Console\Commands;

use App\Models\GlobalData;
use App\Models\Sale;
use App\Models\Transaction;
use App\Http\Services\FetchEmailService;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpImap\Mailbox;

class FetchEmailsTokped extends Command
{
    protected $signature = 'fetch-email:tokped';
    protected $description = 'Fetch emails from Gmail, Tokped order';

    protected $fetchEmailService;

    public function __construct(FetchEmailService $fetchEmailService)
    {
        parent::__construct();
        $this->fetchEmailService = $fetchEmailService;
    }

    public function handle()
    {
        $this->fetchEmailService->fetchTokped();
    }
}

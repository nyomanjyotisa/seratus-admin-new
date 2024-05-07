<?php

namespace App\Console\Commands;

use App\Models\GlobalData;
use App\Models\Sale;
use App\Models\Transaction;
use App\Http\Services\FetchEmailService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpImap\Mailbox;

class FetchEmailsShopee extends Command
{
    protected $signature = 'fetch-email:shopee';
    protected $description = 'Fetch emails from Gmail, Shopee order';

    protected $fetchEmailService;

    public function __construct(FetchEmailService $fetchEmailService)
    {
        parent::__construct();
        $this->fetchEmailService = $fetchEmailService;
    }

    public function handle()
    {
        $this->fetchEmailService->fetchShopee();
    }
}

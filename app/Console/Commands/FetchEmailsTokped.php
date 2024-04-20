<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpImap\Mailbox;

class FetchEmailsTokped extends Command
{
    protected $signature = 'fetch-email:tokped';
    protected $description = 'Fetch emails from Gmail, Tokped order';

    public function handle()
    {
        $mailbox = new Mailbox(
            '{imap.gmail.com:993/imap/ssl}INBOX',
            'seratusart@gmail.com',
            'zzqt ocry gjzc ugty',
            public_path('attachments')
        );

        //new order tokopedia
        $date = date('d-M-Y', strtotime('2020-04-17'));
        $searchCriteria = 'SINCE "' . $date . '" SUBJECT "Ada Pesanan baru dari"';
        $mails = $mailbox->searchMailbox($searchCriteria);

        Log::info(json_encode($mails));
        Log::info(count($mails));

        $invoicePattern = '/INV\/\d+\/MPL\/\w+/';
        $datePattern = '/(\d{1,2} \w+ \d{4})/';
        $productPattern = '/<p class=".*?p-prod-name">\s*(.*?)\s*<\/p>.*?<p class=".*?p-prod-qty-price">\s*(\d+)\s*x\s*Rp([\d\.]+)\s*<\/p>/s'; // Pattern to match name, quantity, and price for each product
    
        foreach ($mails as $mailId) {
            $mail = $mailbox->getMail($mailId);

            preg_match($invoicePattern, $mail->textHtml, $invoice);
            preg_match($datePattern, $mail->textHtml, $date);
            preg_match_all($productPattern, $mail->textHtml, $products, PREG_SET_ORDER);
            
            Log::info($invoice[0]);
            Log::info($date[1]);

            foreach ($products as $product) {
                $name = trim($product[1]);
                $quantity = $product[2];
                $price = intval(str_replace('.', '', $product[3]));
                
                Log::info($name);
                Log::info($quantity);
                Log::info($price);
                Log::info(' ');
            }
        }

        $mailbox = null;
    }
}

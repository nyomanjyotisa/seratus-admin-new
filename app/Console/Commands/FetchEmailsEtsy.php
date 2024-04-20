<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpImap\Mailbox;

class FetchEmailsEtsy extends Command
{
    protected $signature = 'fetch-email:etsy';
    protected $description = 'Fetch emails from Gmail, etsy order';

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
        $searchCriteria = 'SINCE "' . $date . '" TEXT "Congratulations on your Etsy sale"';
        $mails = $mailbox->searchMailbox($searchCriteria);

        Log::info(json_encode($mails));
        Log::info(count($mails));

        foreach ($mails as $mailId) {
            $mail = $mailbox->getMail($mailId);
            
            preg_match('/order number is:\s*<a[^>]*>(\d+)<\/a>/i', $mail->textHtml, $orderNumber);
            preg_match('/Paid via Etsy Payments on (\d{2} [A-Za-z]{3}, \d{4})/', $mail->textHtml, $date);
            preg_match_all('/<div[^>]*>\s*<a[^>]*>\s*([^<]+)\s*<\/a>\s*<\/div>/', $mail->textHtml, $name);
            preg_match_all('/<div[^>]*>\s*Quantity:\s*([0-9]+)\s*<\/div>/', $mail->textHtml, $quantity);
            preg_match_all('/<div[^>]*>\s*Price:\s*([^<]+)\s*<\/div>/', $mail->textHtml, $price);

            Log::info($orderNumber[1]);
            Log::info(isset($date[1]) ? $date[1] : $mail->date);
            Log::info(json_encode($name[1]));
            Log::info(json_encode($quantity[1]));
            Log::info(json_encode($price[1]));
        }

        $mailbox = null;
    }
}

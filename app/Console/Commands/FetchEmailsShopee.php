<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpImap\Mailbox;

class FetchEmailsShopee extends Command
{
    protected $signature = 'fetch-email:shopee';
    protected $description = 'Fetch emails from Gmail, Shopee order';

    public function handle()
    {
        $mailbox = new Mailbox(
            '{imap.gmail.com:993/imap/ssl}INBOX',
            'seratusart@gmail.com',
            'zzqt ocry gjzc ugty',
            public_path('attachments')
        );

        //new order shopee
        $date = date('d-M-Y', strtotime('2020-04-17'));
        $searchCriteria2 = 'SINCE "' . $date . '" SUBJECT "Pesanan" SUBJECT "Siap Dikirim"';
        $searchCriteria = 'SINCE "' . $date . '" SUBJECT "Pesanan" SUBJECT "Telah Dikonfirmasi"';

        $mails = $mailbox->searchMailbox($searchCriteria);
        $mails2 = $mailbox->searchMailbox($searchCriteria2);

        $allMails = array_merge($mails, $mails2);

        Log::info(json_encode($allMails));
        Log::info(count($allMails));

        $invoicePattern = '/#(\w{12})/';
        $datePattern = '/(\d{1,2}\/\d{1,2}\/\d{4}|\d{1,2} \w{3} \d{4})/';

        foreach ($allMails as $mailId) {
            $mail = $mailbox->getMail($mailId);

            preg_match($invoicePattern, $mail->textHtml, $invoice);
            preg_match($datePattern, $mail->textHtml, $date);
            preg_match_all('/<td[^>]*>\s*\d+\.\s*([^<]+)\s*<\/td>/i', $mail->textHtml, $productsName);
            preg_match_all('/<td[^>]*>\s*Jumlah:\s*<\/td>\s*<td[^>]*>\s*(\d+)\s*<\/td>/i', $mail->textHtml, $productsJumlah);
            preg_match_all('/<td[^>]*>\s*Harga:\s*<\/td>\s*<td[^>]*>\s*([^<]+)\s*<\/td>/i', $mail->textHtml, $productsHarga);

            $invoiceNumber = $invoice[0];
            Log::info("Invoice Number: $invoiceNumber");
            
            Log::info("Date: $date[1]");

            // Log::info("Product Name: " . str_replace("\r", "", $productsName[1]));
            Log::info("Product Name: " . json_encode($productsName[1]));
            Log::info("Product Jumlah: " . json_encode($productsJumlah[1]));
            Log::info("Product Harga: " . json_encode($productsHarga[1]));
            // Log::info("Product Harga: " . str_replace('Rp ', '', str_replace(',', '', $productsHarga[1])));
        }

        $mailbox = null;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\Transaction;
use Carbon\Carbon;
use DateTime;
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

        $monthNames = array(
            'Jan' => 'January',
            'Feb' => 'February',
            'Mar' => 'March',
            'Apr' => 'April',
            'Mei' => 'May',
            'Jun' => 'June',
            'Jul' => 'July',
            'Ags' => 'August',
            'Sep' => 'September',
            'Okt' => 'October',
            'Nov' => 'November',
            'Des' => 'December'
        );  

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

            $datePatternCheck = '/^\d{2} (Jan|Feb|Mar|Apr|Mei|Jun|Jul|Ags|Sep|Okt|Nov|Des) \d{4}$/';

            // Log::info("Product Name: " . str_replace("\r", "", $productsName[1]));

            $dateString = $date[1];
            if (preg_match($datePatternCheck, $date[1])) {
                foreach ($monthNames as $indonesianMonth => $englishMonth) {
                    $dateString = str_replace($indonesianMonth, $englishMonth, $date[1]);
                }
                $dateObj = DateTime::createFromFormat('d M Y', $dateString);
                if ($dateObj !== false) {
                    $dateLast = $dateObj->format('Y-m-d H:i:s');
                } else {
                    $dateLast = now();
                }
            }else{
                $dateLast = Carbon::createFromFormat('d/m/Y', $date[1])->format('Y-m-d');
            }

            $transaction = Transaction::create([
                'unique_code' => $invoice[0],
                'status' => 'pending',
                'source' => 'shopee',
                'date' => $dateLast,
                'description' => '',
            ]);

            for ($i = 0; $i < count($productsName[1]); $i++) {
                $name = trim(str_replace(array('"', "  "), "", str_replace("\\r\\n", '', json_encode($productsName[1][$i]))));
                $quantity = json_encode($productsJumlah[1][$i]);
                $price = str_replace('Rp ', '', str_replace(',', '', json_encode($productsHarga[1][$i])));

                $quantity2 = filter_var($quantity, FILTER_SANITIZE_NUMBER_INT);
                $price2 = filter_var($price, FILTER_SANITIZE_NUMBER_INT);
                
                Log::info("Product Name: " . $name);
                Log::info("Product Jumlah: " . $quantity2);
                Log::info("Product Harga: " . $price2);


                Sale::create([
                    'transaction_id' => $transaction->id,
                    'amount' => $quantity2 * $price2,
                    'description' => $quantity2 . 'X ' . $name,
                    'date' => $dateLast,
                ]);

                $transaction->update([
                    'description' => $transaction->description . ' ' . $quantity2 . 'X ' . $name,
                ]);
            }
            // Log::info("Product Harga: " . str_replace('Rp ', '', str_replace(',', '', $productsHarga[1])));
        }

        $mailbox = null;
    }
}

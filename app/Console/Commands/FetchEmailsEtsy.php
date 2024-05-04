<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\Transaction;
use Carbon\Carbon;
use DateTime;
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

        $monthNames = array(
            'Jan' => 'January',
            'Feb' => 'February',
            'Mar' => 'March',
            'Apr' => 'April',
            'May' => 'May',
            'Jun' => 'June',
            'Jul' => 'July',
            'Aug' => 'August',
            'Sep' => 'September',
            'Oct' => 'October',
            'Nov' => 'November',
            'Dec' => 'December'
        );  

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

            $datePatternCheck = '/^\d{2} (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec), \d{4}$/';
            $dateString = isset($date[1]) ? $date[1] : $mail->date;
            if (preg_match($datePatternCheck, $dateString)) {
                foreach ($monthNames as $indonesianMonth => $englishMonth) {
                    $dateString = str_replace(($indonesianMonth . ','), $englishMonth, $dateString);
                }
                $dateObj = DateTime::createFromFormat('d M Y', $dateString);
                if ($dateObj !== false) {
                    $dateLast = $dateObj->format('Y-m-d H:i:s');
                } else {
                    $dateLast = now();
                }
            }else{
                $dateLast = Carbon::createFromFormat('Y-m-d H:i:s', $dateString)->format('Y-m-d');
            }

            $transaction = Transaction::create([
                'unique_code' => $orderNumber[1],
                'status' => 'pending',
                'source' => 'etsy',
                'date' => $dateLast,
                'description' => '',
            ]);            
            
            for ($i = 0; $i < count($quantity[1]); $i++) {

                Log::info('$name'.json_encode($name));
                if($name[1][0] == 'Learn more'){
                    $nameFinal = $name[1][$i+1];
                }else{
                    $nameFinal = $name[1][$i];
                }

                $name0 = trim(str_replace(array('"', "  "), "", str_replace("\\r\\n", '', json_encode($nameFinal))));
                $quantity0 = json_encode($quantity[1][$i]);
                $price0 = str_replace('Rp ', '', str_replace(',', '', json_encode($price[1][$i])));

                $quantity2 = filter_var($quantity0, FILTER_SANITIZE_NUMBER_INT);
                $price2 = filter_var($price0, FILTER_SANITIZE_NUMBER_INT);

                
                Log::info(json_encode($name0));
                Log::info(json_encode($quantity2));
                Log::info(json_encode($price2));

                Sale::create([
                    'transaction_id' => $transaction->id,
                    'amount' => $quantity2 * $price2,
                    'description' => $quantity2 . 'X ' . $name0,
                    'date' => $dateLast,
                ]);

                $transaction->update([
                    'description' => $transaction->description . ' ' . $quantity2 . 'X ' . $name0,
                ]);
            }
        }

        $mailbox = null;
    }
}

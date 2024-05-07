<?php

namespace App\Http\Services;

use App\Models\GlobalData;
use App\Models\Sale;
use App\Models\Transaction;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;
use PhpImap\Mailbox;

class FetchEmailService
{

    public function fetchTokped()
    {
        $mailbox = new Mailbox(
            '{imap.gmail.com:993/imap/ssl}INBOX',
            'seratusart@gmail.com',
            'zzqt ocry gjzc ugty',
            public_path('attachments')
        );

        //new order tokopedia
        $last_tokped_email_fetch = GlobalData::where('key', 'last_tokped_email_fetch')->first();

        $date = date('d-M-Y', strtotime($last_tokped_email_fetch->value));

        $last_tokped_email_fetch->update([
            'value' => date('d-M-Y', strtotime('+1 day'))
        ]);

        Log::alert('$date');
        Log::alert($date);
        Log::alert('$date');

        $searchCriteria = 'SINCE "' . $date . '" SUBJECT "Ada Pesanan baru dari"';
        $mails = $mailbox->searchMailbox($searchCriteria);

        Log::info(json_encode($mails));
        Log::info(count($mails));

        $invoicePattern = '/INV\/\d+\/MPL\/\w+/';
        $datePattern = '/(\d{1,2} \w+ \d{4})/';
        $productPattern = '/<p class=".*?p-prod-name">\s*(.*?)\s*<\/p>.*?<p class=".*?p-prod-qty-price">\s*(\d+)\s*x\s*Rp([\d\.]+)\s*<\/p>/s'; // Pattern to match name, quantity, and price for each product

        $monthNames = array(
            'Januari' => 'January',
            'Februari' => 'February',
            'Maret' => 'March',
            'April' => 'April',
            'Mei' => 'May',
            'Juni' => 'June',
            'Juli' => 'July',
            'Agustus' => 'August',
            'September' => 'September',
            'Oktober' => 'October',
            'November' => 'November',
            'Desember' => 'December'
        );  

        foreach ($mails as $mailId) {
            $mail = $mailbox->getMail($mailId);

            preg_match($invoicePattern, $mail->textHtml, $invoice);
            preg_match($datePattern, $mail->textHtml, $date);
            preg_match_all($productPattern, $mail->textHtml, $products, PREG_SET_ORDER);
            
            Log::info($invoice[0]);
            Log::info($date[1]);


            $dateString = $date[1];
            foreach ($monthNames as $indonesianMonth => $englishMonth) {
                $dateString = str_replace($indonesianMonth, $englishMonth, $dateString);
            }      
            $dateObj = DateTime::createFromFormat('d M Y', $dateString);
            if ($dateObj !== false) {
                $dateLast = $dateObj->format('Y-m-d H:i:s');
            } else {
                $dateLast = now();
            }
            Log::info($dateLast);

            $transaction = Transaction::create([
                'unique_code' => $invoice[0],
                'status' => 'pending',
                'source' => 'tokped',
                'date' => $dateLast,
                'description' => '',
            ]);

            foreach ($products as $product) {
                $name = trim($product[1]);
                $quantity = $product[2];
                $price = intval(str_replace('.', '', $product[3]));
                
                Log::info($name);
                Log::info($quantity);
                Log::info($price);
                Log::info(' ');

                Sale::create([
                    'transaction_id' => $transaction->id,
                    'amount' => $quantity * $price,
                    'description' => $quantity . 'X ' . $name,
                    'date' => $dateLast,
                ]);

                $transaction->update([
                    'description' => $transaction->description . ' ' . $quantity . 'X ' . $name,
                ]);
            }
        }

        $mailbox = null;
    }

    public function fetchEtsy(){
        $mailbox = new Mailbox(
            '{imap.gmail.com:993/imap/ssl}INBOX',
            'seratusart@gmail.com',
            'zzqt ocry gjzc ugty',
            public_path('attachments')
        );

        //new order tokopedia
        $last_etsy_email_fetch = GlobalData::where('key', 'last_etsy_email_fetch')->first();

        Log::alert($last_etsy_email_fetch);
        Log::alert(GlobalData::all());

        $date = date('d-M-Y', strtotime($last_etsy_email_fetch->value));

        $last_etsy_email_fetch->update([
            'value' => date('d-M-Y', strtotime('+1 day'))
        ]);

        Log::alert('$date');
        Log::alert($date);
        Log::alert('$date');

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

    public function fetchShopee(){
        $mailbox = new Mailbox(
            '{imap.gmail.com:993/imap/ssl}INBOX',
            'seratusart@gmail.com',
            'zzqt ocry gjzc ugty',
            public_path('attachments')
        );

        //new order shopee
        $last_shopee_email_fetch = GlobalData::where('key', 'last_shopee_email_fetch')->first();

        $date = date('d-M-Y', strtotime($last_shopee_email_fetch->value));

        $last_shopee_email_fetch->update([
            'value' => date('d-M-Y', strtotime('+1 day'))
        ]);

        
        Log::alert('$date');
        Log::alert($date);
        Log::alert('$date');

        $searchCriteria2 = 'SINCE "' . $date . '" SUBJECT "Pesanan" SUBJECT "Siap Dikirim"';
        $searchCriteria = 'SINCE "' . $date . '" SUBJECT "Pesanan" SUBJECT "Telah Dikonfirmasi"';

        $mails = $mailbox->searchMailbox($searchCriteria);
        $mails2 = $mailbox->searchMailbox($searchCriteria2);

        $allMails = array_merge($mails, $mails2);

        Log::info(json_encode($allMails));
        Log::info(count($allMails));

        $invoicePattern = '/#(\w{14})/';
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
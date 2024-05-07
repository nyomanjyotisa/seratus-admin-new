<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\Sale;
use App\Models\Transaction;
use App\Project;
use Illuminate\Console\Command;
use League\Csv\Reader;

class GetMarketplaceCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:csv {path : Direct path to the .csv file}';

    // php artisan transaction:csv november-2022.csv

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Null';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('path');

        $this->info($path);

        if (!file_exists($path)) {
            $this->error('The specified file cannot be found.');
            return 1;
        }

        $reader = Reader::createFromPath($path, 'r');
        $reader->setHeaderOffset(0);

        $totalRows = iterator_count($reader->getRecords());
        $this->info("Total records to update: $totalRows");

        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($reader->getRecords() as $row) {
            if (!empty($row['Date']) && !empty($row['Nilai'])) {

                if(!empty($row['ID'])){
                    $transaction = Transaction::firstOrCreate(
                        [
                            'unique_code' => $row['ID'],
                        ],
                        [
                            'status' => 'pending',
                            'source' => $row['Sumber'],
                            'date' => $row['Date'],
                            'description' => '',
                        ]
                    );
    
                    if((int)$row['Nilai'] > 0){
                        Sale::create([
                            'transaction_id' => $transaction->id,
                            'amount' => (int)$row['Nilai'],
                            'description' => $row['Keterangan'],
                            'date' => $row['Date'],
                        ]);
                        $this->info("Sale Added: " . $row['Keterangan']);
                    }else{
                        Expense::create([
                            'transaction_id' => $transaction->id,
                            'amount' => (int)$row['Nilai'] * -1,
                            'description' => $row['Keterangan'],
                            'date' => $row['Date'],
                        ]);
                        $this->info("Expense Added: " . $row['Keterangan']);
                    }
                }else{
                    if((int)$row['Nilai'] > 0){
                        OtherIncome::create([
                            'amount' => (int)$row['Nilai'],
                            'description' => $row['Keterangan'],
                            'date' => $row['Date'],
                        ]);
                        $this->info("Global OtherIncome Added: " . $row['Keterangan']);
                    }else{
                        Expense::create([
                            'amount' => (int)$row['Nilai'] * -1,
                            'description' => $row['Keterangan'],
                            'date' => $row['Date'],
                        ]);
                        $this->info("Global Expense Added: " . $row['Keterangan']);
                    }
                }
                
                $updatedCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->info("Total records Added: $updatedCount");
        $this->info("Total records skipped: $skippedCount");

        return 0;
    }
}

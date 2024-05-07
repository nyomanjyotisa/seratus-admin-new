<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\Production;
use App\Models\Sale;
use App\Models\Transaction;
use App\Project;
use Illuminate\Console\Command;
use League\Csv\Reader;

class GetOperationCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:operation-csv {path : Direct path to the .csv file}';

    // php artisan transaction:operation-csv november-2022-opt.csv

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
            if (!empty($row['Keterangan'])) {

                if(!empty($row['ID'])){
                    $transaction = Transaction::where("unique_code", $row['ID'])->first();

                    if ($transaction) {
                        if(!empty($row['Sumber'])){
                            Production::create([
                                'transaction_id' => $transaction->id,
                                'amount' => (int)$row['Pengeluaran'],
                                'description' => $row['Keterangan'],
                                'date' => $row['Date'],
                            ]);
                            $this->info("Added1 Prod: " . $row['Keterangan']);
                        }else{
                            Expense::create([
                                'transaction_id' => $transaction->id,
                                'amount' => (int)$row['Pengeluaran'],
                                'description' => $row['Keterangan'],
                                'date' => $row['Date'],
                            ]);
                            $this->info("Added1 Expense: " . $row['Keterangan']);
                        }
                    } else {
                        $this->info("Skipped1: " . $row['Keterangan']);
                    }
                }else if(!empty($row['Pemasukan'])){
                    $transaction = Transaction::create([
                        'status' => 'pending',
                        'source' => $row['Sumber'],
                        'date' => $row['Date'],
                        'description' => $row['Keterangan'],
                    ]);
                    Production::create([
                        'transaction_id' => $transaction->id,
                        'amount' => (int)$row['Pengeluaran'],
                        'description' => $row['Keterangan'],
                        'date' => $row['Date'],
                    ]);
                    Sale::create([
                        'transaction_id' => $transaction->id,
                        'amount' => (int)$row['Pemasukan'],
                        'description' => $row['Keterangan'],
                        'date' => $row['Date'],
                    ]);
                    $this->info("Added2: " . $row['Keterangan']);
                }else{
                    if(!empty($row['Sumber'])){
                        $this->info("Skipped2: " . $row['Keterangan']);
                    }else{
                        Expense::create([
                            'amount' => (int)$row['Pengeluaran'],
                            'description' => $row['Keterangan'],
                            'date' => $row['Date'],
                        ]);
                        $this->info("Added3: " . $row['Keterangan']);
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

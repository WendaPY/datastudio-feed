<?php

namespace App\Console\Commands;

use App\Models\Models\Post;
use Illuminate\Console\Command;
use App\Http\Resources\PostDataFeed;
use Revolution\Google\Sheets\Facades\Sheets;

class HydrateWendaDataFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wenda-data-feed:hydrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hydrate data feed (Google Spreadsheet) for Google Data Studio';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->comment(PHP_EOL . 'DataStudio Feed started' . PHP_EOL);

        // Retrieve spreadsheet
        $spreadsheetId = config('services.wenda.google_spreadsheet');
        $dataFeed = Sheets::spreadsheet($spreadsheetId)->sheet('data');

        $this->line('→ Retrieve Google Spreadsheet ... <info>✔</info>');

        // Clear the sheet
        $dataFeed->clear();

        // Retrieve all data from database
        $posts = Post::all();

        // Append data to the sheet
        $data = PostDataFeed::collection($posts)->toArray(request());

        $this->line('→ Processing new data from DB ... <info>✔</info>');

        if (sizeof($data) > 0) {
            $keys = array_keys($data[0]);
            $dataFeed->append([$keys]);
        }

        $dataFeed->append($data);

        $this->line('→ Publishing new data ... <info>✔</info>');

        $this->info(PHP_EOL . "Done. {$posts->count()} rows were exported.");
    }
}

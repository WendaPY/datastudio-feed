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
        // Retrieve spreadsheet
        $spreadsheetId = config('services.wenda.google_spreadsheet');
        $dataFeed = Sheets::spreadsheet($spreadsheetId)->sheet('data');

        // Clear the sheet
        $dataFeed->clear();

        // Retrieve all data from database
        $posts = Post::all();

        // Append data to the sheet
        $data = PostDataFeed::collection($posts)->toArray(request());

        if (sizeof($data) > 0) {
            $keys = array_keys($data[0]);
            $dataFeed->append([$keys]);
        }

        $dataFeed->append($data);

        $this->info(PHP_EOL . "Se exportaron {$posts->count()} registros.");
    }
}

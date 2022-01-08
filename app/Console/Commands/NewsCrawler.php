<?php

namespace App\Console\Commands;

use Goutte\Client as GouteClient;
use Illuminate\Console\Command;

class NewsCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    private GouteClient $gouteClient;
    public function __construct(GouteClient $gouteClient)
    {
        $this->gouteClient = $gouteClient;
        parent::__construct();
    }


    public function handle()
    {

        $crawler = $this->gouteClient->request('GET','https://www.wp.pl/');
        $result= [];
         $crawler->filter('.sc-gm52b2-0')->each(function ($main) use (&$result){
             $result['posts'] = $main->filter('a')->each(function ($title){
                return [
                    'postsTitle' => $title->text(),
                    'link' => $title->attr('href'),
                    'img'  =>  $title->filter('img')->attr('src')
                ];
             });
             //sc-1c7s2z7-0
//             $result[]['title'] = $main->filter('.beSrCx')->each(function ($title){
//                return $title->text();
//             });
        });
        dd($result);
    }
}

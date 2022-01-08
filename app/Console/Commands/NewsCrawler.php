<?php

namespace App\Console\Commands;

use Goutte\Client as GouteClient;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

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

    ///php  -dxdebug.start_with_request=yes artisan crawler:news
    private const WP_PAGES_TYPES = [
        'wiadomosci',
        'sportowefakty',
        'money',
        'gwiazdy',
        'pudelek',
        'o2',
        'abczdrowie',
        'kobieta.wp.pl',
        'pilot.wp.pl',
        'kuchnia.wp.pl',
        'gadzetomania.pl',
        'wideo.wp.pl'
    ];

    public function handle()
    {
        $crawler = $this->gouteClient->request('GET', 'https://www.wp.pl/');
        $result = [];
        $crawler->filter('.hWPLle')->each(function ($main) use (&$result) {
            foreach($main->filter('a') as $domElement){
                $domElement = new Crawler($domElement);
                $href = $domElement->attr('href');
                if ($domElement->filter('.beSrCx')->count() > 0) {
                    $postTitle = $domElement->filter('.beSrCx')->text();
                } elseif ($domElement->filter('.irgSwQ')->count() > 0) {
                    $postTitle = $domElement->filter('.irgSwQ')->text();
                } elseif ($domElement->filter('.bpwShS')->count() > 0){
                    $postTitle =$domElement->filter('.bpwShS')->text(); //pudelek
                } else {
                    $postTitle = '';
                }
                foreach (self::WP_PAGES_TYPES as $pageType) {
                    if (str_contains($href, $pageType) && $postTitle!='') {
                        $result[$pageType][$postTitle] = [
                            'postsTitle' => $postTitle,
                            'link' => $domElement->attr('href'),
                            'img' => $domElement->filter('img')->count() > 0 ? $domElement->filter('img')->attr('src') : '',
                        ];
                    }
                }
            }
        });
        $crawler->filter('#glonews')->each(function ($main) use (&$result) {
            foreach($main->filter('a') as $domElement){
                $domElement = new Crawler($domElement);
                $href = $domElement->attr('href');
                if ($domElement->filter('.beSrCx')->count() > 0) {
                    $postTitle = $domElement->filter('.beSrCx')->text();
                } elseif ($domElement->filter('.irgSwQ')->count() > 0) {
                    $postTitle = $domElement->filter('.irgSwQ')->text();
                } elseif ($domElement->filter('.bpwShS')->count() > 0){
                    $postTitle =$domElement->filter('.bpwShS')->text(); //pudelek
                } else {
                    $postTitle = '';
                }
                foreach (self::WP_PAGES_TYPES as $pageType) {
                    if (str_contains($href, $pageType) && $postTitle!='') {
                        $result[$pageType][$postTitle] = [
                            'postsTitle' => $postTitle,
                            'link' => $domElement->attr('href'),
                            'img' => $domElement->filter('img')->count() > 0 ? $domElement->filter('img')->attr('src') : '',
                        ];
                    }
                }
            }
        });
        dd($result);
    }
}

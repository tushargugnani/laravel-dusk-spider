<?php

namespace Tests\Browser;

use App\Page;
use Facebook\WebDriver\WebDriverBy;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class duskSpiderTest extends DuskTestCase
{

    protected static $domain = '5balloons.info';  //Domain of the website to crawl
    protected static $startUrl = 'https://www.5balloons.info/';  //Starting point for your crawl process
    protected static $totalPages = 300;  // Total pages to crawl , put large number to crawl all pages
    protected static $removeUrlParameter = true;    //Consider appened parameters to url duplicate if true
    protected static $excludeUrls = ['/category/'];  //Exclude URL that contains. 
    protected static $includeUrls = ['/post/'];  //Only Include URL that contains

    protected static $meta = []; /*['post-title' => 'h1.post-title',
                              'views' => '.main-content > a:nth-child(7)'];*/
    


    public function setUp(): void{
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    /** @test */
    public function urlSpider()
    {

        $startingLink = Page::create([
            'url' => self::$startUrl,
            'isCrawled' => false,
        ]);

        $this->browse(function (Browser $browser) use ($startingLink) {
            $this->getLinks($browser, $startingLink);
        });

        //Shows Green in console instead of yellow :)
        $this->assertTrue(true);
    }

    protected function getLinks(Browser $browser, $currentUrl){

        $this->processCurrentUrl($browser, $currentUrl);


        try{
            //Run Recursively
            foreach(Page::where('isCrawled', false)->get() as $link) {
                
                $this->getLinks($browser, $link);
            }


        }catch(\Exception $e){

        }
    }

    protected function processCurrentUrl(Browser $browser, $currentUrl){

        //Check if already crawled
        if(Page::where('url', $currentUrl->url)->first()->isCrawled == true)
            return;

        try{
                //Visit URL
                $browser->visit($currentUrl->url);
                

                //Get Links and Save to DB if Valid
                $linkElements = $browser->driver->findElements(WebDriverBy::tagName('a'));
                
                if(Page::count() < self::$totalPages){
                    foreach($linkElements as $element){
                        $href = $element->getAttribute('href');
                        $href = $this->trimUrl($href);
                        if($this->isValidUrl($href)){
                            
                            Page::create([
                                'url' => $href,
                                'isCrawled' => false,
                            ]);
                        }
                    }
                }

            //Check if $meta array is not empty
            if(!empty(self::$meta)){
                $metaArray = $this->getMeta($browser);   //Get Meta information from page 
                $currentUrl->meta = $metaArray;         
            }
        }catch(\Exception $e){

        }
        
        
        $currentUrl->isCrawled = true;
        $currentUrl->status  = $this->getHttpStatus($currentUrl->url);
        $currentUrl->title = $browser->driver->getTitle();
        $currentUrl->save();
    }


    protected function isValidUrl($url){

        if(Page::count() >= self::$totalPages)
            return false;

        foreach(self::$excludeUrls as $excludeUrl){
            if(strpos($url, $excludeUrl))
                return false;
        }    

        foreach(self::$includeUrls as $includeUrl){
            if(!strpos($url, $includeUrl))
                return false;
        } 


        $parsed_url = parse_url($url);

        if(isset($parsed_url['host'])){
            if(strpos($parsed_url['host'], self::$domain) !== false && !Page::where('url', $url)->exists()){
                return true;
            }
        }
        return false;
    }

    protected function trimUrl($url){
        if(self::$removeUrlParameter)
            $url = strtok($url, '?');
        $url = strtok($url, '#');
        $url = rtrim($url,"/");
        return $url;
    }

    protected function getHttpStatus($url){
        $headers = get_headers($url, 1);
        return intval(substr($headers[0], 9, 3));
    }

    protected function getMeta($browser){
        $metaArray = array();
        foreach(self::$meta as $metaName => $elementPath){
            //Check if element exist
            try{
            $metaArray[$metaName] = $browser->text($elementPath); 
            }catch(\Facebook\WebDriver\Exception\NoSuchElementException $e){
                $metaArray[$metaName] = 'Not Found'; 
            }
        }
        return $metaArray;
    }
}

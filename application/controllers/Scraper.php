<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scraper extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

	public function index()
	{
        
		$data = array();
        if( isset($_GET['url'])){
            $data['url'] = trim($_GET['url']);
            $this->load->model("scraping");
            $this->load->model("dataManagement");
            // Scrape the data based on the URL
            $scrapedData = $this->scraping->init_scrape($data['url']);
            $insert_id = $this->dataManagement->pushNewPage($scrapedData);


            for($i = 1; $i <= 5; $i++){

                $lastPage = $this->dataManagement->getPageDetails($insert_id);
                if( $lastPage ){
                    $nextLink = $scrapedData['nextLink'];
                    if( substr($nextLink, 0, 1) !== "h" ){
                        $nextLink = $scrapedData['linkScheme']."://".$scrapedData['baseLink'] . $nextLink;
                    }
                
                }

                $scrapedData = $this->scraping->init_scrape($nextLink);
                $insert_id = $this->dataManagement->pushNewPage($scrapedData);

            }

            $data['query_results'] = $this->dataManagement->getLastQueryResults();
            $data['pagesCrawled'] = $data['query_results']->num_rows();
            $data['results'] = $data['query_results']->result_array();
            $data['pageLoad'] = $this->dataManagement->averagePageLoad();
            $data['wordCount'] = $this->dataManagement->averageWordCount();
            $data['titleLength'] = $this->dataManagement->averageTitleLength();
            
        }
        $this->load->view('dashboard' , $data);
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DataManagement extends CI_Model {

    function pushNewPage($data){
        $ins = array(
            "path" => $data['path'],
            "imgCount" => $data['imgCount'],
            "linkList" => json_encode($data['linkList']),
            "uniqueLinksInt" => $data['uniqueLinksInt'],
            "uniqueLinksExt" => $data['uniqueLinksExt'],
            "pageLoadTime" => $data['pageLoadTime'],
            'titleLength' => $data['titleLength'],
            'statusCode' => $data['statusCode'],
            'wordCount' => $data['wordCount']
        );

        $inserted = $this->db->insert("pages" , $ins);

        if( $inserted ){
            return $this->db->insert_id();
        } else {
            return -1;
        }
    }

    function getPageDetails($insert_id){

        $this->db->select("*");
        $this->db->from("pages");
        $this->db->where("page_id" , $insert_id);

        $query = $this->db->get();

        if( $query ){
            return true;
        } else {
            return false;
        }

    }

    function getLastQueryResults(){

        $query = "SELECT * FROM ( SELECT * FROM pages ORDER BY page_id DESC LIMIT 6 ) sub ORDER BY page_id ASC";
        
        $queryRun = $this->db->query($query);

        return $queryRun;

    }

    function averagePageLoad(){
        $query = "SELECT AVG(pageLoadTime) 'apl' FROM pages ORDER by page_id DESC LIMIT 6";

        return $this->db->query($query)->row();
    }
    
    function averageWordCount(){
        $query = "SELECT AVG(wordCount) 'awc' FROM pages ORDER by page_id DESC LIMIT 6";

        return $this->db->query($query)->row();
    }

    function averageTitleLength(){
        $query = "SELECT AVG(titleLength) 'atl' FROM pages ORDER by page_id DESC LIMIT 6";

        return $this->db->query($query)->row();
    }

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scraping extends CI_Model {

    function init_scrape($url){
        # curl.php

        // Initialize a connection with cURL (ch = cURL handle, or "channel")
        $ch = curl_init();

        // Set the URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set the HTTP method
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        // Return the response instead of printing it out
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Send the request and store the result in $response
        $response = curl_exec($ch);

        $data['path'] = $url;

        // Get Page Loaded Time
        if( !curl_errno ($ch) ){
            $cinfo = curl_getinfo($ch);
            $data['pageLoadTime'] = $cinfo['total_time'];
        }

        

        // Get Page Status Code
        $data['statusCode'] =  curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // New DomDocument
        $htmlDom = new DOMDocument;

        // Load CURL Response into the DomDocument Object
        @$htmlDom->loadHTML(file_get_contents($url));

        // Let's start with grabbing all of the image tags and putting them in an array
        $images['images'] = array();
        $imageTags = $htmlDom->getElementsByTagName('img');
        foreach($imageTags as $imageTag){
            // Get the data-src. Sources were coming up blank. I'm not sure why when they're literally the exact same path..
            $imgSrc = $imageTag->getAttribute('data-src');
            if( !in_array( $imgSrc, $images['images']) ){
                array_push($images['images'], $imgSrc);
            }
        }
        $data['imgCount'] = count($images['images']);

        // Moving on to links
        // Setup Initial Array & Counters
        $data['linkList'] = array();
        $data['uniqueLinksInt'] = 0;
        $data['uniqueLinksExt'] = 0;
        $data['nextLink'] = "";
        $linkTags = $htmlDom->getElementsByTagName("a");
        $parsedPassedURL = parse_url($url);
        foreach( $linkTags as $link ){
            // Grab the value of the HREF attribute for each link
            $linkHREF = $link->getAttribute("href");
            
            // If that link doesn't already exist in our array, let's add it.
            if( !in_array($linkHREF, $data['linkList']) ){
                array_push($data['linkList'] , $linkHREF);

                // Now that we've gotten only unique links, lets see how many there are
                $parsedLinkURL = parse_url($linkHREF);
                $parsedPassedURL = parse_url($url);

                $firstCharacter = substr($linkHREF, 0, 1);
                if( $firstCharacter == "#" || $firstCharacter == "/"){
                    $data['uniqueLinksInt']++;
                } else {
                    $data['uniqueLinksExt']++;
                }
            }
        }

        $link = $this->pickNextLink($data['linkList']);

        if( $link['nextLinkChar'] == "/"){
            $data['nextLink'] = $data['linkList'][$link['nextLink']];
            $data['baseLink'] = $parsedPassedURL['host'];
            $data['linkScheme'] = $parsedPassedURL['scheme'];
        } elseif ( isset($link['parsedNextLink']['host']) ) {

            if( $link['parsedNextLink']['host'] == $parsedPassedURL['host'] ){
                $data['nextLink'] == $linkList[$nextLink];
            }
        } else {
            $nextLink = array_rand($data['linkList'], 1);
            $nextLinkChar = substr($data['linkList'][$nextLink], 0 , 1);
            $parsedNextLink = parse_url($nextLink);
            $data['nextLink'] = $data['linkList'][$nextLink];
            $data['baseLink'] = $parsedPassedURL['host'];
            $data['linkScheme'] = $parsedPassedURL['scheme'];
        }

        $data['nextLink'];

        // Get the title of the page.
        $title = $htmlDom->getElementsByTagName("title")->item(0)->nodeValue;
        // Count the characters
        $data['titleLength'] = strlen($title);
        


        // Get word count
        // New Xpath
        $xpath = new DOMXPath($htmlDom);

        //Grab only text nodes
        $nodes = $xpath->query("//text()");

        $textNodeContent = "";

        // Assign to individual string
        foreach($nodes as $node){
            $textNodeContent .= " $node->nodeValue";
        }

        // Blow each word up into it's own array index and count the amount of indexes
        $data['wordCount'] = count(str_word_count( $textNodeContent, 1));
        

        return $data;

        // Close cURL resource to free up system resources
        curl_close($ch);
    }

    function pickNextLink($linkList){
        $nextLink = array_rand($linkList, 1);
        $nextLinkChar = substr($linkList[$nextLink], 0 , 1);
        $parsedNextLink = parse_url($nextLink);

        return array(
            'nextLink' => $nextLink,
            'nextLinkChar' => $nextLinkChar,
            'parsedNextLink' => $parsedNextLink
        );
    }

}
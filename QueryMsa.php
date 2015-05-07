<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Queries microsoft academics data at azure market place using the oData (v2)
 * restless structure for the URL.
 * Most oData operations are not allowed including extend (no relations are
 * expecified in the metadata) and count.
 *
 * @author 6opC4C3
 */

// MS Academics restricitions (Only documented for the old API (not azure datamarket)):
//  (1) Non-commercial uses of data
//  (2) 200 queries per minute (less than 300 is recommended for azure datamarket)
//  (3) 100 items per call are returned
//  (4) Cannot crawl the entire corpus


class QueryMsa {
    // The url cannot be longer of 2048 chars. Considering a fixed size per ID of 18 chars + the number (up to 6 chars) = 24 chars
    // plus the rest of the url approx. 100 chars
    // url_size(approx.) = MAX_RECORDS_PER_QUERY(80)*24+100 = 2020chars
    const MAX_RECORDS_PER_QUERY = 80; 
    private $msaKey = 'czbHGlnM+XsO89ECula7oXpWXFZ75qlzpxm402Spf7M';
    
    // Paper_Author?$count&$filter=AuthorID%20eq%20599994
    private function queryData($query){
        $jsonData = null;
        $url = 'https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/'.$query.'&$format=json';
        print("\nurl:\n".$url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->msaKey.':'.$this->msaKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        // Wait a bit to avoid reaching the limit of 200 requests per minute, set to a safe time
        sleep(1);
        $httpRespCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpRespCode == 200){
            $jsonData = json_decode($result);
        }
        return $jsonData;
    }
    
    private function joinIdsForFilter($idsArray){
        // Example: 'Paper?$filter=ID%20eq%2029%20or%20ID%20eq%2023%20&$format=json';
        $idsStr = '';
        $arrayCount = count($idsArray);
        $index = 1; // Start in one to account for the decrease to exclude the last element from adding the or condition
        foreach($idsArray as $id){
            if($index < $arrayCount){
                $idsStr = $idsStr.'ID%20eq%20'.$id.'%20or%20';
            }else{
                $idsStr = $idsStr.'ID%20eq%20'.$id;
            }
            $index++;
        }
        return $idsStr;
    }
    
    public function searchAuthorByName($name){
        $nameUrlSafe = urlencode($name);
        $query = 'Author?$filter=Name%20eq%20%27'.$nameUrlSafe.'%27';
        $jsonResults = $this->queryData($query);
        return $jsonResults;
    }
    
    // TODO Ensure that if not all the records are broght then functionality 
    // to query al least a number of defined entries is implemented
    public function searchAuthorPapers($authorMsaId, $skipOffset){
        $query = 'Paper_Author?$filter=AuthorID%20eq%20'.$authorMsaId;
        if($skipOffset > 0){
            $query = $query.'&$skip='.$skipOffset;
        }
        $jsonResults = $this->queryData($query);
        return $jsonResults;
    }
    
    public function searchPapers($papersIds){
        if(count($papersIds)<= 0){
            return array();
        }
        $idsStr = $this->joinIdsForFilter($papersIds);
        $query = 'Paper?$filter='.$idsStr;
        $jsonResults = $this->queryData($query);
        return $jsonResults;
    }
    
    public function searchPaperReferences($paperId, $skipOffset){
        if(isset($paperId)<= 0){
            return array();
        }
        $query = 'Paper_Ref?$filter=DstID%20eq%20'.$paperId;
        if($skipOffset > 0){
            $query = $query.'&$skip='.$skipOffset;
        }
        $jsonResults = $this->queryData($query);
        return $jsonResults;
    }
    
    public function searchJournals($journalIds){
        if(count($journalIds)<= 0){
            return array();
        }
        $idsStr = $this->joinIdsForFilter($journalIds);
        $query = 'Journal?$filter='.$idsStr;
        $jsonResults = $this->queryData($query);
        return $jsonResults;
    }
    
    public function searchConferences($conferenceIds){
        if(count($conferenceIds)<= 0){
            return array();
        }
        $idsStr = $this->joinIdsForFilter($conferenceIds);
        $query = 'Conference?$filter='.$idsStr;
        $jsonResults = $this->queryData($query);
        return $jsonResults;
    }
    
    public function searchAffiliations($affiliationIds){
        if(count($affiliationIds)<= 0){
            return array();
        }
        $idsStr = $this->joinIdsForFilter($affiliationIds);
        $query = 'Affiliation?$filter='.$idsStr;
        $jsonResults = $this->queryData($query);
        return $jsonResults;
    }
}

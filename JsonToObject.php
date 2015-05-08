<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JsonToObject
 *
 * @author 6opC4C3
 */
class JsonToObject {
    // This method can be used while searching by author without or with any
    // filter. The results always follow the same structure.
    public function toAuthors($jsonResults){
        $authors = array();
        $index = 0;
        try{
            $results = $jsonResults->{'d'}->{'results'};
            foreach($results as $result){
                $author = new Author();
                $author->details = new AuthorDetails();
                $author->name = $result->{'Name'};
                $author->details->msaId = $result->{'ID'};
                $author->details->affiliation = $result->{'Affiliation'};
                $author->details->msaAffiliationId = $result->{'AffiliationID'};
                $author->details->homepageUrl = $result->{'Homepage'};
                $author->details->version = $result->{'Version'};
                $author->details->researchInterest = $result->{'ResearchInterests'};
                $authors[$index++] = $author;
            }
        }catch(Exception $e){
            echo "Exception parsing json for author: ".$e->getMessage();
        }
        return $authors;
    }
    
    public function toAuthorPapers($jsonResults){
        $authorPapers = array();
        $index = 0;
        try{
            $results = $jsonResults->{'d'}->{'results'};
            foreach($results as $result){
                $authorPaper = new AuthorPaper();
                $authorPaper->msaPaperId = $result->{'PaperID'};
                $authorPaper->msaSeqId = $result->{'SeqID'};
                $authorPaper->msaAuthorId = $result->{'AuthorID'};
                $authorPapers[$index++] = $authorPaper;
            }
        }catch(Exception $e){
            echo "Exception parsing json for author papers: ".$e->getMessage();
        }
        return $authorPapers;
    }
    
    public function toAffiliations($jsonResults){
        $affiliations = array();
        $index = 0;
        try{
            $results = $jsonResults->{'d'}->{'results'};
            foreach($results as $result){
                $affiliation = new Affiliation();
                $affiliation->msaId = $result->{'ID'};
                $affiliation->officialName = $result->{'OfficialName'};
                $affiliation->homepage = $result->{'Homepage'};
                $affiliation->latitude = $result->{'Latitude'};
                $affiliation->longitude = $result->{'Longitude'};
                $affiliations[$index++] = $affiliation;
            }
        }catch(Exception $e){
            echo "Exception parsing json for affiliations: ".$e->getMessage();
        }
        return $affiliations;
    }
    
    public function toPapers($jsonResults){
        $papers = array();
        $index = 0;
        try{
            $results = $jsonResults->{'d'}->{'results'};
            foreach($results as $result){
                $paper = new Paper();
                $paper->msaId = $result->{'ID'};
                $paper->year = $result->{'Year'};
                $paper->title = $result->{'Title'};
                $paper->keyword = $result->{'Keyword'};
                $paper->msaConferenceId = $result->{'ConfID'};
                $paper->msaJournalId = $result->{'JourID'};
                $papers[$index++] = $paper;
            }
        }catch(Exception $e){
            echo "Exception parsing json for papers: ".$e->getMessage();
        }
        return $papers;
    }
    
    public function toPaperReferences($jsonResults){
        $paperRefs = array();
        $index = 0;
        try{
            $results = $jsonResults->{'d'}->{'results'};
            foreach($results as $result){
                $paperRef = new PaperReference();
                $paperRef->msaCitationId = $result->{'SrcID'};
                $paperRef->msaPaperId = $result->{'DstID'};
                $paperRef->msaSeqId = $result->{'SeqID'};
                $paperRefs[$index++] = $paperRef;
            }
        }catch(Exception $e){
            echo "Exception parsing json for paper refs: ".$e->getMessage();
        }
        return $paperRefs;
    }
    
    public function toJournals($jsonResults){
        $journals = array();
        $index = 0;
        try{
            $results = $jsonResults->{'d'}->{'results'};
            foreach($results as $result){
                $journal = new Journal();
                $journal->msaId = $result->{'ID'};
                $journal->fullname = $result->{'FullName'};
                $journal->homepage = $result->{'Homepage'};
                $journals[$index++] = $journal;
            }
        }catch(Exception $e){
            echo "Exception parsing json for journals: ".$e->getMessage();
        }
        return $journals;
    }
    
    public function toConferences($jsonResults){
        $conferences = array();
        $index = 0;
        try{
            $results = $jsonResults->{'d'}->{'results'};
            foreach($results as $result){
                $conference = new Journal();
                $conference->msaId = $result->{'ID'};
                $conference->fullname = $result->{'FullName'};
                $conference->homepage = $result->{'Homepage'};
                $conferences[$index++] = $conference;
            }
        }catch(Exception $e){
            echo "Exception parsing json for conferences: ".$e->getMessage();
        }
        return $conferences;
    }
    
}

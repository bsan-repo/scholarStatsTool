<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatsDao
 *
 * @author 6opC4C3
 */
class StatsDao {
    
    // Returned papers contain only the id and the conference msa id
    public function calculateStats(){
        $papers = array();
        try {
            $databaseName = 'a75a1';
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname='.$databaseName.';charset=utf8', 'root', 'root');
            
            // Collect the author - author_paper - paper_ref data only for those authors who's stats haven't been calcualted yet
            $stmt = $db->query('create temporary table tmp_author_paper_ref(author_id INTEGER, paper_id INTEGER, citation_id INTEGER)');
            $stmt->execute();
            $stmt = $db->query('insert into tmp_author_paper_ref(author_id, paper_id, citation_id) 
select a_papers.author_id, a_papers.paper_id, pr.citation_id from (select a_to_process.author_id as author_id, ap.paper_id as paper_id from (select a.id as author_id from author as a LEFT JOIN author_stats as ast on a.id = ast.author_id where ast.author_id is null) as a_to_process left join author_paper as ap on a_to_process.author_id = ap.author_id) as a_papers left join paper_ref as pr on a_papers.paper_id = pr.paper_id');
            $stmt->execute();
            /////////////////////////////////////////////////
            $stmt = $db->query('select * from tmp_author_paper_ref');
            $stmt->execute();
            $authorResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            /////////////////////////////////////////////////
            // Get the number of quality citations for each paper
            $stmt = $db->query('create temporary table tmp_author_paper_qcits(author_id INTEGER, paper_id INTEGER, citation_count INTEGER)');
            $stmt->execute();
            $stmt = $db->query('insert into tmp_author_paper_qcits(author_id, paper_id, citation_count) 
select apr.author_id, apr.paper_id, count(*) as qcits from tmp_author_paper_ref as apr LEFT JOIN paper as p on apr.citation_id = p.id where apr.citation_id is not null and (p.journal_id is not null or p.conference_id is not null) group by author_id, paper_id order by qcits DESC');
            $stmt->execute();
            /////////////////////////////////////////////////
            $stmt = $db->query('select * from tmp_author_paper_qcits');
            $stmt->execute();
            $authorResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            /////////////////////////////////////////////////
            // Calculate the total citations for each paper
            $stmt = $db->query('create temporary table tmp_author_paper_cits(author_id INTEGER, paper_id INTEGER, citation_count INTEGER)');
            $stmt->execute();
            $stmt = $db->query('insert into tmp_author_paper_cits(author_id, paper_id, citation_count) 
select apr.author_id, apr.paper_id, count(*) as cits from tmp_author_paper_ref as apr LEFT JOIN paper as p on apr.citation_id = p.id group by author_id, paper_id order by cits DESC');
            $stmt->execute();
            /////////////////////////////////////////////////
            $stmt = $db->query('select * from tmp_author_paper_cits');
            $stmt->execute();
            $authorResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            /////////////////////////////////////////////////
            // Get the ids for all the authors which stats to be calculated 
            $stmt = $db->query('select author_id from tmp_author_paper_qcits group by author_id');
            $stmt->execute();
            $authorResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            $statsCalculated = 0;
            
            print("Authors to search:\n");
            var_dump($authorResults);
            
            // CALCUALTE THE AUTHOR STATS
            $index = 0;
            foreach ($authorResults as $authorResult){
                print("Processing ".$authorResult['author_id']."\n");
                $authorId = $authorResult['author_id'];
                
                // CALCUALTE THE Q INDEX
                $stmt = $db->prepare('select citation_count from tmp_author_paper_qcits where author_id=? order by author_id, citation_count DESC');
                $stmt->execute(array($authorId));
                $qCitResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt = null;

                // Calculate the qindex using the result from the query
                $qindex = 0;
                foreach ($qCitResults as $qCitResult){
                    print("Citation: ".$qCitResult['citation_count']."\n");
                    $qcitations = $qCitResult['citation_count'];
                    if(($qindex + 1) <= $qcitations){
                        $qindex = $qindex + 1;
                    }else{
                        break;
                    }
                }
                print("qindex: ".$qindex."\n");
                
                //  Get the total number of quality citations.
                $stmt = $db->prepare('select sum(citation_count) as qcits_total from tmp_author_paper_qcits where author_id=?');
                $stmt->execute(array($authorId));
                $citsTotalResult = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt = null;
                $qCitationsTotal = 0;

                if (isset($citsTotalResult)){
                    $qCitationsTotal = $citsTotalResult['qcits_total'];
                }
                print("Q_Citation total: ".$qCitationsTotal."\n");
                
                // CALCUALTE THE H INDEX
                $stmt = $db->prepare('select citation_count from tmp_author_paper_cits where author_id=? order by author_id, citation_count DESC');
                $stmt->execute(array($authorId));
                $citResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt = null;

                // Calculate the qindex using the result from the query
                $hindex = 0;
                foreach ($citResults as $citResult){
                    print("Citation: ".$citResult['citation_count']."\n");
                    $citations = $citResult['citation_count'];
                    if(($hindex + 1) <= $citations){
                        $hindex = $hindex + 1;
                    }else{
                        break;
                    }
                }
                print("hindex: ".$hindex."\n");
                
                //  Get the total number of quality citations.
                $stmt = $db->prepare('select sum(citation_count) as cits_total from tmp_author_paper_cits where author_id=?');
                $stmt->execute(array($authorId));
                $citsResults = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt = null;
                $citationsTotal = 0;

                if (isset($citsResults)){
                    $citationsTotal = $citsResults['cits_total'];
                }
                print("Citation total: ".$citationsTotal."\n");
                
                //  Get the total number of publications.
                $stmt = $db->prepare('select count(id) as publications from author_paper where author_id = ?');
                $stmt->execute(array($authorId));
                $publicationsTotalResult = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt = null;
                $publications = 0;

                if (isset($publicationsTotalResult)){
                    $publications = $publicationsTotalResult['publications'];
                }
                print("publications: ".$publications."\n");
                
                //  Get the total number of q publications.
                $stmt = $db->prepare('select count(*) as qpublications from author_paper as ap left join paper as p on ap.paper_id=p.id where ap.author_id=? and (p.journal_id is not null or p.conference_id is not null)');
                $stmt->execute(array($authorId));
                $qPublicationsResult = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt = null;
                $qpublications = 0;

                if (isset($qPublicationsResult)){
                    $qpublications = $qPublicationsResult['qpublications'];
                }
                print("qPublicationsResult: ".$qpublications."\n");
                
                // Insert the author stats 
                $stmt = $db->prepare('insert author_stats(author_id, publications, qpublications, citations, qcitations, hindex, qhindex) values(?, ?, ?, ?, ?, ?, ?)');
                $affectedRows = $stmt->execute(array($authorId, $publications, $qpublications, $citationsTotal, $qCitationsTotal, $hindex, $qindex));
                $statsCalculated += $affectedRows;
                print(">>>>>>> INSERTING STATS: ".$affectedRows);
            }
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }
        return $papers;
    }
}

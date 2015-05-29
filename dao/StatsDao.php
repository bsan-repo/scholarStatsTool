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
            $databaseName = 'academic';
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname='.$databaseName.';charset=utf8', 'root', 'root');
            
            // Collect the author - author_paper - paper_ref data only for those authors who's stats haven't been calcualted yet
            $stmt1 = $db->prepare('create temporary table tmp_author_paper_ref(author_id INTEGER, paper_id INTEGER, citation_id INTEGER, in_qjournal BOOLEAN)');
            $stmt1->execute();
            $stmt1->closeCursor();
            $stmt2 = $db->prepare('insert into tmp_author_paper_ref(author_id, paper_id, citation_id, in_qjournal) select a_papers.author_id, a_papers.paper_id, pr.citation_id, pr.in_qjournal from (select author_id, paper_id from author_paper) as a_papers left join paper_ref as pr on a_papers.paper_id = pr.paper_id');
            $stmt2->execute();
            $stmt2->closeCursor();
            /////////////////////////////////////////////////
            $stmt3 = $db->prepare('select * from tmp_author_paper_ref');
            $stmt3->execute();
            $authorResults = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            var_dump($authorResults);
            $stmt3->closeCursor();
            /////////////////////////////////////////////////
            // Get the number of quality citations for each paper
            $stmt4 = $db->prepare('create temporary table tmp_author_paper_qcits(author_id INTEGER, paper_id INTEGER, citation_count INTEGER);');
            $stmt4->execute();
            $stmt4->closeCursor();
            $stmt5 = $db->prepare('insert into tmp_author_paper_qcits(author_id, paper_id, citation_count) select author_id, paper_id, count(*) as qcits from tmp_author_paper_ref where in_qjournal=1 group by author_id, paper_id order by qcits DESC');
            $stmt5->execute();
            $stmt5->closeCursor();
            /////////////////////////////////////////////////
            $stmt6 = $db->prepare('select * from tmp_author_paper_qcits group by author_id, paper_id;');
            $stmt6->execute();
            $authorResults = $stmt6->fetchAll(PDO::FETCH_ASSOC);
            var_dump($authorResults);
            $stmt6->closeCursor();
            /////////////////////////////////////////////////
            // Calculate the total citations for each paper
            $stmt7 = $db->prepare('create temporary table tmp_author_paper_cits(author_id INTEGER, paper_id INTEGER, citation_count INTEGER)');
            $stmt7->execute();
            $stmt7->closeCursor();
            $stmt8 = $db->prepare('insert into tmp_author_paper_cits(author_id, paper_id, citation_count) 
select author_id, paper_id, count(*) as cits from tmp_author_paper_ref where citation_id IS NOT NULL group by author_id, paper_id order by cits DESC');
            $stmt8->execute();
            $stmt8->closeCursor();
            /////////////////////////////////////////////////
            $stmt9 = $db->prepare('select * from tmp_author_paper_cits ');
            $stmt9->execute();
            $authorResults = $stmt9->fetchAll(PDO::FETCH_ASSOC);
            $stmt9->closeCursor();
            /////////////////////////////////////////////////
            // Get the ids for all the authors which stats to be calculated 
            $stmt10 = $db->prepare('select author_id from tmp_author_paper_qcits group by author_id');
            $stmt10->execute();
            $authorResults = $stmt10->fetchAll(PDO::FETCH_ASSOC);
            $stmt10->closeCursor();
            $statsCalculated = 0;
            
            print("Authors to search:\n");
            var_dump($authorResults);
            
            // CALCUALTE THE AUTHOR STATS
            $index = 0;
            foreach ($authorResults as $authorResult){
                print("Processing ".$authorResult['author_id']."\n");
                $authorId = $authorResult['author_id'];
                
                // CALCUALTE THE Q INDEX
                $stmt11 = $db->prepare('select citation_count from tmp_author_paper_qcits where author_id=:authorId order by author_id, citation_count DESC');
                $stmt11->bindValue(":authorId", $authorId);
                $stmt11->execute();
                $qCitResults = $stmt11->fetchAll(PDO::FETCH_ASSOC);
                $stmt11->closeCursor();
                
                $stmt11 = null;

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
                $stmt12 = $db->prepare('select sum(citation_count) as qcits_total from tmp_author_paper_qcits where author_id=:authorId');
                $stmt12->bindValue(":authorId", $authorId);
                $stmt12->execute();
                $citsTotalResult = $stmt12->fetch(PDO::FETCH_ASSOC);
                $stmt12->closeCursor();
                $stmt12 = null;
                $qCitationsTotal = 0;

                if (isset($citsTotalResult)){
                    $qCitationsTotal = $citsTotalResult['qcits_total'];
                }
                print("Q_Citation total: ".$qCitationsTotal."\n");
                
                // CALCUALTE THE H INDEX
                $stmt13 = $db->prepare('select citation_count from tmp_author_paper_cits where author_id=:authorId order by author_id, citation_count DESC');
                $stmt13->bindValue(":authorId", $authorId);
                $stmt13->execute();
                $citResults = $stmt13->fetchAll(PDO::FETCH_ASSOC);
                $stmt13->closeCursor();
                $stmt13 = null;

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
                $stmt14 = $db->prepare('select sum(citation_count) as cits_total from tmp_author_paper_cits where author_id=:authorId');
                $stmt14->bindValue(":authorId", $authorId);
                $stmt14->execute();
                $citsResults = $stmt14->fetch(PDO::FETCH_ASSOC);
                $stmt14->closeCursor();
                $stmt14 = null;
                $citationsTotal = 0;

                if (isset($citsResults)){
                    $citationsTotal = $citsResults['cits_total'];
                }
                print("Citation total: ".$citationsTotal."\n");
                
                //  Get the total number of publications.
                $stmt15 = $db->prepare('select count(id) as publications from author_paper where author_id = :authorId');
                $stmt15->bindValue(":authorId", $authorId);
                $stmt15->execute();
                $publicationsTotalResult = $stmt15->fetch(PDO::FETCH_ASSOC);
                $stmt15->closeCursor();
                $stmt15 = null;
                $publications = 0;

                if (isset($publicationsTotalResult)){
                    $publications = $publicationsTotalResult['publications'];
                }
                print("publications: ".$publications."\n");
                
                //  Get the total number of q publications.
                $stmt16 = $db->prepare('select count(*) as qpublications from author_paper as ap left join paper as p on ap.paper_id=p.id where ap.author_id=:authorId and (p.journal_id is not null or p.conference_id is not null)');
                $stmt16->bindValue(":authorId", $authorId);
                $stmt16->execute();
                $qPublicationsResult = $stmt16->fetch(PDO::FETCH_ASSOC);
                $stmt16->closeCursor();
                $stmt16 = null;
                $qpublications = 0;

                if (isset($qPublicationsResult)){
                    $qpublications = $qPublicationsResult['qpublications'];
                }
                print("qPublicationsResult: ".$qpublications."\n");
                
                // Insert the author stats 
                $stmt17 = $db->prepare('insert author_stats(author_id, publications, qpublications, citations, qcitations, hindex, qhindex) values(:authorId, :publications, :qpublications, :citationsTotal, :qCitationsTotal, :hindex, :qindex)');
                $stmt17->bindValue(":authorId", $authorId);
                $stmt17->bindValue(":publications", $publications);
                $stmt17->bindValue(":qpublications", $qpublications);
                $stmt17->bindValue(":citationsTotal", $citationsTotal);
                $stmt17->bindValue(":qCitationsTotal", $qCitationsTotal);
                $stmt17->bindValue(":hindex", $hindex);
                $stmt17->bindValue(":qindex", $qindex);
                $affectedRows = $stmt17->execute();
                $stmt17->closeCursor();
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

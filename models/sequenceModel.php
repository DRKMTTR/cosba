<?php

class sequenceModel extends Model{
    protected $_table = "sequences";
    protected  $_key = "IDSEQUENCE";
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getLibelle(){
        return "LIBELLE";
    }
    
    /**
     * Retourne la liste des sequences pour cette anneee academique
     * @param type $anneeacad
     */
    public function getSequences($anneeacad){
        $query = "SELECT s.* "
                . "FROM sequences s "
                . "INNER JOIN trimestres t ON t.IDTRIMESTRE = s.TRIMESTRE AND t.PERIODE = :anneeacad";
        return $this->query($query, ["anneeacad" => $anneeacad]);
    }
    
    public function getSequenceByDate($datejour){
        $query = "SELECT seq.*, tr.*, ann.*, seq.ORDRE AS SEQUENCEORDRE, tr.ORDRE AS TRIMESTREORDRE "
                . "FROM `" . $this->_table ."` seq "
                . "INNER JOIN trimestres tr ON tr.IDTRIMESTRE = seq.TRIMESTRE "
                . "INNER JOIN anneeacademique ann ON ann.ANNEEACADEMIQUE = tr.PERIODE "
                . "WHERE :datejour BETWEEN seq.DATEDEBUT AND seq.DATEFIN";
        return $this->row($query, ["datejour" => $datejour]);
    }
    public function getSequencesVerrouilles($anneeacad){
        $query = "SELECT s.* "
                . "FROM sequences s "
                . "INNER JOIN trimestres t ON t.IDTRIMESTRE = s.TRIMESTRE AND t.PERIODE = :anneeacad "
                . "WHERE s.VERROUILLER = 1";
        return $this->query($query, ["anneeacad" => $anneeacad]);
    }
}

<?php

class recapitulatifModel extends Model {

    protected $_table = "recapitulatifs";
    protected $_key = "IDRECAPITULATIF";

    public function __construct() {
        parent::__construct();
    }

    /**
     * Obtenir le bulletin recapitulatif des eleves de cette classe, a cette 
     * sequence
     * @param type $idclasse
     * @param type $idsequence
     */
    public function getRecapitulatifs($idclasse, $idsequence, $ideleve = "") {
        if (empty($ideleve)) {
            $query = "SELECT r.*, rb.*, seq.* "
                    . "FROM `" . $this->_table . "` r "
                    . "INNER JOIN recapitulatifs_bulletins rb ON rb.IDRECAPITULATIFBULLETIN = r.RECAPITULATIFBULLETIN "
                    . "AND rb.CLASSE = :idclasse  "
                    . "INNER JOIN sequences seq ON seq.IDSEQUENCE = rb.SEQUENCE "
                    . "WHERE rb.SEQUENCE <> :idsequence AND seq.ORDRE < ("
                    . "SELECT ORDRE FROM sequences WHERE IDSEQUENCE = :idsequence1) "
                    . "ORDER BY seq.ORDRE ASC";
            return $this->query($query, ["idclasse" => $idclasse, "idsequence" => $idsequence,
                        "idsequence1" => $idsequence]);
        } else {
            $query = "SELECT r.*, rb.*, seq.* "
                    . "FROM `" . $this->_table . "` r "
                    . "INNER JOIN recapitulatifs_bulletins rb ON rb.IDRECAPITULATIFBULLETIN = r.RECAPITULATIFBULLETIN "
                    . "AND rb.CLASSE = :idclasse  "
                    . "INNER JOIN sequences seq ON seq.IDSEQUENCE = rb.SEQUENCE "
                    . "WHERE r.ELEVE = :ideleve AND rb.SEQUENCE <> :idsequence AND seq.ORDRE < ("
                    . "SELECT ORDRE FROM sequences WHERE IDSEQUENCE = :idsequence1) "
                    . "ORDER BY seq.ORDRE ASC";
            return $this->query($query, ["idclasse" => $idclasse, "ideleve" => $ideleve,
                        "idsequence" => $idsequence, "idsequence1" => $idsequence]);
        }
    }

    /**
     * Obtenir le recapitulatif precedement 
     * @param int $idclasse
     * @param int $idsequence
     */
    public function getRecapitulatifBulletin($idclasse, $idsequence) {
        $query = "SELECT r.*, rb.* "
                . "FROM `" . $this->_table . "` r "
                . "INNER JOIN recapitulatifs_bulletins rb ON rb.IDRECAPITULATIFBULLETIN = r.RECAPITULATIFBULLETIN "
                . "WHERE rb.CLASSE = :idclasse AND rb.SEQUENCE = :sequence "
                . "ORDER BY r.ELEVE";
        return $this->query($query, ["idclasse" => $idclasse, "sequence" => $idsequence]);
    }

}

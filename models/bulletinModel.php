<?php

class bulletinModel extends Model {

    protected $_table = "";

    public function __construct() {
        parent::__construct();
    }

    /**
     * Retourne des enregistrements correspondant aux ligne des bulletin 
     * pour cette classe, cet eleve a cette sequence
     * @param type $idclasse
     * @param type $ideleve
     * @param type $idsequence
     * @param int $idgroupe filtrer par groupe, $idgroupe est le groupe auxquel apartient la matiere
     */
    public function getSequenceNotes($idclasse, $ideleve, $idsequence, $idgroupe) {
        /* $query = "SELECT ens.*, mat.*, prof.*, g.DESCRIPTION AS GROUPELIBELLE "
          . "FROM enseignements ens "
          . "INNER JOIN matieres mat ON mat.IDMATIERE = ens.MATIERE "
          . "INNER JOIN personnels prof ON prof.IDPERSONNEL = ens.PROFESSEUR "
          . "INNER JOIN groupe g ON g.IDGROUPE = ens.GROUPE "
          . "WHERE ens.CLASSE = :idclasse AND ens.GROUPE = :idgroupe";
         */

        $query = "SELECT n.*";
        $params = ["idclasse" => $idclasse, "idgroupe" => $idgroupe];
        return $this->query($query, $params);
    }

    public function getTrimestreNotes($idclasse, $ideleve, $idtrimestre) {
        
    }

    /**
     * Creai la table temporaire pour cette classe a cette sequence
     * @param type $idclasse
     * IDELEVE, IDENSEIGNEMENT, NOTE DP, NOTE DH
     * S'il est absent, alors, retourner VIDE comme note DP et note DH,
     * cette note sera non comptabiliser, s'il a zero ou null, alors comptabiliser)
     * s'il est present, mettre null et remplacer par 0
     */
    public function createTMPNoteTable($idclasse, $idsequence) {
        
        $this->query("DROP TABLE IF EXISTS tmp_notes;");
        $this->query("DROP TEMPORARY TABLE IF EXISTS tmp");

        $query = "CREATE TEMPORARY TABLE IF NOT EXISTS tmp ("
                . "SELECT el.IDELEVE, ens.*, gr.*, mat.*, pers.CIVILITE, pers.NOM, pers.PRENOM, "
                # Note DP
                . "(SELECT IF(n.ABSENT = 1, '', n.NOTE) FROM notes n "
                . "INNER JOIN notations nota ON nota.IDNOTATION = n.NOTATION "
                . "AND nota.TYPENOTE = 1 AND nota.SEQUENCE = :sequence1 "
                . "WHERE n.ELEVE = el.IDELEVE AND nota.ENSEIGNEMENT = ens.IDENSEIGNEMENT) AS DP, "
                # Note DH
                . "(SELECT IF(n.ABSENT = 1, '', n.NOTE) FROM notes n "
                . "INNER JOIN notations nota ON nota.IDNOTATION = n.NOTATION "
                . "AND nota.TYPENOTE = 2 AND nota.SEQUENCE = :sequence2 "
                . "WHERE n.ELEVE = el.IDELEVE AND nota.ENSEIGNEMENT = ens.IDENSEIGNEMENT) AS DH "
                . "FROM eleves el "
                . "INNER JOIN inscription i ON i.IDELEVE = el.IDELEVE AND i.IDCLASSE = :classe "
                . "LEFT JOIN enseignements ens ON ens.CLASSE = i.IDCLASSE "
                . "INNER JOIN groupe gr ON gr.IDGROUPE = ens.GROUPE "
                . "INNER JOIN matieres mat ON mat.IDMATIERE = ens.MATIERE "
                . "INNER JOIN personnels pers ON pers.IDPERSONNEL = ens.PROFESSEUR "
                . "ORDER BY ens.GROUPE, mat.BULLETIN"
                . ");";

        $this->query($query, ["classe" => $idclasse,
            "sequence1" => $idsequence, "sequence2" => $idsequence]);

        $query = "CREATE TABLE IF NOT EXISTS tmp_notes ("
                . "SELECT t.*, "
                . "CAST(IF(DP = '' OR DP IS NULL, IF(DH = '' OR DH IS NULL, '', DH), "
                . "IF(DH = '' OR DH IS NULL, DP, (DP+DH)/2)) AS DECIMAL(5,2)) AS MOYENNE "
                . "FROM tmp t ORDER BY t.BULLETIN"
                . ")";
        $this->query($query);
        $query = "ALTER TABLE `tmp_notes` ADD INDEX(`IDELEVE`, `IDENSEIGNEMENT`, `IDMATIERE`)";
        return $this->query($query);
        ##$this->pdo->exec("LOCK TABLES tmp_notes write;");
    }

    /**
     * Cette fonction utilise la table temporaire precedement cree pour 
     * obtenir les moyenne de classe, et classer les eleves
     */
    public function getNotesByEnseignements($idenseignement) {
        #Requete pour selectionner les notes sous la forme de 
        # ENSEIGNEMENT | DP | DH | MOY | COEF | TOTAL | RANG | MoyCl | MIN | MAX
            $query = "SELECT t.*,  "
                    . "COEFF * MOYENNE AS TOTAL, "
                    . "(SELECT AVG(MOYENNE) FROM tmp_notes t1 WHERE t1.IDENSEIGNEMENT = t.IDENSEIGNEMENT) AS MOYCL, "
                    . "(SELECT MIN(MOYENNE) FROM tmp_notes t2 WHERE t2.IDENSEIGNEMENT = t.IDENSEIGNEMENT) AS NOTEMIN, "
                    . "(SELECT MAX(MOYENNE) FROM tmp_notes t3 WHERE t3.IDENSEIGNEMENT = t.IDENSEIGNEMENT) AS NOTEMAX, "
                    . "IF(MOYENNE <=> @_last_moy, @curRang := @curRang, @curRang := @_sequence) AS RANG, "
                    . "@_sequence := @_sequence + 1, @_last_moy := MOYENNE "
                    . "FROM tmp_notes t, (SELECT @curRang := 1, @_sequence := 1, @_last_moy := 0) r "
                    . "WHERE t.IDENSEIGNEMENT = :enseignement "
                    . "ORDER BY MOYENNE DESC";
            return $this->query($query, ["enseignement" => $idenseignement]);
        } 

    /**
     * Obtenir le rang des eleves
     * Renvoie la liste des eleves, avec leur rang sequentielle et leur moyenne generale
     */
    public function getElevesRang(){
        $query = "SELECT IDELEVE, MOYGENERALE, POINTS, SUMCOEFF, "
                . "CASE WHEN @_last_moy = MOYGENERALE THEN @curRang ELSE @curRang := @_sequence END AS RANG, "
                . "@_last_moy := MOYGENERALE, @_sequence := @_sequence + 1 "
                . "FROM ("
                . "SELECT IDELEVE, SUM(MOYENNE*COEFF)/SUM(COEFF) AS MOYGENERALE, SUM(MOYENNE*COEFF) AS POINTS, "
                . "SUM(COEFF) AS SUMCOEFF "
                . "FROM tmp_notes GROUP BY IDELEVE ORDER BY MOYGENERALE DESC "
                . ") TOTALS, (SELECT @curRang := 1, @_last_moy := 0, @_sequence := 1) r";
        return $this->query($query);
    }
    /**
     * Obtenir la moygenne generale de la classe, obtenir la moyenne min, obtenir la moyenne max
     */
    public function getGlobalMoyenne() {
        $query = "SELECT AVG(MOYGENERALE) AS MOYCLASSE, MIN(MOYGENERALE) AS MOYMIN, "
                . "MAX(MOYGENERALE) AS MOYMAX "
                . "FROM (SELECT SUM(MOYENNE*COEFF)/SUM(COEFF) AS MOYGENERALE "
                . "FROM tmp_notes GROUP BY IDELEVE ORDER BY MOYGENERALE DESC) TOTALS ";
        return $this->row($query);
    }

    /**
     * Supprime la table temporaire precedement creer
     */
    public function dropTMPNoteTable() {
        #$this->pdo->exec("UNLOCK TABLES tmp_notes write;");
        $query = "DROP TABLE IF EXISTS tmp_notes;";
        return $this->query($query);
    }

}

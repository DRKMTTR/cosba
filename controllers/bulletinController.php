<?php

class bulletinController extends Controller {

    private $comboClasses;
    private $comboPeriodes;

    public function __construct() {
        parent::__construct();
        $this->loadModel("sequence");
        $this->loadModel("trimestre");
        $this->loadModel("classe");
        $this->loadModel("anneeacademique");
        $this->loadModel("inscription");
        $this->loadModel("eleve");
        $this->loadModel("enseignement");
        $this->loadModel("note");
        $this->loadModel("recapitulatif");
        $this->loadModel("notificationbulletin");
        $this->loadModel("message");
        $this->loadJPGraph();

        $classes = $this->Classe->selectAll();
        $this->comboClasses = new Combobox($classes, "comboClasses", $this->Classe->getKey(), ['NIVEAUSELECT', 'LIBELLE']);

        $periodes = $this->Anneeacademique->getPeriodes($this->session->anneeacademique);
        $this->comboPeriodes = new Combobox($periodes, "comboPeriodes", "IDPERIODE", "LIBELLE");
    }

    public function index() {
        
    }

    public function imprimer() {
        parent::printable();

        switch ($this->request->code) {
            case "0001":

                # Impression des bulletins individuelle
                if (!empty($this->request->comboEleves)) {
                    $this->bulletinIndividuelle();
                } else {
                    $codeperiode = substr($this->request->comboPeriodes, 0, 1);
                    if ($codeperiode == "S") {
                        $this->sequentielle();
                    } elseif ($codeperiode == "T") {
                        $this->trimestrielle();
                    } else {
                        $this->annuelle();
                    }
                }
                break;
            case "0002":
                break;
        }
    }

    /**
     * Bulletin sequentielle
     */
    public function sequentielle() {
        $view = new View();
        $view->Assign("pdf", $this->pdf);

        # Information generale sur la classe
        $idclasse = $this->request->comboClasses;
        $array_of_redoublant = $this->Classe->getRedoublants($idclasse, $this->session->anneeacademique, true);
        $view->Assign("array_of_redoublants", $array_of_redoublant);

        $classe = $this->Classe->get($idclasse);
        $view->Assign("classe", $classe);

        $inscrits = $this->Inscription->getInscrits($idclasse);
        $view->Assign("effectif", count($inscrits));
        $view->Assign("eleves", $inscrits);

        # Recuperer l'id de la periode
        $idperiode = substr($this->request->comboPeriodes, -1);
        $sequence = $this->Sequence->get($idperiode);

        $view->Assign("sequence", $sequence);

        $this->Bulletin->createTMPNoteTable($idclasse, $idperiode);

        $notes = array();
        # Ajouter les notes par matiere, cette variable contient aussi les notes des eleves
        $enseignements = $this->Enseignement->getEnseignements($idclasse);
        foreach ($enseignements as $ens) {
            $notes = array_merge($notes, $this->Bulletin->getNotesByEnseignements($ens['IDENSEIGNEMENT']));
        }

        $rangs = $this->Bulletin->getElevesRang();
        $view->Assign("rangs", $rangs);

        $view->Assign("notes", $notes);
        $travail = $this->Bulletin->getGlobalMoyenne();
        $view->Assign("travail", $travail);

        $this->Bulletin->dropTMPNoteTable();
        # Precedentes moyennes
        $recapitulatifs = $this->Recapitulatif->getRecapitulatifs($idclasse, $idperiode);
        $view->Assign("recapitulatifs", is_null($recapitulatifs) ? array() : $recapitulatifs);

        # Discripline des eleves de cette classe 
        $discipline = $this->Eleve->getDisciplines($idclasse, $sequence['DATEDEBUT'], $sequence['DATEFIN']);
        $view->Assign("discipline", $discipline);

        echo $view->Render("bulletin" . DS . "impression" . DS . "bulletin", false);
    }

    public function trimestrielle() {
        
    }

    public function annuelle() {
        
    }

    public function impression() {
        $view = new View();
        if (!empty($this->request->comboClasses)) {
            $this->imprimer();
        }
        $this->view->clientsJS("bulletin" . DS . "impression");
        $this->comboClasses->first = " ";
        $this->comboPeriodes->first = " ";
        $view->Assign("comboClasses", $this->comboClasses->view());
        $view->Assign("comboPeriodes", $this->comboPeriodes->view());

        $content = $view->Render("bulletin" . DS . "impression", false);
        $this->Assign("content", $content);
    }

    public function ajaximpression() {
        $view = new View();
        $json = array();

        $action = $this->request->action;
        switch ($action) {
            case "chargerEleves":
                $eleves = $this->Inscription->getInscrits($this->request->idclasse);
                $view->Assign("eleves", $eleves);
                $json[0] = $view->Render("bulletin" . DS . "ajax" . DS . "comboEleves", false);
                break;
        }

        echo json_encode($json);
    }

    public function bulletinIndividuelle() {
        $view = new View();
        $view->Assign("pdf", $this->pdf);

        $ideleve = $this->request->comboEleves;
        $eleve = $this->Eleve->get($ideleve);
        $view->Assign("eleve", $eleve);
        # Information generale sur la classe
        $classe = $this->Eleve->getClasse($ideleve, $this->session->anneeacademique);

        $array_of_redoublant = $this->Classe->getRedoublants($classe['IDCLASSE'], $this->session->anneeacademique, true);
        $view->Assign("array_of_redoublants", $array_of_redoublant);

        $view->Assign("classe", $classe);

        $inscrits = $this->Inscription->getInscrits($classe['IDCLASSE']);
        $view->Assign("effectif", count($inscrits));

        # Recuperer l'id de la periode
        $idperiode = substr($this->request->comboPeriodes, -1);
        $sequence = $this->Sequence->get($idperiode);

        $view->Assign("sequence", $sequence);

        $this->Bulletin->createTMPNoteTable($classe['IDCLASSE'], $idperiode);

        $notes = array();

        $enseignements = $this->Enseignement->getEnseignements($classe['IDCLASSE']);
        foreach ($enseignements as $ens) {
            $notes = array_merge($notes, $this->Bulletin->getNotesByEnseignements($ens['IDENSEIGNEMENT']));
        }

        $rangs = $this->Bulletin->getElevesRang();
        $view->Assign("rangs", $rangs);

        $view->Assign("notes", $notes);
        $travail = $this->Bulletin->getGlobalMoyenne();
        $view->Assign("travail", $travail);

        $this->Bulletin->dropTMPNoteTable();

        # Discripline des eleves de cette classe 
        $discipline = $this->Eleve->getDiscipline($ideleve, $sequence['DATEDEBUT'], $sequence['DATEFIN']);
        $view->Assign("discipline", $discipline);

        # Precedentes moyennes de l'eleve
        $recapitulatifs = $this->Recapitulatif->getRecapitulatifs($classe['IDCLASSE'], $idperiode, $ideleve);
        $view->Assign("recapitulatifs", is_null($recapitulatifs) ? array() : $recapitulatifs);

        echo $view->Render("bulletin" . DS . "impression" . DS . "individuelle", false);
    }

    public function notification() {
        $this->view->clientsJS("bulletin" . DS . "notification");
        $view = new View();
        $this->comboClasses->first = " ";
        $view->Assign("comboClasses", $this->comboClasses->view());

        $periodes = $this->Sequence->getSequencesVerrouilles($this->session->anneeacademique);
        $comboPeriodes = new Combobox($periodes, "comboPeriodes", $this->Sequence->getKey(), $this->Sequence->getLibelle());
        $comboPeriodes->first = " ";
        $view->Assign("comboPeriodes", $comboPeriodes->view());

        $notifications = $this->Notificationbulletin->selectAll();
        $view->Assign("notifications", $notifications);
        $content = $view->Render("bulletin" . DS . "notification", false);
        $this->Assign("content", $content);
    }

    public function ajaxNotification() {
        $action = $this->request->action;
        $view = new View();
        $json = array();
        $json[0] = "";
        switch ($action) {
            case "envoyerBulletin":
                $this->activateSMS();
                $idclasse = $this->request->idclasse;
                $periode = $this->request->periode;
                
                $sequence = $this->Sequence->get($periode);
                $inscrits = $this->Inscription->getInscrits($idclasse);
                $effectif = count($inscrits);

                $nbreparent = 0;
                $nbsms = 0;
                # Obtenir la liste des eleves de cette classe
                $eleves = $this->Recapitulatif->getRecapitulatifBulletin($idclasse, $periode);
                $retVal = false;
                foreach ($eleves as $el) {
                    $ret = $this->sendBulletin($el, $effectif, $sequence, $nbreparent, $nbsms);
                    if ($ret) {
                        $retVal = $ret;
                    }
                }
                $params = ["classe" => $idclasse,
                    "sequence" => $periode,
                    "nbreparent" => $nbreparent,
                    "nbremessage" => $nbsms,
                    "datenotification" => date("Y-m-d", time()),
                    "realiserpar" => $this->getConnectedUser()['IDPERSONNEL']];
                $this->Notificationbulletin->insert($params);
                $json[0] = $retVal;

            case "chargerNotification":
                if (empty($this->request->periode) && !empty($this->request->idclasse)) {
                    $notifications = $this->Notificationbulletin->findBy(["CLASSE" => $this->request->idclasse]);
                } elseif (!empty($this->request->periode) && !empty($this->request->idclasse)) {
                    $notifications = $this->Notificationbulletin->findBy(["CLASSE" => $this->request->idclasse,
                        "SEQUENCE" => $this->request->periode]);
                } else {
                    $notifications = $this->Notificationbulletin->selectAll();
                }
                $view->Assign("notifications", $notifications);
                $json[1] = $view->Render("bulletin" . DS . "ajax" . DS . "notification", false);
                break;
        }
        echo json_encode($json);
    }

    public function sendBulletin($el, $effectif, $seq, &$nbreparent, &$nbsms) {
        $eleve = $this->Eleve->get($el['ELEVE']);
        $responsables = $this->Eleve->getResponsables($eleve['IDELEVE']);

        $sms = $this->Message->getMessage("0008")['MESSAGE'];
        $params = [
            "#eleve " => $eleve['NOM'],
            "#seq " => $seq['ORDRE'],
            "#rang " => $el['RANG'] . "/" . $effectif,
            "#moyenne " => $el['MOYENNE'],
            "#moycl " => $el['MOYCLASSE'],
            "#maxi " => $el['MOYMAX']
        ];
        $message = $this->personnalize($params, $sms);
        $retVal = false;
       
        foreach ($responsables as $resp) {
            $tel = getRespNumPhone($resp);
            if (!empty($tel)) {
                $nbreparent++;
                $retVal = $this->send($tel, $message);
                if ($retVal) {
                    $nbsms++;
                    sleep(3);
                }
            }
        }
        return $retVal;
    }

}

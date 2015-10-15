<?php

class statistiqueController extends Controller {

    public function __construct() {
        parent::__construct();

        $this->loadModel("matiere");
        $this->loadModel("anneeacademique");
        $this->loadModel("sequence");
        $this->loadModel("trimestre");
    }

    public function couverture() {
        $this->view->clientsJS("statistique" . DS . "couverture");
        $view = new View();
        $matieres = $this->Matiere->selectAll();
        $comboMatieres = new Combobox($matieres, "comboMatieres", $this->Matiere->getKey(), $this->Matiere->getLibelle());
        $comboMatieres->first = " ";

        $view->Assign("comboMatieres", $comboMatieres->view());
        $content = $view->Render("statistique" . DS . "couverture", false);
        $this->Assign("content", $content);
    }

    public function bilan() {
        $this->view->clientsJS("statistique" . DS . "bilan");
        $view = new View();

        $content = $view->Render("statistique" . DS . "bilan", false);
        $this->Assign("content", $content);
    }

    public function ajaxbilan() {
        $action = $this->request->action;
        $view = new View();
        $json = array();
        switch ($action) {
            case "chargerDistribution":
                $sequences = $this->Sequence->getSequences($this->session->anneeacademique);
                $view->Assign("periode", $this->request->periode);
                $view->Assign("sequences", $sequences);
                $view->Assign("anneeacademique", $this->session->anneeacademique);

                $trimestres = $this->Trimestre->findBy(["PERIODE" => $this->session->anneeacademique]);
                $view->Assign("trimestres", $trimestres);

                $annee = $this->Anneeacademique->selectAll();
                $view->Assign("annee", $annee);

                $json[0] = $view->Render("appel" . DS . "ajax" . DS . "comboDistribution", false);
                break;
        }
        echo json_encode($json);
    }

    public function imprimer() {
        parent::printable();

        $code = $this->request->code;
        $view = new View();
        $view->Assign("pdf", $this->pdf);

        switch ($code) {
            # Impression du taux de couverture des programmes et heures
            case "0001":
                $this->pdf->isLandscape = true;
                echo $view->Render("statistique" . DS . "impression" . DS . "couverture", false);
                break;
            case "0002":
                # Impression du bilan global des resultats
                echo $view->Render("statistique" . DS . "impression" . DS . "bilanresultat", false);
                break;
        }
    }

}

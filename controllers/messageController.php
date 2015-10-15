<?php

/**
 * 307 : Envoi de SMS
 * 308 : Suivi de SMS
 */
class messageController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->loadModel("repertoire");
        $this->loadModel("personnel");
        $this->loadModel("messageenvoye");
    }

    public function suivi() {
        if (!isAuth(308)) {
            return;
        }
        $this->view->clientsJS("message" . DS . "suivi");
        $view = new View();
        $destinataires = $this->Repertoire->getDestinataires();
        $comboDestinataires = new Combobox($destinataires, "comboDestinataires", "PORTABLE", ["NOM", "PORTABLE"]);
        $comboDestinataires->first = "Tous les destinataires";
        $view->Assign("comboDestinataires", $comboDestinataires->view());

        $messages = $this->Messageenvoye->selectAll();
        $view->Assign("messages", $messages);
        $tableMessages = $view->Render("message" . DS . "ajax" . DS . "suivi", false);
        $view->Assign("tableMessages", $tableMessages);
        $content = $view->Render("message" . DS . "suivi", false);
        $this->Assign("content", $content);
    }

    public function ajaxsuivi() {
        $action = $this->request->action;
        $view = new View();
        $json = array();
        switch ($action) {
            case "supprimerMessageEnvoye":
                $this->Messageenvoye->delete($this->request->idmessage);
                $messages = $this->Messageenvoye->selectAll();
                break;
            case "filterParDestinataire":
                $destinataire = $this->request->destinataire;
                $datedebut = $this->request->datedebut;
                $datefin = $this->request->datefin;
                if(empty($destinataire) && empty($datedebut) && empty($datefin)){
                    $messages = $this->Messageenvoye->selectAll();
                }elseif(!empty($destinataire) && empty ($datedebut) && empty ($datefin)){
                    $messages = $this->Messageenvoye->findBy(["DESTINATAIRE" => $destinataire]);
                }else{
                    # Obtenir les messages par utilisateurs et pour une duree donnee
                    $messages = $this->Messageenvoye->getMessagesBy($destinataire, $datedebut, $datefin);
                }
                break;
        }
        $view->Assign("messages", $messages);
        $json[0] = $view->Render("message" . DS . "ajax" . DS . "suivi", false);
        echo json_encode($json);
    }

    public function envoi() {
        if (!isAuth(307)) {
            return;
        }
        $this->view->clientsJS("message" . DS . "envoi");
        $view = new View();

        if (!empty($this->request->message)) {
            # Envoyer le SMS et rediriger vers la page de suivi de SMS
            $this->activateSMS();
            $retval = $this->send($this->request->destinataire, $this->request->message);
            if ($retval === false) {
                $view->Assign("errors", true);
            } else {
                $view->Assign("errors", false);

                # Inserer dans la table message envoyes
                $personnel = $this->Personnel->getBy(["USER" => $this->session->iduser]);
                $params = [
                    "dateenvoie" => date("Y-m-d H:i:s", time()),
                    "destinataire" => $this->request->destinataire,
                    "expediteur" => $personnel['IDPERSONNEL'],
                    "message" => $this->request->message
                ];
                $this->Messageenvoye->insert($params);
            }
        }

        $destinataires = $this->Repertoire->getDestinataires();
        $view->Assign("destinataires", $destinataires);
        $content = $view->Render("message" . DS . "envoi", false);
        $this->Assign("content", $content);
    }

}

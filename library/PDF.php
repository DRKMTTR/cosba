<?php

class PDF extends TCPDF {

    private $logo;

    /**
     * Est ce que la page est en portrait, permet la modification apres le constructeur
     * @var type 
     */
    public $isLandscape = false;

    /**
     * Faut t-il certifier l'impression par le login de l'utilisateur?
     * Pour desactiver, se situer dans vue qui imprimer et saisir $pdf->bCertify = false;
     * @var type 
     */
    public $bCertify = true;

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4') {
        parent::__construct($orientation, $unit, $format, true, 'UTF-8', false);
        $this->fontpath = SITE_ROOT . "library/tcpdf/fonts";

        $this->logo = SITE_ROOT . "public/img/" . LOGO;

        # set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //$this->Cel
        #est margins
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        #$this->AddFont("xephyr", "", K_PATH_FONTS."Xephyr Shadow.ttf");
    }

    //Page header
    public function Header() {

        $header_gauche = <<<EOD
                <p style = "text-align:center;line-height: 10px">
                    REPUBLIQUE DU CAMEROUN<br/>
                    <i>Paix - Travail - Patrie</i><br/>
                    ________________________________<br/>
                    MINISTERE DES ENSEIGNEMENTS SECONDAIRES<br/>
                    SECRETARIAT A L'EDUCATION ADVENTISTE<br/>
                    ____________________________<br/>
                    <b>Complexe Scolaire Bilingue Adventiste<br/>
                    Jos&eacute;phine Nsangou</b><br/>
                    Tel. 234 19 02 45 - BP 96 - Foumbot<br/>
                    <i>Email : cosbafoumbot@yahoo.fr</i><br/>
                    _____________________<br/>
                    <i>Foi - Engagement - Succ&egrave;s</i>
                </p>
                        
EOD;
        $this->SetFontSize(9);

        $this->writeHTMLCell(90, 50, LEFT_UP_CORNER, 5, $header_gauche);

        //$this->writeHTML($header_gauche);
        if ($this->isLandscape) {
            $this->Image($this->logo, 130, 5, 30, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        } else {
            $this->Image($this->logo, 95, 5, 30, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }

        // Set font
        //$this->WriteHTML
        $header_droit = <<<EOD
                <p style = "text-align:center;line-height: 10px">
                    REPUBLIC OF CAMEROON<br/>
                    <i>Peace - Work - Fatherland</i><br/>
                    _______________________<br/>
                    MINISTRY OF SECONDARY EDUCATION<br/>
                    ADVENTIST EDUCATION SECRETARIAT<br/>
                    _______________________<br/>
                    <b>Jos&eacute;phine Nsangou<br/>
                    Adventist Bilingual Academic Complex</b><br/>
                    Tel. 243 19 02 45 - P.O. Box 96 - Foumbot<br/>
                    <i>Email : cosbafoumbot@yahoo.fr</i><br/>
                    ___________________________<br/>
                    <i>Faith - Commitment - Success</p>
EOD;
        if ($this->isLandscape) {
            $this->writeHTMLCell(0, 5, 240, 5, $header_droit);
        } else {
            $this->writeHTMLCell(150, 5, 100, 5, $header_droit);
        }
        $this->SetFont('helvetica', 'B', 20);
        // set document information
        $this->SetCreator("BAACK Group");
        $this->SetAuthor('BAACK Group');
        # set auto page breaks
        //$this->CEll
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        # $this->writeHTMLCell(50, 50, 20, 20, $this->GetY(), 1);
    }

    /**
     * Fonction defini comme entete pour les page en paysage,
     * le rendu par defaut qui est celui en portrait est defini dans la fonction Header
     */
    public function LandScapeHeader() {
        $header_gauche = <<<EOD
                <p style = "text-align:center;line-height: 10px">
                    REPUBLIQUE DU CAMEROUN<br/>
                    <i>Paix - Travail - Patrie</i><br/>
                    ________________________________<br/>
                    MINISTERE DES ENSEIGNEMENTS SECONDAIRES<br/>
                    SECRETARIAT A L'EDUCATION ADVENTISTE<br/>
                    ____________________________<br/>
                    <b>Complexe Scolaire Bilingue Adventiste<br/>
                    Jos&eacute;phine Nsangou</b><br/>
                    Tel. 234 19 02 45 - BP 96 - Foumbot<br/>
                    <i>Email : cosbafoumbot@yahoo.fr</i>
                    _____________________<br/>
                    <i>Foi - Engagement - Succ&egraves;</i>
                </p>
                        
                        
EOD;
        $this->SetFontSize(10);
        $this->writeHTMLCell(80, 30, 5, 5, $header_gauche);

        //$this->writeHTML($header_gauche);
        $this->Image($this->logo, 130, 5, 35, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        //$this->WriteHTML
         $header_droit = <<<EOD
                <p style = "text-align:center;line-height: 10px">
                    REPUBLIC OF CAMEROON<br/>
                    <i>Peace - Work - Fatherland</i><br/>
                    _______________________<br/>
                    MINISTRY OF SECONDARY EDUCATION<br/>
                    ADVENTIST EDUCATION SECRETARIAT<br/>
                    _______________________<br/>
                    <b>Jos&eacute;phine Nsangou<br/>
                    Adventist Bilingual Academic Complex</b><br/>
                    Tel. 243 19 02 45 - P.O. Box 96 - Foumbot<br/>
                    <i>Email : cosbafoumbot@yahoo.fr</i><br/>
                    ___________________________<br/>
                    <i>Faith - Commitment - Success</p>
EOD;
        $this->writeHTMLCell(150, 50, 230, 5, $header_droit);
        $this->SetFont('helvetica', 'B', 20);
        // set document information
        $this->SetCreator("BAACK Group");
        $this->SetAuthor('BAACK Group');

        # set auto page breaks
        //$this->CEll
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    }

    /**
     *  Page footer
     */
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-20);
        $line_width = (0.85 / $this->k);
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
        # $this->Cell(0, 0, "HUm", 'T', 0, 'R');
        // Position at 15 mm from bottom
        // Set font
        if ($this->bCertify) {

            $this->SetFont('helvetica', 'B', 7);
            $d = new DateFR();
            $signature = '<p style="text-align:right">Imprim&eacute; par : ' . $_SESSION['user'] . " / " . $d->getJour(3) . " " . $d->getDate()
                    . " " . $d->getMois(3) . " " . $d->getYear() . '</p>';
            $this->writeHTML($signature);
        }

        global $url; //$url est une variable globale defini dans Router.php
        global $bas_bulletin;
        $urlArray = explode("/", $url);
        if ($urlArray[0] === "bulletin") {
            $this->setY(-10);
            $this->setX(10);
            $this->setFont("Times", 'B', 7);

            $this->Cell(100, 0, 'INSTITUT POLYVALENT WAGUE ' . $_SESSION['anneeacademique'].".", 'T', 0, 'L');
            $this->Cell(70, 0, $bas_bulletin[0], 'T', 0, 'L');
            if ($bas_bulletin[1] === 1) {
                $this->Cell(0, 0, 'C', 'T', 0, 'R');
            } else {
                $this->Cell(0, 0, 'O', 'T', 0, 'R');
            }
        } else {
            // Page number
            $this->SetFont('helvetica', 'B', 8);
            $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }

    public function LandScapeFooter() {
        
    }

    public function getLogo() {
        return $this->logo;
    }

}

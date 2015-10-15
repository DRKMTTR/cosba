<?php
global $bas_bulletin;
$bas_bulletin[1] = $sequence['VERROUILLER'];
$pdf->SetPrintFooter(true);
# Desactiver le texte de signature pour les bulletins
$pdf->bCertify = false;
$pdf->AddPage();

$pdf->leftUpCorner = 10;

# Largeur des colonnes
$col = array();
$col[0] = 0;
$col[1] = 30;
$col[2] = 7;
$col[3] = 7;
$col[4] = 8;
$col[5] = 7;
$col[6] = 7;
$col[7] = 6;
$col[8] = 8;
$col[9] = 10;
$col[10] = 12;

#creer les trois groupes de matieres et envoyer cela a la vue
$tab = trierParGroupe($notes);
$groupe1 = $tab[0];
$groupe2 = $tab[1];
$groupe3 = $tab[2];
$array_of_redoublants = is_null($array_of_redoublants) ? array() : $array_of_redoublants;
$eff = 1;
# GRANDE BOUCLE POUR LES ELEVES DE LA CLASSE, EN COMMENCANT PAR LE 1er
# rang du precedent, utiliser pour determiner les execo
$prev = 0;

$style = array(
    'text' => true,
);

foreach ($rangs as $rang) {
    # Obtenir les autres infos de l'eleve
    foreach ($eleves as $el) {
        if ($el['IDELEVE'] == $rang['IDELEVE']) {
            break;
        }
    }
    $pdf->SetFont("Times", "B", 15);
    $y = PDF_Y;
    $pdf->RoundedRect(75, $y - 5, 75, 7, 2.0, '1111', 'DF', '', array(255, 255, 255));
    
    $titre = '<div>BULLETIN DE NOTES</div>';
    $pdf->WriteHTMLCell(0, 5, 85, $y - 5, $titre);
    $pdf->SetFont("Times", "B", 10);

    $annee = "Ann&eacute;e scolaire " . $_SESSION['anneeacademique'];
    $pdf->WriteHTMLCell(0, 5, 92, $y + 5, $annee);


# Le cadre pour la photo
    $photo = SITE_ROOT . "public/photos/eleves/" . $el['PHOTO'];

    if (!empty($el['PHOTO']) && file_exists(ROOT . DS . "public" . DS . "photos" . DS . "eleves" . DS . $el['PHOTO'])) {
        //ROOT . DS . "public" . DS . "photos" . DS . "eleves" . DS . 
        $pdf->Image($photo, 15, $y + 12, 20, 20, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
    } else {
        $pdf->WriteHTMLCell(20, 20, 15, $y + 12, '<br/><br/>PHOTO', 1, 2, false, true, 'C');
    }
    $pdf->Rect(37, $y + 12, 160, 20, 'DF');

    if (in_array($el['IDELEVE'], $array_of_redoublants)) {
        $redoublant = "OUI";
    } else {
        $redoublant = "NON";
    }
    $pdf->SetFont("Times", "", 9);
    $d = new DateFR($el['DATENAISS']);

    $matricule = 'Matricule&nbsp;: <b>' . $el['MATRICULE'] . '</b>';
    $pdf->WriteHTMLCell(0, 5, 37, $y + 13, $matricule);

    $nom = 'Nom&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b>' . $el['NOM'] . " " . $el['PRENOM'] . ' ' . $el['AUTRENOM'] . '</b>';
    $pdf->WriteHTMLCell(0, 5, 37, $y + 17, $nom);
    $naiss = "N&eacute; ";
    if($el['SEXE'] === "F"){
        $naiss = "N&eacute;e ";
    }
    $naiss .= "le &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b>"
            . $d->getDate() . " " . $d->getMois(3) . "-" . $d->getYear() . " &agrave; "
            . $el['LIEUNAISS'] . '</b>';
    $pdf->WriteHTMLCell(0, 5, 37, $y + 21, $naiss);

    #Adresse
    #classe
    $classelib = 'Classe&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; <b>' . $classe['NIVEAUHTML'] . '</b>';
    $pdf->WriteHTMLCell(50, 5, 165, $y + 13, $classelib);
    $effectiflib = 'Effectif&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; <b>' . $effectif . '</b>';
    $pdf->WriteHTMLCell(50, 5, 165, $y + 17, $effectiflib);
    $redo = "Redoublant &nbsp;:&nbsp; <b>" . $redoublant . '</b>';
    $pdf->WriteHTMLCell(50, 5, 165, $y + 21, $redo);

$pdf->setFontSize(13);
$pdf->WriteHTMLCell(0, 5, 95, $y + 33, '<div style="text-transform:uppercase">'.$sequence['LIBELLEHTML']."</div>");

    $pdf->setFontSize(8);
    # Table header
$nbMatieres = (count($groupe1) + count($groupe2) + count($groupe3))/$effectif;
$line_height = "11px";
if($nbMatieres >= 14){
    $line_height = "9px";
}
$corps = '<table border="0.5" cellpadding="0.5" style="line-height: '.$line_height.'"><thead>'
            . '<tr style="text-align:center;font-weight:bold; line-height: 15px;background-color:#444444;color:#FFF;">'
            . '<th border="0.5"  width="' . $col[1] . '%" style="text-align:left">&nbsp;&nbsp;Mati&egrave;res</th>'
            . '<th border="0.5" width="' . $col[2] . '%">DP</th>'
            . '<th border="0.5"  width="' . $col[3] . '%">DH</th><th border="0.5" width="' . $col[4] . '%">Moy</th>'
            . '<th border="0.5"  width="' . $col[5] . '%">Coef</th><th border="0.5"  width="' . $col[6] . '%">Total</th>'
            . '<th border="0.5" width="' . $col[7] . '%">Rang</th>'
            . '<th border="0.5"  width="' . $col[8] . '%">Moy.Cl</th>'
            . '<th border="0.5"  width="' . $col[9] . '%">Min/Max</th>'
            . '<th border="0.5"  width="' . $col[10] . '%">Appr&eacute;ciation</th></tr></thead><tbody>';

    # FAIRE UNE BOUCLE SUR LES GROUPES DE MATIERES
    $st1 = $sc1 = $st2 = $sc2 = 0;
    $corps .= getBody($groupe1, $col,$el, $st1, $sc1);
    $corps .= getBody($groupe2, $col, $el, $st2, $sc2);
    $corps .= printGroupe($st1 + $st2, $sc1 + $sc2, $col, "Groupe 1 + Groupe 2");
    $corps .= getBody($groupe3, $col, $el);

    $corps .= "</tbody></table>";
    $pdf->WriteHTMLCell(0, 5, 14, $y + 40, $corps);

    # RESUME DU TRAVAIL ACCOMPLI
    $pdf->setFontSize(7);
    $corps = printTravail($rang, $travail, $el, $prev);
    $prev = $rang['RANG'];
    $pdf->WriteHTMLCell(0, 5, 25, $y + 173, $corps);

    # Discripline
    foreach ($discipline as $disc) {
        if ($disc['IDELEVE'] == $el['IDELEVE']) {
            break;
        }
    }
    $corps = printDiscipline($disc);
    $pdf->WriteHTMLCell(0, 0, 25, $y + 188, $corps);
$pdf->setFont("helvetica", '', 8);

# Desinner la coube d'evolution
    $moyennes = getMoyennesRecapitulatives($recapitulatifs, $el['IDELEVE']);
    $moyennes[] = $rang['MOYGENERALE'];

    genererCourbe($moyennes, $el);
    $courbe = SITE_ROOT . "public/tmp/" . $el['IDELEVE'] . ".png";
    $pdf->Image($courbe, 18, $y + 200, 55, 40, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
    $filename = ROOT . DS . "public" . DS . "tmp" . DS . $el['IDELEVE'] . ".png";
    if (file_exists($filename)) {
        try{
            unlink($filename);
        }  catch (Exception $e){}
    }

    $pdf->StartTransform();
    $pdf->setFontSize(5);
# Ajouter la signature et l'heure d'impression
    $pdf->Rotate(90, 5, $y + 161);

    $pdf->WriteHTMLCell(0, 5, 20, $y + 166, "G&eacute;n&eacute;r&eacute; par BAACK @ IPW version 1.0<br/>" .
            date("d/m/Y ", time()) . "&agrave; " . date("H:i:s", time()));
    $pdf->StopTransform();
    $pdf->StartTransform();
    $pdf->Rotate(90, 40, $y + 185);
    $pdf->Write1DBarcode($el['MATRICULE'], 'C128A', 14, $y + 155, '', 10, 0.4);
    $pdf->StopTransform();

    $pdf->StartTransform();
    $pdf->Rotate(90, 15, $y + 171);
# Numero de la page
    $pdf->WriteHTMLCell(50, 5, 20, $y + 166, '<b>' . $rang['RANG'] . '/' . $effectif . '</b>');
    $pdf->StopTransform();

    $pdf->setFont("helvetica", '', 8);
    # Visa des parents
    $pdf->WriteHTMLCell(0, 5, 80, $y + 205, 'Visa des Parents');
    # Titulaire
    $pdf->WriteHTMLCell(0, 5, 125, $y + 205, 'Titulaire');
    # Le Directeur des etudes
    $pdf->WriteHTMLCell(100, 5, 165, $y + 205, 'Le Directeur des &eacute;tudes');
    $bas_bulletin[0] = $el['NOM'] . " " . $el['PRENOM'];
    
    $eff++;
    if ($eff <= $effectif) {
        $pdf->AddPage();
    }
}

$pdf->Output();

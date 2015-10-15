<?php

function enseignantAbsentByPeriode($date, $absences, $idclasse = "", $horaire = "") {
    $absents = array();
    if (empty($idclasse) && empty($horaire)) {
        foreach ($absences as $abs) {
            if ($abs['DATEJOUR'] == $date) {
                $absents[] = $abs;
            }
        }
        return $absents;
    }

    if (!empty($idclasse) && !empty($horaire)) {
        foreach ($absences as $abs) {
            if ($abs['HORAIRE'] == $horaire && $abs['IDCLASSE'] == $idclasse) {
                $absents[] = $abs;
            }
        }
        return $absents;
    }
    return null;
}

/**
 * Obtenir le lundi de cette date qui correspond a la semaine de cette date
 * utiliser dans l'emploi du temps
 * @param type $date 
 * 0 = Dimanche, 1 = Lundi, ....
 */
function getSemaineDu($date) {
    $days = 0;
    $dayofweek = date("w", strtotime($date));
    if ($dayofweek == 0) {
        $days = "+1 day";
    } else {
        $dayofweek--;
        $days = "-" . $dayofweek . " day";
    }

    return date("Y-m-d", strtotime($days, strtotime($date)));
}

/**
 * Identique au fichier Functions,
 * Extension de ce fichier
 * 
 */
function parseDate($f) {
    if (isset($f)) {
        if (strstr($f, "/") != FALSE) {
            list($d, $m, $y) = explode("/", $f);
            $fl = $y . "-" . $m . "-" . $d;
            return $fl;
        } else {
            return $f;
        }
    }
    return "0000-00-00";
}

function enseignantAbsent($idenseignant, $absences, $horaire = 0, $datejour = "") {
    if (empty($horaire) && !empty($datejour)) {
        foreach ($absences as $abs) {
            if ($abs['PERSONNEL'] == $idenseignant && $abs['DATEJOUR'] == $datejour) {
                return $abs;
            }
        }
    } elseif (empty($datejour)) {
        foreach ($absences as $abs) {
            if ($abs['PERSONNEL'] == $idenseignant && $abs['HORAIRE'] == $horaire) {
                return $abs;
            }
        }
    } else {
        foreach ($absences as $abs) {
            if ($abs['PERSONNEL'] == $idenseignant && $abs['HORAIRE'] == $horaire && $abs['DATEJOUR'] == $datejour) {
                return $abs;
            }
        }
    }
    return null;
}

function enseignantAbsentByClasse($date, $absences, $idclasse = "", $horaire = "") {
    $absents = array();
    if (empty($idclasse) && empty($horaire)) {
        foreach ($absences as $abs) {
            if ($abs['DATEJOUR'] == $date) {
                $absents[] = $abs;
            }
        }
        return $absents;
    }

    if (!empty($idclasse) && !empty($horaire)) {
        foreach ($absences as $abs) {
            if ($abs['HORAIRE'] == $horaire && $abs['IDCLASSE'] == $idclasse) {
                $absents[] = $abs;
            }
        }
        return $absents;
    }
    return null;
}

/**
 * 
 * @param type $derniereleve information sur le dernier eleve
 * @param type $matric sous la forme 16T pour une terminale en 2015-2016, 166 pour une sixieme en 2015-2016
 * @return string le matricule generer
 */
function genererMatricule($derniereleve, $matric) {
    $matricule = $matric;
    # Si un dernier eleve existe, alors concatener
    if ($derniereleve) {
        # Obtenir les trois dernier chiffre du matricule du dernier eleve
        $increment = substr($derniereleve['MATRICULE'], -3);
        $fin = intval($increment) + 1;
        if (strlen($fin) == 1) {
            $fin = "00" . $fin;
        } elseif (strlen($fin) == 2) {
            $fin = "0" . $fin;
        }
        $matricule = $matric . $fin;
    }
    # Sinon, alors il est le premier
    else {
        $matricule = $matric . "001";
    }
    return $matricule;
}

/**
 * Un tableau contenant les moyennes des eleves pour chaque sequences
 * 
 * @param type $moyennes = array()
 */
function genererCourbe($moyennes, $eleve) {
    try {
        # Donnees de la courbe
        $ydata = $moyennes;
        $ydata2 = $moyennes;

        /* for ($i = 1; $i <= 6; $i++) {
          $r = rand(0, 20);
          $ydata[] = $r;
          $ydata2[] = $r;
          } */

        /** Definition des label de l'axe x */
        $datax = array("seq 1", "seq 2", "seq 3", "seq 4", "seq 5", "seq 6");


        # Creation du graph
        $graph = new Graph(350, 250, 'auto');
        $graph->SetMarginColor('white');

        # Definir le max et le min des valeur X
        $graph->SetScale('textlin', 0, 20);

        #$graph->xaxis->title->Set("Séquences");
        $graph->yaxis->title->Set("Moyennes");
        $graph->xaxis->SetTickLabels($datax);
        $graph->xaxis->SetTitle("Séquences", "middle");
        $graph->SetBackgroundGradient('white', 'lightblue', GRAD_HOR, BGRAD_PLOT);

        # Adjuster les margins (left, right, top, bottom)
        $graph->SetMargin(40, 5, 21, 45);

        # Box autour du plotarea
        $graph->SetBox();

        # Un cadre ou frame autour de l'image
        $graph->SetFrame(false);

        # Definir le titre tabulaire
        $graph->tabtitle->SetFont(FF_ARIAL, FS_BOLD, 8);
        $graph->tabtitle->Set($_SESSION['anneeacademique']);
        # Definir le titre du graphe
        $graph->title->SetFont(FF_VERDANA, FS_NORMAL, 8);
        $graph->title->SetAlign("right");
        if (count($ydata) > 1) {
            $prev = $ydata[count($ydata) - 2];
            if ($prev < $ydata[count($ydata) - 1]) {
                $graph->title->Set("Performance en hausse");
            } elseif ($prev == $ydata[count($ydata) - 1]) {
                $graph->title->Set("Performance constante");
            } else {
                $graph->title->Set("Performance en baisse");
            }
        }


        # Definir les grid X et Y
        $graph->ygrid->SetFill(true, '#BBBBBB@0.9', '#FFFFFF@0.9');
        //$graph->ygrid->SetLineStyle('dashed');
        //$graph->ygrid->SetColor('gray');
        //$graph->xgrid->SetLineStyle('dashed');

        $graph->xgrid->SetColor('gray');
        $graph->xgrid->Show();
        //$graph->ygrid->Show();
        #$graph->SetBackgroundGradient('blue','navy:0.5',GRAD_HOR,BGRAD_MARGIN);
        $graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 8);
        $graph->xaxis->SetLabelAngle(0);

        # Creation d'une bar pot
        $bplot = new BarPlot($ydata);
        $bplot->SetWidth(0.9);
        $fcol = '#440000';
        $tcol = '#FF9090';

        $bplot->SetFillGradient($fcol, $tcol, GRAD_LEFT_REFLECTION);

        # Set line weigth to 0 so that there are no border around each bar
        $bplot->SetWeight(0);

        # Create filled line plot
        $lplot = new LinePlot($ydata2);
        $lplot->SetFillColor('skyblue@0.5');
        $lplot->SetStyle(1);
        $lplot->SetColor('navy@0.7');
        $lplot->SetBarCenter();

        $lplot->mark->SetType(MARK_SQUARE);
        $lplot->mark->SetColor('blue@0.5');
        $lplot->mark->SetFillColor('lightblue');
        $lplot->mark->SetSize(5);
        # Afficher les moyenne au dessus des barres
        $accbarplot = new AccBarPlot(array($bplot));
        $accbarplot->value->SetFormat("%.2f");
        $accbarplot->value->Show();
        $graph->Add($accbarplot);
        $graph->SetBackgroundImageMix(50);

        # Definir un fond d'ecran pour l'image
        $background = SITE_ROOT . "public/photos/eleves/" . $eleve['PHOTO'];
        if (!empty($eleve['PHOTO']) &&
                file_exists(ROOT . DS . "public" . DS . "photos" . DS . "eleves" . DS . $eleve['PHOTO'])) {
            $graph->SetBackgroundImage($background, BGIMG_FILLPLOT);

            # $icon = new IconPlot($background, 25, 25, 0.8, 50);
        } else {
            //$graph->SetBackgroundImage(SITE_ROOT . "public/img/". LOGO, BGIMG_FILLPLOT);
            # $icon = new IconPlot(SITE_ROOT . "public/img/ipw.png", 25, 25, 0.8, 50);
        }
        # $icon->SetAnchor('right', 'bottom');

        $graph->Add($lplot);


        // Display the graph
        $filename = ROOT . DS . "public" . DS . "tmp" . DS . $eleve['IDELEVE'] . ".png";
        if (file_exists($filename)) {
            unlink($filename);
        }
        $graph->Stroke($filename);
        //echo "<img src='" . SITE_ROOT . "public/tmp/emp.png' />";
    } catch (Exception $e) {
        var_dump($e);
    }
}

function getMoyennesRecapitulatives($recapitulatifs, $ideleve) {
    $moy = array();
    foreach ($recapitulatifs as $recap) {
        if ($recap['ELEVE'] == $ideleve) {
            $moy[] = $recap['MOYENNE'];
        }
    }
    return $moy;
}

/**
 * FUNCTION UTILISEES DANS LE BULLETIN
 */
function trierParGroupe($notes, $el = "") {
    $tab = array();
    $g1 = $g2 = $g3 = array();
    if (empty($el)) {
        foreach ($notes as $n) {
            if ($n['GROUPE'] == 1) {
                $g1[] = $n;
            } elseif ($n['GROUPE'] == 2) {
                $g2[] = $n;
            } else {
                $g3[] = $n;
            }
        }
    } else {
        foreach ($notes as $n) {
            if ($n['IDELEVE'] == $el['IDELEVE']) {
                if ($n['GROUPE'] == 1) {
                    $g1[] = $n;
                } elseif ($n['GROUPE'] == 2) {
                    $g2[] = $n;
                } else {
                    $g3[] = $n;
                }
            }
        }
    }
    $tab[0] = $g1;
    $tab[1] = $g2;
    $tab[2] = $g3;
    return $tab;
}

function printGroupe($sumtotal, $sumcoeff, $col, $libelle) {
    # Ecrire le GROUPE 1
    $backg = "#F7F7F7";
    if (strlen($libelle) > 10) {
        $backg = "#CCC";
    }
    $str = '<tr style="background-color:' . $backg . ';line-height:14px;text-align:center;font-weight:bold;">';
    $str .= '<td border="0.5" witdh="' . $col[1] . '%" style="text-align:left">&nbsp;&nbsp;' . $libelle . '</td>'
            . '<td border="0.5" colspan="3" width="' . ($col[2] + $col[3] + $col[4]) . '%">'
            . 'Points : ' . $sumtotal . " / " . ($sumcoeff * 20) . '</td>';

    # Moyenne totale du groupe 
    if ($sumcoeff != 0) {
        $moy = ($sumtotal) / $sumcoeff;
    } else {
        $moy = 0;
    }
    $str .= '<td border="0.5" width="' . $col[5] . '%">' . $sumcoeff . '</td>'
            . '<td style="text-align:left" border="0.5" colspan="4" width="' . ($col[6] + $col[7] + $col[8] +
            $col[9] + $col[10]) . '%">'
            . '&nbsp;&nbsp;Moyenne : ' . sprintf("%.2f", $moy) . '</td></tr>';
    return $str;
}

function printDiscipline($disc) {
    $cold = array();
    $cold[0] = 0;
    $cold[1] = 20;
    $cold[2] = $cold[3] = 8;
    $cold[4] = 10;
    $cold[7] = 8;
    $cold[6] = 8;
    $cold[5] = 15;
    $cold[8] = 25;
    $str = '<table style="text-align:center" cellpadding="2"><tr style="font-weight:bold">'
            . '<td width="' . $cold[1] . '%"  border="1">DISCIPLINE</td><td border="1" width="' . $cold[2] . '%" >Abs.Inj(H)</td>'
            . '<td width="' . $cold[3] . '%" border="1">Abs.J(H)</td><td width="' . $cold[4] . '%" border="1">Retards (H)</td>'
            . '<td width="' . $cold[5] . '%"  border="1">Avertissement</td><td width="' . $cold[6] . '%"  border="1">Bl&acirc;mes</td>'
            . '<td width="' . $cold[7] . '%"  border="1" style="font-size:8px">Exclu.(J)</td>'
            . '<td width="' . $cold[8] . '%"  border="1">Mention Conseil Classe</td></tr>';
    # Absences injustifiees
    $str .= '<tr><td></td><td border="1">' . $disc['ABSINJUST'] . '</td>';

    # Absence justifiees
    $str .= '<td border="1">' . $disc['ABSJUST'] . '</td>';

    #Retards
    $str .= '<td border="1">' . $disc['RETARDS'] . '</td>';

    #Avertissements
    $str .= '<td border="1">' . getAvertissements($disc['ABSINJUST']) . '</td>';

    # Blames
    $str .= '<td border="1">' . getBlames($disc['ABSINJUST']) . '</td>';

    #Exlusion
    #$disc['EXCLUSIONS']
    $str .= '<td border="1">' . getExclusions($disc['ABSINJUST']) . '</td>';

    #Mention conseil
    $str .= '<td border="1"></td>';
    $str .= '</tr></table>';
    return $str;
}

function printTravail($rang, $travail, $el, $prev) {
    $colt = array();
    $colt[0] = 0;
    $colt[1] = 20;
    $colt[2] = $colt[3] = 8;
    $colt[5] = 7;
    $colt[7] = $colt[8] = 8;
    $colt[4] = 10;
    $colt[6] = 8;
    $colt[9] = 25;
    $str = '<table style="text-align:center" cellpadding="2"><tr style="font-weight:bold">'
            . '<td width="' . $colt[1] . '%"  border="1">TRAVAIL</td><td width="' . $colt[2] . '%" border="1">Points</td>'
            . '<td width="' . $colt[3] . '%" border="1">Coef</td><td width="' . $colt[4] . '%" border="1">Moyenne</td>'
            . '<td width="' . $colt[5] . '%" border="1">Rang</td><td width="' . $colt[6] . '%" border="1">MOY.CL</td>'
            . '<td width="' . $colt[7] . '%" border="1">Min</td><td width="' . $colt[8] . '%" border="1">Max</td>'
            . '<td width="' . $colt[9] . '%" border="1">Mention</td></tr>';

    $str .= '<tr><td></td><td  border="1">' . sprintf("%.2f", $rang['POINTS']) . '</td>';
    $str .= '<td  border="1">' . $rang['SUMCOEFF'] . '</td>';
    # Moyenne generale
    $str .= '<td  border="1">' . sprintf("%.2f", $rang['MOYGENERALE']) . '</td>';

    # Rang sequentielle
    $expo = "<sup>&egrave;me</sup>";
    if ($rang['RANG'] == 1) {
        $expo = '<sup>' . ($el['SEXE'] == "F" ? "&egrave;re" : "er") . '</sup>';
    }
    $execo = "";
    if ($rang['RANG'] == $prev) {
        $execo = "ex";
    }
    $str .= '<td  border="1">' . $rang['RANG'] . $expo . ' ' . $execo . '</td>';

    # Moyenne generale de la classe
    $str .= '<td  border="1">' . sprintf("%.2f", $travail['MOYCLASSE']) . '</td>';

    #Moyenne Min de la classe
    $str .= '<td  border="1">' . sprintf("%.2f", $travail['MOYMIN']) . '</td>';

    #Moyenne Max de la classe

    $str .= '<td  border="1">' . sprintf("%.2f", $travail['MOYMAX']) . '</td>';

    # Mention en fonction de la moyenne generale
    $mention = getMentions($rang['MOYGENERALE']);
    if (strlen($mention) > 20) {
        $str .= '<td  border="1" style="font-size:5px">' . $mention . '</td>';
    } else {
        $str .= '<td  border="1" >' . $mention . '</td>';
    }
    $str .= '</tr></table>';
    return $str;
}

function getBody($groupe, $col, $el, &$sumtotal = 0, &$sumcoeff = 0) {
    $str = "";
    $sumdh = $sumdp = $summoy = 0;
    foreach ($groupe as $g) {
        if ($g['IDELEVE'] == $el['IDELEVE']) {
            $str .= '<tr style="text-align:center">';
            # Matiere
            //$g['NOM'] = preg_replace("%[^\033-\176\r\n\t]%", '', $g['NOM']);
            //$g['PRENOM'] = preg_replace("%[^\033-\176\r\n\t]%", '', $g['PRENOM']);

            $str .= '<td border="0.5" style="text-align:left" width="' . $col[1] . '%">&nbsp;&nbsp;<b>' . strtoupper($g['BULLETIN']) .
                    '</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:7px">'
                    .($g['CIVILITE'] . ' ' . $g['NOM'] . ' ' . $g['PRENOM']) . '</span></td>';

            # DP
            if ($g['DP'] != "" && $g['DP'] != null) {
                $str .= '<td border="0.5" width="' . $col[2] . '%">' . sprintf("%.2f", $g['DP']) . '</td>';
            } else {
                $str .= '<td border="0.5" width="' . $col[2] . '%"></td>';
            }
            # DH
            if ($g['DH'] != "" && $g['DH'] != null) {
                $str .= '<td border="0.5" width="' . $col[3] . '%">' . sprintf("%.2f", $g['DH']) . '</td>';
            } else {
                $str .= '<td border="0.5" width="' . $col[3] . '%"></td>';
            }
            # Moyenne DP et DH
            $str .= '<td border="0.5" width="' . $col[4] . '%">' . sprintf("%.2f", $g['MOYENNE']) . '</td>';
            # Coeff
            $str .= '<td border="0.5" width="' . $col[5] . '%">' . $g['COEFF'] . '</td>';

            # Total = coeff * moy
            $str .= '<td border="0.5" width="' . $col[6] . '%">' . sprintf("%.2f", $g['TOTAL']) . '</td>';
            # Rang
            $str .= '<td border="0.5" width="' . $col[7] . '%">' . $g['RANG'] . '</td>';

            #Moyenne de classe
            $str .= '<td border="0.5" width="' . $col[8] . '%">' . sprintf("%.2f", $g['MOYCL']) . '</td>';

            #Min / Max
            $str .= '<td border="0.5" width="' . $col[9] . '%">' . sprintf("%.2f", $g['NOTEMIN']) . ' / ' . sprintf("%.2f", $g['NOTEMAX']) . '</td>';

            #Appreciation
            $str .= '<td border="0.5" width="' . $col[10] . '%" style="text-align:left">&nbsp;&nbsp;' . getAppreciations($g['MOYENNE']) . '</td>';

            # Sommes
            $sumdh += $g['DH'];
            $sumdp += $g['DP'];
            $sumcoeff += $g['COEFF'];
            $sumtotal += $g['TOTAL'];
            $summoy += $g['MOYENNE'];

            $str .= '</tr>';
        }
    }
    if (!empty($groupe)) {
        $str .= printGroupe($sumtotal, $sumcoeff, $col, $g['DESCRIPTION']);
    }
    return $str;
}

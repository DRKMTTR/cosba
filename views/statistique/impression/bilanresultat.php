<?php

//$pdf->SetFont('helvetica', '', 10);

$y = FIRST_TITLE;
$x=5;
$pdf->AddPage();
$pdf->SetPrintHeader(false);
$pdf->SetFont("helvetica", "B", 10);
$matiere='MATHS';
$annee='2014/2015';
$trimestre=1;
$sequence =1;
$Date='Janviers';
$pdf->SetFont("helvetica", "B", 8);
$titre ='<p><b>DATE :</b>'.$Date.'</p>';
$pdf->WriteHTMLCell(0, 0, $x, $y+10 , $titre);
$titre ='<p><b>Annee scolaire :</b>'.$annee.'</p>';
$pdf->WriteHTMLCell(100, 0, $x+160, $y+10 , $titre);
$titre ='<p style="text-align:center"><b>BILAN GLOBAL DES RESULTATS</b><br>3 eme SEQUENCE</p>';
$pdf->WriteHTMLCell(0, 0, $x+10, $y , $titre);

$pdf->SetFont("Times", '', 6);
// sup &gt   inf &lt   é &eacute; è &egrave; ;


$corps = '<table style="text-align:center" cellpadding="2">
              <tr  border ="0.5"   style="font-weight:bold"  >
			      <td border="0.5"  width ="3%"   rowspan="3" >N°</td>
				  <td border="0.5"  width ="5%" rowspan="3" >Classe </td>
				  <td border="0.5"  width ="4%"  rowspan="3">&eacute;leves  <br>class&eacute;s</td>
				  <td border="0.5"  width ="4%" rowspan="3">Moy  <br>Classe</td>
				  <td border="0.5"  width ="4%" rowspan="3">Faible <br> moy</td>
				  <td border="0.5"  width ="4%" rowspan="3">Forte<br>moy</td>
				  <td border="0.5"  width ="7%"  colspan="2">Moy &lt; 7,5 </td>
				  <td border="0.5" colspan="2">Moy &lt;  10</td>
				  <td border="0.5" colspan="2">Moy &gt;=10</td>
				  <td border="0.5" colspan="2">Moy &gt;= 12</td>
				  <td border="0.5" colspan="12">Discipline</td>
				  <td border="0.5" >D&eacute;m</td>
				  <td border="0.5"  width ="5%" rowspan="3" align="center" >Effectif<br>r&eacute;el</td>
			  
			  </tr>
			  
			  <tr>  
			   		
			  '		
			  
			  ; 
			   
	for($i=1;$i<=4;$i++){		   
      $corps .= '<td border="0.5" rowspan="2">Nbre</td>
				<td border="0.5" rowspan="2">%</td>';
				}
				
	 $corps .=	'<td border="0.5" colspan="2">Absences</td>
				<td border="0.5" rowspan="2" colspan="2">Cons</td>
				<td border="0.5" rowspan="2" colspan="2">Avtc</td>
				<td border="0.5" rowspan="2"colspan="2">BlmC</td>
				<td border="0.5" rowspan="2"colspan="2">Ex.T</td>
				<td border="0.5" rowspan="2"colspan="2">Ex.D</td>
				<td border="0.5" rowspan="2">Nbre</td>
			  </tr>
			  <tr>
			    <td border="0.5">Nbre</td>
			    <td border="0.5">%</td>	
			  </tr>'; 
$corps .= '</table>';
$pdf->WriteHTMLCell(0, 0, $x, $y+15, $corps);		
$pdf->ln(1);	  
$corps = '<table style="text-align:center" cellpadding="2">
                  
			 <tr>
			    <td border="0.5" width ="12px"></td>
			    <td border="0.5" width ="27px"></td>
				<td border="0.5" width ="24px"></td>
			    <td border="0.5" width ="22px"></td>
				<td border="0.5" width ="22px"></td>
			    <td border="0.5" width ="22px"></td>
				<td border="0.5" width ="20px"></td>
			    <td border="0.5" width ="20px"></td>
				<td border="0.5" width ="20px"></td>
			    <td border="0.5" width ="20px"></td>
				<td border="0.5" width ="20px"></td>
			    <td border="0.5" width ="20px"></td>
				<td border="0.5" width ="20px"></td>
			    <td border="0.5" width ="20px"></td>
				<td border="0.5" width ="20px"></td>
			    <td border="0.5" width ="20px"></td>
				<td border="0.5" width ="40px"></td>
			    <td border="0.5" width ="40px"></td>
				<td border="0.5" width ="40px"></td>
			    <td border="0.5" width ="40px"></td>
				<td border="0.5" width ="40px"></td>
			    <td border="0.5" width ="20px"></td>
				<td border="0.5" width ="20px"></td>
				
			  </tr>';			  
$corps .= '</table>';			
$pdf->WriteHTMLCell(0, 0, $x, $y+40 , $corps);		
$pdf->Output();
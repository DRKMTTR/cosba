<table class="dataTable" id="droitTable">
    <thead><tr><th>Date</th><th>El&egrave;ve</th><th>Ref. Caisse</th><th>Description</th><th>Montant</th>
            <th></th></tr></thead>
    <tbody>
        <?php
        //var_dump($operations);
        $d = new DateFR();
        $montant = 0;
        foreach ($operations as $op) {
            $d->setSource($op['DATETRANSACTION']);
            
            # $type = ($op['TYPE'] == "C" ? "CREDIT" : "DEBIT");
            $type = $op['TYPE'];
            echo "<tr><td>" . $d->getDate() . '-' . $d->getMois(3) . "-" . $d->getYear(2) . "</td>"
            . "<td>" . $op['NOMEL'] . ' ' . $op['PRENOMEL'] . ".</td><td>" . $op['REFCAISSE'] . "</td>"
            . '<td>' . $op['DESCRIPTION'] . '</td><td align="right">' . moneyString($op['MONTANT']) . "</td>";
            
            echo "<td align='center'>";
            if(isAuth(532)){
                echo "<img style='cursor:pointer' src='".  img_valider()."' "
                      . "onclick=\"document.location='".Router::url("caisse", "restaurer", $op['IDCAISSE'])."'\" />";
            }else{
                echo "<img src='".  img_valider_disabled()."' />";
            }
            
            echo "</td></tr>";
            $montant += intval($op['MONTANT']);
        }
        ?>
        <tr><td></td><td style='font-weight: bold'>TOTAL</td><td></td><td></td>
            <td style="text-align: right"><?php echo moneyString($montant); ?></td>
        <td></td></tr>
    </tbody>
</table>
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable("#droitTable")) {
            $("#droitTable").DataTable({
                scrollY : $(".page").height() - 175,
                columns: [
                    {"width": "7%"},
                    null,
                    {"width": "12%"},
                    null,
                    {"width": "8%"},
                    {"width": "7%"}
                ]
            });
        }
    });
</script>

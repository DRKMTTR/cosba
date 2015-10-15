<style>
    #accordionNotes h3{
        padding: 2px 30px 2px;

    }

</style>

<div id="accordionNotes">
    <?php
    $str = "";
    foreach ($classes as $classe) {
        echo "<h3>" . $classe['NIVEAUHTML'] . " - " . $classe['ANNEEACADEMIQUE'] . "</h3>";
        $notes = $tableNotes[$classe['IDCLASSE']];
        $str = "#tableNote" . $classe['IDCLASSE'] . ",";
        echo "<div style='padding:2px;'><table class='dataTable' id='tableNote" . $classe['IDCLASSE'] . "'>";
        echo "<thead><tr><th>S&eacute;quences</th><th>Mati&egrave;res</th><th>Description</th>"
        . "<th>Date</th><th>Notes/Sur</th><th></th></tr></thead><tbody>";
        foreach ($notes as $n) {
            $d = new DateFR($n['DATEDEVOIR']);
            echo "<tr><td>" . $n['LIBELLEHTML'] . "</td><td>" . $n['BULLETIN'] . "</td><td>" . $n['DESCRIPTION'] . "</td>"
            . "<td>" . $d->getDate() . " " . $d->getMois(3) . " " . $d->getYear(2) . "</td>"
            . "<td align='right'>" . $n['NOTE'] . "/" . $n['NOTESUR'] . "</td>"
            . "<td align='center'><img style='cursor:pointer' src='" . img_info() . "' "
            . "onclick = \"tooltip_on(event,'" . $n['IDNOTATION'] . "')\" /></td></tr>";
        }
        echo "</tbody></table></div>";
    }
    $str = substr($str, 0, strlen($str) - 1);
    ?>
</div>
<script>
    $(document).ready(function () {
        $("#accordionNotes").accordion();
    });
    /*$("<?php echo $str; ?>").DataTable({
     bInfo: false,
     paging: false
     });*/

</script>
<?php foreach ($notations as $n) { ?>
    <div style="max-height: 150px; overflow: auto; left: 829px; top: 112px; display: none;font-size: 11px" 
         onmouseout="tooltip_off(<?php echo $n['IDNOTATION']; ?>)" onmouseover="tooltip_stop(<?php echo $n['IDNOTATION']; ?>)"
         class="edt_tooltip" id="tooltip<?php echo $n['IDNOTATION'] ?>">
        <p style="font-weight: bold"><?php echo $n['MATIERELIBELLE']; ?></p>
        <br><span style="width:100px; display:inline-block; font-weight:normal; text-decoration:underline;">Note sur :</span>
        <span style="width:45px; display:inline-block;"><b><?php echo $n['NOTESUR']; ?></b></span>

        <br><span style="width:100px; display:inline-block; font-weight:normal; text-decoration:underline;">Note mini :</span>
        <span style="width:35px; display:inline-block;"><?php echo $n['NOTEMIN'] ?></span>

        <br><span style="width:100px; display:inline-block; font-weight:normal; text-decoration:underline;">Note maxi :</span>
        <span style="width:35px; display:inline-block;"><?php echo $n['NOTEMAX'] ?></span>

        <br><span style="width:100px; display:inline-block; font-weight:normal; text-decoration:underline;">Note moyenne :</span>
        <span style="width:35px; display:inline-block;"><?php echo substr($n['NOTEMOYENNE'], 0, 4); ?></span>

    </div>
    <?php
}


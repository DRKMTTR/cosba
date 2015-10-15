<div id="entete">
    <div class="logo">

    </div>
    <div style="margin-left: 100px">
         <span class="select" style="width: 140px; clear: both;margin-top: 0"><label>P&eacute;riodes :</label>
            <select name="comboPeriodes"><option></option>
                <option value="1">Mensuelle</option>
                <option value="2">S&eacute;quentielle</option>
                <option value="3">Trimestrielle</option>
                <option value="4">Annuelle</option>
            </select></span>
        <span class="select" style="width: 145px; margin-top: 0"><label>Distribution : </label>
            <select name="comboDistributions"></select></span>
    </div>
</div>
<div class="page">

</div>
<div class="recapitulatif">

</div>
<div class="navigation">
    <div class="editions">
        <img src="<?php echo img_imprimer(); ?>" />&nbsp;Editions:
        <select onchange="imprimer();" name = "code_impression">
            <option></option>
            <option value="0002">Bilan global des resultats</option>
        </select>
    </div>
</div>
<div class="status"></div>
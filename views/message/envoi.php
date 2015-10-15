<div id="entete">

</div>
<div class="titre">
    Composition de message
</div>
<form action="<?php echo Router::url("message", "envoi"); ?>" method="post" name="frmenvoi">
    <div class="page">
        <fieldset style="float: none !important; width: 65%; margin: auto; height: 60%">
            <legend>Zone de saisie de message</legend>
            <span class="text" style="width: 80%"><label>Destinataire : </label>
                <input type="text" name="destinataire" list="listDestinataire" />
                <datalist id="listDestinataire">
                    <?php 
                    foreach($destinataires as $d){
                        echo "<option value='".$d['PORTABLE']."'>".$d['NOM']."</option>";
                    }
                    ?>
                </datalist>
            </span>
            <span class="text" style="width: 79%; clear: both; height: 90%"><label>Message : </label>
                <textarea name="message" rows="10" cols="3" ></textarea></span>
        </fieldset>
    </div>
    <div class="recapitulatif"></div>
    <div class="navigation">
        <?php
        echo btn_ok("envoyerSMS();");
        ?>
    </div>
</form>
<div class="status">
    <?php if (isset($errors)) { ?>
        <script>
            $(document).ready(function () {
                <?php
                if ($errors) {
                    echo "alertWebix('Message non envoy&eacute; <br/>Une erreur s\'est prouite');";
                } else {
                    echo "alertWebix('Message envoy&eacute; avec succ&egrave;s');";
                }
                ?>
            });
        </script>
    <?php }
    ?>
</div>
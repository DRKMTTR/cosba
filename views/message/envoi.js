function envoyerSMS() {
    var frm = $("form[name=frmenvoi]");
    removeRequiredFields([$("input[name=destinataire]"),$("textarea[name=message]")]);
    if ($("input[name=destinataire]").val() === "" || $("textarea[name=message]").val() === "") {
        
        addRequiredFields([$("input[name=destinataire]"),$("textarea[name=message]")]);
        alertWebix("Veuilez remplir tous les champs obligatoires");
        return;
    }
    frm.submit();
}
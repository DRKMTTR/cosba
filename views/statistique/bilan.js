$(document).ready(function () {
    $("select[name=comboPeriodes]").change(chargerDistribution);
});

chargerDistribution = function () {

    if ($("select[name=comboPeriodes]").val() === "") {
        return;
    }

    $.ajax({
        url: "./ajaxbilan",
        type: "POST",
        dataType: "json",
        data: {
            "periode": $("select[name=comboPeriodes]").val(),
            "action": "chargerDistribution"
        },
        success: function (result) {
            $("select[name=comboDistributions]").html(result[0]);
        },
        error: function (xhr, status, error) {
            alert("Une erreur s'est produite " + xhr + " " + error);
        }
    });
};

function imprimer() {
    if ($("select[name=code_impression]").val() === "") {
        return;
    }
    removeRequiredFields([$("select[name=comboPeriodes]"), $("select[name=comboDistributions]")]);
    if ($("select[name=comboPeriodes]").val() === "" && $("select[name=comboDistributions]").val() === "") {
        addRequiredFields([$("select[name=comboPeriodes]"), $("select[name=comboDistributions]")]);
        alertWebix("Veuillez remplir les champs obligatoires");
        $("select[name=code_impression]")[0].selectedIndex = 0;
        return;
    }
    var frm = $("<form>", {
        action: "./imprimer",
        target: "_blank",
        method: "post"
    }).append($("<input>", {
        name: "code",
        type: "hidden",
        value: $("select[name=code_impression]").val()
    })).append($("<input>", {
        name: "distribution",
        type: "hidden",
        value: $("select[name=comboDistributions]").val()
    })).appendTo("body");

    frm.submit();
}
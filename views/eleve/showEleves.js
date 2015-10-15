function ouvrirFiche(_ideleve) {
    var frm = $("<form>", {
        action: "../eleve",
        method: "post"
    }).append($("<input>", {
        name: "ideleve",
        type: "hidden",
        value: _ideleve
    })).appendTo("body");

    frm.submit();
}

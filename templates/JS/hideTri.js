function affichageTri() {
    let tri = document.getElementById("tri");
    let btnAfficher = document.getElementById("btnAfficher");
    let btnMasquer = document.getElementById("btnMasquer");

    if(getComputedStyle(tri).display !== "none"){
        tri.style.display = "none";
        btnAfficher.style.display = "block";
        btnMasquer.style.display = "none";

    } else {
        tri.style.display = "block";
        btnAfficher.style.display = "none";
        btnMasquer.style.display = "block";
    }
}


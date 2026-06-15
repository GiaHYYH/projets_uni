/**
 * Calcule, parmi une population totale, le nombre de Mandaloriens suivant encore la Voie des guerriers,
 * ainsi que le nombre de Mandaloriens pacifistes et loyaux à la République.
 */
function calculerPopulation() {
    let population = document.getElementById("popTotale").value;
    if (isNaN(population)) {
        alert("Vous devez rentrer une valeur numérique.")
    } else {
        let resMando = population * (40/100);
        let resEvaar = population * (60/100);
        document.getElementById("resultOldMando").value = resMando; 
        document.getElementById("resultNewMando").value = resEvaar;  
    }
}

/**
 * Fais en sorte que l'image du vaisseau X bouge.
 */
function mvtVaisseau() {
    let vaisseau = document.getElementById("imgVaisseau");
    let marginLeft = parseInt(window.getComputedStyle(vaisseau).marginLeft);
    vaisseau.style.marginLeft = (marginLeft + 100) + "px";
}

//Variable indiquant le numéro de la slides actuelle.
let sIndex = 0;

/**
 * Permet de passer au slide suivant dans un diaporama.
 * @param {number} n indiquant le nombre de slides que l'on doit passer. Peut être positif ou négatif.
 */
function nextSlide(n) {
    sIndex = sIndex + n;
    displaySlide();
}

//variable pour les diaporamas
let slides = document.getElementsByClassName("slides");

/**
 * Affiche une diapositive spécifique, tout en faisant de sorte que l'on aille pas cherche un slides qui n'existe pas.
 */
function displaySlide() {
    let i;

    if (sIndex >= slides.length) {
        sIndex = 0;
    }

    if (sIndex < 0) {
        sIndex = slides.length - 1;
    }

    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    slides[sIndex].style.display = "block";
}

/**
 * Affiche la diapositive dont le numéro est donné.
 * @param {number} n le numéro de la slides à afficher
 */
function showImage(n) {

    for (var i = 0; i < slides.length; i++) {
        slides[i].style.display = 'none';
    }

    if (n >= 0 && n < slides.length) {
        slides[n].style.display = 'block';
    } else {
        alert('Numéro de diapositive invalide !');
    }
}

//utilisation d'une fonction fléchée retarde l'exécution de nextSlide jusqu'à l'appel de setInterval
setInterval(() => nextSlide(1), 2000);

/**
 * Permet de vérifier et afficher les informations données par l'utilisateur dans le formulaire.
 */
function validateData() {
    let nom = document.getElementById("txtNom").value;
    let regexAlpha = /^[A-Za-z]+$/;
    let clan = document.getElementById("txtClan").value;
    let comp_combat = document.getElementById("cbCombat");
    let comp_ingenieur = document.getElementById("cbInge");
    let comp_diplomat = document.getElementById("cbDiplo");
    let comp_soins = document.getElementById("cbSoins");
    let cause = document.getElementById("txtCause").value;
    let serment = document.getElementById("serment");

    if (nom.length == 0 || !regexAlpha.test(nom)) {
        alert("Le nom est obligatoire et ne doit contenir que des caractères alphabétiques.")
    }

    if (clan.length == 0 || !regexAlpha.test(nom)) {
        alert("Le nom de votre clan est obligatoire et ne doit contenir que des caractères alphabétiques.")
    }

    if (cause.length == 0) {
        alert("Il est obligatoire que vous expliquiez les raisons de votre envie d'aider Mandalore.")
    }

    if (!serment.checked) {
        alert("Si vous ne jurer pas, votre aide ne sera pas acceptée.")
    }

    if (!comp_combat.checked && !comp_diplomat.checked && !comp_ingenieur.checked && !comp_soins.checked) {
        alert("Indiquez au moins une de vos compétences.")
    }

    let competences = [];
    let checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
    checkboxes.forEach(function(checkbox) {
        competences.push(checkbox.value);
    });

    //affichage d'un récapitulatif des données saisies
    let message = "Récapitulatif de votre inscription :\n";
    message += "Nom : " + nom + " du clan " + clan + "\n";
    message += "Competences offertes : " + competences + "\n";
    message += "Raison de l'offre d'aide : " + cause + "\n";
    message += "Mandalore et son peuple vous remercie de votre aide.\n Telle est la Voie."
    alert(message);
}

/**
 * Permet d'effacer les données rentrées par l'utilisateur.
 */
function clearForm() {
    volontaire.reset()
}
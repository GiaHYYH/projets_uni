//VARIABLES GLOBALES
let xmlhttp = new XMLHttpRequest();
let nbPage = 0;
let pageSize = 5;
let startIndex = 0;
let endIndex = 0;
let page = 1;
let totalQuestions = 0; //nb total de questions dans le XML
let allQuestions = []; //contient toutes les questions chargées non-filtrées
let currentQuestions = []; //contient toutes les questions chargées filtrées/triées

//CHARGEMENT DU XML
function loadXMLDoc() {
    /** 
        * charge le fichier XML contenant la base de données des questions
    */
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            fetchData();
            showPageLinks();
        }
    };
    xmlhttp.open("GET", "https://obiwan.univ-brest.fr/~e22403362/data/bdd_GE.xml", true);
    xmlhttp.send();
}

//EXTRACTION ET AFFICHAGE LES DONNÉES
function fetchData() {
    /**
        * extrait les données XML et génère le tableau XML initial
        * est appelée lors du premier chargement de la page
    */
    let i;
    let xmlDoc = xmlhttp.responseXML;
    allQuestions = Array.from(xmlDoc.getElementsByTagName("question")); //création d'un tableau à partir de la liste des nodes "questions"
    currentQuestions = allQuestions.slice(); //copie du tab complet dans celui des questions actuelles
    let table = "<tr><th>Thematique</th><th>Question</th><th>Réponse1</th><th>Réponse2</th><th>Réponse3</th><th></th></tr>";
    let x = xmlDoc.getElementsByTagName("question");
    totalQuestions = x.length;

    nbPage = Math.ceil(totalQuestions / pageSize);

    startIndex = (page - 1) * pageSize; //calcul l'index de la première question affichée
    endIndex = Math.min(startIndex + pageSize, totalQuestions); //calcul de l'index de la dernière question affichée

    //boucle d'affichage du tableau dans le HTML
    for (i = startIndex; i < endIndex; i++) {
        table += "<tr><td>" +
        x[i].getElementsByTagName("thematique")[0].textContent +
        "</td><td>" +
        x[i].getElementsByTagName("contenu")[0].textContent +
        "</td><td>" +
        x[i].getElementsByTagName("reponse1")[0].textContent +
        "</td><td>" +
        x[i].getElementsByTagName("reponse2")[0].textContent + 
        "</td><td>" +
        x[i].getElementsByTagName("reponse3")[0].textContent +
        "</td><td>" +
        "<a href='detail.html?id=" +
        x[i].getElementsByTagName("id")[0].textContent +
        "'>Details</a>" +          
        "</td>" +                       
        "</tr>";
    }
    document.getElementById("data").innerHTML = table;
} 

//FONCTION DE NAVIGATION ENTRE LES PAGES
function loadPage(pageNumber) {
    /**
        * charge et affiche une page spécifique de querstions
        * @param {number} pageNumber - le numéro de la page à afficher
    */
    page = pageNumber;
    drawQuestionTable(currentQuestions); //redessine le tableau avec les questions de la nouvelle page
    showPageLinks(); //mise à jour des boutons de pagination
}   

//AFFICHAGE DES LIENS DE PAGINATION
function showPageLinks() {
    /**
        * génère et affiche les boutons de pagination numérotés
    */
    let i;
    let pageLinks = "";
    let divpl = document.getElementById("pageLinks"); //récupération du div qui contiendra les bontons
    divpl.style.display = "block"; //affichage du conteneur de pagination (au cas ou il est caché)
    for (i = 1; i <= nbPage; i++) {
        pageLinks += "<input type='button' class='btn btn-outline-dark' onclick='loadPage(" + i
        +")' value='"+i+"'></input>";
    }
    divpl.innerHTML = pageLinks;
}

//FONCTION DE FILTRAGE PAR THÉMATIQUE
function filtrerData() {
    /**
        * filtre les questions selon la thématique sélectionnée dans le menu
        * met à jour l'affichage avec uniquement les questions correspondantes
    */
    let sThematique = document.getElementById("thematique"); //récupération de l'élément select
    let theme = sThematique.options[sThematique.selectedIndex].value; //extraction de la valeur de l'option sélectionnée
    let i;
    let xmlDoc = xmlhttp.responseXML;
    let x = xmlDoc.getElementsByTagName("question");
    currentQuestions = []; //réinitialisation du tableau (rempli dans la boucle)
    for (i = 0; i < x.length; i++) {
        let questionTheme = x[i].getElementsByTagName("thematique")[0].textContent;
        if (questionTheme == theme) {
            currentQuestions.push(x[i]);
        }
    }

    nbPage = Math.ceil(currentQuestions.length / pageSize); //recalcul du nombre de pages
    page = 1; //retour à la première page
    
    drawQuestionTable(currentQuestions); //redessinage du tableau avec les question filtrées
    showPageLinks();
}

//FONCTION DE RECHERCHE TEXTUELLE
function searchQuestions() {
    /**
         * recherche les questions contenant le texte saisi par l'utilisateur
         * la recherche s'effectue dans les champs : contenu, thematique, difficulte et reponses
         * non-sensible a la casse
     */
    let searchInput = document.getElementById("searchInput").value.toLowerCase().trim(); //le texte est converti en minuscules, et les espaces de début et de fin ont supprimés

    //toutes les questions sont affichées si le champ de recherche est vide
    if (searchInput === "") {
        currentQuestions = allQuestions;
    } else {

        //création d'un nouveau tableau avec les éléments qui passent le test (filter())
        currentQuestions = allQuestions.filter(function(question) {
            let questionText = question.getElementsByTagName("contenu")[0].textContent.toLowerCase();
            let theme = question.getElementsByTagName("thematique")[0].textContent.toLowerCase();
            let diff = question.getElementsByTagName("difficulte")[0].textContent.toLowerCase();
            let rep1 = question.getElementsByTagName("reponse1")[0].textContent.toLowerCase();
            let rep2 = question.getElementsByTagName("reponse2")[0].textContent.toLowerCase();
            let rep3 = question.getElementsByTagName("reponse3")[0].textContent.toLowerCase();

            //retourne true si le texte recherché est trouvé dans au moins un des champs
            //includes() vérifie si une chaîne contient une sous-chaîne
            return (
                questionText.includes(searchInput) ||
                theme.includes(searchInput) ||
                diff.includes(searchInput) ||
                rep1.includes(searchInput) ||
                rep2.includes(searchInput) ||
                rep3.includes(searchInput)
            );
        });
    }

    nbPage = Math.ceil(currentQuestions.length / pageSize);
    page = 1;
    drawQuestionTable(currentQuestions);
    showPageLinks();
}

//FONCTION DESSIN DE TABLEAU
function drawQuestionTable(list) {
    /**
         * génère et affiche le tableau HTML des question à partir d'une liste donnée en paramètre
         * @param {Array} list - tablea d'éléments XML <question> à afficher
     */
    let table = "<tr><th>Thematique</th><th>Question</th><th>Réponse1</th><th>Réponse2</th><th>Réponse3</th><th></th></tr>";

    let start = (page - 1) * pageSize; //index de début pour la page courante
    let end = Math.min(start + pageSize, list.length); //index de fin (sans dépasser la longueur de la liste)

    for (let i = start; i < end; i++) {
        let q = list[i];
        table += "<tr><td>" +
        q.getElementsByTagName("thematique")[0].textContent +
        "</td><td>" +
        q.getElementsByTagName("contenu")[0].textContent +
        "</td><td>" +
        q.getElementsByTagName("reponse1")[0].textContent +
        "</td><td>" +
        q.getElementsByTagName("reponse2")[0].textContent +
        "</td><td>" +
        q.getElementsByTagName("reponse3")[0].textContent +
        "</td><td>" +
        "<a href='detail.html?id=" +
        q.getElementsByTagName("id")[0].textContent +
        "'>Details</a>" +          
        "</td>" +                       
        "</tr>";
    }

    document.getElementById("data").innerHTML = table;
}

//FONCTION DE COMPARAISON POUR LE TRI
function compareQuestionByContent(a, b) {
    /**
         * fonction de comparaison utilisée dans la fonction sort()
         * compare deux question selon leur contenu (ordre alphabétique)
         * 
         * @param {Element} a - premier élément XML <question>
         * @param {Element} b - deuxième élément XML <question>
         * @returns {number} -1 si a < b, 1 si a > b, 0 si égaux
    */
    let contentA = a.getElementsByTagName("contenu")[0].textContent;
    let contentB = b.getElementsByTagName("contenu")[0].textContent;
    if (contentA < contentB) {
        return -1;
    }
    if (contentA > contentB) {
        return 1;
    }
    return 0;
}

//FONCTION DE TRI DES QUESTIONS
function sortData() {
    /**
         * trie les questions courantes par ordre alphabétique de leur contenu
         * utilise la fonction compareQuestionByContent comme critère de tri
         * réaffiche ensuite le tableau trié à parir de la première page
     */
    currentQuestions.sort(compareQuestionByContent); //tri en place
    nbPage = Math.ceil(currentQuestions.length / pageSize); //recalcul du nombre de pages (normalement inchangé, mais par sécurité)
    page = 1;
    drawQuestionTable(currentQuestions);
    showPageLinks();
}
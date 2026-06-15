//VARIABLES GLOBALES
let xmlhttp = new XMLHttpRequest();
let nbPage = 0;
let pageSize = 10;
let startIndex = 0;
let endIndex = 0;
let page = 1;
let totalQuestions = 0; //nombre total de questions
let allQuestions = []; //tableau contenant toutes les questions chargées depuis le XML (non modifiée)
let currentQuestions = []; //tableau contenant les questions actuellement affichées
let sQuestions = new Set(); //Set contenant les IDs des questions sélectionnées via les checkboxes; pour éviter les doublons et permettre opérations rapides

//CHARGEMENT DU XML
function loadXMLDoc() {
    /**
        * charge le fichier XML
        * déclenche l'affichage initial et la pagination
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

//EXRACTION ET AFFICHAGE INITIAL
function fetchData() {
    /**
         * extrait les données du XML et génère le tableau HTML initial
     */
    let i;
    let xmlDoc = xmlhttp.responseXML;
    allQuestions = Array.from(xmlDoc.getElementsByTagName("question")); //conversion de la NodeList en Array pour faciliter les manipulations
    currentQuestions = allQuestions.slice(); //copie superficielle
    let table = "<tr><th>Thematique</th><th>Question</th><th>Réponse1</th><th>Réponse2</th><th>Réponse3</th><th></th></tr>";
    let x = xmlDoc.getElementsByTagName("question");
    totalQuestions = x.length;

    nbPage = Math.ceil(totalQuestions / pageSize);

    startIndex = (page - 1) * pageSize;
    endIndex = Math.min(startIndex + pageSize, totalQuestions);

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
            "</td><td>";
        
        table += "<input type='checkbox' onclick='setQuestion(event)' value='" + x[i].getElementsByTagName("id")[0].textContent + "'";
        //onclick='setQuestion(event)' = appel de la fonction de gestion lors du clic

        //vérification si cette question est déjà dans l'ensemble des questions sélectionnées. si oui, on coche la checkbox automatiquement (persistance de la sélection)
        if (sQuestions.has(x[i].getElementsByTagName("id")[0].textContent)) {
            table += " checked='true'";
        }

        table += ">";

        table += "</td></tr>";
    }
    document.getElementById("data").innerHTML = table;
}

//FONCTION DE LA NAVIGATION ENTRE LES PAGES
function loadPage(pageNumber) {
    /**
         * charge une page spécifique de questions
         * met à jour le numéro de page et réaffiche le tableau
         * 
         * @param {number} pageNumber - le numéro de la page à afficher
     */
    page = pageNumber;
    fetchData();
    showPageLinks();
}   

//FONCTION D'AFFICHAGE DE LA PAGINATION
function showPageLinks() {
    /**
         * génère et affiche les boutons de pagination numérotés
     */
    let i;
    let pageLinks = "";
    let divpl = document.getElementById("pageLinks"); //récupération du div pour les liens de pagination
    divpl.style.display = "block"; //affichage du conteneur (au cas où il serait caché)
    for (i = 1; i <= nbPage; i++) {
        pageLinks += "<input type='button' class='btn btn-outline-dark' onclick='loadPage(" + i
        +")' value='"+i+"'></input>";
    }
    divpl.innerHTML = pageLinks;
}

//FONCTION DE RECHERCHE TEXTUELLE
function searchQuestions() {
    /**
         * filtre les questions selon une recherche textuelle, qui s'effectue dans contenu, thématique, difficulté et les 3 réponses
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

//FONCTION DE DESSIN DU TABLEAU
function drawQuestionTable(list) {
    /**
         * génère et affiche le tableau HTML à partir d'une liste de questions (réutilisable)
         * 
         * @param {Array} list - Tableau d'éléments XML <question> à afficher
     */
    let table = "<tr><th>Thematique</th><th>Question</th><th>Réponse1</th><th>Réponse2</th><th>Réponse3</th><th></th></tr>";

    let start = (page - 1) * pageSize;
    let end = Math.min(start + pageSize, list.length);

    for (let i = start; i < end; i++) {
        let q = list[i];//question actuelle
        table += "<tr>" +    
        "<td>" + q.getElementsByTagName("thematique")[0].textContent + "</td>" +
        "<td>" + q.getElementsByTagName("contenu")[0].textContent + "</td>" +
        "<td>" + q.getElementsByTagName("reponse1")[0].textContent + "</td>" +
        "<td>" + q.getElementsByTagName("reponse2")[0].textContent + "</td>" +
        "<td>" + q.getElementsByTagName("reponse3")[0].textContent + "</td>";
        
        //mettre la checkbox dans le <td>
        table += "<td>";
        table += "<input type='checkbox' onclick='setQuestion(event)' value='" + q.getElementsByTagName("id")[0].textContent + "'";
        if (sQuestions.has(q.getElementsByTagName("id")[0].textContent)) {
            table += " checked='true'";
        }
        table += ">";
        table += "</td></tr>";
    }

    document.getElementById("data").innerHTML = table;
}

//FONCTION DE DÉMARRAGE DU JEU
function startGame() {
    /**
         * prépare et lance le jeu avec les questions sélectionnées
         * 3 modes de sélection possibles :
            * 1. manuelle : 10 questions cochées par l'utilisateur
            * 2. par thème : 10 questions aléatoires du thème choisi
            * 3. par page : toutes les questions de la page courante
         * 
         * redirige ensuite vers jeu.html avec les IDs des questions en paramètre
     */
    let xmlDoc = xmlhttp.responseXML;

    let sThematique = document.getElementById("thematique");
    let theme = sThematique.options[sThematique.selectedIndex].value; //extraction de la valeur de la thématique sélectionnée

    let x = xmlDoc.getElementsByTagName("question");
    let selectedQuestions = []; //tableau qui contiendra les IDs des questions sélectionnées

    //MODE 1 : SÉLECTION MANUELLE (10 QUESTIONS COCHÉES)
    if (sQuestions.size === 10) {
        selectedQuestions = Array.from(sQuestions); //conversion du Set en Array
    //MODE 2 : SÉLECTION PAR THÉMATIQUE
    } else if (theme) {
        let themeQuestions = [];
        for (let i = 0; i < x.length; i++) {
            if (x[i].getElementsByTagName("thematique")[0].textContent === theme) {
                themeQuestions.push(x[i].getElementsByTagName("id")[0].textContent);
            }
        }

        //Math.random() - 0.5 génère un nombre aléatoire entre -0.5 et 0.5
        //crée un tri aléatoire (algorithme de Fisher-Yates simplifié)
        themeQuestions.sort(() => Math.random() - 0.5);
        selectedQuestions = themeQuestions.slice(0, 10);
        //MODE 3 : SÉLECTION VIA LA PAGINATION (PAGE COURANTE)
    } else {
        startIndex = (page - 1) * pageSize;
        endIndex = Math.min(startIndex + pageSize, totalQuestions);
        for (let i = startIndex; i < endIndex; i++) {
            selectedQuestions.push(x[i].getElementsByTagName("id")[0].textContent);
        }
    }

    //vérification (au moins 1 question sélectionnée)
    if (selectedQuestions.length === 0) {
        alert("Aucune question trouvée pour ce choix.");
        return;
    }

    const params = new URLSearchParams(); //réparation des paramètres URL pour la page jeu.html
    params.set("questions", selectedQuestions.join(",")); //conversion tableau -> chaîne séparée par virgules
    window.location.href = "jeu.html?" + params.toString(); //redirection vers page de jeu avec les IDs des questions en paramètre
}

//GESTION DES CHECKBOXES
function setQuestion(ev){
    /**
         * gère l'ajout/la suppression d'une question dans l'ensemble des sélections
         * appelée à chaque fois qu'une checkbox est cochée ou décochée
         * met à jour automatiquement le tableau des questions sélectionnées
         * 
         * @param {Event} ev - l'événement click de la checkbox
     */
    //vérification de l'état de la checkbox
    if (ev.currentTarget.checked) {
        sQuestions.add(ev.currentTarget.value); //ev.currentTarget.value = ID question   
    } else {
        sQuestions.delete(ev.currentTarget.value);     
    }
    fetchSelectedQuestion();
}

//AFFICHAGE DES QUESTIONS SÉLECTIONNÉES
function fetchSelectedQuestion(){
    /**
         * génère et affiche un tableau récapitulatif de toutes les questions sélectionnées
     */
    let i;
    let xmlDoc = xmlhttp.responseXML;
    let table = "<tr><th>Thematique</th><th>Question</th><th>Réponse1</th><th>Réponse2</th><th>Réponse3</th><th></th></tr>";
    let x = xmlDoc.getElementsByTagName("question");
    for (i = 0; i < x.length; i++) {
        //vérification si l'ID de cette question est dans l'ensemble des sélections
        if (sQuestions.has(x[i].getElementsByTagName("id")[0].textContent)) {
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
            "</td>" +            
            "</tr>";
        }
    }
    document.getElementById("tblSelected").innerHTML = table;    
}
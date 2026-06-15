//VARIABLES GLOBALES
let xmlhttp = new XMLHttpRequest();
let questionsList = []; //tableau contenant les questions pour cette partie (provienne de index.html)
let currentIndex = 0; //index de la question actuelle
let userAnswers = []; //tableau stockant les réponses de l'utilisateur

//CHARGEMENT DU XML
function loadXMLDoc() {
    /**
        * charge le fichier XML
        * initialise le jeu
    */
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            initGame();
        }
    };
    xmlhttp.open("GET", "https://obiwan.univ-brest.fr/~e22403362/data/bdd_GE.xml", true);
    xmlhttp.send();
}

//INITIALISATION DU JEU
function initGame() {
    /**
         * initialise le jeu en :
            * 1. récupérant les IDs des questions depuis l'URL
            * 2. filtrant les questions correspondantes dans le XML
            * 3. préparant le tableau des réponses utilisateur
            * 4. affichant la première question
     */
    let xmlDoc = xmlhttp.responseXML;
    let x = xmlDoc.getElementsByTagName("question");

    //récupération des IDs envoyés depuis index.html
    let params = new URLSearchParams(window.location.search); //parsing des paramètres de l'URL (query string)
    let idsParam = params.get("questions");//extraction du paramètre "questions" qui contient les questions
    //si le paramètre n'existe pas, cela veut dire qu'il n'y a pas de questions, donc la partie ne peut pas avoir lieu
    if (!idsParam) {
        document.getElementById("game-area").innerHTML = "<p>Aucune question sélectionnée.</p>";
        return;
    }

    let ids = idsParam.split(","); //conversion en tableau
    //on parcours la base de donnée XML, et on vérifie via leur ID lesquelles font parties de la sélection. si cela est le cas, on la rajoute a la liste des questions
    for (let i = 0; i < x.length; i++) {
        let id = x[i].getElementsByTagName("id")[0].textContent;
        if (ids.includes(id)) {
            questionsList.push(x[i]);
        }
    }

    //si aucune question n'a été trouvée, on affiche une erreur à l'écran et on termine la partie
    if (questionsList.length === 0) {
        document.getElementById("game-area").innerHTML = "<p>Aucune question trouvée.</p>";
        return;
    }

    //création d'un tableau de la même taille que celle qui contient les questions, initialisées à null (vide)
    userAnswers = new Array(questionsList.length).fill(null);

    displayQuestion();
}

//FONCTION D'AFFICHAGE DE QUESTION
function displayQuestion() {
    /**
         * affiche la question actuelle avec : le texte de la question, 
         * son image, 
         * la thématique,
         * les 3 boutons de réponse (feedback visuel coloré),
         * l'état des bountons selon si une réponse a déjà été donnée.
    */
    let q = questionsList[currentIndex]; //question courante
    if (!q) return;

    //initialisation des éléments de la question (sans l'image)
    let questionText = q.getElementsByTagName("contenu")[0].textContent;
    let r1 = q.getElementsByTagName("reponse1")[0].textContent;
    let r2 = q.getElementsByTagName("reponse2")[0].textContent;
    let r3 = q.getElementsByTagName("reponse3")[0].textContent;
    let theme = q.getElementsByTagName("thematique")[0].textContent;
    let bonneReponse = q.getElementsByTagName("bonne_reponse")[0].textContent;
    let points = parseInt(q.getElementsByTagName("point")[0].textContent); //conversion du texte en entier

    //recherche de l'image associée à la question.
    let picElements = q.getElementsByTagName("image");
    if (picElements.length > 0) {
        let picUrl = picElements[0].getAttribute("src"); //récupération de l'url de l'image
        if (picUrl && picUrl.trim() !== "") {
            document.getElementById("question-pic").innerHTML = `<img src="${picUrl}" alt="Image question" style="max-width: 400px;">`;
        } else {
            document.getElementById("question-pic").innerHTML = "";
        }
    } else {
        document.getElementById("question-pic").innerHTML = "";
    }

    //affichage de la question et de la thématique
    document.getElementById("question-text").innerText = questionText;
    document.getElementById("thematique").innerText = "Thématique : " + theme;

    //affichage des réponses
    let answersDiv = document.getElementById("answers"); //récupération du div qui contiendra les réponses
    answersDiv.innerHTML = "";
    let correctAnswerIndex = parseInt(bonneReponse) - 1; //conversion de la bonne réponse en index (JavaScript utilise des index 0-based)
    //création d'un bouton pour chaque réponse
    [r1, r2, r3].forEach((rep, index) => {
        let btn = document.createElement("button");
        btn.className = "btn btn-outline-secondary m-2"; //style par défaut
        btn.innerText = rep;
        
        //vérification de si l'utilisateur a déjà répondu à cette question
        if (userAnswers[currentIndex] !== null) {
            //récupération des informations de la réponse donnée (index sélectionné et si la réponse est bonne ou non)
            let selectedIndex = userAnswers[currentIndex].selectedIndex;
            let isCorrect = userAnswers[currentIndex].isCorrect;
            
            //colorier le bouton de la réponse sélectionnée
            if (index === selectedIndex) { //si c'est le bouton que l'utilisateur a sélectionné
                if (isCorrect) {
                    btn.className = "btn btn-success m-2"; //le bouton devient vert si la réponse est correcte
                } else {
                    btn.className = "btn btn-danger m-2"; //le bouton devient souge si la réponse n'est pas correcte
                }
            } else if (index === correctAnswerIndex && !isCorrect) { //si l'utilisateur sélectionne la mauvaise réponse, on affiche aussi la bonne réponse
                btn.className = "btn btn-success m-2";
            }
            
            //désactivation des boutons
            btn.disabled = true;
        } else { //une réponse n'a pa été sélectionnée (nouvelle question)
            btn.onclick = () => { selectAnswer(index, rep, bonneReponse, points); };
        }

        //ajout du bouton au div
        answersDiv.appendChild(btn);
    });
    //affichage du bouton pour la question suivante. si une question a été sélectionnée, on affiche le bouton; sinon, il reste caché
    if (userAnswers[currentIndex] !== null) {
        document.getElementById("next-btn").style.display = "inline-block";
    } else {
        document.getElementById("next-btn").style.display = "none";
    }
}

//SÉLECTION D'UNE RÉPONSE
function selectAnswer(index, selectedAnswer, correctAnswer, points) {
    /**
     * enregistre la réponse de l'utilisateur et évalue si elle est correcte
     * met à jour le tableau userAnswers et réaffiche la question avec feedback
     * 
     * @param {number} index - index de la réponse sélectionnée (0, 1 ou 2)
     * @param {string} selectedAnswer - texte de la réponse sélectionnée
     * @param {string} correctAnswer - index de la bonne réponse ("1", "2" ou "3")
     * @param {number} points - points attribués à cette question
     */ 
    correctAnswer = parseInt(correctAnswer) - 1; //conversion d'un string en entier
    let isCorrect = (index === correctAnswer); //vérification de si la réponse est correcte
    
    //stockage de la réponse de l'utilisateur dans un dictionnaire
    userAnswers[currentIndex] = {
        selectedIndex: index,
        selectedAnswer: selectedAnswer,
        correctAnswer: correctAnswer,
        isCorrect: isCorrect,
        points: points,
        earnedPoints: isCorrect ? points : 0
    };

    //réaffchage de la question avec les nouvelles couleurs
    displayQuestion()
}

//PASSAGE À LA QUESTION SUIVANTE
function nextQuestion() {
    /**
         * passe à la question suivante/affiche les résultats si terminé
     */
    currentIndex++; //incrémente l'index pour passer à la question suivante
    //vérification de s'il reste encore des questions
    if (currentIndex < questionsList.length) { //il en reste, le bouton suivant est masqué
        document.getElementById("next-btn").style.display = "none";
        displayQuestion();
    } else { //il ne reste plus de questions, donc les résultats finaux sont affichés
        displayResults();
    }
}

//AFFICHE LES RÉSULTATS FINAUX
function displayResults() {
    /**
         * calcul et affiche les résultats finaux de la partie : le nombre de bonnes réponses, score total obtenu/score maximum
         * sauvegarde de la tentative dans localStorage pour statistiques
    */
    let correctCount = userAnswers.filter(a => a && a.isCorrect).length; //nombre de bonnes réponses (filter() fait en sorte que seules les bonnes réponses soient gardées)
    let totalCount = questionsList.length; //nombre total de questions
    let totalScore = userAnswers.reduce((sum, a) => sum + (a ? a.earnedPoints : 0), 0); //somme des points gagnés (reduce() accumule les valeurs : sum + earnedPoints pour chaque réponse)
    let maxScore = userAnswers.reduce((sum, a) => sum + (a ? a.points : 0), 0); //somme de tous les points

    //construction de l'objet attempt (pour sauvegarde)
    let answersForSave = userAnswers.map((ua, i) => { //transformation de userAnswers en format sérialisable JSON (map() crée un nouveau tableau avec uniquement les données nécessaires)
        let q = questionsList[i];
        let id = q.getElementsByTagName("id")[0].textContent;
        let contenu = q.getElementsByTagName("contenu")[0].textContent;
        //création d'un objet simplifié pour chaque réponse
        return {
            questionId: id,
            questionText: contenu,
            selectedIndex: ua ? ua.selectedIndex : null,
            selectedAnswer: ua ? ua.selectedAnswer : null,
            correctAnswer: ua ? ua.correctAnswer : null,
            isCorrect: ua ? ua.isCorrect : false,
            points: ua ? ua.points : 0,
            earnedPoints: ua ? ua.earnedPoints : 0
        };
    });

    //création de l'objet représentant la partie complète
    let attempt = {
        date: new Date().toISOString(), //date & heure au format ISO
        correctCount: correctCount,
        totalCount: totalCount,
        totalScore: totalScore,
        maxScore: maxScore,
        answers: answersForSave
    };

    //récupérer les tentatives précédentes, ajouter et sauvegarder dans le local storage
    let attempts = [];
    try {
        attempts = JSON.parse(localStorage.getItem('quizAttempts') || '[]'); //récupération des tentatives précédentes depuis localStorage (JSON.parse() convertit la chaîne JSON en objet JavaScript)
        if (!Array.isArray(attempts)) attempts = []; //vérification de sécurité que c'est bien un tableau
    } catch (e) {
        attempts = []; //réinitialisation si une erreur se produit
    }
    attempts.push(attempt);
    localStorage.setItem('quizAttempts', JSON.stringify(attempts)); //sauvegarde du tableau complet dans localStorage (JSON.stringify() convertit l'objet JavaScript en chaîne JSON)
    
    //création du HTML
    let resultHTML = `
        <div class="text-center">
            <h3>Partie terminée !</h3>
            <div class="alert alert-info mt-4">
                <h4>Score : ${totalScore} / ${maxScore} points</h4>
                <p>Bonnes réponses : ${correctCount} / ${totalCount}</p>
            </div>
        </div>
        <div class="text-center my-4">
            <a class="btn btn-primary" href="index.html" role="button">Retour vers page d'acceuil</a>
        </div>
        <div class="text-center my-4">
            <a class="btn btn-outline-success" href="statistiques.html" role="button">Voir les statistiques</a>
        </div>
    `;
    
    document.getElementById("game-area").innerHTML = resultHTML;
}
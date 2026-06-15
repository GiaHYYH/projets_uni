//VARIABLE GLOBALES
let xmlhttp = new XMLHttpRequest();
let pageSize = 1;
let page = 1;

//CHARGEMENT DU XML
function loadXMLDoc(){  
    /**
         * charge le fichier XML
         * une fois les données chargées, déclenche l'affichage de la question demandée
    */     
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            displayQuestionById();
        }
    };
    xmlhttp.open("GET", "https://obiwan.univ-brest.fr/~e22403362/data/bdd_GE.xml", true);
    xmlhttp.send();     
}

//FONCTION D'AFFICHE DES DÉTAILS
function displayQuestionById() {
    /**
         * recheche et affiche les détails d'une question spécifique
         * l'ID de la question est récupéré depuis les paramètres de l'URL
         * 
         * cette fonction :
            * 1. récupère l'ID depuis l'URL
            * 2. parcourt toutes les questions du XML
            * 3. trouve la question correspondante
            * 4. remplit tous les champs du formulaire
    */
    //sécurité : s'assurer que la réponse XML est disponible
    if (!xmlhttp.responseXML) {
        console.error("XML non chargé ou format incorrect.");
        return;
    }

    let xmlDoc = xmlhttp.responseXML;
    let questions = xmlDoc.getElementsByTagName("question");
    let params = new URLSearchParams(window.location.search);
    let idCherche = params.get("id"); //extraction de la valeur du paramètre "id" depuis l'URL
    //vérification que l'ID existe
    if (!idCherche) {
        console.warn("Aucun paramètre id dans l'URL.");
        return;
    }

    //parcours de toutes les questions pour trouver celle qui correspond à l'ID
    for (let i = 0; i < questions.length; i++) {
        let q = questions[i]; //question actuelle
        let idNode = q.getElementsByTagName("id")[0];
        if (!idNode) continue; //passage a la question suivante si <id> n'existe pas
        let idVal = idNode.childNodes[0] && idNode.childNodes[0].nodeValue; //extraction du contenu textuel de l'élément <id>

        if (idVal === idCherche) {
            //fonction utilitaire pour récupérer le texte d'un élément XML en évitant les erreurs
            function getText(tagName) {
                /**
                     * fonction helper locale pour extraire le texte d'un élément XML en toute sécurité
                     * 
                     * @param {string} tagName - nom de la balise XML à rechercher
                     * @returns {string} Lle contenu textuel de l'élément, ou "" si non trouvé
                */
                let node = q.getElementsByTagName(tagName)[0];
                if (!node) return "";
                return node.textContent || (node.childNodes[0] ? node.childNodes[0].nodeValue : "");
            }

            // REMPLISSAGE DES CHAMPS
            let elImg = document.getElementById("txtImg");
            let elQuestion = document.getElementById("txtQuestion");
            let elThematique = document.getElementById("txtThematique");
            let elDifficulte = document.getElementById("txtDifficulte");
            let elRp1 = document.getElementById("txtRp1");
            let elRp2 = document.getElementById("txtRp2");
            let elRp3 = document.getElementById("txtRp3");
            let elBonne = document.getElementById("txtBonneRp");
            let elPoints = document.getElementById("txtPnts");

            let imageNode = q.getElementsByTagName("image")[0]; //récupération de l'élément <image> dans le XML
            //si l'élément HTML pour l'image existe et si le XML contient un élément <image>
            if (elImg && imageNode) {
                let imageSrc = imageNode.getAttribute("src"); //extraction de l'attribut "src"
                if (imageSrc) {
                    elImg.src = imageSrc;
                    elImg.style.maxWidth = "400px";
                    elImg.style.maxHeight = "400px";
                }
            }
            if (elQuestion) elQuestion.value = getText("contenu");
            if (elThematique) elThematique.value = getText("thematique");
            if (elDifficulte) elDifficulte.value = getText("difficulte");
            if (elRp1) elRp1.value = getText("reponse1");
            if (elRp2) elRp2.value = getText("reponse2");
            if (elRp3) elRp3.value = getText("reponse3");
            if (elBonne) elBonne.value = getText("bonne_reponse");
            if (elPoints) elPoints.value = getText("point");

            //on a trouvé la question : arrêter la boucle
            break;
        }
    }
}
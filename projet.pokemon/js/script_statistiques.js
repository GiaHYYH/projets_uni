//FONCTION DE CHARGEMENT PRINCIPAL
function loadXMLDoc() {
    /**
         * fonction appelée au chargement de la page
     */
    displayStats();
 }
 
 //AFFICHAGE DES STATISTIQUES
 function displayStats() {
    /**
         * fonction maîtresse qui orchestre l'affichage complet des statistiques :
            * 1. récupère les tentatives depuis localStorage
            * 2. génère le graphique animé
            * 3. affiche le tableau comparatif
            * 4. affiche l'historique des parties
            * 5. affiche les statistiques globales
     */
    const statsArea = document.getElementById('stats-area'); //zone principale pour les tableaux et statistiques textuelles
    const graphArea = document.getElementById('chart-container'); //zone pour le graphique en barres animé
 
 
    //récupération des tentatives depuis localStorage
    let attempts = [];
    try {
        attempts = JSON.parse(localStorage.getItem('quizAttempts') || '[]');
        if (!Array.isArray(attempts)) attempts = [];
    } catch (e) {
        attempts = [];
    }
 
    //effacement du contenu précédent pour un affichage propre
    graphArea.innerHTML = "";
    statsArea.innerHTML = "";
 
    //appel de la fonction de dessin du graphique Bootstrap
    drawBootstrapGraph(graphArea, attempts);
 
 
    //si aucune statistique
    if (attempts.length === 0) {
        statsArea.innerHTML = `
            <div class="alert alert-warning">
                <p class="mb-0">Aucune statistique disponible. Jouez à un quiz pour générer des statistiques !</p>
            </div>
        `;
        return;
    }
 
 
    //récupérer la dernière partie
    const lastAttempt = attempts[attempts.length - 1];
   
    //calculer les moyennes des parties précédentes (toutes sauf la dernière)
    let avgCorrect = 0;
    let avgTotal = 0;
    let avgPoints = 0;
    let avgMaxPoints = 0;

    //si au moins 2 parties ont été jouées (nécessaire pour calculer une moyenne)
    if (attempts.length > 1) {
        const previousAttempts = attempts.slice(0, -1);//extraction de toutes les parties sauf la dernière (slice(0, -1) : du début jusqu'à l'avant-dernier élément)
        //calcul des sommes avec reduce() (reduce() parcourt le tableau et accumule les valeurs)
        const sumTotal = previousAttempts.reduce((sum, att) => sum + att.totalCount, 0);
        const sumCorrect = previousAttempts.reduce((sum, att) => sum + att.correctCount, 0); 
        const sumPoints = previousAttempts.reduce((sum, att) => sum + (att.totalScore || 0), 0);
        const sumMaxPoints = previousAttempts.reduce((sum, att) => sum + (att.maxScore || 0), 0);
       
        //calcul des moyennes avec arrondi à 1 décimale (Math.round(x * 10) / 10 : arrondit à 1 décimale)
        avgCorrect = Math.round(sumCorrect / previousAttempts.length * 10) / 10;
        avgTotal = Math.round(sumTotal / previousAttempts.length * 10) / 10;
        avgPoints = Math.round(sumPoints / previousAttempts.length * 10) / 10;
        avgMaxPoints = Math.round(sumMaxPoints / previousAttempts.length * 10) / 10;
    }
 
    const lastDate = new Date(lastAttempt.date); //conversion de la chaîne ISO en objet Date
    const formattedDate = lastDate.toLocaleDateString('fr-FR', { //formatage de la date en format français lisible
        day: '2-digit', 
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
 
    
    //extraction des scores de la dernière partie
    const lastTotalScore = lastAttempt.totalScore || 0;
    const lastMaxScore = lastAttempt.maxScore || 0;
 
 
    //construction du HTML pour le tableau de comparaisons (utilisation de template literals pour insérer dynamiquement les valeurs)
    let html = `
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Comparaison des résultats</h4>
                    </div>
                    <div class="card-body p-0">
                       
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Statistique</th>
                                    <th scope="col" class="text-center">Dernière partie</th>
                                    <th scope="col" class="text-center">Moyenne précédente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">Date</th>
                                    <td class="text-center">${formattedDate}</td>
                                    <td class="text-center">${attempts.length > 1 ? (attempts.length - 1) + ' partie(s)' : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Points obtenus</th>
                                    <td class="text-center">
                                        <strong class="${getPointsClass(lastTotalScore, avgPoints, attempts.length > 1)}">${lastTotalScore} / ${lastMaxScore} points</strong>
                                    </td>
                                    <td class="text-center">${attempts.length > 1 ? avgPoints + ' / ' + avgMaxPoints + ' points' : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Bonnes réponses</th>
                                    <td class="text-center">
                                        <strong>${lastAttempt.correctCount} / ${lastAttempt.totalCount}</strong>
                                    </td>
                                    <td class="text-center">${attempts.length > 1 ? avgCorrect + ' / ' + avgTotal : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Nombre total de parties</th>
                                    <td colspan="2" class="text-center"><strong>${attempts.length}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
 
 
    //contruction du HTML pour l'historique des dernières parties
    if (attempts.length > 1) {
        html += `
            <div class="row justify-content-center mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Historique des parties</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                ${attempts.slice(-5).reverse().map((att, idx) => { //traitement des 5 dernières parties (slice(-5) : extraction des 5 derniers éléments; reverse() : inversion pour afficher du plus récent au plus ancien; map() : transformation de chaque tentative en HTML)
                                    const attemptDate = new Date(att.date);
                                    const formattedAttemptDate = attemptDate.toLocaleDateString('fr-FR', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    });
                                    //idx === 0 signifie que c'est la première après reverse(), donc la plus récente (la dernière partie)
                                    const isLast = idx === 0;
                                    //extraction des scores avec fallback
                                    const attTotalScore = att.totalScore || 0;
                                    const attMaxScore = att.maxScore || 0;
                                   
                                    return `
                                        <div class="list-group-item ${isLast ? 'list-group-item-primary' : ''}">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">
                                                        ${isLast ? '<span class="badge bg-primary me-2">Dernière</span>' : ''}
                                                        ${formattedAttemptDate}
                                                    </h6>
                                                    <p class="mb-0">
                                                        <span class="badge bg-secondary">${att.correctCount}/${att.totalCount} bonnes réponses</span>
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="badge bg-info fs-5">${attTotalScore}/${attMaxScore} pts</span>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }).join('') //join('') : concatène tous les éléments HTML sans séparateur
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
   
    //construction du HTML pour les statistiques globales
    if (attempts.length > 1) {
        const allPoints = attempts.map(att => att.totalScore || 0);
        const allMaxPoints = attempts.map(att => att.maxScore || 0);
        const totalPoints = allPoints.reduce((sum, pts) => sum + pts, 0);
        const totalMaxPoints = allMaxPoints.reduce((sum, pts) => sum + pts, 0);
        const bestPoints = Math.max(...allPoints); //spread operator (...) pour passer le tableau comme arguments individuels
        const worstPoints = Math.min(...allPoints);
       
        html += `
            <div class="row justify-content-center mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Statistiques globales</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-muted mb-2">Meilleur score</h6>
                                        <h3 class="text-success mb-0">${bestPoints} pts</h3>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-muted mb-2">Total cumulé</h6>
                                        <h3 class="text-primary mb-0">${totalPoints} / ${totalMaxPoints}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-muted mb-2">Score le plus bas</h6>
                                        <h3 class="text-danger mb-0">${worstPoints} pts</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
 
 
    statsArea.innerHTML = html;
 }
 
 //DESSIN DU GRAPHIQUE ANIMÉ
 function drawBootstrapGraph(container, attempts) {
    /**
         * génère un graphique en barres animé avec Bootstrap
         * 
         * @param {HTMLElement} container - conteneur DOM où injecter le graphique
         * @param {Array} attempts - tableau des tentatives de quiz
     */

    container.innerHTML = "";
 
    //vérification qu'il y a des données à afficher
    if (!attempts || attempts.length === 0) {
        container.innerHTML = `
            <div class="alert alert-warning mb-0">
                Aucune statistique disponible.
            </div>
        `;
        return;
    }
 
    //extraction de tous les scores dans un tableau
    const scores = attempts.map(att => att.totalScore || 0);
    //recherche du score maximum pour normaliser les barres (Math.max(...scores, 1) : minimum de 1 pour éviter division par zéro)
    const maxScore = Math.max(...scores, 1);

    //définition d'une palette de 4 couleurs pour les barres ; les couleurs se répètent en boucle si plus de 4 parties
    const colors = ['#B7E5CD', '#8ABEB9', '#305669', '#C1785A']; 
 
    //génération des barres
    scores.forEach((score, i) => {
        //calcul du pourcentage par rapport au score maximum (permet de normaliser toutes les barres à la même échelle)
        const percent = Math.round((score / maxScore) * 100);
 
        //création d'un conteneur pour cette barre
        const item = document.createElement('div');
        item.className = "mb-3"; //espacement des barres
 
        const color = colors[i % colors.length]; //sélection de la couleur avec rotation cyclique (% : 0,1,2,3,0,1,2,3... même avec plus de 4 éléments)
 
        //génération du HTML de la barre
        item.innerHTML = `
            <div class="d-flex justify-content-between mb-1">
                <span>Partie ${i + 1}</span>
                <span>${score} pts</span>
            </div>
            <div class="progress" style="height: 25px; border-radius: 12px; overflow: hidden;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width:0%; background-color: ${color}; border-radius: 12px;" 
                     aria-valuenow="0" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
            </div>
        `;
        container.appendChild(item);
 
 
        //animation de la barre
        const bar = item.querySelector('.progress-bar');
        setTimeout(() => { //décalage de l'animation pour effet en cascade; chaque barre commence son animation après la précédente (i * 150 : délai de 150ms multiplié par l'index)
            bar.style.transition = 'width 1s ease-in-out'; //définition de la transition CSS pour animation fluide (1s = durée de l'animation, ease-in-out = courbe d'accélération)
            bar.style.width = `${percent}%`;
            bar.setAttribute('aria-valuenow', percent);
        }, i * 150);
    });
 }
 
 //SUPPRESSION DES STATISTIQUES
 function clearStats() {
    /**
         * supprime toutes les statistiques sauvegardées
         * demande confirmation avant suppression (action irréversible)
         * recharge l'affichage après suppression
     */
    if (confirm('Êtes-vous sûr de vouloir supprimer toutes les statistiques ? Cette action est irréversible.')) {
        localStorage.removeItem('quizAttempts');
        displayStats();
        alert('Toutes les statistiques ont été supprimées.');
    }
 }
 
 //FONCTION UTILITAIRE : CLASSE DE COULEUR POUR LES POINTS
 function getPointsClass(lastPoints, avgPoints, hasPrevious) {
    /**
         * Détermine la classe CSS à appliquer selon la performance
         * Compare le score de la dernière partie à la moyenne précédente
         * 
         * @param {number} lastPoints - points obtenus lors de la dernière partie
         * @param {number} avgPoints - moyenne des points des parties précédentes
         * @param {boolean} hasPrevious - indique s'il existe des parties précédentes
         * @returns {string} nom de la classe CSS Bootstrap à appliquer
     */
    if (!hasPrevious) { //si c'est la première partie (pas de comparaison possible)
        return 'text-info';
    }
   
    if (lastPoints > avgPoints) { //si le dernier score est supérieur à la moyenne
        return 'text-success';
    } else if (lastPoints < avgPoints) { //si le dernier score est inférieur à la moyenne
        return 'text-warning';
    } else { //si le dernier score est égal à la moyenne
        return 'text-secondary';
    }
 }
 
 
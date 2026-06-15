ATTRIBUTS D'UNE QUESTION : 
    * le thème associé à la question,
    * la difficulté associée à la question,
    * l'id de la question,
    * l'image associée à la question,
    * la question,
    * les réponses possible (3 maximum),
    * la bonne réponse,
    * le nombre de points donnés pour la bonne réponse.
    
ANALYSE DE LA FONCTION fetchData() (ligne par ligne) [dans le fichier script_index.js]:
    * ligne 34 : Déclaration de la variable i qui servira de compteur dans la boucle for.
    * ligne 35 : Récupération du document XML depuis l'objet xmlhttp et stockage dans xmlDoc.
    * ligne 36 : Récupération de tous les éléments <question> du XML. Convertion de la NodeList en Array avec Array.from(). Stockage du résultat dans allQuestions (variable globale).
    * ligne 37 : Création d'une copie superficielle du tableau allQuestions dans currentQuestions (également globale). Utilisée pour conserver l'original intact lors de filtres/tris.
    * ligne 38 : Initialisation de la variable table avec l'en-tête HTML du tableau (6 colonnes : thématique, question, 3 réponses, et une colonne vide pour les checkboxes).
    * ligne 39 : Seconde récupération de tous les éléments <question>.
    * ligne 40 : Stockage du nombre total de questions dans totalQuestions (variable globale).
    * ligne 42 : Calcule du nombre de pages nécessaires en divisant le total par pageSize et arrondissant au supérieur.
    * ligne 44 : Calcule de l'index de début pour la page courante.
    * ligne 45 : Calcule de l'index de fin en prenant le minimum entre startIndex + pageSize et le total (évite de dépasser le tableau sur la dernière page).
    * ligne 47 : Bouclage sur les questions de la page courante uniquement.
    * lignes 48 - 58 : Ajout du contenu textuel des éléments thématique, contenu et réponses a la variable table.
    * ligne 60 : Début de création d'une checkbox avec : un gestionnaire d'événement onclick appelant setQuestion(event); une value contenant l'ID de la question
    * lignes 64 - 65 : Vérification de si l'ID de la question existe dans sQuestions (Set global). Si la question est déjà sélectionnée, ajout de l'attribut checked à la checkbox.
    * lignes 68 - 70 : Fermeture de la balise <input>, de la cellule et de la ligne du tableau.
    * ligne 72 : Insèrtion du HTML généré dans l'élément ayant l'ID "data".
    
MANUEL UTILISATION INTERFACE "APPRENDRE":
    * On peut filtrer les questions par rapport à leur thème principal. Il y a 6 thèmes:
    	- Type : questions qui ont en rapport avec les types de Pokémons, que ce soit des questions sur le type d'un Pokémons ou les types eux-mêmes;
    	- Objet : questions qui portent sur les objets du monde des Pokémons;
    	- Lieu : questions qui portent sur les lieus divers du monde des Pokémons;
    	- Nom : questions qui portent sur les noms des Pokémons, mais aussi les noms des humains du monde des Pokémons;
    	- General : des questions de type général/vague;
    	- Evolution : questions en rapport avec les évolutions de Pokémons.
    * Sans filtrage, toutes les pages sont automatiquement affichées par "batch" de 5, avec une pagination permettant de voir les autres questions.
    * En haut à droite, il y a une barre de recherche qui permet à l'utilisateur de rechercher un mot clé ou un élément spécifique parmi les questions, les thématiques, les réponses et les niveaux de difficultés
    * Le bouton sort sous le tableau permet de trie les questions par ordre alphabétique.

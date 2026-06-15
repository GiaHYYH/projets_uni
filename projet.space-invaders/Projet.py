'''Authors : Eraste Georgia and Seng Gabrielle - Final version'''

import json

try:
    import tkinter as tk
    import tkinter.messagebox as tkMessageBox
except:
    import Tkinter as tk # type: ignore

class SpaceInvaders(object):
    
    class Game(object):
        
        def __init__(self, frame):
            self.frame = frame
            self.fleet = Fleet()
            self.defender = Defender()
            self.canvas_height = 600
            self.canvas_width = 600
            self.canvas = tk.Canvas(self.frame, width=self.canvas_width, height=self.canvas_height, bg="black")
            self.canvas.pack()
            self.score = 0
            self.points_per_alien = 100
            self.text = self.canvas.create_text((100, 15), text=f"SCORE: {self.score}", fill="white", font="Cambria 15") #affichage du score dans le jeu.
            self.fleet.install_in(self.canvas)
            self.defender.install_in(self.canvas, 0, 0)
            self.fleet.animation(self.canvas, self.defender, self)            
            self.fleet.manage_touched_aliens_by(self.canvas, self.defender.fired_bullets, self)
            self.game_over = False
            self.update_collisions()

        def update_score(self):
            self.canvas.itemconfig(self.text, text=f"SCORE: {self.score}") #affiche bien le score actuel sur le canvas après le texte "SCORE".

        def add_points(self, points):
            self.score += points
            self.update_score()

        def update_collisions(self): #vérification toutes les 50ms si les aliens ont été touchés tant que la partie n'est pas terminée.
            if not self.game_over:
                self.fleet.manage_touched_aliens_by(self.canvas, self.defender.fired_bullets, self)
                self.canvas.after(50, self.update_collisions)

        def save_score(self, nom_joueur): #sauvegarde le score dans le fichier en appelant les classes Score et Resultat.
            score_obj = Score(nom_joueur, self.score)
            try:
                resultats = Resultats.fromFile("resultats.json")
                if len(resultats.get_lesScores()) >= 5: #suppression des scores dans le fichier JSON une fois qu'on atteint les 5 scores.
                    resultats = Resultats() #reset les scores.
            except:
                resultats = Resultats()
            resultats.ajout_score(score_obj)
            resultats.toFile("resultats.json")
        
        def end_game_lose(self): #ajout d'une fonction pour la partie si perdue.
            self.game_over = True
            nom_joueur = self.demander_nom_joueur()
            self.save_score(nom_joueur)
            tkMessageBox.showinfo("Game Over", "Les aliens ont gagné!\nPartie terminée!") #message annonçant la fin du jeu (perdu).
            self.canvas.quit()
            
        def end_game_win(self): #ajout d'une fonction pour la partie si gagnée.
            self.game_over = True
            nom_joueur = self.demander_nom_joueur()
            self.save_score(nom_joueur)
            tkMessageBox.showinfo("Félicitations!", "La horde d'aliens est vaincue!\nPartie terminée!")
            self.canvas.quit()

        def demander_nom_joueur(self): #fenêtre de demande du nom du joueur.
            dialog = tk.Toplevel() #fenêtre toujours au dessus de la fenêtre principale.
            dialog.title("Sauvegarde du score")
            dialog.geometry("300x100")
            
            tk.Label(dialog, text="Entrez votre nom:").pack(pady=10)
            
            nom_entry = tk.Entry(dialog)
            nom_entry.pack(pady=5)
            nom_entry.focus() #focus sur l'entrée et l'utilisateur a juste à taper directement sans avoir à cliquer au préalable dessus.
            
            nom_joueur = ["joueur"] #initialisation obligatoire avant.
            
            def limit_carac(event): #event indispensable.
                texte = nom_entry.get()
                if len(texte) > 3:
                    nom_entry.delete(3, tk.END) #supprime tout ce qui est tapé après les 3 caractères.
            
            nom_entry.bind("<KeyRelease>", limit_carac)
            
            def valider():
                texte_saisi = nom_entry.get().strip() #.strip() retire les espaces en trop.
                if texte_saisi and len(texte_saisi) <= 3:
                    nom_joueur[0] = texte_saisi.upper()[:3]
                    dialog.destroy()
            
            tk.Button(dialog, text="OK", command=valider).pack(pady=10)

            nom_entry.bind("<Return>", lambda e: valider())
            
            dialog.transient(self.canvas.master) #fenêtre enfant de la fenêtre principale (le jeu).
            dialog.grab_set() #on ne peut pas interagir avec la fenêtre principale.
            dialog.wait_window() #attend qu'on interagisse avec.
            
            return nom_joueur[0]
    
    def __init__(self):
        self.root = tk.Tk()
        self.root.title("Space Invaders")
        self.frame = tk.Frame(self.root)
        self.frame.pack(side="top", fill="both")
        self.game = self.Game(self.frame)
        
    def play(self):
        self.root.bind("<Left>", self.game.defender.move_in)
        self.root.bind("<Right>", self.game.defender.move_in)
        self.root.bind("<space>", self.game.defender.fire)
        self.root.mainloop()

class Defender(object):
    def __init__(self):
        self.width = 20
        self.height = 20
        self.move_delta = 20
        self.id = None
        self.x = 0
        self.y = 0
        original_image = tk.PhotoImage(file='defender2.png') #récupération de l'image originale dans une variable.
        self.pim = original_image.subsample(2, 2) #redimenssionne l'image pour meilleure incrémentation (taille de départ trop grande).
        self.max_fired_bullets = 8
        self.fired_bullets = []
        
    def install_in(self, canvas, x, y): #création du defender.
        self.canvas = canvas
        self.canvas_width = int(canvas.cget("width"))
        self.canvas_height = int(canvas.cget("height"))
        
        self.x = self.canvas_width // 2
        self.y = self.canvas_height - 50
        self.id = self.canvas.create_image(self.x, self.y, image=self.pim)
        
    def move_in(self, event): #initialisation des touches "flèche gauche" et "flèche droite".
        
        if event.keysym == 'Left':
            self.x -= self.move_delta
        elif event.keysym == 'Right':
            self.x += self.move_delta

        #ne pas dépasser le canvas.
        self.x = max(self.width, min(self.x, self.canvas_width - self.width))
        
        #déplacement de l'image.
        self.canvas.coords(self.id, self.x, self.y)
        
    def fire(self, event=None): #event=None permet à cette méthode d'être appelée avec bind ou directement dans le code sans argument, sinon Erreur.
        if len(self.fired_bullets) < self.max_fired_bullets: #on tire des bullets tant qu'il n'y en a pas 8 à l'écran. On stoppe les tirs si c'est le cas.
                bullet = Bullet(self)
                bullet.install_in(self.canvas)
                self.fired_bullets.append(bullet) 
        
class Bullet(object):
    def __init__(self, shooter):
        self.radius = 5
        self.color = "white"
        self.speed = 8
        self.id = None
        self.shooter = shooter
        
    def install_in(self, canvas):
        self.canvas = canvas
        self.canvas_width = int(canvas.cget("width"))
        self.canvas_height = int(canvas.cget("height"))
        
        #après récupération des coordoonnées du defender, on créé la bullet en fonction de ces coords.
        defender_x = self.shooter.x + 1 #le +1 permet à la bullet d'être bien centrée sur le defender.
        defender_y = self.shooter.y

        #création de la bullet.
        self.id = canvas.create_oval(
            defender_x - self.radius,
            defender_y - self.radius,
            defender_x + self.radius,
            defender_y + self.radius,
            fill=self.color
        )

        self.move_in()
        
    def move_in(self): #animation de la bullet qui fonce vers le haut du canvas.
        if self.id is not None:
            self.canvas.move(self.id, 0, -self.speed)
            y2 = self.canvas.coords(self.id)[3] #besoin uniquement de y2 pour la collision avec le haut du canvas.
            if y2 < 0:
                self.canvas.delete(self.id)
                if self in self.shooter.fired_bullets:
                    self.shooter.fired_bullets.remove(self) #on recharge de 1 bullet tirable.
            else:
                self.canvas.after(30, self.move_in)

class Alien:
    def __init__(self):
        self.id = None
        self.alive = True #va servir lors de la collision avec bullet.
        original_image = tk.PhotoImage(file='alien.gif') #récupération de l'image originale dans une variable.
        self.pim = original_image.subsample(2, 2)
        self.explosion_image = None 
        self.x = 0
        self.y = 0

    def install_in(self, canvas, x, y): #création d'un alien avec alien.gif.
        self.x = x
        self.y = y
        self.canvas = canvas
        self.id = self.canvas.create_image(self.x, self.y, image=self.pim)

    def move_in(self, canvas, dx=0, dy=0): #animation de l'alien s'il est alive, donc présent sur le canvas.
        if self.alive and self.id is not None:
            canvas.move(self.id, dx, dy)
            self.x += dx
            self.y += dy

    def touched_by(self, canvas, projectile):
        if self.alive and self.id is not None and projectile.id is not None:
            #coordonnées de l'alien.
            alien_coords = canvas.bbox(self.id)
            if alien_coords is None:
                return False
            x1, y1, x2, y2 = alien_coords
            
            #coordonnées de la bullet.
            bullet_coords = canvas.coords(projectile.id)
            if not bullet_coords or len(bullet_coords) < 4:
                return False
            bx1, by1, bx2, by2 = bullet_coords
            
            #centre de la bullet.
            bullet_center_x = (bx1 + bx2) / 2
            bullet_center_y = (by1 + by2) / 2
            
            #vérifie si le centre de la bullet est dans la boîte de l'alien.
            if x1 <= bullet_center_x <= x2 and y1 <= bullet_center_y <= y2:
                # Calcul du centre avant de détruire l'alien.
                centre_x = (x1 + x2) / 2
                centre_y = (y1 + y2) / 2
                
                #affiche l'explosion.
                self.explosion(canvas, centre_x, centre_y)
                
                #détruit l'alien.
                self.alive = False
                canvas.delete(self.id)
                self.id = None
                return True
        return False
    
    def explosion(self, canvas, x, y):
        explosion = canvas.create_image(x, y, image=self.explosion_image, tag="explosion")
        canvas.after(200, lambda: canvas.delete(explosion))

class Fleet:
    def __init__(self):
        self.aliens_rows = 5
        self.aliens_cols = 10
        self.h_gap = 20 #horizontal gap entre les aliens.
        self.v_gap = 20 #vertical gap.
        self.aliens_fleet = []
        self.alien_width = 30
        self.alien_height = 30
        self.initial_alien_count = 0
        self.base_speed = 80
        self.explosion_image = tk.PhotoImage(file='explosion.png')

    def install_in(self, canvas): #création de la flotte d'alien en appelant la création d'un alien.
        self.aliens_fleet.clear()
        start_x, start_y = 50, 50
        for row in range(self.aliens_rows):
            for col in range(self.aliens_cols):
                alien = Alien()
                x = start_x + col * (self.alien_width + self.h_gap)
                y = start_y + row * (self.alien_height + self.v_gap)
                alien.explosion_image=self.explosion_image
                alien.install_in(canvas, x, y)
                self.aliens_fleet.append(alien) #ajout de l'alien à la flotte.
        self.initial_alien_count = len(self.aliens_fleet) #mémorise le nombre initial d'aliens

    def move_in(self, canvas, dx=5, dy=0):
        for alien in self.aliens_fleet:
            alien.move_in(canvas, dx, dy)
    
    def get_alien_row(self, alien): #accesseur pour récupérer la ligne de la flotte.
        alien_index = self.aliens_fleet.index(alien)
        aliens_per_row = len(self.aliens_fleet) // self.aliens_rows
        return (alien_index // aliens_per_row) + 1
    
    def calculate_speed(self): #calcul pour augmenter la vitesse en fonction du nombre d'aliens tués.
        alive_count = sum(1 for alien in self.aliens_fleet if alien.alive)
        if self.initial_alien_count == 0:
            return self.base_speed
        
        destruction_ratio = 1 - (alive_count / self.initial_alien_count)
        speed = int(self.base_speed * (1 - destruction_ratio * 0.7)) #formule : vitesse de base * (1 - ratio * 0.7)
        return max(20, speed) #min de 20ms pour ne pas être trop rapide.

    def manage_touched_aliens_by(self, canvas, projectiles, game):
        for projectile in projectiles[:]:  #copie de la liste pour éviter les problèmes de modification.
            if projectile.id is not None:
                for alien in self.aliens_fleet:
                    if alien.touched_by(canvas, projectile):
                        alien_row = self.get_alien_row(alien) #récupération de la ligne où se trouve l'alien.
                        points_multiplier = (self.aliens_rows - alien_row + 1) #multiplicateur pour faire en sorte que la ligne du dessus donne le plus de points.
                        game.add_points(game.points_per_alien*points_multiplier)
                        canvas.delete(projectile.id) #supprime la bullet.
                        projectile.id = None
                        if projectile in projectiles:
                            projectiles.remove(projectile)
                        break  #passe à la bullet suivante.
                        
    def animation(self, canvas, defender, game, dx=5):
        alive_aliens = [alien for alien in self.aliens_fleet if alien.alive]
        if not alive_aliens:
            game.end_game_win()
            return

        #coordonnées des extrêmes.
        rightmost = max(alien.x + self.alien_width for alien in alive_aliens)
        leftmost = min(alien.x for alien in alive_aliens)

        #changement de direction quand on touche les bords.
        if rightmost + dx > int(canvas.cget("width")) or leftmost + dx < 30: #30 ici pour uniformiser des deux côtés.
            dx = -dx
            for alien in alive_aliens:
                alien.move_in(canvas, 0, 10)  #descend de 10 pixels.
        
        #arrêt lorsque la dernière ligne avec des aliens en vie touche le defender.
        last_line = max(alien.y + self.alien_height for alien in alive_aliens)
        
        if last_line >= defender.y - 20:
            game.end_game_lose()
            return

        #déplacement horizontal.
        self.move_in(canvas, dx, 0)

        current_speed = self.calculate_speed()
        canvas.after(current_speed, lambda: self.animation(canvas, defender, game, dx)) 



class Score(object):
    
    def __init__(self, nom_joueur, nb_points):
        self.nom=nom_joueur
        self.points=nb_points

    def get_nom(self):
        return self.nom

    def get_points(self):
        return self.points

    def toFile(self, fich):
        f = open(fich,"w")
        l=self
        json.dump(l.__dict__,f) #recense les attributs de la classe et leurs valeurs.
        f.close()

    @classmethod
    def fromFile(cls, fich):
        f = open(fich,"r")
        d = json.load(f)
        lnew=Score(d["nom"],d["points"])
        f.close()
        return lnew

    def __str__(self):
         return "Nom du joueur: " + self.nom + ',' + "Score: " +str(self.points)

class Resultats(object):
    
    def __init__(self):
        self.lesScores=[]

    def get_lesScores(self):
        return self.lesScores

    def ajout_score(self, score):
        self.lesScores.append(score)
    
    def __str__(self):
        chaine=str(self.lesScores[0])
        for e in self.lesScores[1:]:
            chaine=chaine+ "," + str(e)
        return chaine

    def toFile(self, fich):
        f = open(fich,"w")
        tmp = []
        for s in self.lesScores:
            d = {}
            d["nom"] = s.nom
            d["points"] = s.points
            tmp.append(d)
        json.dump(tmp,f)
        f.close()


    @classmethod
    def fromFile(cls, fich):
        f = open(fich,"r")
        tmp = json.load(f)
        
        liste = []
        for d in tmp:
            s=Score(d["nom"],d["points"])
            liste.append(s)
        score=Resultats()
        score.lesScores=liste
        f.close()
        return score


SpaceInvaders().play()
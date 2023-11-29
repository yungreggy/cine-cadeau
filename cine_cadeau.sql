CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    email VARCHAR(255),
    photo_profile VARCHAR(255),
    id_privilege INT,
    FOREIGN KEY (id_privilege) REFERENCES privileges(id)
);


CREATE TABLE films (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) UNIQUE,
    description TEXT,
    genre VARCHAR(255),
    realisateur VARCHAR(255),
    acteurs TEXT,
    duree INT,
    lien_image VARCHAR(255),
    lien_video VARCHAR(255),
    annee_originale INT
);


CREATE TABLE emissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) UNIQUE,
    description TEXT,
    duree INT,
    genre VARCHAR(255),
    lien_video VARCHAR(255),
    lien_image VARCHAR(255),
    annee_originale INT
);

CREATE TABLE series (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) UNIQUE,
    description TEXT,
    lien_image VARCHAR(255),
    lien_video VARCHAR(255),
    annee_originale INT,
    id_emission INT,
    FOREIGN KEY (id_emission) REFERENCES emissions(id)
);

CREATE TABLE publicites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255),
    type VARCHAR(255),
    duree INT,
    lien_video VARCHAR(255),
    annee_originale INT
);

CREATE TABLE plages_horaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_emission INT,
    id_serie INT,
    id_film INT,
    heure_debut DATETIME,
    heure_fin DATETIME,
    FOREIGN KEY (id_emission) REFERENCES emissions(id),
    FOREIGN KEY (id_serie) REFERENCES series(id),
    FOREIGN KEY (id_film) REFERENCES films(id)
);



CREATE TABLE privileges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    privilege_level VARCHAR(255),
    description TEXT
);

CREATE TABLE journal_de_bord (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    action TEXT,
    timestamp DATETIME,
    FOREIGN KEY (id_user) REFERENCES users(id)
);


INSERT INTO films (titre, duree, realisateur, genre, annee_originale) VALUES 
('Le Chat dans le Sac', 74, '', '', 1964),
('La Guerre des Tuques', 91, '', '', 1984),
('Le Matou', 141, 'Jean Beaudin', '', 1985),
('Mon Oncle Antoine', 104, 'Claude Jutra', '', 1971),
('Bach et Bottine', 96, 'André Melançon', '', 1986),
('Matusalem', 108, '', '', 1993),
('Histoires d''Hiver', 105, 'François Bouvier', '', 1999),
('1965. La vie heureuse de Leopold Z', 68, 'Gilles Carle', '', 1965),
('Opération Beurre de Pinottes', 90, '', '', 1985),
('La Grenouille et la Baleine', 92, 'Jean-Claude Lord', '', 1987),
('Bye Bye Chaperon Rouge', 94, '', '', 1989),
('Il était une fois Les Boys', 106, '', 'Québécois', 2013),
('Maman, j''ai encore raté l''avion', 115, 'Chris Columbus', 'Fêtes', 1992),
('Mickey Il Etait Une Fois Noel', 63, '', '', 1999),
('Une Nuit à l''École', 46, '', '', 1997),
('Le Sapin a des Boules', 97, '', 'Comédie', 1989);

INSERT INTO films (titre, duree, realisateur, genre, annee_originale) VALUES 
('L''Antilope d''Or', 30, '', '', 0),
('Le Prince et le Cygne', 40, '', '', 0),
('Le Petit Cheval Bossu', 71, '', '', 0),
('La Ferme des Animaux', 72, '', 'Animation', 0),
('La Rose de Bagdad', 66, '', '', 0),
('La Princesse Grenouille', 39, '', 'Animation', 0),
('Le Postier des neiges', 17, '', '', 0),
('La Reine des Neiges', 58, '', '', 0),
('Bouratino Pinocchio', 65, '', '', 0),
('Les Dents du singe', 14, 'René Laloux', '', 0),
('Les Escargots', 11, '', '', 0),
('Astérix le Gaulois', 68, '', '', 1967),
('Astérix et Cléopâtre', 73, '', '', 1968),
('Tintin et le Temple du Soleil', 77, '', '', 1969),
('La Planète Sauvage', 73, 'René Laloux', 'Animation', 1973);

INSERT INTO films (titre, duree, realisateur, genre, annee_originale) VALUES 
('Les 12 Travaux d\'Astérix', 79, '', 'Animation', 0),
('Les Cygnes Sauvages', 62, '', 'Animation', 0),
('La Ballade des Dalton', 80, '', 'Animation', 0),
('Le Seigneur des Anneaux', 133, '', 'Animation', 1978),
('Les Misérables', 68, '', 'Animation', 0),
('Le Roi et l\'Oiseau', 85, '', 'Animation', 1980),
('Aladin et la lampe merveilleuse', 66, '', '', 0),
('Brisby et le Secret de NIMH', 79, '', '', 1982),
('Les Maitres du Temps', 79, 'René Laloux', '', 1982),
('Astérix et la Surprise de César', 77, '', '', 1985),
('Astérix chez les Bretons', 79, '', '', 1986),
('Gandahar', 80, 'René Laloux', 'Animation', 1988),
('Comment Wang Fô fut sauvé', 15, 'René Laloux', '', 1987),
('La Prisonnière', 7, 'René Laloux', '', 1988),
('Astérix et le coup du Menhir', 80, '', '', 1989),
('Kiki la petite sorcière', 99, '', '', 1989),
('Wallace & Gromit - Une Grande Expédition', 24, '', '', 1989),
('Un Mauvais Pantalon', 30, '', '', 1993),
('Astérix et les Indiens', 81, '', '', 1994),
('Rasé de Près', 32, '', '', 1995),
('Kirikou et la sorcière', 71, 'Michel Ocelot', '', 1998),
('Le Prince d\'Egypte', 95, '', 'Animation', 1998),
('Poulets en fuite', 81, 'Nick Park', 'Animation', 2000),
('Le Voyage de Chihiro', 120, '', '', 2001),
('Shrek', 87, '', '', 2001);

INSERT INTO films (titre, duree, realisateur, genre, annee_originale, collection) VALUES 
('Les Triplettes de Belleville', 93, '', '', 2004, ''),
('Tokyo Godfathers', 93, '', '', 2007, ''),
('', 93, '', '', 2010, ''),
('', 93, '', '', 2004, ''),
('', 93, '', '', 2007, ''),
('', 93, '', '', 2010, ''),
('', 93, '', '', 2004, ''),
('', 93, '', '', 2007, ''),
('', 93, '', '', 2010, ''),
('', 93, '', '', 2004, ''),
('', 93, '', '', 2007, ''),
('', 93, '', '', 2010, ''),
('', 93, '', '', 2004, ''),
('', 93, '', '', 2007, ''),
('', 93, '', '', 2010, ''),
('', 93, '', '', 2004, ''),
('', 93, '', '', 2007, ''),
('', 93, '', '', 2010, ''),
('', 93, '', '', 2004, ''),
('', 93, '', '', 2007, ''),
('', 93, '', '', 2010, ''),
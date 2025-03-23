# animals-hospital

Nécessaire d'avoir Postman

0/ Installation
Cloner le projet
Importer la collection Postman
Dans le dossier du projet, faire 
    composer i
    php bin/console make:migration
    php bin/console doctrine:migrations:migrate
    symfony serve
Remplacer les {BASE_URL} des requêtes et body avec l'url de votre application

1/ Ajouter les utilisateurs de base

Importer les utilisateurs de base : php bin/console doctrine:fixtures:load --append
3 util : director (direction@email.fr), veterinarian (veterinarian@email.fr), assistant (assistant@email.fr)
mdp : 123

2/ Créer les premiers Clients

Se connecter en tant qu'assistant, vétérinaire ou directeur et copier le token
Sur Postman -> dossier Clients -> Add Client -> ajouter le token dans Authorization -> remplir la clef fullname

3/ Ajouter des photos d'animaux (optionnel)
Se connecter en tant qu'assistant ou vétérinaire et copier le token
Sur Postman -> dossier Medias -> Add Media -> ajouter le token dans Authorization -> remplir la valeur de la clef file avec une image

3/ Créer les premiers Animaux

Se connecter en tant qu'assistant et copier le token
Sur Postman -> dossier Animals -> Add Animal -> ajouter le token dans Authorization -> remplir la clef fullname

4/ Créer les premiers rendez-vous

Se connecter en tant qu'assistant et copier le token
Sur Postman -> dossier Appointments -> Add Appointment -> ajouter le token dans Authorization -> remplir les valeurs

5/ Créer des traitements

Se connecter en tant que vétérinaire et copier le token
Sur Postman -> dossier Treatments -> Add Treatments -> ajouter le token dans Authorization -> remplir les valeurs


Un user peut changer ton propre mot de passe, mais c'est la seule modification qu'il peut faire
Le directeur pour changer toutes les informations d'un user, sauf son mdp
Les assistants peuvent mettre n'importe quels vétérinaires sur un rendez-vous. Par contre, un vétérinaire ne peut se mettre que lui-même, et pas un autre vétérinaire, sur un rendez-vous
Un vétérinaire ne peut changer que le statut (programmé/ en cours/ terminé) et le vétérinaire sur un rendez-vous. Il ne peut pas changer les autres propriétés d'un rendez-vous


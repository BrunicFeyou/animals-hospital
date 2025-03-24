# animals-hospital

Ce projet comprend le travail de Brunic Feyou & Aurore Dimech pour la matière Architecture API.

## Prérequis & Installation

Il est nécessaire d'avoir Postman ou un logiciel permettant d'éxécuter des actions sur des ressources.
Il est également conseillé d'avoir un logiciel permettant d'accéder à PHPMyAdmin, ou une autre base de données.

Pour mettre en place ce projet, veuillez suivre les étapes suivantes :
1. Cloner le projet (`git clone git@github.com:BrunicFeyou/animals-hospital.git`)
2. Importer la collection Postman (télécharger `Animal_hospital.postman_collection.json` et le glisser dans postman)
3. Dans le dossier du projet, faire :
    - `composer i`
    - `php bin/console d:d:c` - cela créera une base de données PHPMyAdmin appelée 'Animal_hospital' ; si vous souhaitez utiliser une autre base de données, veuillez modifier le .env
    - `php bin/console make:migration`
    - `php bin/console doctrine:migrations:migrate`
    - `php bin/console lexik:jwt:generate-keypair`
    - `symfony serve`
4. Remplacer les {BASE_URL} des requêtes Postman (dans l'url et les body) avec l'url de votre application

## Créations des premières données

Avant de pouvoir librement profiter de cette api, il est conseillé de faire les actions suivantes :

### 1. Ajouter les utilisateurs de base

La première étape est d'importer les utilisateurs de base avec la commande `php bin/console doctrine:fixtures:load --append`
Suite à cette commande, 3 utilisateurs apparaitront dans votre base de données :
- Un director, avec l'email direction@email.fr, et le rôle de directeur (ROLE_DIRECTOR)
- Un vétérinaire, avec l'email veterinarian@email.fr, et le rôle de vétérinaire (ROLE_VETERINARIAN)
- Un assistant, avec l'email assistant@email.fr, et le rôle d'assistant (ROLE_ASSISTANT)

Il est possible de se connecter aux profils de ces trois utilisateurs avec leur adresse mail, et le mot de passe "123".
Pour se connecter, allez sur Postman, puis dans le dossier Users, et utilisez l'action appelée `Test User Connection`

### 2. Créer les premiers clients

Il faut ensuite créer les premiers clients. Pour cela, veuillez vous connecter en tant qu'assistant, vétérinaire ou directeur, et copiez le token que vous obtenez suite à cette connexion.
Puis, sur Postman, allez dans le dossier Clients et cliquez sur l'action nommée Add Client.
Dans l'onglet Authorization, ajoutez le token précédemment obtenu, puis remplissez la clef `fullname` dans l'onglet body sous l'option raw.

### 3. Ajouter des photos d'animaux (optionnel)

On peut décider, bien que cela ne soit pas obligatoire, d'ajouter des images qu'il sera par la suite possible de lier à des animaux.
Pour cela, il faut se connecter en tant qu'assistant ou vétérinaire, et copier le token obtenu suite à cette connexion.
Puis, sur Postman, allez dans le dossier Medias et cliquez sur l'action nommée Add Media.
Dans l'onglet Authorization, ajoutez le token précédemment obtenu, puis remplissez la clef `file` avec une image dans l'onglet body sous l'option form-data.

### 4. Créer les premiers animaux

Maintenant, nous avons la possibilité de créer les premiers animaux.
Pour cela, il faut se connecter en tant qu'assistant, et copier le token obtenu suite à cette connexion.
Puis, sur Postman, allez dans le dossier Animals et cliquez sur l'action nommée Add Animal.
Dans l'onglet Authorization, ajoutez le token précédemment obtenu, puis remplissez les clefs suivantes dans l'onglet body sous l'option raw :
-  `name` : obligatoire, sous format d'une string
-  `race` : obligatoire, sous format d'une string
-  `birthDate` : obligatoire, sous format d'une d'une datetime
-  `picture` : optionnelle, sous format d'une relation vers une ressource présente dans la table Media
-  `owner` : obligatoire, sous format d'une relation vers une ressource présente dans la table Client

Il sera par la suite possible d'ajouter des éléments dans la clef `appointments`. Cependant, puisqu'à cette étape il n'existe pas encore de rendez-vous, il n'est pas conseillé de le faire dans le cas présent.

### 5. Créer les premiers rendez-vous

Il faut ensuite créer des rendez-vous. Pour cela, veuillez vous connecter en tant qu'assistant, et copiez le token que vous obtenez suite à cette connexion.
Puis, sur Postman, allez dans le dossier Appointments et cliquez sur l'action nommée Add Appointment.
Dans l'onglet Authorization, ajoutez le token précédemment obtenu, puis remplissez les clefs suivantes dans l'onglet body sous l'option raw :
- `appointmentDate` : obligatoire, sous format d'une datetime
- `reason` : obligatoire, sous format d'une string
- `animal` : obligatoire, sous format d'une relation vers une ressource présente dans la table Animal
- `veterinary` : optionnel, sous format d'une relation vers une ressource présente dans la table User
    - La ressource en question doit avoir le role 'ROLE_VETERINARY'
- `assistant` : optionnel, sous format d'une relation vers une ressource présente dans la table User.
    - La ressource en question doit avoir le role 'ROLE_ASSISTANT'
- `state` : optionnel, sous format d'une string.
    - La string en question doit correspondre à l'un des 3 cas suivants : `Programmé`, `En cours`, `Terminé`.
    - Pour que la valeur `Terminé` soit acceptée, il est nécessaire que la valeur de `paymentStatus` soit `true`.
    - Si aucune valeur n'est renseignée, alors un état de base (Programmé) sera appliqué par défault
- `paymentStatus` : optionnel, sous format d'un boolean
    - Si aucune valeur n'est renseignée, alors l'état par défaut sera `false`

### 6. Créer des traitements

Enfin, nous avons pouvons de créer des traitements.
Pour cela, il faut se connecter en tant que vétérinaire, et copier le token obtenu suite à cette connexion.
Puis, sur Postman, allez dans le dossier Treatments et cliquez sur l'action nommée Add Treatment.
Dans l'onglet Authorization, ajoutez le token précédemment obtenu, puis remplissez les clefs suivantes dans l'onglet body sous l'option raw :
- `name` : obligatoire, sous format d'une string
- `description` : optionnel, sous format d'une string
- `price` : obligatoire, sous format d'un float
- `duration` : obligatoire, sous format d'une string
- `appointments` : optionnel, sous format d'un tableau de relations vers des ressources présentes dans la table Appointment

## Informations supplémentaires

Un utilisateur (une ressource de la table User) ne peut changer que son propre mot de passe. Seuls les utilisateurs avec le role 'ROLE_DIRECTOR' peuvent modifier les autres informations. Ainsi, un directeur peut changer toutes les informations d'un utilisateur à l'exception de son mot de passe -et sauf s'il s'agit de son propre profil-. Un vétérinaire ou un assistant ne peut quant à lui changer que son propre mot de passe.

Les assistants peuvent mettre n'importe quel vétérinaire sur un rendez-vous. Par contre, un vétérinaire ne peut se mettre que lui-même, et pas un autre vétérinaire, sur un rendez-vous.

Un vétérinaire ne peut changer que le statut (Programmé/ En cours/ Terminé) d'un rendez-vous, et le vétérinaire sur ce rendez-vous. Il ne peut pas changer les autres propriétés d'un rendez-vous.


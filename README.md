# Application Web Procost

### Auteur :
- Nom : Moniez
- Prénom : Franck
- Email : franck.moniez90@gmail.com



### Bundle :
- KnpPaginatorBundle : Utilisé pour la pagination des données
- Les bundles pour la fonction de recherche m'ont paru plus compliqués à installer/configurer qu'à faire soi-même

### Fonctionnalitées implantées :
Toutes les fonctionnalitées demandées ont été implantées, à savoir entre autres :
- Edition / suppression / modification des projets, employés et métiers soumises à conditions (projet en production, employé archivé, métier "utilisé", etc). Les contrôleurs testent les objets visés par les routes et redirigent en cas d'erreurs etc...
- Affichage paginé des informations (détails, listes de projets / employés / métiers)
- Fonction de recherche (nom de projet) présente sur tout le site, sauf sur la page de résultats
- Les noms prénoms, noms de projet pointent tous vers le détail concerné
- Les asserts sur les entity sécurisent le "back"
- Les fixtures ont été générées en partie aléatoirement (temps de production par exemple)

Quelques éléments ont été ajoutés indépendemment du cahier des charges :
- Ajout d'une URL d'image dans l'entité Employee, affichage de cette dernière dans le suivi
- Le footer "colle" au bas de page
- Les contenus des tableaux HTML ont été centrés par une classe CSS de boostrap (text-center), l'application d'un style CSS conventionnel ne fonctionnait pas correctement ( boucles for en twig et sélecteur css incompatibles ? )
- L'implantation de la fonction de recherche dans les contrôleurs aurait pu être simplifiée (réécriture de code importante)

---

## Remarques diverses :

- Je vous rends ce projet très en avance car je prends l'avion le dimanche 22 avril pour revenir en France le 5 mai (je n'aurai pas internet durant le séjour). C'est pour cette raison que je n'ai pas eu le temps de factoriser et commenter plus le code-source
- Je vous joint l'URL de mon dépôt github, dans le cas ou BitBucket ne fonctionnerait pas (repo public) : https://github.com/Franck90/procost

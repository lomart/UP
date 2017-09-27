# UP
Universal Plugin for Joomla

Ce plugin pour joomla permet d'insérer du contenu dans les articles et les modules. 

Il se compose de scripts de base qui ont pour rôle d'analyser les shortcodes saisis par le rédacteur de l'article pour le diriger vers un script action qui le traitera et renverra le contenu HTML à afficher.

De par son concept, UP est destiné à être complété par de nouvelles actions au fur et à mesure des besoins et des apports de différents développeurs.

Voir démo sur http://up.lomart.fr

## Il est destiné à plusieurs types d'utilisateurs de Joomla :

- **Le novice** : 
  - UP lui propose un tas de fonctionnalités en français. 
  - Les recherches sur la JED et l'installation de multiples extensions sont évitées. 
  - UP évite, dans de nombreux cas, de passer en mode code avec TinyMCE et JCE et de modifier les fichiers CSS. 
  - Sur un forum, il est facile de fournir un shortcode pour aider à la réalisation d''opérations délicates.
- **Le webmaster** : 
  - il est assez facile d'expliquer à un client comment l'utiliser. 
  - UP peut créer une interface de saisie qui évite à son client de saisir du HTML. 
  - UP peut personnaliser le style d'une page directement d'un article.
  - UP évite l'installation de multiples extensions
- **Le programmeur** : 
  - la phase récupération du shortcode et de ses options est inutile, on entre directement dans le vif du besoin: coder la fonctionnalité. 
  - Il est facile et rapide de tester une fonctionnalité ou un script trouvé sur le Net
  - UP gère le multilangage en affichage et lors de la saisie des shortcodes
  - UP est léger pour Joomla. La suppression d'une action ne laisse aucune trace. 

## Les règles de base pour la conception de ce plugin:

- une compatibilité à 99% avec les éditeurs wysiwyg (TinyMCE, JCE ...) 
- une documentation accessible et à jour. Elle est construite automatiquement par lecture du script des actions. Elle peut être traduite.
- un impact minimal sur Joomla. Une action non utilisée, c'est uniquement de la place disque pas des ressources serveur
- la possibilité d'ajouter de nouvelles actions pour les personnes ayant des notions de PHP
- la langue française est prioritaire avec la possibilité d'une traduction lors des affichages, mais surtout pour la saisie des instructions
- une installation/désinstallation des actions uniquement par l'ajout/suppression de son dossier
- le coeur du plugin est mis à jour automatiquement par la procédure update de Joomla. Vos actions personnalisées ne sont pas modifiées. 

## Crédits

Initiateur : Loïc Martin (aka Lomart) | https://up.lomart.fr 

Les actions utilisent des scripts libres trouvés sur le Net. Les liens vers les auteurs se trouvent dans la documentation des actions concernées. 

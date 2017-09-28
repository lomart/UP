![LOGO UP](https://up.lomart.fr/images/template/up-logo-carre.png)

Ce plugin pour joomla permet d'insérer du contenu dans les articles et les modules du CMS **Joomla!**. 

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

----------------------
This plugin for joomla allows to insert content in articles and modules.

It consists of basic scripts whose role is to analyze the shortcodes entered by the article's editor to direct it to an action script that will process it and return an HTML content to be displayed.

By its concept, UP is intended to be complemented by new actions as and when needs and contributions of different developers.

See demo at http://up.lomart.fr

It is intended for several types of users of Joomla:

- The novice:
  - UP offers a lot of features in French.
  - Research on JED and installation of multiple extensions are avoided.
  - UP avoids, in many cases, switching to code mode with TinyMCE and JCE and editing CSS files.
  - On a forum, it is easy to provide a shortcode to help perform delicate operations.
- The webmaster:
  - it is easy enough to explain to a customer how to use it.
  - UP can create an input interface that prevents its client from entering HTML.
  - UP can customize the style of a page directly from an article.
  - UP avoids the installation of multiple extensions
- The programmer:
  - the recovery phase of the shortcode and its options is unnecessary, one enters directly into the heart of the need: code functionality.
  - It is easy and fast to test a feature or a script found on the Net
  - UP handles multilanguage in display and when entering shortcodes
  - UP is lightweight for Joomla. Deleting an action leaves no trace.

The basic rules for the design of this plugin:

- 99% compatibility with wysiwyg editors (TinyMCE, JCE ...)
- accessible and up-to-date documentation. It is built automatically by reading the action script. It can be translated.
- minimal impact on Joomla. An unused action is only the disk space, not server resources
- the possibility of adding new actions for people with PHP basic knowledge
- the French language has priority with the possibility of a translation during the displays, but especially for the input of the instructions
- an installation / uninstallation of the actions only by the addition / deletion of its file
- the core of the plugin is updated automatically by the Joomla update procedure. Your custom actions are not changed.

Credits

Initiator: Loïc Martin (aka Lomart) | https://up.lomart.fr

Actions use free scripts found on the Net. Links to the authors can be found in the documentation of the actions concerned.

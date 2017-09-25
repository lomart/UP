<?php

/**
 *
 * @package plg_UP for Joomla! 3.0+
 * @version $Id: up.php 2017-07-02 $
 * @author Lomart
 * @copyright (c) 2017 Lomart
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 * */
//namespace up;
defined('_JEXEC') or die('Restricted access');

class plgContentUP extends JPlugin {

// jimport('joomla.plugin.plugin');

    public $upPath = 'plugins/content/up/';
    public $actionPath = 'plugins/content/up/actions/';

    public function __construct(&$subject, $params) {
        parent::__construct($subject, $params);
        $this->LoadLanguage();
    }

    function onContentPrepare($context, &$article, &$params, $limitstart = 0) {

        $app = JFactory::getApplication();

        // ========> DOIT-ON EXECUTER ?
        // uniquement en frontend
        if ($app->isAdmin())
            return false;

        // Chargement systematique de la feuile de style
        if ($this->params->def('loadcss', '1')) {
            JHtml::stylesheet('plugins/content/up/assets/up.css');
        }

        // sortie directe si pas de texte a traiter
        if (!isset($article->text)) {
            return false;
        }
        if (trim($article->text) == '') {
            return false;
        }
        // pas d'analyse pour les listes d'articles
        if ($context == "com_content.category") {
            if ($app->input->get('layout', 0) !== 'blog') {
                return false;
            }
        }
        // liste des shortcodes utilisables
        $tag = $this->params->def('tagname', 'up|xx');
        $regexopen = '/\{(?:' . $tag . ')\b(.*)\}/siU';

        // retour direct si pas de upAction dans l'article
        if (!preg_match($regexopen, $article->text))
            return false;

        // ==========> C'EST BON, IL FAUT Y ALLER !
        // fonctions utilitaires pour les actions
        include_once $this->upPath . 'upAction.php';

        // charger le dictionnaire
        $dico = file_get_contents('dico.json', FILE_USE_INCLUDE_PATH);
        $dico = json_decode($dico, true);

        // ===== NETTOYAGE AJOUT EDITEURS
        // on supprime la balise P ajoute par les editeurs wysiwyg
        // note: $article->text contient introtext ou fulltext selon $context
        $regex = '#(<p[^<]*>)({/?[' . $tag . '].*)(</p>)#i';
        $article->text = preg_replace($regex, '$2', $article->text);

        $id = 0;                    // compteur de boucle
        $classObjList = array();  // les objets ACTION initialisés
        // ==== Recherche du shortcode ouvrant
        while (preg_match($regexopen, $article->text, $matches, PREG_OFFSET_CAPTURE)) {
            // reset variables
            unset($actionUserName);    // nom de l'action saisi dans le shortcode
            unset($actionClassName);   // nom du dossier, script php et classe de l'action
            unset($options_user);

            // identifiant unique pour l'action
            if (isset($article->id)) {  // si article
                $options_user['id'] = 'up-' . $article->id . '-' . ++$id;
            } else { // si module
                $options_user['id'] = 'up-' . ++$id;
            }

            // position shortcode pour remplacement
            $replace_deb = $matches[0][1];
            $replace_len = strlen($matches[0][0]);

            // -- analyse des options du shortcode
            $content = '';
            $arr = explode('|', $matches[1][0]);
            foreach ($arr as $tmp) {
                $tmp = preg_split("/=/", trim($tmp), 2); // permet = dans argument
                // le mot clé tel que saisi
                $key = strtolower(trim($tmp[0]));
                // sa valeur (true si aucune)
                $value = (count($tmp) == 2) ? trim($tmp[1]) : true;

                // la 1ere option est le nom de l'action
                if (!isset($actionUserName)) {
                    $actionUserName = $key;  // tel que saisi dans article
                    // le mot clé traduit pour le script action
                    if (array_key_exists($key, $dico)) {
                        $key = $dico[$key];
                    }
                    $key = str_replace('-', '_', $key); // tel que le script
                    $actionClassName = $key;  // Nom dossier et classe
                } else {
                    // le mot clé traduit pour le script action
                    if (array_key_exists($key, $dico)) {
                        $key = $dico[$key];
                    }
                }
                // analyse de l'argument de l'option
                if (substr(strtolower($value), 0, 5) == 'lang[') {
                    if (preg_match('#lang ?\[(.*)\]#', $value, $tmp) !== false) {
                        if (isset($tmp[1]))
                            $value = $this->lang($tmp[1]);
                    }
                }
                $options_user[$key] = $value;
            }

            // -- on recherche un potentiel shortcode fermant
            $actionCleanName = strtr($actionUserName, './{}[]', 'xxxxxx');
            $regexclose = '/\{\/' . $tag . ' +' . $actionCleanName . '\}/siU';

            if (preg_match($regexclose, $article->text, $matches, PREG_OFFSET_CAPTURE)) {
                $content_deb = $replace_deb + $replace_len;
                $content_len = $matches[0][1] - $content_deb;
                $content = substr($article->text, $content_deb, $content_len);
                // maj longueur shortcode complet
                $replace_len += $content_len + strlen($matches[0][0]);
            }

            // ==== EXECUTION DE L'ACTION
            $text = '';

            // le chemin du script
            $actionfile = 'actions/' . $actionClassName . '/' . $actionClassName . '.php';

            // --- instanciation de l'action
            // si premier appel de l'action
            if ($text == '') {
                if (array_key_exists($actionClassName, $classObjList) == false) {
                    // on charge la classe de l'action
                    if (@include_once $actionfile) {
                        $classObjList[$actionClassName] = new $actionClassName($actionClassName);
                        $classObjList[$actionClassName]->actionUserName = $actionUserName;
                        $classObjList[$actionClassName]->init();
                    } else {
                        $text = $this->info_debug(JText::sprintf('UP_ACTION_NOT_FOUND', $actionUserName));
                    }
                } else {
                    $classObjList[$actionClassName] = new $actionClassName($actionClassName);
                }
            }

            if ($text == '') {
                // l'objet est cree et initialisé
                $classObjList[$actionClassName]->actionUserName = $actionUserName;
                $classObjList[$actionClassName]->options_user = $options_user;
                $classObjList[$actionClassName]->content = $content;
                $classObjList[$actionClassName]->article = $article;
                $classObjList[$actionClassName]->actionprefs = $this->params->get('actionprefs');
                $classObjList[$actionClassName]->usehelpsite = $this->params->get('usehelpsite', '2');
                $classObjList[$actionClassName]->urlhelpsite = $this->params->get('urlhelpsite');
                $classObjList[$actionClassName]->demopage = '';
                $classObjList[$actionClassName]->dico = $dico;

                // on exécute l'action
                $text = $classObjList[$actionClassName]->run();
            }

            // on remplace le shortcode par le code retourné par l'action
            $article->text = substr_replace($article->text, $text, $replace_deb, $replace_len);
        } // while preg_match

        unset($classObjList);
        return true;
    }

// onContentPrepare

    /**
     * fonction utilitaire pour UP
     * @param  [string] $str [alternative de traduction sous la forme "en=apple & fr=pomme"]
     * @return [string]      [la traduction dans la langue]
     */
    function lang($str) {
        // test langue uniquement sur les 2 premiers caractères
        $lang = substr(JFactory::getLanguage()->getTag(), 0, 2);
        // valeur de retour par défaut
        $out = trim($str);
        // recherche du motif dans $str
        if (preg_match_all('#(\w\w) *= *(.*);#U', $out . ';', $tmp) !== false) {
            $trad = array_combine($tmp[1], $tmp[2]);

            if (isset($trad[$lang])) {
                $out = $trad[$lang];   // dans la langue
            } elseif (isset($trad['en'])) {
                $out = $trad['en'];    // sinon en anglais
            } else {
                $out = $trad($tmp[1][0]); // sinon le premier
            }
        }
        return trim($out);
    }

// lang

    function info_debug($txt) {
        return ' <mark style="color:red;font-weight:bolder"> &rArr; ' . $txt . '</mark>';
    }

}

// class

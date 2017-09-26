<?php

/**
 * UP - Universal Plugin
 * Fonctions utilitaires pour les actions
 * @author    Lomart
 * @version   1.0
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 */
defined('_JEXEC') or die;

class upAction extends plgContentUP {

    // en public pour JDump
    // public $actionClassName;  // nom de la classe et du dossier de l'action
    // public $actionUserName;   // nom de l'action tel que saisi dans shortcode
    // public $options_user;     // les options saisies dans le shortcode
    // public $content;   			 // le contenu entre les shortcodes
    // public $article;   			 // le contenu complet de l'article
    // public $actionprefs;   	 // les préférences plugin pour les actions
    // public $dico;   					 // les correspondances de noms

    function __construct($name) {

        $this->name = $name;
        //  $this->actionPath = 'plugins/content/up/actions/' . $name .'/';
        $this->actionPath .= $name . '/';
        //  $this->upPath = 'plugins/content/up/';

        if ($this->name == '') {
            throw new \Exception('Erreur de programmation, veuillez s\'il ' .
            'vous plaît initialiser la variable plugName dans votre constructeur.');
        }

        return true;
    }

    /*     * ***************************************** load_file
     * charge un fichier CSS ou JS du dossier d'une action
     * @param  string  $ficname : nom et extension du fichier
     * @param  string  $ficpath  : chemin si different du dossier de l'action
     * @return none
     */

    function load_file($ficname, $ficpath = '') {

        if ($ficpath == '')
            $ficpath = $this->actionPath;

        if (file_exists($ficpath . $ficname) == false) {
            $this->msg_error(JText::sprintf('UP_FIC_NOT_FOUND', $ficpath . $ficname));
            return false;
        }

        switch (strtolower(pathinfo($ficname, PATHINFO_EXTENSION))) {
            case 'css' :
                JHtml::stylesheet($ficpath . $ficname);
                return true;

            case 'js' :
                JHtml::_('jquery.framework');
                JHtml::script($ficpath . $ficname);
                return true;

            default :
                $this->msg_error(JText::sprintf('UP_FIC_BAD_EXT', $ficpath . $ficname));
                return false;
        }
    }

    /*     * ***************************************** load_upcss
     * charge un fichier CSS ou JS du dossier d'une action
     */

    function load_upcss() {
        JHtml::stylesheet($this->upPath . 'assets/up.css');
        return true;
    }

    /*     * ********************************* load_jquery_code
     * ajoute du code jQuery ($code) en l'encapsulant
     * Par défaut le code est ajouté dans le head ($in_head)
     * sinon, il sera à la position d'appel
     */

    function load_jquery_code($code, $in_head = true) {
        $tmp = 'jQuery(document).ready(function($) {';
        $tmp .= $code;
        $tmp .= '});';
        if ($in_head) {
            // ajout du code dans head
            $doc = JFactory::getDocument();
            $doc->addScriptDeclaration($tmp);
            return '';
        } else {
            // code pour insertion dans code
            $tmp = '<script>' . $tmp . '</script>';
            return $tmp;
        }
    }

    /*     * ************************************ load_css_head
     * Ajoute du code CSS ($code) dans le head
     * */

    function load_css_head($code) {
        if (trim($code)) {
            $code = str_replace('[', '{', $code);
            $code = str_replace(']', '}', $code);
            $doc = JFactory::getDocument();
            $doc->addStyleDeclaration($code);
            return true;
        }
        return false;
    }

    /*     * ***************************** load_custom_code_head
     * Ajoute du code libre ()$code) dans le head de la page
     * exemple :
     * <link href="https://fonts.googleapis.com/css?family=xxx" rel="stylesheet">
     */

    function load_custom_code_head($code) {
        if (strlen(trim($code, ' \t\n\r\0\x0B')) > 0) {
            $doc = JFactory::getDocument();
            $doc->addCustomTag($code);
            return true;
        }
        return false;
    }

    /*     * *********************************** get_html_contents
     * Récupère un flux sur le web ($url) avec un timeout de 5s ($timeout)
     * @return [string]      [le contenu recuperer]
     */

    // TODO intercepter message d'erreur système

    function get_html_contents($url, $timeout = 5) {
        $ctx = stream_context_create(array(
            'http' => array('timeout' => $timeout)
        ));

        $out = file_get_contents($url, 0, $ctx);
        if ($out === false) {
            $this->msg_error(JText::sprintf('UP_TIMEOUT_FOR', $url));
            return '';
        } else {
            return $out;
        }
    }

    /*     * ***************************************** msg_error
     * ajoute un message d'erreur dans la file des messages de Joomla
     * on affiche le nom de l'action tel que saisi par le rédacteur
     */

    function msg_error($text) {
        $app = JFactory::getApplication();
        $app->enqueueMessage('[UP ' . $this->actionUserName . '] ' . $text, 'error');
    }

    /*     * ******************************************* msg_info
     * ajoute un message d'information dans la file des messages de Joomla
     * */

    function msg_info($text, $title = 'informations') {
        $app = JFactory::getApplication();
        $app->enqueueMessage($text, '<b>[UP] ' . $title . '</b>');
    }

    /*     * ************************************** get_attr_tag
     * retourne un array tous les attributs de la balise HTML ($tag)
     * $force est la liste des attributs a créer pour s'assurer de leurs disponibilités
     * ----------------------------------------------
     * Utilisation : modifier les attributs avant de reconstruire la balise
     * ******************************* */

    function get_attr_tag($tag, $force = 'id,class,style') {
        // création du tableau avec les valeurs forcées
        foreach (explode(',', $force) as $key) {
            $attr[$key] = '';
        }
        // récupération des attributs de la balise
        if (preg_match_all('# (.*)="(.*)"#U', $tag, $matches)) {
            $tmp = array_combine(array_change_key_case($matches[1]), $matches[2]);
            $attr = array_merge($attr, $tmp);
        }

        return $attr;
    }

    /*     * ************************************** set_attr_tag
     * retourne une chaine balise HTML avec ses attributs non vides
     * @var $tag  string  balise HTML
     * @var $attr array   liste des attributs
     * @var $close bool   tag fermant si true
     * ----------------------------------------------
     * Utilisations :
     * reconstruire la balise apres modification des attributs
     * ******************************* */

    function set_attr_tag($tag, $attr, $close = false) {
        $out = '<' . $tag;
        foreach ($attr as $key => $val) {
            if ($val === null) {
                // attribut sans valeur
                $out .= ' ' . $key;
            } else {
                if (trim($val)) {
                    $out .= ' ' . $key . '="' . trim($val) . '"';
                }
            }
        }
        $out .= '>';
        if ($close)
            $out .= '</' . $tag . '>';

        return $out;
    }

    /*     * ****************************************** stradd
     * Ajoute une chaine a une autre avec separateur si necessaire
     * ******************************* */

    /**
     * Ajoute une chaine 'non vide' à une autre en insérant un séparateur
     * ex: str_append('titre','soustitre',' ','<small>','</small>')
     * retourne:  'titre <small>soustitre</small>'
     * @param  string $str    chaine cible
     * @param  string $add    chaine à ajouter
     * @param  string $sep    séparateur
     * @param  string $prefix texte avant la chaine
     * @param  string $suffix texte après la chaine
     * @return string         chaine completée
     */
    function str_append($str, $add, $sep = ' ', $prefix = '', $suffix = '') {
        if (trim($add)) {
            $str = trim($str);
            if ($str && substr($str, strlen($sep) * -1) != $sep) {
                $str .= $sep;
            }
            $str .= $prefix . $add . $suffix;
        }
        return $str;
    }

    /** versions raccourcies de str_append qui modifie directement la chaine d'origine */
    function add_str(&$str, $add, $sep = ' ', $prefix = '', $suffix = '') {
        $str = $this->str_append($str, $add, $sep, $prefix, $suffix);
        return $str;
    }

    function add_class(&$str, $newclass, $prefix = '') {
        $str = $this->str_append($str, $newclass, ' ', $prefix);
        return $str;
    }

    function add_style(&$str, $property, $val) {
        $str = $this->str_append($str, $val, ';', $property . ':');
        return $str;
    }

    /*     * ******************************************* ctrl_unit
     * Retourne $size complété par $unit[0] si nécessaire
     * auto et inherit ne sont pas géré volontairement
     * @param  [type] $size   valeur. ex: 10px, 10, 15%
     * @param  [type] $unit   unité autorisée.
     * @return [type]              [description]
     */

    function ctrl_unit($size, $unit = 'px,%,em,rem') {
        $unit = explode(',', strtolower($unit));
        if (preg_match('#([0-9.]*)(.*)#', strtolower($size), $match)) {
            $size = intval($match[1]);
            if ($size > 0) {
                $size .= (in_array($match[2], $unit)) ? $match[2] : $unit[0];
            }
        }
        return $size;
    }

    /*     * *************************************** link_humanize
     * Retourne l'UNC nettoyé des chemins, extensions, underscore et autres tirets
     * @var [string]   chemin fichier ou url
     * @return [string]
     */

    function link_humanize($unc) {
        // TODO
        return $unc;
    }

    /*     * ********************************* ctrl_content_exists
     * teste si le shortode contient du contenu, affiche un message si besoin
     * @return [bool] [true si contenu]
     */

    function ctrl_content_exists() {
        if ($this->content == '') {
            $this->msg_error(JText::_('UP_NO_CONTENT'));
            return false;
        }
        return true;
    }

    /**
     * retourne vrai si $content contient différentes parties séparées par {===}
     */
    function ctrl_content_parts($content) {
        return strpos($content, '{===');
    }

    /**
     * retourne un array avec les différentes parties séparées par {===}
     * en supprimant les balises <p> mise par l'éditeur wysiwyg
     */
    function get_content_parts($content) {
        $content_part = preg_split('/(?:\<p\>)?\{\={3,}\}(?:\<\/p\>)?/i', $content);
        return $content_part;
    }

    /*     * ************************************** CTRL_OPTIONS
     * retourne un array avec toutes les options geres par l'action
     * avec les valeurs saisies dans le shortcode
     * la recherche des keys est case-insensitive
     * les cles retournees sont case-sensitive
     * ----------------------------------------------
     * Utilisation : tableau de toutes les options pretes a l'emploi
     * ******************************* */

    function ctrl_options($options_def, $js_options_def = []) {

        // === création options génériques
        foreach ($options_def as $key => $val) {
            // -- créer les options indicées pour éviter les erreurs
            // todo : les créer par le script action ??
            if (substr($key, -2) == '-*') {
                for ($i = 1; $i <= 6; $i++) {
                    $options_def[substr($key, 0, -2) . '-' . $i] = '';
                }
            }
        }

        // -- si l'action n'a pas d'argument, on met la valeur par defaut
        if ($this->options_user[$this->name] === true) {
            $this->options_user[$this->name] = $options_def[$this->name];
        }

        // -- fusion tableau def
        $out = array_merge($options_def, $js_options_def);

        // -- table correspondance pour recherche case insensitive
        foreach ($out as $key => $val) {
            $out_lowercase[strtolower($key)] = $key;
        }

        // -- ajout des valeurs saisies par utilisateur
        foreach ($this->options_user as $key => $val) {
            if (array_key_exists($key, $out_lowercase)) {
                $key = $out_lowercase[$key];
                settype($val, gettype($out[$key]));
                $out[$key] = $val;
            } else {
                // on prévient si le motclé n'est pas géré
                if (!in_array($key, array('id', '?', 'debug')) && substr($key, -1, 1) != '*') {
                    $this->msg_error(JText::sprintf('UP_UNKNOWN_OPTION', $key . '=' . $val));
                    $this->options_user['?'] = true; // force affichage aide (1 seule fois)
                }
            }
        }

        // demande d'aide
        if (array_key_exists('?', $this->options_user)) {
            $info = $this->up_action_options($this->name);
            $title = $this->name;
            if ($this->usehelpsite > 0 && $this->demopage != '') {
                $title .= ' [<a href="' . $this->demopage . '"';
                if ($this->usehelpsite == 2) {
                    $title .= ' target = "_blank"';
                }
                $title .= ">DEMO</a>]";
            }
            $txt = '<p>';
            while (list($key, $val) = each($info)) {
                $txt .= "<b>$key</b>&nbsp;:&nbsp;$val<br>";
            }
            $txt .= '</p>';
            $this->msg_info($txt, JText::sprintf('UP_ACTION_OPTIONS', $title));
        }
        // demande debug
        if (array_key_exists('debug', $this->options_user)) {
            $debug = '<ul>';
            while (list($key, $val) = each($out)) {
                $debug .= "<li><b>$key</b>&nbsp;=>&nbsp;$val</li>";
            }
            $debug .= '</ul>';
            $this->msg_info($debug, JText::sprintf('UP_INFOS_DEBUG', $this->actionUserName));
        }

        // -- on retourne un array avec les cles dans la case attendue par le script
        //    et les valeurs saisies par utilisateur
        return $out;
    }

// ctrl_options

    /*     * ******************************** only_using_options
     * retourne un array avec uniquement les parametres saisi dans le shortcode
     * la recherche des keys est case-insensitive
     * ----------------------------------------------
     * Utilisations :
     * - isoler les parametres JS
     * - reduire la chaine json d'initialisation
     * ******************************* */

    function only_using_options($options_def) {
        $out = [];
        // -- table pour recherche case insensitive
        foreach ($options_def as $key => $val) {
            $options_key[strtolower($key)] = $key;
        }

        // -- recup des params JS du shortcode
        foreach ($this->options_user as $key => $val) {
            if (array_key_exists($key, $options_key)) {
                $key = $options_key[$key];
                settype($val, gettype($options_def[$key]));
                if ($val != $options_def[$key]) { // pas si valeur par defaut
                    $out[$key] = $val;
                }
            }
        }

        // -- on retourne un array avec la cle dans la case attendue par le script
        return $out;
    }

    /*     * *********************************** add_options_json
     * ajoute et/ou actualise les options avec celles d'un fichier json
     * ----------------------------------------------
     * Utilisations : paramétres webmaster
     * ******************************* */

    function add_options_json(&$options_def, $jsonfile) {
        $json = file_get_contents($jsonfile, FILE_USE_INCLUDE_PATH);
        $json = json_decode($json, true);
        // on fusionne avec celles dans shortcode
        $options_def = array_merge($options_def, $json);
        return true;
    }

    /**
     * [json_arrtostr description]
     * @param  [type]  $array [description]
     * @param  integer $mode  [description]
     * @return [type]         [description]
     */
    function json_arrtostr($array, $mode = 1) {
        if (empty($array))
            return '';
        switch ($mode) {
            // méthode PHP
            case 1 :
                $out = json_encode($array, JSON_UNESCAPED_SLASHES);
                break;

            // méthode perso sans guillemet et gestion sous-clés
            case 2 :
                $out = '';
                foreach ($array as $key => $val) {
                    if (trim($val)) {
                        // ajout séparateur
                        if ($out)
                            $out .= ',';
                        // si :, c'est une sous-clé
                        if (strpos($val, ':') > 0) {
                            $out .= $key . ':' . '{' . $val . '}';
                        } else {
                            $out .= $key . ':' . $val;
                        }
                    }
                }
                if ($out)
                    $out = '{' . $out . '}';
                break;
        }
        return $out;
    }

    /** contrôle que l'argument soit dans la liste (sep virgule)
     *  corrige la case silencieusement si nécessaire
     *  return ??? TODO
     */
    function ctrl_argument(&$arg, $autorized_list) {
        $array_autorized_list = explode(',', $autorized_list);
        $regex = '#' . $arg . '\b#i';
        $out = preg_grep($regex, $array_autorized_list);
        if ($out) {
            $arg = current($out);   // on force la case
            return true;
        } else {
            $this->msg_info(JText::sprintf('UP_UNKNOWN_ARGUMENT', $arg, $autorized_list));
            $arg = current($array_autorized_list);   // on force sur 1er pour éviter erreur
        }
        return false;
    }

    /*     * ************************************ get_action_pref
     * Retourne la valeur pour une préf action (ex: apikey)
     * @param  [string] $key le mot-clé
     * @return [string]      valeur ou vide
     */

    function get_action_pref($key) {
        $regex = '#' . $key . ' *\= *(.*)$#';
        preg_match($regex, $this->actionprefs, $val);
        if (preg_match($regex, $this->actionprefs, $val) == 1) {
            return $val[1];
        }
        return false;
    }

    /*     * ***************************************************
      FONCTIONS DE GESTION INTERNE UP
     * *************************************************** */
    /*     * *********************************** up_actions_list
     * @return [array] la liste des actions
     */

    function up_actions_list() {
        $actionsFolder = __DIR__ . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR;
        $list = array(); // retour si vide
        $actionsPathList = glob($actionsFolder . '[!_]*', GLOB_ONLYDIR);

        foreach ($actionsPathList as $e) {
            $list[] = substr($e, strlen($actionsFolder));
        }
        return $list;
    }

    /*     * ********************************* set_demopage
     * affecte la propriété demopage avec l'URL de la page d'aide
     */

    function set_demopage($webpage = '') {
        if ($webpage == '') {
            // on remplace les underscores du nom de la classe par des tirets pour compatibilité avec les alias Joomla
            $this->demopage = $this->urlhelpsite . '/demo/action-' . str_replace('_', '-', $this->name);
        } else {
            $this->demopage = $webpage;
        }
    }

    /*     * ********************************* get_dico_synonym
     * Retourne une liste de tous les synonymes d'un mot-clé
     * @param  [string] $keyword [nom du mot clé]
     * @return [string]      [synonyme sour la forme: 1,un,one,ein ]
     */

    function get_dico_synonym($keyword) {
        $out = array();
        foreach ($this->dico as $key => $val) {
            if ($val == $keyword) {
                $out[] = $key;
            }
        }
        return implode(',', $out);
    }

    /*     * ************************************ shortcode2code
     * Retourne la chaine avec un shortcode UP neutralisé
     * @param  [string] $str [ligne à annalyser]
     * @return [string]      [ligne avec shortcode neutralisé]
     */

    function shortcode2code($str) {
        $motif = '#(?:\&\#123;|\{)(.*)(?:\&\#125;|\})#U';
        $replace = '<code><b>{</b>$1<b>}</b></code>';
        $out = preg_replace($motif, $replace, $str);
        return $out;
    }

    /*     * *********************************** up_action_infos
     * Retourne les infos dans l'entête du script PHP de l'action
     * @param  [string] $action_name nom de l'action
     * @param  [string] $keys        les infos a chercher
     * @return [array]  les infos de l'entete sous la forme : key => commentaire
     */

    function up_action_infos($action_name) {
        $actionFolder = $this->upPath . 'actions/' . $action_name . '/';
        $tmp = file_get_contents($actionFolder . $action_name . '.php');

        $out = '';

        // info dans entete script
        $desc = array();
        if (preg_match('#\/\*\*(.*)\*\/#siU', $tmp, $desc)) {
            $desc = array_map('trim', explode('*', $desc[1]));
            $desc = str_replace('{', '&#123;', $desc); // inactive les shortcodes dans commentaires
            $out['_shortdesc'] = '';
            $out['_longdesc'] = '';

            foreach ($desc as $lign) {
                $lign = trim($lign, ' *');
                if ($lign) {
                    if ($lign[0] == '@') {   //
                        // ligne avec @motcle  contenu
                        list($key, $val) = explode(' ', $lign . ' ', 2);
                        if (trim($val))
                            $out[trim($key, '@')] = $val;
                    } else {
                        // ligne description
                        if ($out['_shortdesc'] > '') {
                            $lign = $this->shortcode2code($lign);
                            $this->add_str($out['_longdesc'], $lign, '<br>');
                        } else {
                            $out['_shortdesc'] = $lign;
                        }
                    }
                }
            }
        }

        // Traduction disponible ?
        $lng = JFactory::getLanguage()->getTag();
        $infos_trad = array();
        if (file_exists($actionFolder . 'up/' . $lng . '.ini')) {
            $infos_trad = parse_ini_file($actionFolder . 'up/' . $lng . '.ini');
            if (isset($infos_trad['shortdesc'])) {
                $out['_shortdesc'] = $infos_trad['shortdesc'];
            }
            if (isset($infos_trad['longdesc'])) {
                $out['_longdesc'] = $this->shortcode2code($infos_trad['longdesc']);
            }
        }

        // Site de démonstration
        $out['_demopage'] = '';
        if (preg_match('#\$this->set_demopage\([w"]?(.*)[w"]?\)#', $tmp, $arrtmp) === 1) {
            if ($arrtmp[1] == '') {
                $out['_demopage'] = $this->urlhelpsite . '/demo/action-' . $action_name;
            } else {
                $out['_demopage'] = $arrtmp[1];
            }
        }
        return $out;
    }

    /*     * ******************************** up_action_options
     * Retourne un tableau avec les options de l'action
     * @param  [string] $action_name nom de l'action
     * @return [array]  les options sous la forme: option=defaut => commentaire
     */

    function up_action_options($action_name) {
        // on récupère le script php
        $actionFolder = $this->upPath . 'actions/' . $action_name . '/';
        $tmp = file_get_contents($actionFolder . $action_name . '.php');

        // Traduction disponible ?
        $lng = JFactory::getLanguage()->getTag();
        $comment_trad = array();
        if (file_exists($actionFolder . 'up/' . $lng . '.ini')) {
            $comment_trad = parse_ini_file($actionFolder . 'up/' . $lng . '.ini');
        }

        // options définies
        $optlist = array();
        $regexs = array('/\$options_def.*\((.*\);)/siU', '/\$js_options_def.*\((.*\);)/siU');
        foreach ($regexs as $regex) {
            // le contenu de $options_def ou $js_options_def
            if (preg_match($regex, $tmp, $deflist)) {
                $search = array('__class__', '$this->name');
                $deflist = str_replace($search, '\'' . $action_name . '\'', $deflist[1]);
                // les lignes avec une option
                $regex = '/\'(.*)\' *\=\>(.*)[\r|\n]/siU';
                preg_match_all($regex, $deflist, $options);

                for ($i = 0; $i < count($options[0]); $i++) {
                    $optionName = $options[1][$i]; // l'option
                    $key = $optionName;
                    list($val, $comment) = explode('//', $options[2][$i] . '//', 2);
                    $this->add_str($key, $this->get_dico_synonym($key), ' ', '(', ')');
                    $this->add_str($key, trim($val, ' ,\'/'), '=');  // option=defaut
                    if (isset($comment_trad[$optionName])) {
                        $optlist[$key] = $comment_trad[$optionName];  // commentaire traduit
                    } else {
                        $optlist[$key] = trim($comment, ' ,/');  // commentaire du script php
                    }
                }
            }
        }
        unset($optlist['id']);  // inutile, jamais argumenté dans shortcode

        return $optlist;
    }

    /*     * ****************************
     *  TRADUCTION
     * ***************************** */

    /*     * ****************************************** translate
     * retourne la traduction dans la langue utilisateur pour le mot-clé
     * si le motclé commence par xx=, on considère que c'est déjà des trads
     * @param  [type] $keyword [description]
     * @return [string]          [texte traduit]
     */

    function translate($arg) {
        $arg = trim($arg);
        $lang = JFactory::getLanguage()->getTag();
        // $lang='en';
        // valeur de retour par défaut
        $out = $arg;

        // ====> ALTERNATIVES TRADUCTION
        // par recherche du motif 'xx=texte;' dans $str (mini 2)
        if (preg_match_all('#(\w\w) *= *(.*);#U', $arg . ';', $tmp) > 1) {
            $trad = array_combine($tmp[1], $tmp[2]);
            $lang = substr($lang, 0, 2); // recherche sur la première partie
            // si trouvé, on retourne la meilleure traduction
            if (isset($trad[$lang])) {
                $out = $trad[$lang];      // 1-dans la langue
            } elseif (isset($trad['en'])) {
                $out = $trad['en'];       // 2-sinon en anglais
            } else {
                $out = $trad($tmp[1][0]); // 3-sinon le premier
            }
        } else { //====> MOTCLE A RECHERCHER DANS FICHIER LANGUE
            if (isset($this->tradaction) === false) {
                // on charge uniquement les fichiers si nécessaire
                $this->tradup = array();      // traductions générale
                $this->tradaction = array();  // traductions pour l'action
                // on charge les trads de l'action
                $inifile = $this->actionPath . 'up/' . $lang . '.ini';
                if (file_exists($inifile)) {
                    $this->tradaction = (parse_ini_file($inifile));
                }
                // on charge les trads générales à UP
                $inifile = $this->upPath . 'language/' . $lang . '/' . $lang . '.plg_content_up.ini';
                if (file_exists($inifile)) {
                    $this->tradup = (parse_ini_file($inifile));
                }
                // on merge avec celles de up (traduction de l'action prioritaire)
                $this->tradaction = array_merge($this->tradup, $this->tradaction);
            }
            // si le mot clé est trouvé, on retourne
            if (isset($this->tradaction[$arg])) {
                $out = $this->tradaction[$arg];
            }
        } // else
        return $out;
    }

}

// class upaction

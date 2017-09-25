<?php

/**
 * affiche du contenu si toutes les conditions sont remplies
 * <i>Reprise du plugin LM-Filter de Lomart</i>
 *
 * Le contenu si faux est optionnel. Il doit être après le contenu si vrai et séparé par {===} (au minima 3 signes égal)
 * {up filter | datemax=20171225} contenu si vrai {====} contenu si faux {/up filter}
 * {up filter | admin} contenu si vrai  {====} contenu si faux {/up filter}
 *   --> affiche si admin connecté. admin=0 affiche si admin NON connecté
 *
 * @author    Lomart
 * @version   1.0
 * @license   <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL</a>
 */
defined('_JEXEC') or die;

class filter extends upAction {

    function init() {
        // aucune
    }

    function run() {
        // vérifier que du contenu est saisi entre les shortcodes
        if (!$this->ctrl_content_exists()) {
            return false;
        }
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut
        // il est indispensable de tous les définir ici
        $options_def = array(
            $this->name => '', // aucun argument prévu
            'id' => '',
            'datemax' => '', // vrai jusqu'à cette date AAAAMMJJHHMM
            'datemin' => '', // vrai à partir de cette date AAAAMMJJHHMM
            'period' => '', // vrai entre ces dates AAAAMMJJHHMM-AAAAMMJJHHMM
            'day' => '', // liste des jours autorisés. 1=lundi, 7=dimanche
            'month' => '', // liste des mois autorisés. 1=janvier, ...
            'hmax' => '', // vrai jusqu'à cette heure HHMM
            'hmin' => '', // vrai à partir de cette heure HHMM
            'hperiod' => '', // vrai entre ces heures HHMM-HHMM
            'guest' => '', // vrai si utilisateur invité
            'admin' => '', // vrai si admin connecté
            'user' => '', // liste des userid autorisé. ex: 700,790
            'username' => '', // liste des username autorisé. ex: admin,lomart
            'group' => '', // liste des usergroup autorisé. ex: 8,12
            'lang' => '', // liste des langages autorisé. ex: fr,ca
            'mobile' => '', // vrai si affiché sur un mobile
            'homepage' => '', // vrai si affiché sur la page d'accueil
        );

        // ===== fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // ===== récupérer contenu vrai et contenu faux
        $tmp = $this->get_content_parts($this->content);
        $out_true = (isset($tmp[0])) ? $tmp[0] : '';
        $out_false = (isset($tmp[1])) ? $tmp[1] : '';

        // -- Les options indiquées dans le shortcode
        $options = $this->only_using_options($options_def);

        foreach ($options as $key => $val) {
            $ok = false;
            switch ($key) {
                case 'filter':
                case 'id':
                    break;
                // --------------- Date
                case 'datemax' :
                    $val = (str_pad($val, 12, '9'));
                    if (date('YmdHi') > $val)
                        return $out_false;
                    break;
                case 'datemin' :
                    $val = (str_pad($val, 12, '0'));
                    if (date('YmdHi') < $val)
                        return $out_false;
                    break;
                case 'day' :
                    $tmp = (date("w")) ? date("w") : 7;
                    if (!in_array($tmp, explode(',', $val)))
                        return $out_false;
                    break;
                case 'month' :
                    $tmp = (date("n")) ? date("n") : date("n") + 1;
                    if (!in_array($tmp, explode(',', $val)))
                        return $out_false;
                    break;

                // --------------- Heure
                case 'hmax' :
                    $val = (str_pad($val, 4, '0'));
                    if (date('Hi') > $val)
                        return $out_false;
                    break;
                case 'hmin' :
                    $val = (str_pad($val, 4, '0'));
                    if (date('Hi') < $val)
                        return $out_false;
                    break;
                case 'hperiod' :
                    $plages = explode(',', trim($val));
                    $now = date('Hi');
                    foreach ($plages as $plage) {
                        $heure = explode('-', trim($plage) . '-');
                        $ok = $ok || ((str_pad($heure[0], 4, '0') <= $now) && ($now <= str_pad($heure[1], 4, '0')));
                    }
                    if (!$ok)
                        return $out_false;
                    break;

                // --------------- Utilisateur
                case 'guest' :
                    if (JFactory::getUser()->guest != intval($val))
                        return $out_false;
                    break;
                case 'admin' :
                    $ok = intval(in_array(8, JFactory::getUser()->groups));
                    if ($ok != $val)
                        return $out_false;
                    break;
                case 'user' :
                    $ok = in_array(JFactory::getUser()->id, explode(',', $val));
                    if (!$ok)
                        return $out_false;
                    break;
                case 'username' :
                    $ok = in_array(JFactory::getUser()->username, explode(',', $val));
                    if (!$ok)
                        return $out_false;
                    break;
                case 'group' :
                    foreach (JFactory::getUser()->groups as $tmp) {
                        $ok = $ok || in_array($tmp, explode(',', $val));
                    }
                    if (!$ok)
                        return $out_false;
                    break;

                // --------------- Langue
                case 'lang' :
                    $lang = strtolower(JFactory::getLanguage()->getTag());
                    $ok = array_intersect(explode('-', $lang), explode(',', $val));
                    if (!$ok)
                        return $out_false;
                    break;

                // --------------- Divers
                case 'mobile' :
                    jimport('joomla.environment.browser');
                    $browser = jBrowser::getInstance();
                    if ($browser->isMobile() != $val)
                        return $out_false;
                    break;
                case 'homepage' :
                    // j'utilise une comparaison d'url au lieu de la méthode classique
                    // qui ne distingue pas le blog d'un article
                    $root_link = str_replace('/index.php', '', JUri::root());
                    $current_link = preg_replace('/index.php(\/)?/', '', JUri::current(true));
                    if (intval($current_link == $root_link) != $val)
                        return $out_false;
                    break;
            } // switch
        } // foreach
        // ===== le code en retour
        // si on arrive ici, c'est OK
        return $out_true;
    }

// run
}

// class

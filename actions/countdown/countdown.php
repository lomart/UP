<?php

/**
 * affiche un compte à rebours
 *
 * syntaxe:
 *   {up countdown=201801010000}  // délai jusqu'à une date
 *   {up countdown=120}           // compte à rebours en secondes
 *   {up countdown}               // affiche une horloge
 *
 * TODO :     Position chiffres avec grandes polices
 *           Voir alternative: http://flipclockjs.com/
 * @author    Lomart
 * @version   v1.0 - 20/07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit    <a href="https://github.com/Lexxus/jq-timeTo" target="_blank">Lexxus - jq-timeTo</a>
 *
 */
defined('_JEXEC') or die;

class countdown extends upAction {

    function init() {
        //===== Ajout dans le head (une seule fois)
        $this->load_file('timeTo.css');
        $this->load_file('jquery.time-to.min.js');
    }

    function run() {

        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut (hors JS)
        // il est indispensable de tous les définir ici
        $options_def = array(
            $this->name => '', // date, nombre de secondes ou vide pour horloge
            'id' => '',
            'align' => '', // left, center ou right
            'class' => '', // classe
            'style' => ''   // style inline
        );

        // ===== paramétres spécifique pour JS
        // traite a part pour avoir uniquement ceux indique
        $js_options_def = array(
            'callback' => '', // Fonction appellée à la fin du compte à rebours
            'captionSize' => 0, // fontsize legendes
            // 'countdown' => true,    // false = horloge, true = countdown INUTILE
            'countdownAlertLimit' => 10, // alerte en seconde avant fin countdown
            'displayCaptions' => false, // true = légendes affichées
            'displayDays' => 0, // nb chiffres affichés pour jours
            'fontFamily' => 'Verdana, sans-serif', // Police pour chiffres
            'fontSize' => 28, // Taille police en pixels pour chiffres
            'lang' => 'en', // Défini automatiquement par UP
            'seconds' => 0, // Temps initial en secondes pour le compte à rebours
            'start' => true, // démarrer automatiquement la minuterie
            'theme' => 'white', // black ou blue
                // 'timeTo' => '',      // date, nb secondes ou vide pour horloge
        );

        // forcer la langue
        $lang = JFactory::getLanguage()->getTag();
        $options_user['lang'] = substr($lang, 0, 2);

        // -- consolidation argument principal
        if ($this->options_user[$this->name] === true) {
            $this->options_user[$this->name] = '';
        }
        // pour la démo : x jours plus tard
        elseif ($this->options_user[$this->name][0] == '+') {
            $date = date('Y/m/d', strtotime($this->options_user[$this->name]));
            $this->options_user[$this->name] = $date;
        }

        // fusion et controle des options
        $options = $this->ctrl_options($options_def, $js_options_def);

        // ---- conversion params JS en chaine JSON
        $js_params = $this->only_using_options($js_options_def);
        $js_params = $this->json_arrtostr($js_params);

        // -- initialisation
        // ==== le code JS
        $js_code = '$("#' . $options['id'] . '").timeTo(';
        if ($options[$this->name] > '') {
            if (strpos($options[$this->name], '/')) {
                $js_code .= 'new Date("' . $options[$this->name] . '"),';
            } else {
                $js_code .= (int) $options[$this->name] . ',';
            }
        }
        $js_code .= $js_params;
        $js_code .= ');';
        $this->load_jquery_code($js_code);

        // ==== Attribut STYLE pour le div principal
        $attr_out['id'] = $options['id'];
        $attr_out['class'] = $options['class'];
        $this->add_class($attr_out['class'], 'clear');
        $attr_out['style'] = $options['style'];
        $this->add_style($attr_out['style'], 'text-align', $options['align']);
        // correction bug : forcer hauteur si fontsize plus grand
        if (isset($options['fontSize'])) {
            $coef = 1.10;
            if (isset($options['displayCaptions']) && $options['displayCaptions'])
                $coef = 1.80;
            $this->add_style($attr_out['style'], 'height', ($options['fontSize'] * $coef) . 'px');
        }

        // ==== le HTML
        $out = $this->set_attr_tag('div', $attr_out) . '</div>';

        return $out;
    }

// run
}

// class

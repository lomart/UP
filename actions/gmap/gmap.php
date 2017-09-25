<?php

/**
 * affiche une carte google pour une adresse
 *
 * syntaxe : {up gmap=1 rue de la paix, Paris}
 * IMPORTANT: il faut saisir son APIKey dans les paramétres du plugin sous la forme: gmap-key=apikey
 *
 * @author     lomart
 * @version    1.0 - Juillet 2017
 * */
defined('_JEXEC') or die;

class gmap extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        return true;
    }

    function run() {
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ==== PARAMETRES
        $options_def = array(
            $this->name => '', // adresse postale
            'id' => '',
            'width' => '100%', // largeur de la carte
            'height' => '300px', // hauteur de la carte
            'class' => '', // classe
            'style' => ''         // style-inline
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // recup APIKEY dans les params de UP
        $options['apikey'] = $this->get_action_pref('gmap-key');


        $main_attr['style'] = $options['style'];
        $main_attr['class'] = $options['class'];
        // add_style($main_attr['style'], 'width', $options['width']);
        // ==== EXECUTION
        if ($options['apikey'] !== false) {
            $address = str_replace(' ', '+', $options[$this->name]);

            $out = $this->set_attr_tag('div', $main_attr);
            $out .= '<iframe';
            $out .= ' width="' . $options['width'] . '"';
            $out .= ' height="' . $options['height'] . '"';
            $out .= ' frameborder="0" style="border:0"';
            $out .= ' src="https://www.google.com/maps/embed/v1/place';
            $out .= '?key=' . $options['apikey'];
            $out .= '&q=' . $address;
            $out .= '"></iframe>';
            $out .= '</div>';
        } else {
            $out = 'APIKEY not found. Please indicate it in up plugin parameters<br>form: gmap-key=<i>apikey</i>';
        }

        return $out;
    }

// run
}

// class

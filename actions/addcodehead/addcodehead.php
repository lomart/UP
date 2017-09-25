<?php

/**
 * ajoute du code libre dans le head.
 *
 * possibilité d'ajouter du code libre dans le head sans risque de nettoyage par un éditeur WYSIWYG
 *
 * syntaxe {up addCodeHead=<meta property="og:title" content="Page title" />}
 *
 * @author   LOMART
 * @version  1.0
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class addcodehead extends upAction {

    function init() {
        // aucune
    }

    function run() {

        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        $options_def = array(
            __class__ => '', // code à ajouter dans le head
            'id' => ''
        );

        $options = $this->ctrl_options($options_def);

        // il suffit de charger le code dans le head
        $this->load_custom_code_head($options[__class__]);

        // -- aucun code en retour
        return '';
    }

// run
}

// class addcsshead

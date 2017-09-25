<?php

/**
 * ajoute du code CSS dans le head.
 *
 * possibilité d'ajouter du code CSS dans le head
 * sans risque de nettoyage par un éditeur WYSIWYG
 *
 * syntaxe {up addCssHead=.foo[color:red]} Attention: mettre des [ ] au lieu de {}
 *
 * @author   LOMART
 * @version  1.0
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class addcsshead extends upAction {

    function init() {
        // aucune
    }

    function run() {
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        $options_def = array(
            __class__ => '', // code CSS. ATTENTION [ ] à la place des {}
            'id' => ''
        );

        $options = $this->ctrl_options($options_def);

        // il suffit de charger le code dans le head
        $this->load_css_head($options[__class__]);

        // -- aucun code en retour
        return '';
    }

// run
}

// class addcsshead

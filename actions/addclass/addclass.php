<?php

/**
 * ajoute une classe à la balise body par défaut.
 *
 * avec possibilité d'ajouter du code CSS dans le head
 *
 * syntaxe {up addclass=nom}
 *
 * Utilisation : changer l'image de fond à partir d'un article
 *
 * @author   LOMART
 * @version  1.0
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class addclass extends upAction {

    function init() {
        // aucune
    }

    function run() {

        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut
        // il est indispensable de tous les définir ici
        $options_def = array(
            $this->name => '', // nom de la classe ajoutée à la balise
            'id' => '',
            'selector' => 'body', // balise cible. Ne pas oublié le point pour une classe ou le # pour un ID
            'parent' => '', // 1 si on cible le parent de selector
            'css-head' => ''        // code CSS pour head. Attention utiliser [] au lieu de {}
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // === Ajout dans le head
        $parent = ($options['parent']) ? '.parent()' : '';

        $this->load_jquery_code('$("' . $options['selector'] . '")' . $parent . '.addClass("' . $options[$this->name] . '");');

        // CSS dans le head
        if ($options['css-head'] != '')
            $this->load_css_head($options['css-head']);

        // -- le code en retour
        return '';
    }

// run
}

// class

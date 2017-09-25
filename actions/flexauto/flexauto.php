<?php

/**
 * affiche les blocs enfants dans une grille fluide et responsive
 *
 * syntaxe : {up flexauto=x}contenu{/up flexauto} // x=1 à 6: nb colonnes sur grand écran.
 *
 * @author    Lomart
 * @version   1.0 - juillet 2017
 *
 */
defined('_JEXEC') or die;

class flexauto extends upAction {

    function init() {
        return true;
    }

    function run() {

        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut
        // il est indispensable de tous les définir ici
        $options_def = array(
            $this->name => '3', // nombre de colonnes sur grand écran
            'tablet' => '2', // nombre de colonnes sur moyen écran
            'mobile' => '1', // nombre de colonnes sur petit écran
            'id' => '',
            'class' => '', // classe(s) ajoutée(s) au bloc parent
            'style' => ''        // style inline ajouté au bloc parent
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // === le code HTML
        // -- ajout options utilisateur dans la div principale
        $outer_div['id'] = $options['id'];
        $outer_div['class'] = 'fg-row fg-auto-' . $options[$this->name];
        $this->add_class($outer_div['class'], 'fg-auto-m' . $options['tablet']);
        $this->add_class($outer_div['class'], 'fg-auto-s' . $options['mobile']);
        $this->add_class($outer_div['class'], $options['class']);
        $outer_div['style'] = $options['style'];

        // -- le code en retour
        $out = $this->set_attr_tag('div', $outer_div);
        $out .= $this->content;
        $out .= '</div>';

        return $out;
    }

// run
}

// class

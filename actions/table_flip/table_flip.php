<?php

/**
 * tables responsives par permutation lignes/colonnes
 *
 * Inversion colonnes/lignes. les titres de colonnes deviennent la 1ère colonne et reste visibles.
 * Une barre de défilement est ajoutée pour les autres colonnes.
 *
 * @author    lomart
 * @version   1.0beta - 07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit    <a href="https://codepen.io/JasonAGross/pen/rjmyx" target="_blank">Jason Gross</a>
 *
 * */
defined('_JEXEC') or die;

class table_flip extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        $this->load_file('flip.js');
        $this->load_file('flip.css');
        return true;
    }

    function run() {

        // cette action a obligatoirement du contenu
        if (!$this->ctrl_content_exists()) {
            return false;
        }

        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut
        // il est indispensable tous les parametres generaux
        // sauf ceux du script JS
        $options_def = array(
            __class__ => '', // aucun argument
            'id' => '',
            'class' => '', // classe(s) pour balise table
            'style' => '', // style inline pour balise table
        );
        // on fusionne avec celles dans shortcode
        $options = $this->ctrl_options($options_def);
        $id = $options['id'];  // l'id qui identifie le bloc action
        // ===== Analyse et MAJ de la table
        // balise ouvrante de la table originale et array des attributs
        preg_match('#<table.*>#U', $this->content, $table_opentag_old);
        $table_opentag_old = (!empty($table_opentag_old)) ? $table_opentag_old[0] : '';
        $table_attr = $this->get_attr_tag($table_opentag_old);

        // ==== actualisation attributs de la table
        $this->add_class($table_attr['class'], 'fliptable');
        $this->add_style($table_attr['style'], 'max-width', '100%');
        $table_opentag_new = $this->set_attr_tag('table', $table_attr);
        $this->content = str_replace($table_opentag_old, $table_opentag_new, $this->content);

        // ===== Bloc conteneur pour la table (outer)
        // preparer un array vide pour la div outer
        $outer_attr = $this->get_attr_tag(null);

        $outer_attr['id'] = $id;
        $this->add_style($outer_attr['style'], 'overflow', 'auto');
        // ajout paramétres user
        $this->add_str($outer_attr['class'], $options['class']);
        $this->add_str($outer_attr['style'], $options['style'], ';');

        //  ====  action principale
        // $code = '$("#'.$id.' table").flip();';
        // load_jquery_code($code);
        // ==== RETOUR HTML
        $out = '';
        $out .= $this->set_attr_tag('div', $outer_attr);
        $out .= $this->content;
        $out .= '</div>';

        return $out;
    }

// run
}

// class

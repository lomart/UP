<?php

/**
 * tables responsives par empilement des lignes d'une colonne.
 *
 * Syntaxe {up table-par-colonnes}contenu table{/up table-par-colonnes}
 *
 * Les lignes sont empilées par colonnes. Très pratique pour des plannings
 *
 * @author    lomart
 * @version   1.0beta - 07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit    <a href="http://johnpolacek.github.io/stacktable.js/" target="_blank">John Polacek</a>
 *
 * */
defined('_JEXEC') or die;

class table_by_columns extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        $this->load_file('up-stacktable.js');
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
            __class__ => '', // méthode responsive : fixed, flip, groupbycols, groupbyrows
            'id' => '',
            'breakpoint' => '720px', // bascule en vue responsive
            'model' => 'up-stacktable', // nom d'un fichier CSS prévu par le webmaster pour toutes les tables de la page
            'class' => '', // classe(s) pour balise table
            'style' => '', // style inline pour balise table
            'max-height' => '', // max-height pour la table
            'key-width' => '35%', // pour bloquer le header en haut
            'title-style' => '', // style pour la ligne titre en vue responsive
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
        $table_opentag_new = $this->set_attr_tag('table', $table_attr);
        $this->content = str_replace($table_opentag_old, $table_opentag_new, $this->content);

        // ===== Bloc conteneur pour la table (outer)
        // preparer un array vide pour la div outer
        $outer_attr = $this->get_attr_tag(null);

        $outer_attr['id'] = $id;
        $this->add_style($outer_attr['style'], 'max-height', $options['max-height']);
        $this->add_style($outer_attr['style'], 'overflow', 'auto');
        // ajout paramétres user
        $this->add_str($outer_attr['class'], $options['class']);
        $this->add_str($outer_attr['style'], $options['style'], ';');

        //  ====  fichier modele css
        $this->load_file($options['model'] . '.css');

        //  ====  action principale
        $code = '$("#' . $id . ' table").stackcolumns();';
        $this->load_jquery_code($code);

        // ==== code CSS dans head pour gestion breakpoint
        $prefix = '#' . $id . ' table.stacktable';
        $css = $prefix . ' .st-key {width:' . $options['key-width'] . '}';
        $css .= $prefix . ' .st-val {width: auto}';
        $css .= '@media (max-width:' . $this->ctrl_unit($options['breakpoint']) . ') {';
        $css .= $prefix . '.small-only { display: table; }';
        $css .= $prefix . '.large-only { display: none; }';
        $css .= '}';
        if ($options['title-style'])
            $css .= $prefix . ' .st-head-row.st-head-row-main{' . $options['title-style'] . '}';
        $this->load_css_head($css);

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

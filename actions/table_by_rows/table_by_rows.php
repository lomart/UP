<?php

/**
 * tables responsives par empilement des colonnes par lignes.
 *
 * Syntaxe {up table-par-lignes}contenu table{/up table-par-lignes}
 *
 * les colonnes  sont empilés par lignes avec la possibilité de les déplacer, de les fusionner, de supprimer le titre, d'afficher seulement certaines colonnes. https://github.com/codefog/restables/blob/master/README.md
 *
 * @author    lomart
 * @version   1.0 - 07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit    <a href="https://github.com/codefog/restables/blob/master/README.md" target="_blank">codefog</a>
 * */
defined('_JEXEC') or die;

class table_by_rows extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        $this->load_file('restables.min.js');
        return true;
    }

    function run() {

        $this->ctrl_content_exists();

        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut (sauf JS)
        $options_def = array(
            __class__ => '', // rien
            'id' => '',
            'breakpoint' => '720px', // bascule en vue responsive
            'class' => '', // classe(s) pour balise table
            'style' => '', // style inline pour balise table
            'max-height' => '', // max-height pour la table
        );

        // ===== paramétres attendus par le script JS
        $js_options_def = array(
            'merge' => '', // fusion de colonnes. 1:[2,3],5:[6] = 2&3 avec 1 et 6 avec 5
            'move' => '', // deplacement colonne. 1:0,6:1 = 1 au debut et 6 en 2eme
            'skip' => '', // non visible. [3,5] = col 3 et 5 non visible
            'span' => '', // [2,4] = valeur sans libellé (eq: colspan)
        );
        /* Paramètres JS non utilisés
          'cssClassOrigin' => '',     // defaut: restables-origin
          'cssClassClone' => '',      // defaut: restables-clone
          'uniqueAttributes' => '',   // defaut: ['id', 'for']
          'attributeSuffix' => '',    // defaut: -restables-clone
          'cloneCallback' => '',      // fonction callback. defaut: null
          'preserveCellClasses' => '' // defaut: true
         */

        // on fusionne avec celles dans shortcode
        $options = $this->ctrl_options($options_def, $js_options_def);

        $id = $options['id'];  // l'id qui identifie le bloc action
        // balise table originale et array des attributs
        preg_match('#<table.*>#U', $this->content, $table_opentag_old);
        $table_opentag_old = (!empty($table_opentag_old)) ? $table_opentag_old[0] : '';
        $table_attr = $this->get_attr_tag($table_opentag_old);

        // si l'user force l'id de la table, on la conserve
        if ($table_attr['id'] > '')
            $id = $table_attr['id'];
        $table_attr['id'] = $id;

        // preparer un array vide pour la div outer
        $outer_attr = $this->get_attr_tag(null);

        // ajout paramétres user
        $this->add_str($table_attr['class'], $options['class']);
        $this->add_str($table_attr['style'], $options['style'], ';');
        $this->add_style($outer_attr['style'], 'max-height', $options['max-height']);
        $this->add_style($outer_attr['style'], 'overflow', 'auto');

        // =========== le code JS
        // les options saisis par l'utilisateur concernant le script JS
        $js_options = $this->only_using_options($js_options_def);
        // -- conversion en chaine Json
        $js_params = $this->json_arrtostr($js_options, 2);

        $code = '$("#' . $id . '").resTables(';
        $code .= $js_params;
        $code .= ');';
        $this->load_jquery_code($code);

        // ==== code CSS dans head
        $prefix = 'table#' . $id . '.restables-';
        $css = $prefix . 'clone { display: none; }';
        $css .= $prefix . 'clone tr:first-child td { ';
        $css .= 'background: #eee; font-weight:bold; font-size:120% }';
        $css .= '@media (max-width:' . $this->ctrl_unit($options['breakpoint']) . ') {';
        $css .= $prefix . 'origin { display: none; }';
        $css .= $prefix . 'clone { display: table; }';
        $css .= '}';
        $this->load_css_head($css);

        // === mise à jour attributs de la table dans $content
        $table_opentag_new = $this->set_attr_tag('table', $table_attr);
        $this->content = str_replace($table_opentag_old, $table_opentag_new, $this->content);

        // ==== code pour retour
        $out = '';
        $out .= $this->set_attr_tag('div', $outer_attr);
        $out .= $this->content;
        $out .= '</div>';

        return $out;
    }

// run
}

// class

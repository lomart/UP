<?php

/**
 * tables responsives: entete reste visible
 *
 * Syntaxe {up table-fixe}contenu table{/up table-fixe}
 *
 * fixed:  la première colonne est toujours visible.
 * Une barre de défilement est ajoutée pour les autres colonnes.
 *
 * @author    lomart
 * @version   1.0beta - 07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit    <a href="http://www.jqueryscript.net/table/jQuery-Plugin-For-Fixed-Table-Header-Footer-Columns-TableHeadFixer.html" target="_blank">lai32290</a>
 *
 * */
defined('_JEXEC') or die;

class table_fixe extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        $this->load_file('tableHeadFixer.js');
        return true;
    }

    function run() {
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // cette action a obligatoirement du contenu
        if ($this->content == '') {
            $this->msg_error('no data for action: ' . __class__ . '. Control end-shortcode');
            return false;
        }

        // ===== valeur paramétres par défaut
        // il est indispensable tous les parametres generaux
        // sauf ceux du script JS
        $options_def = array(
            __class__ => '', // aucun argument
            'col-left' => '0', // nombre de colonnes fixées à gauche
            'id' => '',
            'class' => '', // classe(s) pour le bloc parent
            'style' => '', // style inline pour le bloc parent
            'max-height' => '', // max-height pour le bloc parent
        );
        // on fusionne avec celles dans shortcode
        $options = $this->ctrl_options($options_def);
        $id = $options['id'];  // l'id qui identifie le bloc action
        // ===== Analyse et MAJ de la table
        // balise ouvrante de la table originale et array des attributs
        preg_match('#<table.*>#U', $this->content, $table_opentag_old);
        $table_opentag_old = (!empty($table_opentag_old)) ? $table_opentag_old[0] : '';
        $table_attr = $this->get_attr_tag($table_opentag_old);

        // si l'user force l'id de la table, on la conserve
        if ($table_attr['id'] > '')
            $id = $table_attr['id'];
        $table_attr['id'] = $id;

        // ==== actualisation attributs de la table
        $table_opentag_new = $this->set_attr_tag('table', $table_attr);
        $content = str_replace($table_opentag_old, $table_opentag_new, $this->content);

        // ===== Bloc conteneur pour la table (outer)
        // preparer un array vide pour la div outer
        $outer_attr = $this->get_attr_tag(null);

        $this->add_style($outer_attr['style'], 'max-height', $options['max-height']);
        $this->add_style($outer_attr['style'], 'overflow', 'auto');
        // ajout paramétres user
        $this->add_str($outer_attr['class'], $options['class']);
        $this->add_str($outer_attr['style'], $options['style'], ';');

        //  ====  action principale
        $code = '$("#' . $id . '").tableHeadFixer(';
        if ($options['col-left'])
            $code .= '{"left" :' . $options['col-left'] . '}';
        $code .= ');';
        $this->load_jquery_code($code);

        // ==== RETOUR HTML
        $out = '';
        $out .= $this->set_attr_tag('div', $outer_attr);
        $out .= $content;
        $out .= '</div>';

        return $out;
    }

// run
}

// class

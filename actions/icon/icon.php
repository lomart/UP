<?php

/**
 * uniformise l'appel des icons. Evite de passer en mode code pour la saisie
 *
 * TODO : mettre bascule pour gestion line-height
 *
 * @author     Lomart
 * @version    v1.0beta
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class icon extends upAction {

    function init() {
        // aucune
    }

    function run() {
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        $options_def = array(
            $this->name => '', // nom de l'icone
            'id' => '',
            'size' => '', // taille icone en px, em
            'color' => '', // couleur
            'color-hover' => '', // couleur lors survol icone
            'style' => '', // style inline
            'class' => '', // classe
        );
        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // ------ les styles
        $attr['class'] = 'icon-' . $options[$this->name];
        $this->add_class($attr['class'], $options['class']);
        $attr['style'] = $options['style'];
        $this->add_style($attr['style'], 'color', $options['color']);
        // color-hover est traité en javascript
        if ($options['color-hover']) {
            $attr['onMouseOver'] = "this.style.color='" . $options['color-hover'] . "'";
            $attr['onMouseOut'] = "this.style.color='" . $options['color'] . "'";
            // note: si vide, equivaut à inherit
        }
        // si une taille est définie, on considère que l'icône est carrée
        // on force la largeur à la hauteur
        if ($options['size']) {
            $this->add_style($attr['style'], 'font-size', $options['size']);
            $this->add_style($attr['style'], 'width', $options['size']);
            $this->add_style($attr['style'], 'line-height', $options['size']);
        }

        // ------ code en retour
        $out = $this->set_attr_tag('i', $attr, true);
        return $out;
    }

// run
}

// class

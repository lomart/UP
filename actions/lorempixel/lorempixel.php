<?php

/**
 * affiche une image aleatoire
 *
 * Syntaxe :  {up lorempixel=type | width=xx | height=xx }
 * -- type = abstract, animals, business, cats, city, food, nightlife, fashion, people nature, sports, technics, transport
 * <b>Note</b> : width & height sont les dimensions de l'image retournée par lorempixel. Pour l'afficher en remplissant le bloc parent, il faut ajouter style=width:100%
 *
 * @author   Lomart
 * @version  07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class lorempixel extends upAction {

    function init() {
        // aucune
    }

    function run() {

        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut
        // il est indispensable de tous les définir ici
        $options_def = array(
            $this->name => 'cats', // type d'image: abstract, animals, business, cats, city, food, nightlife, fashion, people nature, sports, technics, transport
            'id' => '',
            'align' => '', // alignement horizontal : left, center, right
            'height' => '200', // hauteur image téléchargée
            'width' => '200', // largeur image téléchargée
            'class' => '', // classe(s)
            'style' => ''      // style inline
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // controle options
        $type = 'abstract, animals, business, cats, city, food, nightlife, fashion, people, nature, sports, technics, transport';

        if (in_array($options[$this->name], explode(', ', $type))) {
            // si le type existe, on affiche l'image
            $img_attr = array();
            $img_attr['class'] = $options['class'];
            $img_attr['style'] = $options['style'];
            $img_attr['src'] = 'http://lorempixel.com';
            $img_attr['src'] .= '/' . (int) $options['width'];
            $img_attr['src'] .= '/' . (int) $options['height'];
            $img_attr['src'] .= '/' . $options[$this->name];
            // --
            $txt = $this->set_attr_tag('div', ['style' => $options['align']]);
            $txt .= $this->set_attr_tag('img', $img_attr);
            $txt .= '</div>';
        } else {
            // on affiche un rappel des styles disponibles
            $error_attr['style'] = '';
            $this->add_style($error_attr['style'], 'width', (int) $options['width'] . 'px');
            $this->add_style($error_attr['style'], 'height', (int) $options['height'] . 'px');
            $this->add_style($error_attr['style'], 'background-color', 'salmon');
            $this->add_style($error_attr['style'], 'color', 'black');
            $txt = $this->set_attr_tag('div', $error_attr);
            $txt .= 'LOREMPIXEL - Type = ' . $type;
            $txt .= '</div>';
        }

        return $txt;
    }

// run
}

// class

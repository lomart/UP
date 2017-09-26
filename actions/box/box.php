<?php

/**
 * affiche un titre et du texte dans une boite
 *
 * syntaxe: {up box=titre}contenu HTML{/up box}
 *
 * @author      LOMART
 * @update      2017-07-15
 * @version     v1.0
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class box extends upAction {

    function init() {
        $this->load_file('up-box.css');
    }

    function run() {

        // cette action a obligatoirement du contenu
        if (!$this->ctrl_content_exists()) {
            return false;
        }
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut
        // il est indispensable de tous les définir ici
        $options_def = array(
            $this->name => '', // titre de la box
            'id' => '',
            'modele' => '', // [danger, info] classe prédéfinie
            'box-class' => '', // class user
            'box-style' => '', // style inline
            'title-class' => '', // class user pour titre
            'title-style' => '', // style inline pour titre
            'content-class' => '', // class user pour contenu
            'content-style' => '', // style inline pour contenu
            'css-head' => '', // style CSS inséré dans le HEAD
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // CSS dans le head
        if ($options['css-head']) {
            $this->load_css_head($options['css-head']);
        }
        // ===== Style
        $attr_box['class'] = 'up-box m-child-raz';
        $this->add_str($attr_box['class'], $options['modele']);
        $this->add_str($attr_box['class'], $options['box-class']);
        $attr_box['style'] = $options['box-style'];

        $attr_title['class'] = $options['title-class'];
        $attr_title['style'] = $options['title-style'];

        $attr_content['class'] = $options['content-class'];
        $attr_content['style'] = $options['content-style'];

        $txt = $this->set_attr_tag('div', $attr_box);

        if ($options[$this->name]) {
            $txt .= $this->set_attr_tag('h3', $attr_title);
            $txt .= $options[$this->name];
            $txt .= '</h3>';
        }

        $txt .= $this->set_attr_tag('div', $attr_content);
        $txt .= $this->content;
        $txt .= '</div>';

        $txt .= '</div>';

        return $txt;
    }

// run
}

// class

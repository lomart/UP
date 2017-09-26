<?php

/**
 * affiche du contenu dans des panneaux avec onglets en haut, à gauche ou à droite. Mode responsive
 *
 * Sur mobile ou sur demande, l'affichage est en mode accordion
 *
 * @author    Lomart
 * @version   v1.0 - 07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit    Script de <a href="http://www.jqueryscript.net/accordion/Responsive-Multipurpose-Tabs-Accordion-Plugin-With-jQuery.html" target="_blank">bhaveshcmedhekar</a>
 */
defined('_JEXEC') or die;

class tab extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        $this->load_file('tab.css');
        $this->load_file('jquery.multipurpose_tabcontent.js');
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
        // il est indispensable de tous les définir ici
        $options_def = array(
            __class__ => 'tab', // tab ou accordion
            'id' => '',
            'title-tag' => 'h4', // classe(s) onglets
            'title-class' => '', // classe(s) onglets
            'title-style' => '', // style inline onglets
            'content-style' => 'background-color:#ddd', // style inline contenu
            'content-class' => ''  // classe(s) contenu
        );

        $js_options_def = array(
            'plugin_type' => '', // accordion
            'side' => '', // left ou right
            'active_tab' => '', // 1 a N
        );

        // -- recup paramétre principal --> plugin_type
        if ($this->options_user[__class__] !== true) {
            $js_params['plugin_type'] = $this->options_user[__class__];
        }

        // fusion et controle des options
        $options = $this->ctrl_options($options_def, $js_options_def);

        // =========== le code JS
        // les options saisis par l'utilisateur concernant le script JS
        $js_options = $this->only_using_options($js_options_def);
        // -- conversion en chaine Json
        $js_params = $this->json_arrtostr($js_options);

        $js_code = '$("#' . $options['id'] . '").champ(';
        $js_code .= $js_params;
        $js_code .= ');';
        $this->load_jquery_code($js_code);

        // === le code HTML
        // --- STYLES
        $attr_main['class'] = 'tab_wrapper';
        $attr_main['id'] = $options['id'];

        $attr_title['class'] = $options['title-class'];
        $attr_title['style'] = $options['title-style'];

        $attr_content['class'] = $options['content-class'];
        $attr_content['style'] = $options['content-style'];

        // -- titre + contenu RESTE A REPRENDRE STYLE DU H4

        $tag = $options['title-tag'];
        $regex_title = '#<' . $tag . '*>(.*)</' . $tag . '>#siU';
        preg_match_all($regex_title, $this->content, $array_title);
        $regex_text = '#</' . $tag . '>(.*)<' . $tag . '.*>#siU';
        preg_match_all($regex_text, $this->content . '<' . $tag . '>', $array_txt);
        $nb = count($array_title[0]);

        // --- code retourne
        $out = $this->set_attr_tag('div', $attr_main);

        $out .= '<ul class="tab_list">';
        $active = ' class="active"';
        for ($i = 0; $i < $nb; $i++) {
            $out .= '<li' . $active . '>';
            $out .= $array_title[1][$i];
            $out .= '</li>';
            $active = '';
        }
        $out .= '</ul>';

        $out .= '<div class="content_wrapper">';
        $active = ' active';
        for ($i = 0; $i < $nb; $i++) {
            $out .= '<div class="tab_content' . $active . '">';
            $out .= $array_txt[1][$i];
            $out .= '</div>';
            $active = '';
        }
        $out .= '</div>';

        $out .= '</div>';

        return $out;
    }

// run
}

// class

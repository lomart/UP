<?php

/**
 * accordion très simple
 *
 * syntaxe : une alternance de titres pour les onglet en H4 et de contenu HTML
 * {up faq}
 * -- titre en H4
 * -- contenu HTML
 * {/up faq}
 *
 *
 * @author    lomart
 * @version   v1.0 - 15/07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit    <a href="http://jsfiddle.net/ryanstemkoski/6gbq0yLv/" target="_blank">ryans temkoski</a>
 *
 */
defined('_JEXEC') or die;

class faq extends upAction {

    function init() {
        //===== Ajout dans le head (une seule fois)
        $this->load_file('faq.css');

        //-- le JS
        // JHtml::_('jquery.framework');
        // JHtml::script('plugins/content/up/assets/js/faq.js');
        $this->load_file('faq.js', 'plugins/content/up/assets/js/');
    }

    function run() {
        // cette action a obligatoirement du contenu
        if (!$this->ctrl_content_exists()) {
            return false;
        }
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // contenu obligatoire
        if (!$this->ctrl_content_exists()) {
            return false;
        }
        // ===== valeur paramétres par défaut (hors JS)
        // il est indispensable de tous les définir ici
        $options_def = array(
            $this->name => '', // rien
            'id' => '', //
            'title-tag' => 'h4', // pour utiliser une autre balise pour les titres
            'title-class' => '', // classe pour le titre (onglet)
            'title-style' => '', // style inline pour le titre
            'title-icon' => '', // TODO
            'content-class' => '', // classe pour le contenu
            'content-style' => ''  // style inline pour le contenu
        );
        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // === code spécifique à l'action
        // qui doit retourner le code pour remplacer le shortcode
        // <div id="upfaq">
        // 	<div class="upfaq-button">Button 1</div>
        //   <div class="upfaq-content">Content<br />More Content<br /></div>
        // 	<div class="upfaq-button">Button 2</div>
        // 	<div class="upfaq-content">Content</div>
        // </div>
        // -- les styles
        $attr_title['class'] = 'upfaq-button';
        $this->add_str($attr_title['class'], $options['title-class']);
        $attr_title['style'] = $options['title-style'];

        $attr_content['class'] = 'upfaq-content';
        $this->add_str($attr_content['class'], $options['content-class']);
        $attr_content['style'] = $options['content-style'];

        // -- titre + contenu RESTE A REPRENDRE STYLE DU H4
        $tag = $options['title-tag'];
        $regex_title = '#<' . $tag . '.*>(.*)</' . $tag . '>#siU';
        preg_match_all($regex_title, $this->content, $array_title);
        $regex_text = '#</' . $tag . '>(.*)<' . $tag . '.*>#siU';
        preg_match_all($regex_text, $this->content . '<' . $tag . '>', $array_txt);
        $nb = count($array_title[1]);

        // -- code retour
        $out = '<div class="upfaq">';
        for ($i = 0; $i < $nb; $i++) {
            $out .= $this->set_attr_tag('div', $attr_title) . $array_title[1][$i] . '</div>';
            $out .= $this->set_attr_tag('div', $attr_content) . $array_txt[1][$i] . '</div>';
        }
        $out .= '</div>';

        return $out;
    }

// run
}

// class

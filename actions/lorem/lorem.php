<?php

/**
 * affiche du texte aléatoire (enrichissement possible)
 *
 * Syntaxe :  {up lorem=liste des arguments ci-dessous }
 * - (integer) - The number of paragraphs to generate.
 * - short, medium, medium, verylong - The average length of a paragraph.
 * - decorate - Add bold, italic and marked text.
 * - link - Add links.
 * - ul - Add unordered lists.
 * - ol - Add numbered lists.
 * - dl - Add description lists.
 * - bq - Add blockquotes.
 * - code - Add code samples.
 * - headers - Add headers.
 * - allcaps - Use ALL CAPS.
 * - prude - Prude version.
 * - plaintext - Return plain text, no HTML.
 * exemple appel : http://loripsum.net/api/2/code/decorate
 *
 * @author  Lomart
 * @version 1.0 - 07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class lorem extends upAction {

    function init() {
        // aucune
    }

    function run() {
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        $options_def = array(
            $this->name => '', // nombre de paragraphe
            'id' => '',
            'class' => '', // classe(s) pour bloc
            'style' => '', // style inline pour bloc
            'max-char' => '', // nombre maxima de caractères
            'max-word' => ''     // nombre maxima de mots
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // les arguments
        $arg = preg_split("/[\s\,\/\.]+/", $options[$this->name]);
        $arg = implode('/', $arg);

        // le texte
        $txt = $this->get_html_contents('http://loripsum.net/api/' . $arg);

        if ($options['max-char']) {
            $txt = substr($txt, 0, $options['max-char']);
        }
        if ($options['max-word']) {
            $tmp = explode(' ', $txt);
            $txt = implode(' ', array_slice($tmp, 0, $options['max-word']));
        }

        $attr_main = array();
        $attr_main['class'] = $options['class'];
        $attr_main['style'] = $options['style'];

        // code en retour
        $out = $this->set_attr_tag('div', $attr_main);
        $out .= $txt;
        $out .= '</div>';

        return $out;
    }

}

// class lorem

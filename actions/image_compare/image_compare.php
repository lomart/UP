<?php

/**
 * comparaison de 2 images par déplacement d'un volet
 *
 * {up imagecompare}
 * < img src="avant.jpg" >
 * < img src="apres.jpg" >
 * {/up imagecompare}
 *
 * @author lomart
 * @version 1.0 - Juillet 2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit  script de <a href="https://github.com/sylvaincombes/jquery-images-compare" target="_blank">Sylvain Combes</a>
 *
 */
defined('_JEXEC') or die;

class image_compare extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        $this->load_file('images-compare.css');
        $this->load_file('jquery.images-compare.min.js');
        JHtml::script('https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js');
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
            __class__ => '',
            'id' => '',
            'class' => '', // classe bloc
            'style' => ''         // style inline bloc
        );

        $js_options_def = array(
            'initVisibleRatio' => 0.2, // position initiale
            'interactionMode' => 'drag', // mode: drag, mousemove, click
            'addSeparator' => true, // ajoute séparateur (ligne verticale)
            'addDragHandle' => true, // ajoute poignee sur séparateur
            'animationDuration' => 450, // Duree animation en ms
            'animationEasing' => 'linear', // animation: linear, swing
            'precision' => 2                  // Precision rapport, nb decimales
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def, $js_options_def);

        $regeximg = '#<img .*>#U';
        preg_match_all($regeximg, $this->content, $img);

        // =========== le code JS
        // les options saisis par l'utilisateur concernant le script JS
        $js_options = $this->only_using_options($js_options_def);

        // conversion params JS en chaine JSON
        $js_params = '';
        if (isset($js_options)) {
            $js_params = json_encode($js_options, JSON_UNESCAPED_SLASHES);
        }

        // -- initialisation
        $js_code = '$("#' . $options['id'] . '").imagesCompare(';
        $js_code .= $js_params;
        $js_code .= ');';
        $this->load_jquery_code($js_code);

        // === le code HTML
        // -- ajout options utilisateur dans la div principale
        $outer_div['id'] = $options['id'];
        $outer_div['class'] = $options['class'];
        $outer_div['style'] = $options['style'];

        // -- le code en retour
        $out = $this->set_attr_tag('div', $outer_div);
        $out .= '<div style="display: none;">';
        $out .= $img[0][0];
        $out .= '</div>';
        $out .= '<div>';
        $out .= $img[0][1];
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }

// run
}

// class

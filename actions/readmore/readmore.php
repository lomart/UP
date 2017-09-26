<?php

/**
 * afficher/cacher un bloc HTML à l'aide d'un bouton 'lire la suite'
 *
 * syntaxe:  {up readmore=texte bouton}contenu caché{/up readmore}
 *
 * TODO : style différent pour bouotn more et less
 *
 * @author   Lomart
 * @version  1.0 - 07/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit   script de <a href="https://www.skyminds.net/jquery-script-toggle-pour-afficher-et-cacher-de-multiples-blocs-html/#read" traget="_blank">Matt</a>
 *
 */
defined('_JEXEC') or die;

class readmore extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        //-- le CSS bloc des boutons
        $css_code = '.uprm-toggle {margin-top: 10px; margin-bottom: 10px}';
        // selon etat active
        $css_code .= '.uprm-less.active .uprm-btn-more, .uprm-btn-less {display: none !important}';
        $css_code .= '.uprm-less.active .uprm-btn-less, .uprm-btn-more {display: block !important}';
        // le style par defaut des boutons
        $css_code .= '.uprm-btn {background-color:#ddd; border:1px #aaa solid; padding:4px; text-align:center; text-decoration:none}';
        $css_code .= '.uprm-btn:hover {background-color:#cdcdcd; text-decoration:none}';
        $this->load_css_head($css_code);

        //-- le JS
        $js_code = '$(".uprm-more").hide();';
        $js_code .= '$(".uprm-btn-more").click(function () {';
        $js_code .= '  $(this).closest(".uprm-less").addClass("active");';
        $js_code .= '  $(this).closest(".uprm-less").prev().stop(true).slideDown("1000");';
        $js_code .= '});';
        $js_code .= '$(".uprm-btn-less").click(function () {';
        $js_code .= '  $(this).closest(".uprm-less").removeClass("active");';
        $js_code .= '  $(this).closest(".uprm-less").prev().stop(true).slideUp("1000");';
        $js_code .= '});';
        $this->load_jquery_code($js_code);
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
            __class__ => '', // idem textmore
            'id' => '',
            'textmore' => 'lire la suite', // texte bouton
            'textless' => 'replier', // texte bouton
            'class' => 'uprm-btn', // classe(s) pour les boutons. remplace la classe par défaut.
            'style' => '', // style inline pour les boutons
        );

        // si readmore=label-text-more
        if ($this->options_user[__class__] !== true) {
            $this->options_user['textmore'] = $this->options_user[__class__];
        }
        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // === styler
        $attr_btn_more['class'] = 'uprm-btn-more';
        $this->add_str($attr_btn_more['class'], $options['class']);
        $attr_btn_more['style'] = $options['style'];
        $attr_btn_more['href'] = '#read';

        $attr_btn_less['class'] = 'uprm-btn-less';
        $this->add_str($attr_btn_less['class'], $options['class']);
        $attr_btn_less['style'] = $options['style'];
        $attr_btn_less['href'] = '#read';

        // === code spécifique à l'action
        // qui doit retourner le code pour remplacer le shortcode

        $out = '<div class="uprm-toggle">';
        $out .= '<div class="uprm-more">';
        $out .= $this->content;
        $out .= '</div>';
        $out .= '<div class="uprm-less">';
        $out .= $this->set_attr_tag('a', $attr_btn_more);
        $out .= $options['textmore'];
        $out .= '</a>';
        $out .= $this->set_attr_tag('a', $attr_btn_less);
        $out .= $options['textless'];
        $out .= '</a>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }

// run
}

// class

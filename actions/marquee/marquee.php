<?php

/**
 * contenu HTML défilant horizontalement ou verticalement
 *
 * {up marquee=label} texte du message défilant {/up marquee}
 *
 *
 * @author    Lomart
 * @version   1.0 - 28/7/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 * @credit    <a href="http://www.jqueryscript.net/other/Smooth-Marquee-like-Content-Scroller-Plugin-For-jQuery-limarquee.html" target"_blank">script JS limarquee de omcg33</a>
 *
 */
defined('_JEXEC') or die;

class marquee extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        $this->load_file('liMarquee.css');
        $this->load_file('jquery.liMarquee.min.js');
        return true;
    }

    function run() {
        // cette action a obligatoirement du contenu
        if (!$this->ctrl_content_exists()) {
            return false;
        }
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ==== la configuration par défaut de l'action
        $options_def = array(
            __class__ => '', // le texte de l'etiquette
            'id' => '',
            'height' => '100px', // hauteur defaut pour scroll vetical
            'model' => 'base', // style CSS dans .upmq-*style*. aucun=none
            // avec ajout suffix left, right, top, bottom
            'out-class' => '', // classe(s) pour div out
            'out-style' => '', // style inline pour div out
            'msg-class' => '', // classe(s) pour div msg
            'msg-style' => '', // style inline pour div msg
            'lbl-class' => '', // classe(s) pour div label
            'lbl-style' => '', // style inline pour div label
            'lbl-pos' => '', // position label : left, right, up, down, none
            'lbl-nowrap' => false, // true = label sur une ligne
        );

        // ===== paramétres spécifique pour JS
        // traite a part pour avoir uniquement ceux indique
        $js_options_def = array(
            'direction' => 'left', // right, up, down
            'loop' => -1, // nombre d'affichage, -1 : infini
            'scrolldelay' => 0, // delai en millisecondes
            'scrollamount' => 50, // vitesse
            'circular' => true, // mode carousel. si contenu plus large que .str_wrap
            'drag' => true, // deplacement msg avec souris
            'runshort' => true, // scroll si texte court (visible sans scroll)
            'hoverstop' => true, // pause lors survol
            'inverthover' => false, // scroll uniquement lors survol
        );

        // l'option principale est le texte label. Si non saisi = vide (pas true)
        // if ($options_user[$action]===true) $options_user[$action] = '';

        $options = $this->ctrl_options($options_def, $js_options_def);

        $js_params = $this->only_using_options($js_options_def);
        $js_params = $this->json_arrtostr($js_params);

        // ==== code appel du plugin
        $js_code = '$("#' . $options['id'] . ' .str_wrap").liMarquee(' . $js_params . ');';
        $this->load_jquery_code($js_code);

        // == CONSOLIDATION OPTIONS
        // -- lbl-pos : nom verifie et complete pour faciliter CSS
        // si titre, lblpos doit être defini
        $tmp = array(
            'left' => 'lmmq-h-left',
            'right' => 'lmmq-h-right',
            'top' => 'lmmq-v-top',
            'bottom' => 'lmmq-v-bottom',
            'none' => ''
        );
        // si position definie, on la verifie et complete
        $class_label_pos = '';
        if ($options['lbl-pos'] && array_key_exists($options['lbl-pos'], $tmp)) {
            $class_label_pos = $tmp[$options['lbl-pos']];
        } else {
            // si titre, il faut la forcer
            // note: pas de reciproque. le titre peut etre en css
            if ($options[__class__]) {
                $options['lbl-pos'] = 'left';
                $class_label_pos = $tmp['left'];
            }
        }

        // -- BLOC MAIN ----------------------------------------------
        $main_attr['id'] = $options['id'];
        // si demande utilisation fichier style CSS
        if ($options['model']) {
            $this->load_file($options['model'] . '.css', $this->actionPath . 'style/'); // TODO chemin
            $main_attr['class'] = 'lmmq-' . $options['model'];
        }

        // -- BLOC OUT ----------------------------------------------
        // -- les classes
        $out_attr['class'] = 'lmmq-out';
        $this->add_class($out_attr['class'], $class_label_pos);

        // les classes utilisateurs
        $this->add_class($out_attr['class'], $options['out-class']);

        // -- les styles
        $out_attr['style'] = $options['out-style'];
        if (!$class_label_pos) {
            $this->add_style($out_attr['style'], 'padding-right', '5px');
        }

        // -- BLOC LABEL ------------------------------------------------
        // les classes
        $lbl_attr['class'] = 'lmmq-lbl';

        // les styles
        $lbl_attr['style'] = $options['lbl-style'];
        if ($options['lbl-nowrap']) {
            $this->add_style($lbl_attr['style'], 'white-space', 'nowrap');
        }

        // -- MSG ------------------------------------------------
        $msg_attr['class'] = 'lmmq-msg';
        $this->add_class($msg_attr['class'], $options['msg-class']);
        // les styles
        $msg_attr['style'] = $options['msg-style'];

        // -- STR_WRAP -------------------------------------------
        // si scroll vertical, il faut forcer un height
        $wrap_attr['class'] = 'str_wrap';
        if (in_array(strtolower($options['direction']), array('up', 'down'))) {
            $wrap_attr['style'] = 'height:' . $options['height'];
        }

        // ==== CODE RETOURNE POUR ACTION
        // outer
        $out = $this->set_attr_tag('div', $main_attr);
        $out .= $this->set_attr_tag('div', $out_attr);
        // label left ou top
        if (in_array($options['lbl-pos'], array('left', 'top'))) {
            $out .= $this->set_attr_tag('div', $lbl_attr);
            $out .= $options[__class__];
            $out .= '</div>';
        }
        $out .= $this->set_attr_tag('div', $msg_attr);
        $out .= $this->set_attr_tag('div', $wrap_attr);
        $out .= $this->content;
        $out .= '</div>';
        $out .= '</div>';
        // label right ou bottom
        if (in_array($options['lbl-pos'], array('right', 'bottom'))) {
            $out .= $this->set_attr_tag('div', $lbl_attr);
            $out .= $options[__class__];
            $out .= '</div>';
        }
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }

// run
}

// class

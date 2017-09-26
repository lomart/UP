<?php

/**
 * permet de créer une entité HTML (balise) avec classe(s), style et attribut sans passer en mode code
 *
 * exemples :
 * {up html=div | class=foo | id=x123}contenu{/up html}
 * --> <div id="x123" class="foo">contenu</div>
 *
 * {up html=img | class=foo | src=images/img.jpg}
 * --> < img class="foo" src="images/img.jpg" >
 *
 * note: toutes les options sont considérées comme des attributs de la balise
 *
 * @author   LOMART 2017-08
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class html extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        return true;
    }

    function run() {

        // contenu non obligatoire, ex: IMG
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut
        // il est indispensable de tous les définir ici
        $options_def = array(
            $this->name => 'div', // balise html
            'id' => '', // ID spécifique
            'class' => '', // classe(s)
            'style' => '', // style inline
            'attr' => ''        // couple attribut-valeur (title:le titre)
        );

        // On accepte toutes les options. Il faut les ajouter avant contrôle
        foreach (array_diff_key($this->options_user, $options_def) AS $key => $val) {
            $options_def[$key] = '';
        }

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // === le code HTML
        // -- toutes les options sont des attributs sauf $action
        $outer_div = $options;
        unset($outer_div[$this->name]);

        // -- le code en retour
        $out = $this->set_attr_tag($options[$this->name], $outer_div);
        if ($this->content) {
            $out .= $this->content;
            $out .= '</' . $options[$this->name] . '>';
        }

        return $out;
    }

// run
}

// class

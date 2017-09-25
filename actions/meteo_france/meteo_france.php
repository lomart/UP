<?php

/**
 * affiche le widget de Météo France
 *
 * Basé sur une idée de Robert Gastaud - joomxtensions.com
 * <a href="https://forum.joomla.fr/showthread.php?221374" target="_blank">et la discussion sur le forum JOOMLA.FR</a>
 * Merci à Daneel pour la correction du code
 * --------------------------------
 * Syntaxe :  {up meteo=ville | orientation=sens}
 *
 * TODO :récupérer automatiquement le code commune (insee)
 * https://public.opendatasoft.com/explore/dataset/correspondance-code-insee-code-postal/api/?flg=fr&q=77810
 * https://api.gouv.fr/api/api-geo.html#!/Communes/get_communes
 *
 * @author      LOMART
 * @update      2017-07-01
 * @version     v1.0 du 01-07-2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 */
defined('_JEXEC') or die;

class meteo_france extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        return true;
    }

    function run() {

        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // sortie si pas de code commune
        if ($this->options_user[__class__] === true) {
            $txt = '<a href="http://www.meteofrance.com/meteo-widget" target="_blank">';
            $txt .= 'METEO: Récupérer le code de la commune ici';
            $txt .= '</a>';
            msg_info($txt);
            return $txt;
        }

        // ===== valeur paramétres par défaut
        // il est indispensable de tous les définir ici
        $options_def = array(
            __class__ => '', // le code commune de la ville à récupérer sur <a href="http://www.meteofrance.com/meteo-widget" target="_blank">cette page</a>
            'id' => '',
            'orientation' => 'v', // bloc horizontal (H) ou vertical (V)
            'block' => 'p', // balise HTML autour du module météo
            'class' => '', // classe(s) pour bloc parent
            'style' => ''          // style inline pour bloc parent
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // le bloc conteneur
        $main_attr['class'] = $options['class'];
        $main_attr['style'] = $options['style'];

        // récupération script meteo
        $ville = $options[__class__];
        $sens = strtolower($options['orientation'][0]);
        $sens = ($sens == 'v') ? 'PORTRAIT_VIGNETTE' : 'PAYSAGE_VIGNETTE';

        $url = 'https://www.meteofrance.com/mf3-rpc-portlet/rest/vignettepartenaire/';
        $url .= $ville;
        $url .= '/type/VILLE_FRANCE/size/';
        $url .= $sens;

        // $meteo = file_get_contents($url);
        $meteo = $this->get_html_contents($url, 10);
        $meteo = str_replace('<head>', '', $meteo);
        $meteo = str_replace('</head>', '', $meteo);
        $meteo = str_replace('http://logc279', 'https://logs', $meteo);
        $meteo = str_replace('http://', '//', $meteo);
        $meteo = str_replace('target="_blank"', 'target="_blank" rel="noopener noreferrer" ', $meteo);

        // code retour
        $out = $this->set_attr_tag($options['block'], $main_attr);
        $out .= '<script charset="UTF-8" type="text/javascript">';
        $out .= $meteo;
        $out .= '</script>';
        $out .= '</' . $options['block'] . '>';

        return $out;
    }

// run
}

// class

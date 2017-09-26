<?php

/**
 * affiche de 1 à 6 blocs enfants sur une même ligne
 *
 * syntaxe 1 : {up cell=x1-x2}contenu avec 2 blocs enfants{/up cell}
 * syntaxe 2 : {up cell=x1-x2}contenu cell-1 {====} contenu cell-2{/up cell}
 *
 * x1-x2 sont les largeurs sur la base d'une grille de 12 colonnes
 * exemple cell=6-6 pour 2 colonnes égales.
 * On utilise les largeurs de la classe UP-Width
 *
 * @demo  <a href="http://up.lomart.fr/actions">up.lomart.fr</a>
 * @author  Lomart
 * @version 1.0 - 08/2017
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class cell extends upAction {

    function init() {
        // aucune
    }

    function run() {

        if (!$this->ctrl_content_exists()) {
            return false;
        }
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        // ===== valeur paramétres par défaut
        // il est indispensable de tous les définir ici
        $options_def = array(
            $this->name => '12', // nombre de colonnes
            'mobile' => '', // nombre de colonnes sur petit écran
            'tablet' => '', // nombre de colonnes sur moyen écran
            'id' => '',
            'class' => '', // class bloc principal
            'style' => '', // style inline bloc parent
            'class-*' => '', // class pour tous les blocs colonnes. sinon voir class-1 à class-6
            'style-*' => ''       // style inline pour tous les blocs colonnes. sinon voir style-1 à style-6
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // -- ajout options utilisateur dans la div principale
        $outer_div['class'] = 'cell-row';
        $this->add_class($outer_div['class'], $options['class']);
        $outer_div['style'] = $options['style'];

        // ======== les styles des colonnes
        // -- taille des colonnes (version rwd)
        $col[0] = array_map('intval', explode('-', $options[$this->name]));
        $tmp = $this->str_append($options['mobile'], '0-0-0-0-0-0', '-');
        $col[1] = array_map('intval', explode('-', $tmp));
        $tmp = $this->str_append($options['tablet'], '0-0-0-0-0-0', '-');
        $col[2] = array_map('intval', explode('-', $tmp));
        // le nombre de colonnes est défini par col
        $nbcol = count($col[0]);

        // ajout des styles pour les colonnes
        // note: le style général est toujours appliqué
        // exemple: bordure identique pour toutes les colonnes + fond pour une spécifique
        for ($i = 0; $i < $nbcol; $i++) {
            $bloc[$i]['class'] = 'cell w' . $col[0][$i];
            if ($options['mobile']) {
                $this->add_class($bloc[$i]['class'], 'ws' . $col[1][$i]);
            }
            if ($options['tablet']) {
                $this->add_class($bloc[$i]['class'], 'wm' . $col[2][$i]);
            }
            $this->add_class($bloc[$i]['class'], $options['class-*']);
            $this->add_class($bloc[$i]['class'], $options['class-' . ($i + 1)]);
            $bloc[$i]['style'] = $options['style-*'];
            $this->add_str($bloc[$i]['style'], $options['style-' . ($i + 1)], ';');
        }

        // RECUPERATION & ANALYSE CONTENU
        // si les 2 colonnes ne sont pas séparées par {====}
        // on prend les maxi 6 premiers blocs enfants
        if ($this->ctrl_content_parts($this->content) === false) {
            // === analyse structure HTML du content
            $dom = new domDocument;
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $this->content);
            $xpath = new DOMXpath($dom);
            $nodes = $xpath->query('/html/body/*');

            $i = 0;
            foreach ($nodes as $node) {
                if (isset($bloc[$i % $nbcol]['class'])) {
                    $tmp = @$nodes->item($i)->getAttribute('class');
                    $nodes->item($i)->setAttribute('class', $this->str_append($tmp, $bloc[$i % $nbcol]['class']));
                }
                if (isset($bloc[$i % $nbcol]['style'])) {
                    $tmp = @$nodes->item($i)->getAttribute('style');
                    $nodes->item($i)->setAttribute('style', $this->str_append($tmp, $bloc[$i % $nbcol]['style'], ';'));
                }
                $i++;
            }

            $this->content = $dom->saveHTML($dom->documentElement);
            $this->content = preg_replace('~<(?:/?(?:html|head|body))[^>]*>\s*~i', '', $this->content);
        } else { // séparation par {============}
            // recup texte des colonnes sans le tag P ajouté par éditeur
            $coltxt = $this->get_content_parts($this->content);
            // mise en forme
            $this->content = '';
            for ($i = 0; $i < $nbcol; $i++) {
                $this->content .= $this->set_attr_tag('div', $bloc[$i]);
                $this->content .= $coltxt[$i];
                $this->content .= '</div>';
            }
        }

        // === le code HTML en retour
        $out = $this->set_attr_tag('div', $outer_div);
        $out .= $this->content;
        $out .= '</div>';

        return $out;
    }

// run
}

// class

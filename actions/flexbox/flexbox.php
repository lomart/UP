<?php

/**
 * affiche des blocs enfants dans une grille FLEXBOX responsive
 *
 * syntaxe 1 : {up flexbox=x1-x2}contenu avec plusieurs blocs enfants{/up flexbox}
 * syntaxe 2 : {up flexbox=x1-x2}contenu bloc-1 {====} contenu bloc-2{/up flexbox}
 *
 * x1-x2 sont les largeurs relatives des colonnes
 * exemple col=4-8 ou col=33-66 pour 2 colonnes span4 et span8
 *
 * @demo  <a href="http://up.lomart.fr/actions">up.lomart.fr/css</a>
 * @author  Lomart
 * @version 1.0 - 08/2017
 *
 */
defined('_JEXEC') or die;

class flexbox extends upAction {

    function init() {
        // charge la feuille de style UP
        $this->load_upcss();
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
            $this->name => '6-6', // nombre de colonnes
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
        $outer_div['class'] = 'fg-row';
        $this->add_class($outer_div['class'], $options['class']);
        $outer_div['style'] = $options['style'];

        // ======== les styles des colonnes
        // -- taille des colonnes (version rwd)
        $col[0] = explode('-', $options[$this->name]);
        $nbcol = count($col[0]);  // le nombre de colonnes est défini en vue normale

        $tmp = $this->str_append($options['mobile'], 'x-x-x-x-x-x', '-');
        $col[1] = explode('-', $tmp);
        $tmp = $this->str_append($options['tablet'], 'x-x-x-x-x-x', '-');
        $col[2] = explode('-', $tmp);

        // ajout des styles pour les colonnes
        // note: le style général est toujours appliqué
        // exemple: bordure identique pour toutes les colonnes + fond pour une spécifique
        for ($i = 0; $i < $nbcol; $i++) {
            $bloc[$i]['class'] = 'fg-c' . $col[0][$i];
            if ($col[1][$i] != 'x') {
                $this->add_class($bloc[$i]['class'], 'fg-cs' . $col[1][$i]);
            }
            if ($col[2][$i] != 'x') {
                $this->add_class($bloc[$i]['class'], 'fg-cm' . $col[2][$i]);
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
            // $dom->loadHTML($content);
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
            $allcoltxt = $this->get_content_parts($this->content);
            // mise en forme
            $this->content = '';

            $i = 0;
            foreach ($allcoltxt as $coltxt) {
                // $numcol = ($i%$nbcol) ? $i%$nbcol : $nbcol;
                $this->content .= $this->set_attr_tag('div', $bloc[$i % $nbcol]);
                $this->content .= $coltxt;
                $this->content .= '</div>';
                $i++;
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

<?php

/**
 * liste des articles d'une catégorie
 *
 * syntaxe {up article-category=catid}
 *
 * Une action très pratique pour lister les articles de la catégorie en cours, il suffit de taper {up article-category}
 *
 * TODO: gestion contenu affiché. ex: #TITRE# (#DATE#). Prévoir modèle par défaut dans actionprefs
 * TODO: filtre sur vue pour afficher uniquement en vue blog, article, ...
 *
 * @author   LOMART
 * @version  1.0
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 *
 */
defined('_JEXEC') or die;

class article_category extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        return true;
    }

    function run() {
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        $options_def = array(
            __class__ => '', // ID de la catégorie ou vide pour celle de l'article actuel
            'maxi' => '', // Nombre maxi d'article dans la liste
            'no_published' => '1', // Liste aussi les articles non publiés
            'sort_by' => 'title', // tri: title, ordering, created_time, modified_time, publish_up, id, hits
            'sort_order' => 'asc', // ordre de tri
            'id' => '',
            'class' => '', // classe(s) pour bloc
            'style' => '', // style inline pour bloc
        );

        // si catégorie article non indiquée, c'est celle de l'article
        if ($this->options_user[__class__] === true) {
            if (isset($this->article->catid)) {
                // le shortcode est dans un article
                $this->options_user[__class__] = intval($this->article->catid);
            } else {
                // le shortcode est dans un module
                // TODO : récupérer le catid de l'article actuellement affiché
                return ''; // sortir tant que pas de CATID
            }
        }

        // ======> fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // ======> contrôle clé de tri
        $list_sortkey = 'title, ordering, created, modified, publish_up, id, hits';
        $this->ctrl_argument($options['sort_by'], $list_sortkey);

        $catid = intval($options[__class__]);

        // =====> RECUP DES DONNEES
        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

        // Set application parameters in model
        $app = JFactory::getApplication();
        $appParams = $app->getParams();
        $model->setState('params', $appParams);

        // Set the filters based on the module params
        // nombre d'article
        $model->setState('list.start', 0);
        if ($options['maxi'] != '') {
            $model->setState('list.limit', (int) $options['maxi']);
        }
        // etat publication
        if ($options['no_published'] !== true) {
            $model->setState('filter.published', 1);
        }

        // Access filter
        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Category filter
        $model->setState('filter.category_id', array($catid));

        // Filter by language
        $model->setState('filter.language', $app->getLanguageFilter());

        // Ordering
        $model->setState('list.ordering', 'a.' . $options['sort_by']);
        $model->setState('list.direction', $options['sort_order']);

        $items = $model->getItems();

        // ======> mise en forme résultat
        $artlist = array();
        if (count($items)) {
            foreach ($items as $item) {
                $url = '';
                $slug = ($item->alias) ? ($item->id . ':' . $item->alias) : $itemid;
                $catslug = ($item->category_alias) ? ($item->catid . ':' . $item->category_alias) : $item->catid;
                $route = ContentHelperRoute::getArticleRoute($slug, $catslug);
                $url = JRoute::_($route);

                $artlist[] = '<a href="' . $url . '">' . $item->title . '</a>';
            }
        }

        // attributs du bloc principal
        $attr_main = array();
        $attr_main['class'] = $options['class'];
        $attr_main['style'] = $options['style'];

        // ======> code en retour
        $out = $this->set_attr_tag('ul', $attr_main);
        foreach ($artlist as $lign) {
            $out .= '<li>' . $lign . '</li>';
        }
        $out .= '</ul>';

        return $out;
    }

// run
}

// class

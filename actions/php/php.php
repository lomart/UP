<?php

/**
 * permet d'exécuter du code PHP dans un article.
 *
 * Exemples :
 * date actuelle :  {up php=echo date('d-m-Y H:i:s');}
 * langage : {up php=echo JFactory::getLanguage()getTag(); }
 * nom user : {up php=
 *   $user = JFactory::getUser();
 *   echo  ($user->guest!=1) ? $user->username : 'invité';
 * }
 *
 * @author   LOMART
 * @version  1.0
 * @license   <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
 */
defined('_JEXEC') or die;

class php extends upAction {

    function init() {
        // charger les ressources communes à toutes les instances de l'action
        return true;
    }

    function run() {
        // lien vers la page de demo (vide=page sur le site de UP)
        $this->set_demopage();

        $options_def = array(
            __class__ => '', // le code PHP
            'id' => ''
        );

        // fusion et controle des options
        $options = $this->ctrl_options($options_def);

        // liste des fonctions interdites
        $block_list = explode(' ', 'basename chgrp chmod chown clearstatcache copy delete dirname disk_free_space disk_total_space diskfreespace fclose feof fflush fgetc fgetcsv fgets fgetss file_exists file_get_contents file_put_contents file fileatime filectime filegroup fileinode filemtime fileowner fileperms filesize filetype flock fnmatch fopen fpassthru fputcsv fputs fread fscanf fseek fstat ftell ftruncate fwrite glob lchgrp lchown link linkinfo lstat move_uploaded_file opendir parse_ini_file pathinfo pclose popen readfile readdir readllink realpath rename rewind rmdir set_file_buffer stat symlink tempnam tmpfile touch umask unlink fsockopen system exec passthru escapeshellcmd pcntl_exec proc_open proc_close mkdir rmdir base64_decode');

        // ====> Contrôle du code
        $errmsg = '';
        $function_list = array();
        // liste des fonctions dans l'argument
        if (preg_match_all('/([a-zA-Z0-9_]+)\s*[(|"|\']/s', $options[__class__], $matches)) {
            $function_list = $matches[1];
        }
        // Recherche dans la liste des interdits
        foreach ($function_list as $command) {
            if (in_array($command, $block_list)) {
                $errmsg = $command;
                break;
            }
        }

        // ====> Execution du code
        if ($errmsg == '') {
            ob_start();
            eval($options[__class__]);
            $out = ob_get_contents();
            ob_end_clean();
        } else {
            $out = '<mark>****** INVALID CODE IN PHP : ' . $errmsg . ' ******</mark>';
        }

        return $out;
    }

// run
}

// class

<?php

/**
 * DokuWiki Plugin Glip (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  David Lumm <david.lumm@twinklebob.co.uk> 2015-07-24
 *
 * DokuWiki Plugin kato: https://github.com/kato-im/dokuwiki-kato
 * @author  Yaroslav Lapin <jlarky@gmail.com> 2013-09-16
 *
 * DokuWiki Plugin hipchat: https://github.com/jacobko/dokuwiki-hipchat
 * @author  Jeremy Ebler <jebler@gmail.com> 2011-09-29
 *
 * DokuWiki log: https://github.com/cosmocode/log.git
 * @author  Adrian Lang <lang@cosmocode.de> 2010-03-28
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

if (!defined('DOKU_LF')) {
    define('DOKU_LF', "\n");
}
if (!defined('DOKU_TAB')) {
    define('DOKU_TAB', "\t");
}
if (!defined('DOKU_PLUGIN')) {
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
}

require_once DOKU_PLUGIN . 'action.php';

class action_plugin_glip extends DokuWiki_Action_Plugin {

    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_action_act_preprocess');
    }

    function handle_action_act_preprocess(Doku_Event $event) {
        if (isset($event->data['save'])) {
            $this->handle();
        }
        return;
    }

    private function handle() {
        global $SUM;
        global $INFO;

        $fullname = $INFO['userinfo']['name'];
        $username = $INFO['client'];
        $page = $INFO['namespace'] . $INFO['id'];
        $summary = $SUM;
        $minor = (boolean) $_REQUEST['minor'];

        /* Namespace filter */
        $ns = $this->getConf('glip_namespaces');
        if (!empty($ns)) {
            $namespaces = explode(',', $ns);
            $current_namespace = explode(':', $INFO['namespace']);
            if (!in_array($current_namespace[0], $namespaces)) {
                return;
            }
        }

        $say = '**' . $fullname . '** updated the Wikipage **[' . $INFO['id'] . '](' . $this->urlize() . ')**';
        if ($minor) {
            $say = $say . ' [minor edit]';
        }
        if ($summary) {
            $say = $say . PHP_EOL . 'Edit summary: *' . $summary . '*';
        }

        //error_log($say);
        //error_log(json_encode($config));

        $data = array(
            "icon" => $this->getConf('glip_icon_url'),
            "activity" => $this->getConf('glip_activity'),
            "body" => $say
        );

        $data_string = json_encode($data);

        $http = new DokuHTTPClient();
        $http->headers['content-type'] = 'application/json';
        $response = $http->post($this->getConf('glip_url'), $data_string);
        if ($response === false) {
            error_log("Error notifying Glip: " . $http->error . " Trying cURL.");
            // Try curl
            $ch = curl_init($this->getConf('glip_url'));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
            );
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $result = curl_exec($ch);
            if ($result === false) {
                error_log("Error notifying Glip: " . curl_error($ch));
            }
        }
    }

    /* Make our URLs! */

    private function urlize() {
        global $INFO;
        global $conf;
        $page = $INFO['id'];

        switch ($conf['userewrite']) {
            case 0:
                $url = DOKU_URL . "doku.php?id=" . $page;
                break;
            case 1:
                if ($conf['useslash']) {
                    $page = str_replace(":", "/", $page);
                }
                $url = DOKU_URL . $page;
                break;
            case 2:
                if ($conf['useslash']) {
                    $page = str_replace(":", "/", $page);
                }
                $url = DOKU_URL . "doku.php/" . $page;
                break;
        }
        return $url;
    }

}

// vim:ts=4:sw=4:et:enc=utf-8:

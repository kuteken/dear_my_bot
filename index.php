<?php 
require_once("NetIrc/SmartIRC.php"); 
require_once("config.php");

class mybot {

    function timer(&$irc) {
        global $server;
        $timelist = array(
            '12:00' => 'おなかすいた。',
            '15:00' => 'おやつのじかん。。',
            '18:00' => 'ねむくなってきた。',
            '19:30' => 'みんなおつかれさま。',
        );
        foreach($timelist as $time => $message) {
            if (mktime() > strtotime(date('Y-m-d ').$time) && mktime()  <= strtotime(date('Y-m-d ').$time) + 60) {
                $irc->message(SMARTIRC_TYPE_CHANNEL, $server['channel'], $time .'になりました。test反映ある人おしえてください＠＠');
                $irc->message(SMARTIRC_TYPE_NOTICE, $server['channel'], $message);
            }
        }
    }

    function hello(&$irc, &$data) {
        $hellolist = array('...','こんにちは','ハイサーイ','Hello','Ciao','Buon giorno','God dag','Selamat siang','ナマステー','サワディカー','Bonjour','Boa tarde','Guten Tag');
        $irc->message(SMARTIRC_TYPE_NOTICE, $data->channel, $hellolist[array_rand($hellolist)]);
    }

    function spoof(&$irc, &$data) {
        global $server, $tease_users;
        if ($data->channel == $server['channel'].'_bot') {
            if (isset($tease_users) && is_array($tease_users)) {
                foreach ($tease_users as $tease_user) {
                    if ($tease_user == $data->nick) {
                        $bonus_message = ' ※'.$data->nick.'です。';
                    }
                }
            }

            $irc->message(SMARTIRC_TYPE_NOTICE, $server['channel'], $data->message . $bonus_message);
        }
    }

    function quit (&$irc) {
      $irc->quit('さようなら');
    }

} 

$bot = new mybot();
$irc = new Net_SmartIRC();

$irc->registerTimehandler(60000, $bot, 'timer');
$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^hi$', $bot, 'hello');
$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^quit$', $bot, 'quit');
$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '.+', $bot, 'spoof');

$irc->connect($server['url'], $server['port']);
$irc->login($bot_name, $bot_name, 0, null, $server['password']);
$irc->join(array($server['channel'], $server['channel'].'_bot'));

$irc->listen();

?>

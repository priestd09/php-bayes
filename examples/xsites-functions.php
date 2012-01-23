<?php

$dic = file_get_contents(__DIR__.'/xsites.txt');
$dic = explode(PHP_EOL, $dic);

function xsites_log() {
    print '--> ';
    call_user_func_array('printf', func_get_args());
    print PHP_EOL;
}

function xsites_get_dictionary() {
    global $dic;

    $bayes_dic = new Noop\Bayes\Dictionary\Dictionary;
    $bayes_dic->loadStopwords(array('en'));
    $bayes_dic->setMinimalTokenCount(5);

    $tokenizer = new Noop\Bayes\Tokenizer\Html;
    $tokenizer->setPolicy(DICTIONARY_POLICY);

    // use half of sites
    foreach (array_slice($dic, 0, floor(count($dic) / 2)) as $site) {
        $contents = xsites_get_site($site);

        if($contents != '') {
            $bayes_dic->addTokens($tokenizer->tokenize($contents));
        }
    }

    return $bayes_dic;
}

function xsites_get_site($url) {
    $cache = sys_get_temp_dir() . '/xsite-cache-'.trim($url);
    if (is_readable($cache)) {
        return file_get_contents($cache);
    } else {
        xsites_log('Caching site "%s" to "%s"', $url, $cache);

        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 1      // Timeout in seconds
            )
        ));
        $contents = file_get_contents('http://'.$url, 0, $context);
        ini_set('default_socket_timeout', 1);
        file_put_contents($cache, $contents);
        return $contents;
    }
}
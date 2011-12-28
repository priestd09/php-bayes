<?php

/**
 * MIT licensed
 */

/**
 * So, here we are collecting dictionary from ~5 popular porn sites to detect
 * that some other sites are in fact porn sites )
 */

require_once(__DIR__.'/autoload.php');

function cache_or_fetch($url) {
    $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'url_' . crc32($url);

    if (is_readable($file)) {
        return file_get_contents($file);
    }

    print "Fetching url $url\n";

    $data = file_get_contents($url);

    file_put_contents($file, $data);

    return $data;
}

print "Hi, now I'll try to load dictionary of create it if not exists\n";

$dic = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bayes_dic';

if (!is_readable($dic) || false === unserialize(file_get_contents($dic))) {
    print "Opps, creating dictionary\n";

    $tokenizer = new Noop\Bayes\Tokenizer\Html();
    $tokenizer->setPolicy(Noop\Bayes\Tokenizer\Html::POLICY_METAS | Noop\Bayes\Tokenizer\Html::POLICY_HEADERS);

    $d = new Noop\Bayes\Dictionary\Dictionary();

    foreach(array('tube8.com', 'youporn.com', 'extremetube.com', 'redtube.com') as $site) {
        $html = cache_or_fetch('http://'.$site);

        $d->addTokens($tokenizer->tokenize($html));

        file_put_contents($dic, serialize($d));
    }
} else {
    $d = unserialize(file_get_contents($dic));
}

print_r($d->dump());

$t = new Noop\Bayes\Tokenizer\Html();
$t->setPolicy(Noop\Bayes\Tokenizer\Html::POLICY_TEXTS | Noop\Bayes\Tokenizer\Html::POLICY_METAS | Noop\Bayes\Tokenizer\Html::POLICY_HEADERS | Noop\Bayes\Tokenizer\Html::POLICY_LINKS);

print "Cheching other sites:\n";

print "Youtube.com: " . $d->match($t->tokenize(cache_or_fetch('http://youtube.com'))) . "\n";
print "kink.com: " . $d->match($t->tokenize(cache_or_fetch('http://kink.com'))) . "\n";
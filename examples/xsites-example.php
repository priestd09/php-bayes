<?php

/**
 * made for markosweb.com / SmartViper llc, something like google SafeSearch analogue
 */

require_once(__DIR__.'/../autoload.php');
require_once(__DIR__.'/xsites-functions.php');

define('DICTIONARY_POLICY', Noop\Bayes\Tokenizer\Html::POLICY_METAS | Noop\Bayes\Tokenizer\Html::POLICY_HEADERS);
define('MATCH_POLICY', Noop\Bayes\Tokenizer\Html::POLICY_TEXTS | Noop\Bayes\Tokenizer\Html::POLICY_METAS | Noop\Bayes\Tokenizer\Html::POLICY_HEADERS | Noop\Bayes\Tokenizer\Html::POLICY_LINKS);

xsites_log('Loading dictionary');

$bayes_dic = xsites_get_dictionary();

xsites_log('Matching now');

$tokenizer = new Noop\Bayes\Tokenizer\Html;
$tokenizer->setPolicy(MATCH_POLICY);

foreach (array_slice($dic, floor(count($dic)/2) + 1) as $site) {
    $contents = xsites_get_site($site);
    if ($contents == '') {
        xsites_log('Site not responsible, skipping');
    } else {
        xsites_log('Matching "%s"', $site);
        printf('Probability: %.6f'.PHP_EOL, $bayes_dic->match($tokenizer->tokenize($contents)));
    }
}
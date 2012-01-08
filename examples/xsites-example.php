<?php

/**
 * made for markosweb.com / SmartViper llc, something like google SafeSearch analogue
 */

require_once(__DIR__.'/../autoload.php');
require_once(__DIR__.'/xsites-functions.php');

$dictionaryPolicy = Noop\Bayes\Tokenizer\Html::POLICY_METAS | Noop\Bayes\Tokenizer\Html::POLICY_HEADERS;
$matchPolicy = Noop\Bayes\Tokenizer\Html::POLICY_TEXTS | Noop\Bayes\Tokenizer\Html::POLICY_METAS | Noop\Bayes\Tokenizer\Html::POLICY_HEADERS | Noop\Bayes\Tokenizer\Html::POLICY_LINKS;

xsites_log('Loading dictionary');

$dic = xsites_get_dictionary();


<?php

require_once(__DIR__.'/../autoload.php');

class DictionaryTest extends PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $d = new Noop\Bayes\Dictionary\Dictionary();
        $t = new Noop\Bayes\Token\TokenArray();
        $t->fromArray(array('foo' => 1, 'bar' => 2, 'buzz' => 3));

        $d->addTokens($t);

        $this->assertEquals(array('foo' => array('count' => 1, 'weight' => 1/6),
            'bar' => array('count' => 2, 'weight' => 1/3),
            'buzz' => array('count' => 3, 'weight' => 1/2)), $d->dump());

        $t->fromArray(array('foo' => 5));

        $d->removeTokens($t);

        $this->assertEquals(array('bar' => array('count' => 2, 'weight' => 2/5),
            'buzz' => array('count' => 3, 'weight' => 3/5)), $d->dump());
    }

    public function testSaving()
    {

    }

    public function testMatch()
    {
        $d = new Noop\Bayes\Dictionary\Dictionary();
        $t = new Noop\Bayes\Token\TokenArray();
        $ts = new Noop\Bayes\Tokenizer\String();

        $t->fromArray(array('foo' => 1, 'bar' => 2, 'buzz' => 3));

        $d->addTokens($t);

        $p = $d->match($ts->tokenize('Some text containing no important words'));

        $this->assertEquals(0.5, $p);

        $p = $d->match($ts->tokenize('Some text containing only some minor words, like "foo"'));

        $this->assertTrue($p < 0.6);

        $p = $d->match($ts->tokenize('Some text containing some important words, like foo or bar'));

        $this->assertTrue($p > 0.6 && $p < 0.7);

        $p = $d->match($ts->tokenize('Some text containing majority of words, like buzz and bar'));

        $this->assertTrue($p > 0.7);
    }
}

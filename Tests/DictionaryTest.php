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
        $d = new Noop\Bayes\Dictionary\Dictionary();
        $t = new Noop\Bayes\Token\TokenArray();
        $t->fromArray(array('foo' => 1, 'bar' => 2, 'buzz' => 3));

        $d->addTokens($t);

        $file = tempnam(sys_get_temp_dir(), 'noop_bayes');

        $this->assertEquals(array('foo' => array('count' => 1, 'weight' => 1/6),
            'bar' => array('count' => 2, 'weight' => 1/3),
            'buzz' => array('count' => 3, 'weight' => 1/2)), $d->dump());

        file_put_contents($file, serialize($d));

        $d2 = unserialize(file_get_contents($file));

        $this->assertEquals(array('foo' => array('count' => 1, 'weight' => 1/6),
            'bar' => array('count' => 2, 'weight' => 1/3),
            'buzz' => array('count' => 3, 'weight' => 1/2)), $d2->dump());
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

    public function testSerialize()
    {
        //todo
    }

    public function testNormalizeAndLengths()
    {
        $d = new \Noop\Bayes\Dictionary\Dictionary;
        $t = new Noop\Bayes\Token\TokenArray;

        $t->fromArray(array('a' => 6, 'b' => 10, 'caad' => 12, 'baad' => 13, 'asfgerrybbewaaadfasdfsadfadsfca' => 7));

       //a,b, and that long token should have zero weight with default min/max lengths
        $d->addTokens($t);

        $this->assertEquals(array('a' => array('count' => 6, 'weight' => 0),
            'b' => array('count' => 10, 'weight' => 0),
            'caad' => array('count' => 12, 'weight' => 12/25),
            'baad' => array('count' => 13, 'weight' => 13/25),
            'asfgerrybbewaaadfasdfsadfadsfca' => array('count' => 7, 'weight' => 0)), $d->dump());

        $d->setMinimalTokenLength(0);

        $this->assertEquals(array('a' => array('count' => 6, 'weight' => 6/41),
            'b' => array('count' => 10, 'weight' => 10/41),
            'caad' => array('count' => 12, 'weight' => 12/41),
            'baad' => array('count' => 13, 'weight' => 13/41),
            'asfgerrybbewaaadfasdfsadfadsfca' => array('count' => 7, 'weight' => 0)), $d->dump());

        $d->setMaximalTokenLength(100);

        $this->assertEquals(array('a' => array('count' => 6, 'weight' => 6/48),
            'b' => array('count' => 10, 'weight' => 10/48),
            'caad' => array('count' => 12, 'weight' => 12/48),
            'baad' => array('count' => 13, 'weight' => 13/48),
            'asfgerrybbewaaadfasdfsadfadsfca' => array('count' => 7, 'weight' => 7/48)), $d->dump());
    }
}

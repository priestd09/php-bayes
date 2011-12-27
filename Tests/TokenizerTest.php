<?php

require_once(__DIR__.'/../autoload.php');

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function testStringTokenizer()
    {
        $tokenizer = new Noop\Bayes\Tokenizer\String();
        
        $this->assertEquals($tokenizer->tokenize('This is some string that should be tokenized tokenized')->toArray(),
                array('this' => 1, 'is' => 1, 'some' => 1, 'string' => 1, 'that' => 1, 'should' => 1, 'be' => 1, 'tokenized' => 2));
        
        $this->assertEquals($tokenizer->tokenize('А если utf-8?')->toArray(),
                array('а' => 1, 'если' => 1, 'utf' => 1));
    }
}

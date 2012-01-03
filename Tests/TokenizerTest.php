<?php

require_once(__DIR__.'/../autoload.php');
//TODO: nested tags policy
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

    public function testHtmlTokenizer()
    {
        $tokenizer = new Noop\Bayes\Tokenizer\Html();

        $html = '<html>
                    <head>
                        <title>This is some cool site</title>
                        <meta name="keywords" content="cool, site, very cool, seo text"/>
                        <meta name="description" content="Uber cool site, I swear!"/>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    </head>
                <body>
                    <h1>This is some text that shouldn\'t be taken in account.</h1>

                    <p title="text paragraph">
                        lorem ipsum text
                        <span>Some text too!</span>
                        <img alt="cat image" title="cat image title"/>
                    </p>

                    <a href="google.com">go to google</a>
                    <strong>Тест unicode</strong>
                </body>
            </html>';

        // we use default settings indexing name, description and title
        // this is POLICY_META
        $this->assertEquals($tokenizer->tokenize($html)->toArray(),
                array('this' => 1, 'is' => 1, 'some' => 1, 'cool' => 4, 'site' => 3, 'uber' => 1, 'i' => 1, 'swear' => 1, 'very' => 1, 'seo' => 1, 'text' => 1));

        $tokenizer->setPolicy(Noop\Bayes\Tokenizer\Html::POLICY_HEADERS);

        $this->assertEquals($tokenizer->tokenize($html)->toArray(),
                array('this' => 1, 'is' => 1, 'some' => 1, 'text' => 1, 'that' => 1, 'shouldn' => 1, 't' => 1, 'be' => 1, 'taken' => 1, 'in' => 1, 'account' => 1));

        $tokenizer->setPolicy(Noop\Bayes\Tokenizer\Html::POLICY_TITLES);

        $this->assertEquals($tokenizer->tokenize($html)->toArray(),
                array('text' => 1, 'paragraph' => 1, 'cat' => 1, 'image' => 1, 'title' => 1));

        $tokenizer->setPolicy(Noop\Bayes\Tokenizer\Html::POLICY_IMAGES);

        $this->assertEquals($tokenizer->tokenize($html)->toArray(),
                array('cat' => 1, 'image' => 1));

        $tokenizer->setPolicy(Noop\Bayes\Tokenizer\Html::POLICY_TEXTS);

        $this->assertEquals(array('lorem' => 1, 'ipsum' => 1, 'text' => 2, 'unicode' => 1, 'тест' => 1, 'some' => 1, 'too' => 1),
                $tokenizer->tokenize($html)->toArray());

        $tokenizer->setPolicy(Noop\Bayes\Tokenizer\Html::POLICY_LINKS);

        $this->assertEquals(array('go' => 1, 'to' => 1, 'google' => 1),
                $tokenizer->tokenize($html)->toArray());

        $tokenizer->setPolicy(Noop\Bayes\Tokenizer\Html::POLICY_TEXTS | Noop\Bayes\Tokenizer\Html::POLICY_METAS);

        $this->assertEquals(array('this' => 1, 'is' => 1, 'some' => 2, 'cool' => 4, 'site' => 3, 'uber' => 1, 'i' => 1, 'swear' => 1, 'very' => 1,
            'lorem' => 1, 'ipsum' => 1, 'text' => 3, 'unicode' => 1, 'тест' => 1, 'seo' => 1, 'too' => 1),
                $tokenizer->tokenize($html)->toArray());

        $tokenizer->setTokenPaths(array('//title'));

        $this->assertEquals($tokenizer->tokenize($html)->toArray(),
                array('this' => 1, 'is' => 1, 'some' => 1, 'cool' => 1, 'site' => 1));
    }
}

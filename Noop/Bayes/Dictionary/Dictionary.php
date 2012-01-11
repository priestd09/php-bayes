<?php

/**
 * MIT licensed
 */

namespace Noop\Bayes\Dictionary;

use Noop\Bayes\Token\TokenArray;
use Noop\Bayes\Tokenizer\String;

class Dictionary implements \Serializable
{
    /**
     * Dictionary of all tokens
     * @var array
     */
    protected $dictionary;

    /**
     * Minimal frequency in document for token to be included.
     * For example, if this is 0.1, then all tokens found in less than 1 doc of
     * 10 will be not taken in account
     * @var double
     */
    protected $minimalFrequencyInDocuments;

    /**
     * Minimal token length to be processed
     * @var integer
     */
    protected $minimalTokenLength;

    /**
     * Maximal token length to be processed
     * @var integer
     */
    protected $maximalTokenLength;

    /**
     * Dictionary size
     * @var integer
     */
    protected $tokenCount;

    /**
     * Usable token count (not filtered by length, stemmer or so on)
     * @var integer
     */
    protected $usableTokenCount;

    /**
     * Stopwords array
     * @var array
     */
    protected $stopwords;

    public function __construct()
    {
        $this->dictionary = array();
        $this->tokenCount = 0;
        $this->minimalFrequencyInDocuments = 0.05;
        $this->minimalTokenLength = 3;
        $this->maximalTokenLength = 16;
        $this->stopwords = array();
    }

    /**
     * Load stopwords from files
     * @param array $codes
     */
    public function loadStopwords($codes)
    {
        $this->stopwords = array();

        foreach ($codes as $code) {
            $file = __DIR__.'/Stopwords/'.$code.'.txt';

            if (!is_readable($file)) {
                throw new \RuntimeException(sprintf('Stopword file "%s" failed to load', $file));
            } else {
                $string = file_get_contents($file);

                $st = new String();
                $this->stopwords = array_merge($this->stopwords,
                        array_keys($st->tokenize($string)->toArray()));
            }
        }

        $this->recount();
    }

    /**
     * Adds tokens to dictionary
     * @param TokenArray $tokens
     */
    public function addTokens(TokenArray $tokens)
    {
        foreach ($tokens as $token => $count) {
            if (isset($this->dictionary[$token])) {
                $this->dictionary[$token]['count'] += $count;
            } else {
                $this->dictionary[$token]['count'] = $count;
            }
        }

        $this->documentCount ++;

        $this->recount();
    }

    /**
     * Removes tokens to dictionary
     * @param TokenArray $tokens
     */
    public function removeTokens(TokenArray $tokens)
    {
        foreach ($tokens as $token => $count) {
            if (isset($this->dictionary[$token])) {
                $this->dictionary[$token]['count'] -= $count;

                if ($this->dictionary[$token]['count'] <= 0) {
                    unset($this->dictionary[$token]);
                }
            }
        }

        $this->documentCount--;

        $this->recount();
    }

    /**
     * summates tokens and recounts probabilities
     */
    protected function recount()
    {
        // count dictionary size
        $this->tokenCount = array_reduce($this->dictionary,
                function($previous, $value) {
                    return $value['count'] + $previous;
                }, 0);

        $this->usableTokenCount = 0;

        // check tokens
        foreach (array_keys($this->dictionary) as $token) {

            // max/min token length
            if(mb_strlen($token) > $this->getMaximalTokenLength() ||
                    mb_strlen($token) < $this->getMinimalTokenLength()) {
                $this->dictionary[$token]['weight'] = 0;
                continue;
            }

            // skip stopwords
            if (in_array($token, $this->stopwords)) {
                $this->dictionary[$token]['weight'] = 0;
                continue;
            }

            // this is temporary value before normalization
            $this->dictionary[$token]['weight'] = 1;
            $this->usableTokenCount += $this->dictionary[$token]['count'];
        }

        // recount weights
        foreach (array_keys($this->dictionary) as $token) {
            if (1 == $this->dictionary[$token]['weight']) {
                $this->dictionary[$token]['weight'] = $this->dictionary[$token]['count'] / $this->usableTokenCount;
            }
        }
    }

    /**
     * Dumps tokens
     * @return array
     */
    public function dump()
    {
        return $this->dictionary;
    }

    public function serialize()
    {
        return serialize(array('dic' => $this->dictionary,
            'minimal_token_length' => $this->getMinimalTokenLength(),
            'maximal_token_length' => $this->getMaximalTokenLength(),
            'stopwords' => $this->stopwords));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        // set internal arrays
        $this->dictionary = $data['dic'];
        $this->stopwords = $data['stopwords'];

        // this calls recount, so data must be filled before setters
        $this->setMinimalTokenLength($data['minimal_token_length']);
        $this->setMaximalTokenLength($data['maximal_token_length']);

        $this->recount();
    }

    /**
     * Matches disctionary against tokens
     * @param TokenArray $tokens
     * @return double
     */
    public function match(TokenArray $tokens)
    {
        $poly = array();

        foreach ($tokens as $token => $count) {

            if(isset($this->dictionary[$token])) {
                $weight = $this->dictionary[$token]['weight'];

                if ($weight != 0) {
                    $poly[] = log(1 - $weight, M_E);
                }
            }
        }

        return 1 / ( 1 + pow(M_E, array_sum($poly)));
    }

    /**
     * Gets minimal token length
     * @return integer
     */
    public function getMinimalTokenLength()
    {
        return $this->minimalTokenLength;
    }

    /**
     * Sets minimal token length
     * @param integer $minimalTokenLength
     */
    public function setMinimalTokenLength($minimalTokenLength)
    {
        $this->minimalTokenLength = $minimalTokenLength;
        $this->recount();
    }

    /**
     * Gets maximal token length
     * @return integer
     */
    public function getMaximalTokenLength()
    {
        return $this->maximalTokenLength;
    }

    /**
     * Sets maximal token length
     * @param integer $maximalTokenLength
     */
    public function setMaximalTokenLength($maximalTokenLength)
    {
        $this->maximalTokenLength = $maximalTokenLength;
        $this->recount();
    }

    /**
     * Gets total token count
     * @return integer
     */
    public function getTokenCount()
    {
        return $this->tokenCount;
    }

    /**
     * Gets usable token count (not filtered by length, stemmer and so on)
     * @return integer
     */
    public function getUsableTokenCount()
    {
        return $this->usableTokenCount;
    }


}

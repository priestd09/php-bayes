<?php

/**
 * MIT licensed
 */

namespace Noop\Bayes\Dictionary;

use Noop\Bayes\Token\TokenArray;

class Dictionary implements \Serializable
{
    /**
     * Dictionary of all tokens
     * @var array
     */
    protected $dictionary;
    
    /**
     * Current document count
     * @var int
     */
    protected $documentCount;
    
    /**
     * Minimal frequency in document for token to be included.
     * For example, if this is 0.1, then all tokens found in less than 1 doc of
     * 10 will be not taken in account
     * @var double
     */
    protected $minimalFrequencyInDocuments;

    /**
     * Dictionary size
     * @var integer
     */
    protected $tokenCount;

    public function __construct()
    {
        $this->dictionary = array();
        $this->tokenCount = 0;
        $this->minimalFrequencyInDocuments = 0.05;
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

        // count weights
        foreach ($this->dictionary as $token => $data) {
            // skip tokens that are less popular than $minimalFrequencyInDocs
            if($this->data['count'] / $this->documentCount < $this->getMinimalFrequencyInDocuments()) {
                continue;
            }
            
            $this->dictionary[$token]['weight'] = $data['count'] / $this->tokenCount;
        }
        
        $this->normalize();
    }
    
    /**
     * Normalizes frequences
     */
    protected function normalize()
    {
        
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
        return serialize(array('dic' => $this->dictionary));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->dictionary = $data['dic'];

        $this->recount();
    }

    /**
     * Matches disctionary against tokens
     * @param TokenArray $tokens
     * @return double
     */
    public function match(TokenArray $tokens)
    {
        $poly = 1;

        foreach ($tokens as $token => $count) {

            if(isset($this->dictionary[$token])) {
                $weight = $this->dictionary[$token]['weight'];

                $poly = $poly * (1 - $weight);
            }
        }

        return 1 / ( 1 + $poly);
    }
    
    /**
     * Gets document count
     * @return integer
     */
    public function getDocumentCount()
    {
        return $this->documentCount;
    }

    /**
     * Sets document count
     * @param integer $documentCount 
     */
    public function setDocumentCount($documentCount)
    {
        $this->documentCount = $documentCount;
    }

    /**
     * Gets minimal frequency in document for token to be included.
     * For example, if this is 0.1, then all tokens found in less than 1 doc of
     * 10 will be not taken in account
     * @return double
     */
    public function getMinimalFrequencyInDocuments()
    {
        return $this->minimalFrequencyInDocuments;
    }

    /**
     * Sets minimal frequency in document for token to be included.
     * For example, if this is 0.1, then all tokens found in less than 1 doc of
     * 10 will be not taken in account
     * @var double $minimalFrequencyInDocuments
     */
    public function setMinimalFrequencyInDocuments($minimalFrequencyInDocuments)
    {
        $this->minimalFrequencyInDocuments = $minimalFrequencyInDocuments;
    }
}

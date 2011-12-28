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
     * Dictionary size
     * @var int
     */
    protected $tokenCount;

    public function __construct()
    {
        $this->dictionary = array();
        $this->tokenCount = 0;
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
            $this->dictionary[$token]['weight'] = $data['count'] / $this->tokenCount;
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
     * @return float
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
}

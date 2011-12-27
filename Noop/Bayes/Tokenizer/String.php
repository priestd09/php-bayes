<?php

/**
 * MIT licensed
 */

namespace Noop\Bayes\Tokenizer;

use Noop\Bayes\Token\TokenArray;

class String extends AbstractTokenizer
{
    /**
     * regex constant for string parsing
     */
    const WORD_REGEX = '/[[:alpha:]]+/u';
    
    /**
     * Tokenizer implementation - splits string to words
     * @param string $string
     * @return TokenArray
     */
    public function tokenize($string)
    {
        $result = new TokenArray();
        
        preg_match_all(self::WORD_REGEX, $string, $matches);
        
        if (!isset($matches[0])) return $result;
        
        foreach ($matches[0] as $match) {
            
            $match = $this->normalizeValue($match);
            
            if (isset($result[$match])) {
                $result[$match] += 1;
            } else {
                $result[$match] = 1;
            }
        }
        
        return $result;
    }
}

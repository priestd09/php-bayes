<?php

namespace Noop\Bayes\Tokenizer;

class String extends AbstractTokenizer
{
    const WORD_REGEX = '/[[:alpha:]]+/u';
    
    public function tokenize($string)
    {
        preg_match_all(self::WORD_REGEX, $string, $matches);
        
        if (!isset($matches[0])) return array();
        
        $result = array();
        
        foreach ($matches[0] as $match) {
            
            $match = $this->normalizeValue($match);
            
            if (isset($result[$match])) {
                $result[$match] ++;
            } else {
                $result[$match] = 1;
            }
        }
        
        return $result;
    }
}

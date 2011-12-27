<?php

namespace Noop\Bayes\Tokenizer;

abstract class AbstractTokenizer
{
    abstract public function tokenize($data);
    
    protected function normalizeValue($value)
    {
        $value = mb_strtolower($value, 'utf-8');
        
        return $value;
    }
}

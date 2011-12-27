<?php

namespace Noop\Bayes\Tokenizer;

abstract class AbstractTokenizer
{
    protected $encoding;
    
    protected $defaultEncoding = 'utf-8';
    
    abstract public function tokenize($data);
    
    public function __construct()
    {
        $this->setEncoding($this->defaultEncoding);
    }
    
    protected function normalizeValue($value)
    {
        $value = mb_strtolower($value);
        
        return $value;
    }
    
    public function getEncoding()
    {
        return $this->encoding;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        mb_internal_encoding($encoding);
    }
}

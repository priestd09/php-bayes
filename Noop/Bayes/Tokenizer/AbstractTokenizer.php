<?php

/**
 * MIT licensed
 */

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
    
    /**
     * Normalizes token value to use in dictionary
     * @param string $value
     * @return string 
     */
    protected function normalizeValue($value)
    {
        $value = mb_strtolower($value);
        
        return $value;
    }
    
    /**
     * Gets encoding used in tokenizer
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Sets encoding used in tokenizer (mbstring internally)
     * @param string $encoding 
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        mb_internal_encoding($encoding);
    }
}

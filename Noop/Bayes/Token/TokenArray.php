<?php

/**
 * MIT licensed
 */

namespace Noop\Bayes\Token;

class TokenArray implements \ArrayAccess, \Countable
{
    /**
     * Internal token array
     * @var array
     */
    protected $tokens;
    
    /**
     * Token count
     * @var int
     */
    protected $tokenCount;
    
    public function __construct()
    {
        $this->tokens = array();
        $this->tokenCount = 0;
    }
    
    public function offsetExists($offset)
    {
        return isset($this->tokens[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->tokens[$offset];
    }

    public function offsetSet($offset, $value)
    {
        // we need to recount $tokenCount
        $this->offsetUnset($offset);
        
        $this->tokens[$offset] = $value;
        
        $this->tokenCount += $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->tokenCount -= $this->offsetGet($offset);
            unset($this->tokens[$offset]);
        }
    }
    
    public function count()
    {
        return count($this->tokens);
    }
    
    /**
     * Dumps tokens
     * @return array
     */
    public function toArray()
    {
        return $this->tokens;
    }
    
    /**
     * Loads tokens from array
     * @param array $tokens 
     */
    public function fromArray($tokens)
    {
        $this->tokens = $tokens;
        $this->tokenCount = array_sum($tokens);
    }
    
    public function getTokenCount()
    {
        return $this->tokenCount;
    }
}

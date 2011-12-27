<?php

require_once(__DIR__.'/../autoload.php');

class TokenArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenArray()
    {
        $ta = new \Noop\Bayes\Token\TokenArray();
        
        $this->assertEquals(count($ta), 0);
        
        $ta['foo'] = 1;
        $ta['boo'] = 25;
        
        $this->assertEquals(count($ta), 2);
        $this->assertEquals($ta->getTokenCount(), 26);
        
        $ta['bar'] = 3;
        $ta['bar'] += 2;
        
        $this->assertEquals(count($ta), 3);
        $this->assertEquals($ta->getTokenCount(), 31);
        
        unset($ta['boo']);
        $this->assertEquals(count($ta), 2);
        $this->assertEquals($ta->getTokenCount(), 6);
        
        $this->assertEquals($ta->toArray(), array('foo' => 1, 'bar' => 5));
        
        $ta->fromArray(array('sex' => 2, 'drugs' => 3, 'rocknroll' => 4));
        
        $this->assertEquals(count($ta), 3);
        $this->assertEquals($ta->getTokenCount(), 9);
    }
}

<?php
namespace Framework;
class DiTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->di = new Di;
    }
    
    public function testMagic()
    {
        $this->assertFalse(isset($this->di->foo));
        $this->di->foo = 'bar';
        $this->assertTrue(isset($this->di->foo));
        $this->assertSame('bar', $this->di->foo);
        unset($this->di->foo);
        $this->assertFalse(isset($this->di->foo));
    }
    
    public function testSetHasGet()
    {
        $this->assertFalse($this->di->has('mock'));
        
        $this->di->set('mock', function () {
            return new \StdClass;
        });
        
        $this->assertTrue($this->di->has('mock'));
        
        $instance1 = $this->di->get('mock');
        $this->assertInstanceOf('StdClass', $instance1);
        
        $instance2 = $this->di->get('mock');
        $this->assertSame($instance1, $instance2);
        $instance3 = $this->di->newInstance('mock');
        $this->assertFalse($instance2 === $instance3);
    }
    
    public function testGetNoSuchInstance()
    {
        $this->expectException('UnexpectedValueException');
        $this->di->get('NoSuchInstance');
    }
}

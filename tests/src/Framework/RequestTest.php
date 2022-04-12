<?php
namespace Tests\Framework;
use PHPUnit\Framework\TestCase;
use Framework\Request;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    public function newRequest()
    {
        return new Request($GLOBALS);
    }
    public function testCookie()
    {
        $_COOKIE['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->cookie['foo']);
    }
    public function testEnv()
    {
        $_ENV['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->env['foo']);
    }
    public function testFiles()
    {
        $_FILES['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->files['foo']);
    }
    public function testGet()
    {
        $_GET['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->get['foo']);
    }
    public function testPost()
    {
        $_POST['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->post['foo']);
    }
    public function testRequest()
    {
        $_REQUEST['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->request['foo']);
    }
    public function testServer()
    {
        $_SERVER['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->server['foo']);
    }
}

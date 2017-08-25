<?php

namespace Ticme\Csrf\Test;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ticme\Csrf\CsrfMiddleware;

class CsrfMiddleWareTest extends TestCase
{



    private function makeMiddleware($session = []){
        return new CsrfMiddleware($session);
    }

    private function makeRequest($method = 'GET', $params = null){
        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
        $request->method('getMethod')->willReturn($method);
        $request->method('getParsedBody')->willReturn($params);
        return $request;
    }

    private function makeDelegate(){
        $delegate = $this->getMockBuilder(DelegateInterface::class)->getMock();
        $delegate->method('process')->willReturn($this->makeResponse());
        return $delegate;
    }

    private function makeResponse(){
        return $this->getMockBuilder(ResponseInterface::class)->getMock();
    }

    private function testAcceptValidSession(){
        $a = [];
        $b = $this->getMockBuilder(\ArrayAccess::class)->getMock();
        $middlewareA = $this->makeMiddleware($a);
        $middlewareB = $this->makeMiddleware($b);
        $this->assertInstanceOf(CsrfMiddleware::class, $middlewareA);
        $this->assertInstanceOf(CsrfMiddleware::class, $middlewareB);
    }

    private function testRejectInvalidSession(){
        $this->expectException();
        $middlewareA = $this->makeMiddleware(new \stdClass());
    }

    private function testGetPass(){
        $middleware = $this->makeMiddleware();
        $delegate = $this->makeDelegate();
        $delegate->expects($this->once())->method('process');
        $middleware->process(
            $this->makeRequest('GET'),
            $delegate
        );
    }

    private function testPreventPost(){
        $middleware = $this->makeMiddleware();
        $delegate = $this->makeDelegate();
        $delegate->expects($this->never())->method('process');
        $this->expectException(\Exception::class);
        $middleware->process(
            $this->makeRequest('POST'),
            $delegate
        );
    }

    private function testPostSuccessfullyWithToken(){
        $middleware = $this->makeMiddleware();
        $token = $middleware->generateToken();
        $delegate = $this->makeDelegate();
        $delegate->expects($this->once())->method('process')->willReturn($this->makeResponse());
        $this->expectException(\Exception::class);
        $middleware->process(
            $this->makeRequest('POST', ['_csrf' => $token]),
            $delegate
        );
    }

    private function testPostWithInvalidToken(){
        $middleware = $this->makeMiddleware();
        $token = $middleware->generateToken();
        $delegate = $this->makeDelegate();
        $delegate->expects($this->once())->method('process')->willReturn($this->makeResponse());
        $this->expectException(\Exception::class);
        $middleware->process(
            $this->makeRequest('POST', ['_csrf' => 'badtoken']),
            $delegate
        );
    }
}
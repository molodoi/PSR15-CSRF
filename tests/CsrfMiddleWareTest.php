<?php

namespace Ticme\Csrf\Test;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ticme\Csrf\CsrfMiddleware;

class CsrfMiddleWareTest extends TestCase {

    public function makeRequest($method = 'GET'){
        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
        $request->method('getMethod')->willReturn($method);
        return $request;
    }

    public function makeDelegate(){
        $request = $this->getMockBuilder(DelegateInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $request->method('process')->willReturn($method);
        return $request;
    }

    public function testGetPass(){
        $middleware = new CsrfMiddleware();
        $middleware->process(
            $this->makeRequest('GET'),

        );
    }
}
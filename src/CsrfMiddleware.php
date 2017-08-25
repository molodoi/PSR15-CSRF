<?php

namespace Ticme\Csrf;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfMiddleware implements MiddlewareInterface
{

    /**
     * @var array|\ArrayAccess
     */
    private $session;
    /**
     * @var string
     */
    private $formKey;


    /**
     * CsrfMiddleware constructor.
     * @param array|\ArrayAccess $session
     * @param string $sessionKey
     * @param string $formKey
     */
    public function __construct(&$session, $sessionKey = 'csrf.tokens', $formKey = '_csrf')
    {
        $this->session = $session;
        $this->sessionKey = $sessionKey;
        $this->formKey = $formKey;
    }

    private $sessionKey;

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if(in_array($request->getMethod(), ['PUT', 'POST', 'DELETE'])){

        }
        return $delegate->process($request);
    }

    /**
     * Generate and store a random token
     * @return string
     */
    public function generateToken()
    {
        $token = bin2hex(random_bytes(16));
        $tokens = $this->session['csrf.tokens'] ?? [];
        $tokens[] = $token;
        $this->session['csrf.tokens'] = $tokens;
        return $token;
    }

}
<?php

/*
 * The MIT License
 *
 * Copyright 2020 Semyon Mamonov <semyon.mamonov@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace AppBundle\Classes\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\KernelEvent;

/**
 * Description of UserPlaceFilterEvent
 *
 * Generated: Jan 23, 2020 7:39:49 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class UserPlaceFilterEvent extends KernelEvent{
    
    const NAME = 'housekeeper.user.place.filter';

    protected $userPlaces = null;
    
    protected $container = null;
    
    
    public function __construct(\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, \Symfony\Component\HttpFoundation\Request $request, $requestType) {
        parent::__construct($kernel, $request, $requestType);
        
        $this->container = $kernel;
        
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        if (!\is_null ( $token = $this->container->get('security.token_storage')->getToken()) ) {
            if (\is_object($user = $token->getUser())) {
                $this->userPlaces = $user->getPlaces()->toArray();
            }
        }
    }
    
    
    public function getUserPlaces(){
        return $this->userPlaces;
    }
   
}

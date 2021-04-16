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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Description of UserPlaceFilterListener
 *
 * Generated: Jan 23, 2020 8:08:45 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class UserPlaceFilterListener {
    //put your code here
    
    protected $userPlaces = null;
    
    
    /**
     *
     * @var Container
     */
    private $container;

    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container){
        $this->container = $container;
    }
    
    
    public function onKernelController(FilterControllerEvent $event, $eventName, EventDispatcherInterface $eventDispatcher){

        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        if (!\is_null ( $token = $this->container->get('security.token_storage')->getToken()) ) {
            if (\is_object($user = $token->getUser())) {
                $em = $this->container->get('doctrine.orm.entity_manager');
                $filterName = 'user_place_filter';
                $filter = $em->getFilters()->enable($filterName)
                        ->setEntityManager($em)
                        ->setFilterName($filterName)
                        ->setPlaces($user->getPlaces());
            }
        }
        
        
        
//        if (!$this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
//            throw $this->createAccessDeniedException();
//        }
//        
//        
//        if (!$this->container->has('security.token_storage')) {
//            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
//        }
//
//        if (!\is_null ( $token = $this->container->get('security.token_storage')->getToken()) ) {
//            if (\is_object($user = $token->getUser())) {
//                $this->userPlaces = $user->getPlaces()->toArray();
//            }
//        }
//        
        
        
        
        //$controller->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

//        $user = $controller->getUser();
        
        
    }
    
}

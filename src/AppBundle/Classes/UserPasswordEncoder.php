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

namespace AppBundle\Classes;


//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * Description of UserPasswordEncoder
 * 
 * 
 * Probably possible for using for same aims is the Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder
 *
 * Generated: Jan 7, 2020 2:48:53 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */

class UserPasswordEncoder implements PasswordEncoderInterface{
    //put your code here

    const HMAC_ALGORITHM = 'sha512';

    private function getHMACedData($raw,$salt){
        return hash_hmac(self::HMAC_ALGORITHM,$raw,$salt); 
    } 
    
    /**
     * Encodes the raw password.
     *
     * @param string      $raw  The password to encode
     * @param string|null $salt The salt
     *
     * @return string The encoded password
     *
     * @throws BadCredentialsException   If the raw password is invalid, e.g. excessively long
     * @throws \InvalidArgumentException If the salt is invalid
     */
    public function encodePassword($raw, $salt) {
        $encodedPass = $this->getHMACedData($raw, $salt);
        return $encodedPass;
    }

    /**
     * Checks a raw password against an encoded password.
     *
     * @param string      $encoded An encoded password
     * @param string      $raw     A raw password
     * @param string|null $salt    The salt
     *
     * @return bool true if the password is valid, false otherwise
     *
     * @throws \InvalidArgumentException If the salt is invalid
     */
    public function isPasswordValid($encoded, $raw, $salt) {
        return $encoded === $this->getHMACedData($raw, $salt);
    }

}

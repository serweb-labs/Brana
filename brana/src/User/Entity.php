<?php

namespace App\User;
use Brana\CmfBundle\Store\Entity\BaseEntity;
/**
 *
 */
class Entity extends BaseEntity
{
    protected $contentTypeName = 'users';

    private $id;
    private $hash;
    private $password;
    private $user;
    public $username;
    public $type;
    public $email;
    public $from_date;
    public $is_admin;
    public $name;

    public function setPassword($value)
    {   
        $this->hash = password_hash($value, PASSWORD_DEFAULT);
    }

    public function getPassword()
    { 
        return $this->hash;
    }

    public function setId($value)
    { 
        $this->id = $value;
    }

    public function getId()
    { 
        return $this->id;
    }

    public function getHash()
    {
        return $this->hash;
    }

    // consider avoid expose setter for sensible data
    // currently necessary for hydration
    public function setHash($value)
    {
        $this->hash = $value;
    }

}

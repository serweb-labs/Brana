<?php
namespace Brana\CmfBundle\Store\Query;

interface Command {

    public function register() : boolean;

    public function getName() : string;

    public function methodName() : string;

    public function directiveName() : string;
    
    public function execute(array $args) : array;

}

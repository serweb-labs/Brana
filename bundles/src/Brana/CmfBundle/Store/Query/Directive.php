<?php
namespace Brana\CmfBundle\Store\Query;

interface Directive {

    public function getName() : string;

    public function setParam($param, $val) : void;

    public function execute() : string;

    public function toRepresentation();

}

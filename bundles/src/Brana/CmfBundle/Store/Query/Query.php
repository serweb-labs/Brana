<?php
namespace Brana\CmfBundle\Store\Query;

interface Query {

    // public state
    public function qs(array $raw = []);

    public function getState();

    public function getArray() : array;

    public function execute(array $args);

}

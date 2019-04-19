<?php

namespace Brana\CmfBundle\Psr;

interface ContentTypesConfig
{

    public function load();

    public function get($path = null);

    public function set($path = null);

    public function __invoke();
}

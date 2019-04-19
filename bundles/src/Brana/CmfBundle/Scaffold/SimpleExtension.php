<?php
namespace Brana\Scaffold;

use Brana\Scaffold;

class SimpleExtension {

    public function __construct(BranaHooks $hooks) {
        $this->hooks = $hooks;
        $this-registerAll();
    }

    public function registerAll() {
        $this->hooks->register('set-store', $this->onSetStore);
        $this->hooks->register('app-init', $this->onAppInit);
        $this->hooks->register('app-end', $this->onAppEnd);
    }

    public function onSetStore()
    {
    }

    public function onAppInit()
    {
    }

    public function onAppEnd()
    {
    }

} 
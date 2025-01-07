<?php

namespace App\Livewire;

use Livewire\Component;

class BaseFunction extends Component
{

    public function alert($type, $message)
    {
        $this->dispatch('showAlert', [
            'type' => $type,
            'message' => $message
        ]);
    }
}

<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;

class DeleteModal extends Component
{
    public bool $open = false;
    public string $title = 'Etes-vous absolument sur ?';
    public string $description = 'Cette action est definitive et supprimera ces donnees.';
    public string $confirmButtonText = 'Continuer';
    public string $confirmButtonClass = '';
    public string $component = '';
    public string $method = '';
    public array $params = [];

    #[On('open-delete-modal')]
    public function open(
        string $component,
        string $method,
        array $params = [],
        string $title = '',
        string $description = '',
        string $confirmButtonText = 'Continuer',
        string $confirmButtonClass = ''
    ) {
        $this->component = $component;
        $this->method = $method;
        $this->params = $params;

        if ($title) $this->title = $title;
        if ($description) $this->description = $description;
        $this->confirmButtonText = $confirmButtonText;
        $this->confirmButtonClass = $confirmButtonClass ?: 'bg-red-600 text-white hover:bg-red-500';

        $this->open = true;
    }

    public function confirm()
    {
        if ($this->component && $this->method) {
            $this->dispatch($this->method, ...$this->params)->to($this->component);
        }
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.components.delete-modal');
    }
}

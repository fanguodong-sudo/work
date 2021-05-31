<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    /**
     * @var string
     */
    public $type;
    public $message;
    public $isSelected;
    public $caseData = [
        'aa' => 'aa',
        'bb' => 'bb',
        'cc' => 'cc',
        'dd' => 'dd'
    ];

    /**
     * Create a new component instance.
     *
     * @param string $type
     * @param string $message
     */
    public function __construct($type='1',$message = 'aa',$isSelected='cc')
    {
        $this->type = $type;
        $this->message = $message;
        $this->isSelected = $isSelected;
    }

    public function isSelected($option): bool
    {
        return $option === $this->isSelected;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.alert');
    }
}

<?php

namespace App\View\Components;

use Illuminate\View\Component;

class message extends Component
{

    public $type;
    public $message;
    //protected $except = ['type'];

    /**
     * Create a new component instance.
     *
     * @param string $message
     * @param string $type
     */
    public function __construct($message='1111222333',$type='2222')
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {

//        //直接显示view
//        return <<<'blade'
//            <div class="alert alert-danger">
//                {{ $slot }}
//            </div>
//        blade;

        return view('components.message');
    }

    public function formatAlert($s){
        return strtoupper($s);
    }
}

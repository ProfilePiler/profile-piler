<?php

namespace App\View\Components;

use App\Core\Mappers\ProfileMapper;
use Illuminate\View\Component;

class ProfileItem extends Component
{
    public $profile;
    /**
     * Mode can be selection/move/view
     */
    public $mode = "selection";
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($profile, $mode)
    {
        $this->profile = $profile;
        $this->mode = $mode;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.profile-item');
    }
}

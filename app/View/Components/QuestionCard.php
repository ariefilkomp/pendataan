<?php

namespace App\View\Components;

use App\Models\Question;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class QuestionCard extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Question | null $question = null,
        public bool $disabled = false
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.question-card');
    }
}

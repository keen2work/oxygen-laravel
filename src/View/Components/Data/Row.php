<?php

namespace EMedia\Oxygen\View\Components\Data;

class Row extends \Illuminate\View\Component
{

	public $label;

	/**
	 * Create a new component instance.
	 *
	 * @return void
	 */
	public function __construct($label)
	{
		$this->label = $label;
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\Contracts\View\View|\Closure|string
	 */
	public function render()
	{
		return view('oxygen::components.data.row');
	}

}

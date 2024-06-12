<?php

namespace App\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Form;
use App\Models\Area;

class AreaForm extends Form
{
  public ?Area $area = null;
  #[Locked]
  public $id = null;
  public $name = '';
  public $state = 1;
  public function rules()
  {
    return [
      'name' => [
        'required',
        'string',
        'max:150',
        Rule::unique('areas')->ignore($this->area),
      ],
      'state' => 'required|integer|in:1,2',
    ];
  }
  public function setArea(Area $area)
  {
    $this->area = $area;
    $this->id = $area->id;
    $this->name = $area->name;
    $this->state = $area->state;
  }
  public function store()
  {
    $this->validate();
    Area::create($this->all());
    $this->reset();
  }
  public function update()
  {
    $this->validate();
    $this->area->update($this->all());
    $this->reset();
  }
  public function delete()
  {
    $this->area->delete();
  }
}

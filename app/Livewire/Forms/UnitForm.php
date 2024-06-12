<?php

namespace App\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Form;
use App\Models\Unit;

class UnitForm extends Form
{
  public ?Unit $unit = null;
  #[Locked]
  public $id = null;
  public $name = '';
  public $abbreviation = '';
  public $state = 1;

  public function rules()
  {
    return [
      'name' => [
        'required',
        'string',
        'max:150',
      ],
      'abbreviation' => [
        'required',
        'string',
        'max:50',
        Rule::unique('units')->ignore($this->unit),
      ],
      'state' => [
        'required',
        'integer'
      ],
    ];
  }

  public function setUnit(Unit $unit)
  {
    $this->unit = $unit;
    $this->id = $unit->id;
    $this->name = $unit->name;
    $this->abbreviation = $unit->abbreviation;
    $this->state = $unit->state;
  }

  public function store()
  {
    $this->validate();
    Unit::create($this->all());
    $this->reset();
  }

  public function update()
  {
    $this->validate();
    $this->unit->update($this->all());
    $this->reset();
  }

  public function delete()
  {
    $this->unit->delete();
  }
}

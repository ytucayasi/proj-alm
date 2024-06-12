<?php

namespace App\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Form;
use App\Models\Category;

class CategoryForm extends Form
{
  public ?Category $category = null;
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
        Rule::unique('categories')->ignore($this->category),
      ],
      'state' => [
        'required',
        'integer'
      ],
    ];
  }
  public function setCategory(Category $category)
  {
    $this->category = $category;
    $this->id = $category->id;
    $this->name = $category->name;
    $this->state = $category->state;
  }
  public function store()
  {
    $this->validate();
    Category::create($this->all());
    $this->reset();
  }
  public function update()
  {
    $this->validate();
    $this->category->update($this->all());
    $this->reset();
  }
  public function delete()
  {
    $this->category->delete();
  }
}
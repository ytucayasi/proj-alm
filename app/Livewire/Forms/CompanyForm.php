<?php

// app/Livewire/Forms/CompanyForm.php
namespace App\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use App\Models\Company;

class CompanyForm extends Form
{
  public ?Company $company = null;
  #[Locked]
  public $id = null;
  public $name = '';
  public $ruc = '';

  public function rules()
  {
    return [
      'name' => [
        'required',
        'string',
        'max:150',
        Rule::unique('companies')->ignore($this->company),
      ],
      'ruc' => [
        'required',
        'string',
        'max:11',
        Rule::unique('companies')->ignore($this->company),
      ],
    ];
  }

  public function setCompany(Company $company)
  {
    $this->company = $company;
    $this->id = $company->id;
    $this->name = $company->name;
    $this->ruc = $company->ruc;
  }

  public function store()
  {
    $this->validate();
    Company::create($this->all());
    $this->reset();
  }

  public function update()
  {
    $this->validate();
    $this->company->update($this->all());
    $this->reset();
  }

  public function delete()
  {
    $this->company->delete();
  }
}

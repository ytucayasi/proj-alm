<?php

namespace App\Livewire\Forms;

use App\Models\Inventory;
use App\Models\Variation;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Form;
use App\Models\Product;

class ProductForm extends Form
{
  public ?Product $product = null;
  #[Locked]
  public $id = null;
  public $name = '';
  public $description = '';
  public $category_id = null;
  public $state = 1;
  public $product_type = 1;
  public $price_base = 0;
  public $stock_min = 0;
  public $variationId = null;
  public $variation = null;

  public function rules()
  {
    return [
      'name' => [
        'required',
        'string',
        'max:150',
        Rule::unique('products')->ignore($this->product),
      ],
      'description' => 'nullable|string',
      'category_id' => 'required|integer|exists:categories,id',
      'state' => 'required|integer|in:1,2',
      'product_type' => 'required|integer|in:1,2',
      'stock_min' => 'required|numeric|min:0.01'
    ];
  }

  public function setProduct(Product $product)
  {
    $this->product = $product;
    $this->id = $product->id;
    $this->name = $product->name;
    $this->description = $product->description;
    $this->category_id = $product->category_id;
    $this->state = $product->state;
    $this->product_type = $product->product_type;
    $this->stock_min = $product->stock_min;
  }
  public function setVariation($variationId)
  {
    $this->variation = Variation::findOrFail($variationId);
    $this->price_base = $this->variation->price_base;
  }
  public function updateVariation()
  {
    $this->variation->update($this->only(['price_base']));
  }
  public function store()
  {
    $this->validate();
    Product::create($this->all());
    $this->reset();
  }
  public function update()
  {
    $this->validate();
    $this->product->update($this->all());
    $this->reset();
  }
  public function delete()
  {
    $this->product->delete();
  }
}

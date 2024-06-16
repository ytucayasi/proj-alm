<?php

namespace App\Livewire\Forms;

use App\Models\Area;
use App\Models\AreaProduct;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Variation;
use Exception;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AreaProductForm extends Form
{
  public ?AreaProduct $area_product = null;
  public ?Area $area = null;
  #[Locked]
  public $products = [];
  public $units = [];
  public $id = null;
  public $area_id = null;
  public $product_id = null;
  public $unit_id = null;
  public $quantity = 0;
  public $price = 0;
  public function rules()
  {
    return [
      'area_id' => 'required|integer|exists:areas,id',
      'product_id' => 'required|integer|exists:products,id',
      'unit_id' => 'required|integer|exists:units,id',
      'quantity' => 'required|integer|min:1',
      'price' => 'required|numeric|min:0.01',
    ];
  }
  public function init()
  {
    $this->products = Product::all();
    $this->units = collect();
  }
  public function updatedProductId($productId)
  {
    $product = Product::find($productId);
    if ($product) {
      $variations = $product->variations;
      $unitIds = $variations->pluck('unit_id')->unique();
      $units = Unit::whereIn('id', $unitIds)->get();
      $this->units = $units;
      if ($units->isNotEmpty()) {
        $this->unit_id = $units->first()->id;
        $this->updatedUnitId($this->unit_id);
      }
    } else {
      $this->units = collect();
      $this->unit_id = null;
      $this->price = 0;
    }
  }
  public function updatedUnitId($unitId)
  {
    $this->price = Variation::where("unit_id", $unitId)->first()->price_base;
  }
  public function setArea($areaId)
  {
    $this->area = Area::findOrFail($areaId);
    $this->area_id = $this->area->id;
  }
  public function setAreaProduct($areaProductId)
  {
    $this->area_product = AreaProduct::findOrFail($areaProductId);
    $this->id = $this->area_product->id;
    $this->product_id = $this->area_product->product_id;
    $this->unit_id = $this->area_product->unit_id;
    $this->quantity = $this->area_product->quantity;
    $this->price = $this->area_product->price;
  }
  public function store()
  {
    $this->validate();
    AreaProduct::create($this->all());
    $this->resetOnly();
  }
  public function update()
  {
    $this->validate();
    $this->area_product->update($this->all());
    $this->resetOnly();
  }
  public function delete($id)
  {
    $this->area_product = AreaProduct::findOrFail($id);
    $this->area_product->delete();
    $this->resetOnly();
  }
  public function resetOnly()
  {
    $this->reset(["area_product", "id", "product_id", "unit_id", "quantity", "price"]);
  }
}

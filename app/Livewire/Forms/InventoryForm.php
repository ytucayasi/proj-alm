<?php

// app/Livewire/Forms/InventoryForm.php
namespace App\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Variation;
use Exception;

class InventoryForm extends Form
{
  public ?Inventory $inventory = null;
  #[Locked]
  public $id = null;
  public $product_id = null;
  public $quantity = 0;
  public $movement_type = 1;
  public $unit_price = 0.0;
  public $unit_id = null;
  public $description = '';

  public function rules()
  {
    return [
      'product_id' => 'required|integer|exists:products,id',
      'unit_id' => 'required|integer|exists:units,id',
      'movement_type' => 'required|integer|in:1,2',
      'quantity' => 'required|integer|min:1',
      'unit_price' => 'required|numeric|min:0',
      'description' => 'nullable|string',
    ];
  }

  public function setInventory(Inventory $inventory)
  {
    $this->inventory = $inventory;
    $this->id = $inventory->id;
    $this->product_id = $inventory->product_id;
    $this->quantity = $inventory->quantity;
    $this->movement_type = $inventory->movement_type;
    $this->unit_price = $inventory->unit_price;
    $this->unit_id = $inventory->unit_id;
    $this->description = $inventory->description;
  }

  public function store()
  {
    $this->validate();

    if ($this->movement_type == 1 && $this->unit_price < 1) {
      throw new Exception('Ingrese el precio');
    }

    $product = Product::findOrFail($this->product_id);
    $variation = Variation::firstOrCreate([
      'product_id' => $this->product_id,
      'unit_id' => $this->unit_id,
    ], [
      'quantity_base' => 0,
      'price_base' => 0,
    ]);

    if (!$this->updateProductQuantity($variation, 'create')) {
      return;
    }

    $inventory = Inventory::create($this->all());

    if ($this->movement_type == 1) { // Entrada
      $this->updateProductPriceBase($variation);
    }

    $this->reset();
  }

  public function update()
  {
    $this->validate();

    $originalQuantity = $this->inventory->quantity;
    $originalMovementType = $this->inventory->movement_type;
    $originalUnitId = $this->inventory->unit_id;

    $product = Product::findOrFail($this->product_id);
    $newVariation = Variation::firstOrCreate([
      'product_id' => $this->product_id,
      'unit_id' => $this->unit_id,
    ], [
      'quantity_base' => 0,
      'price_base' => 0,
    ]);

    if ($originalUnitId != $this->unit_id) {
      $originalVariation = Variation::where('product_id', $this->product_id)
        ->where('unit_id', $originalUnitId)
        ->firstOrFail();

      // Restar la cantidad original de la variación original
      if (!$this->updateProductQuantity($originalVariation, 'delete', $originalQuantity, $originalMovementType)) {
        return;
      }

      // Sumar la nueva cantidad a la nueva variación
      if (!$this->updateProductQuantity($newVariation, 'create')) {
        return;
      }

      // Actualizar precios para ambas variaciones
      $this->updateProductPriceBase($originalVariation);
      $this->updateProductPriceBase($newVariation);
    } else {
      // Solo actualizar la cantidad y precio de la misma variación
      if (!$this->updateProductQuantity($newVariation, 'update', $originalQuantity, $originalMovementType)) {
        return;
      }

      // Actualizar precio solo para la variación afectada
      $this->updateProductPriceBase($newVariation);
    }

    if ($this->movement_type == 2) {
      $this->unit_price = 0;
    }

    $this->inventory->update($this->all());

    $this->reset();
  }

  public function delete()
  {
    $product = Product::findOrFail($this->inventory->product_id);
    $variation = Variation::where('product_id', $this->inventory->product_id)
      ->where('unit_id', $this->inventory->unit_id)
      ->firstOrFail();

    if (!$this->updateProductQuantity($variation, 'delete')) {
      return;
    }

    $this->inventory->delete();

    if ($this->movement_type == 1) { // Entrada
      $this->updateProductPriceBase($variation);
    }
  }

  private function updateProductQuantity(Variation $variation, string $action, int $originalQuantity = 0, int $originalMovementType = 0)
  {
    if ($action === 'create') {
      if ($this->movement_type == 1) { // Entrada
        $variation->quantity_base += $this->quantity;
      } else { // Salida
        if ($variation->quantity_base < $this->quantity) {
          throw new Exception('No hay suficiente stock para realizar esta salida.');
        }
        $variation->quantity_base -= $this->quantity;
      }
    } elseif ($action === 'update') {
      if ($originalMovementType == 1 && $this->movement_type == 1) { // Ambas entradas
        $variation->quantity_base += ($this->quantity - $originalQuantity);
      } elseif ($originalMovementType == 2 && $this->movement_type == 2) { // Ambas salidas
        if ($variation->quantity_base < ($this->quantity - $originalQuantity)) {
          throw new Exception('No hay suficiente stock para realizar esta salida.');
        }
        $variation->quantity_base -= ($this->quantity - $originalQuantity);
      } elseif ($originalMovementType == 1 && $this->movement_type == 2) { // Cambio de entrada a salida
        $variation->quantity_base -= ($originalQuantity + $this->quantity);
        if ($variation->quantity_base < 0) {
          throw new Exception('No hay suficiente stock para realizar esta salida.');
        }
      } elseif ($originalMovementType == 2 && $this->movement_type == 1) { // Cambio de salida a entrada
        $variation->quantity_base += ($originalQuantity + $this->quantity);
      }
    } elseif ($action === 'delete') {
      if ($this->movement_type == 1) { // Entrada
        $variation->quantity_base -= $originalQuantity; // Usar la cantidad original aquí
      } else { // Salida
        $variation->quantity_base += $originalQuantity; // Usar la cantidad original aquí
      }

      if ($variation->quantity_base < 0) {
        throw new Exception('No hay suficiente stock para realizar esta eliminación.');
      }
    }

    $variation->save();
    return true;
  }

  private function updateProductPriceBase(Variation $variation)
  {
    $totalEntries = Inventory::where('product_id', $variation->product_id)
      ->where('unit_id', $variation->unit_id)
      ->where('movement_type', 1) // Entradas
      ->count();

    $totalUnitPrice = Inventory::where('product_id', $variation->product_id)
      ->where('unit_id', $variation->unit_id)
      ->where('movement_type', 1) // Entradas
      ->sum('unit_price');

    $variation->price_base = $totalEntries ? $totalUnitPrice / $totalEntries : 0;
    $variation->save();
  }
}


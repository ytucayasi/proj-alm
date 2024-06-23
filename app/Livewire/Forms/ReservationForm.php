<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use App\Models\Unit;
use App\Models\Variation;
use Exception;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use App\Models\Reservation;
use Illuminate\Support\Collection;

class ReservationForm extends Form
{
  public ?Reservation $reservation = null;
  #[Locked]
  public $id = null;
  public $company_name = '';
  public $description = '';
  public $status = 3;
  public $payment_status = 2;
  public $type = 1;
  public $order_date = null;
  public $execution_date = null;
  public $total_cost = 0.00;
  public $people_count = 0;
  public $total_products = 0;
  public $variations = [];
  public $selectedVariation = null;
  public $selectedProducts;
  public $uniqueIndex = 0;

  public function __construct()
  {
    $this->selectedProducts = collect();
  }

  public function rules()
  {
    return [
      'company_name' => 'required|string|max:150',
      'description' => 'nullable|string',
      'status' => 'required|integer|in:1,2,3,4,5',
      'payment_status' => 'required|integer|in:1,2',
      'type' => 'required|integer',
      'order_date' => 'required|date',
      'execution_date' => 'nullable|date',
      'total_cost' => 'required|numeric|min:0',
      'people_count' => 'required|integer|min:1',
      'total_products' => 'required|integer|min:0',
    ];
  }

  public function setProduct($productId)
  {
    $selectedProduct = Product::findOrFail($productId);
    $this->variations = $selectedProduct->variations()->get();
  }

  public function setVariation($variationId, $action)
  {
    $this->selectedVariation = Variation::findOrFail($variationId);
    if ($action == "create") {
      $this->addProduct($variationId);
    } elseif ($action == "edit") {
      $this->editProduct($variationId);
    } elseif ($action == "delete") {
      $this->deleteProduct($variationId);
    } else {
      throw new Exception('La acción no es válida');
    }
  }

  public function deleteProduct($variationId)
  {
    $this->selectedProducts = $this->selectedProducts->filter(function ($product) use ($variationId) {
      return $product['variation_id'] !== $variationId;
    })->values();
    $this->reindexProducts();
  }

  public function editProduct($variationId)
  {
    $this->selectedProducts = $this->selectedProducts->map(function ($product) use ($variationId) {
      if ($product['variation_id'] === $variationId) {
        if (!$product['price_edit'] && !$product['quantity_edit']) {
          if ($product['quantity'] <= 0) {
            return null; // Eliminar el producto de la lista
          } elseif ($product['quantity'] > $product['variation_stock']) {
            $product['quantity'] = $product['variation_stock'];
          }
          if ($product['variation_price'] <= 0) {
            $product['variation_price'] = 1;
          }
        }
        $product['price_edit'] = !$product['price_edit'];
        $product['quantity_edit'] = !$product['quantity_edit'];
      }
      return $product;
    })->filter();
    $this->reindexProducts();
  }

  public function incrementQuantityVariation($variationId)
  {
    $existingVariation = $this->selectedProducts->firstWhere('variation_id', $variationId);
    if ($existingVariation) {
      $this->selectedProducts = $this->selectedProducts->map(function ($product) use ($variationId) {
        if ($product['variation_id'] === $variationId) {
          if ($product['quantity'] < $product['variation_stock']) {
            $product['quantity'] += 1;
          } else {
            $product['quantity'] = $product['variation_stock'];
          }
        }
        return $product;
      });
    } else {
      throw new Exception('La variación no existe para su edición');
    }
  }

  public function decrementQuantityVariation($variationId)
  {
    $this->selectedProducts = $this->selectedProducts->map(function ($product) use ($variationId) {
      if ($product['variation_id'] === $variationId) {
        if ($product['quantity'] > 1) {
          $product['quantity'] -= 1;
        } else {
          return null; // Remover el producto de la lista si la cantidad es menor que 1
        }
      }
      return $product;
    })->filter()->values();
    $this->reindexProducts();
  }

  public function addProduct($variationId)
  {
    $existingVariation = $this->selectedProducts->firstWhere('variation_id', $variationId);
    if ($existingVariation) {
      $this->selectedProducts = $this->selectedProducts->map(function ($product) use ($variationId) {
        if ($product['variation_id'] === $variationId) {
          $product['quantity'] += 1;
        }
        return $product;
      });
    } else {
      $this->selectedProducts->push([
        "variation_id" => $this->selectedVariation->id,
        "product_id" => $this->selectedVariation->product->id,
        "product_name" => $this->selectedVariation->product->name,
        "unit_id" => $this->selectedVariation->unit->id,
        "unit_name" => $this->selectedVariation->unit->name,
        "unit_abbreviation" => $this->selectedVariation->unit->abbreviation,
        "quantity" => 1,
        "variation_price" => $this->selectedVariation->price_base,
        "variation_stock" => $this->selectedVariation->quantity_base,
        "price_edit" => true,
        "quantity_edit" => true,
        "index" => $this->uniqueIndex
      ]);
      $this->uniqueIndex += 1;
    }
    $this->reindexProducts();
  }

  public function reindexProducts()
  {
    $this->selectedProducts = $this->selectedProducts->map(function ($product, $index) {
      $product['index'] = $index;
      return $product;
    });
    $this->uniqueIndex = $this->selectedProducts->count();
  }

  public function setReservation(Reservation $reservation)
  {
    $this->reservation = $reservation;
    $this->id = $reservation->id;
    $this->company_name = $reservation->company_name;
    $this->description = $reservation->description;
    $this->status = $reservation->status;
    $this->payment_status = $reservation->payment_status;
    $this->type = $reservation->type;
    $this->order_date = $reservation->order_date->format('Y-m-d\TH:i');
    $this->execution_date = optional($reservation->execution_date)->format('Y-m-d\TH:i');
    $this->total_cost = $reservation->total_cost;
    $this->people_count = $reservation->people_count;
    $this->total_products = $reservation->total_products;
  }

  public function store()
  {
    $this->validate();
    Reservation::create($this->all());
    $this->reset();
  }

  public function update()
  {
    $this->validate();
    $this->reservation->update($this->all());
    $this->reset();
  }

  public function delete()
  {
    $this->reservation->delete();
  }
}

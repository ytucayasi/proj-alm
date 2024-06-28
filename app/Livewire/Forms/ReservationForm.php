<?php

namespace App\Livewire\Forms;

use App\Models\Area;
use App\Models\AreaProduct;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Variation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
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
  public $status = 1;
  public $payment_status = 1;
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
  public $selectedAreas = [];
  public $area = null;
  public $productsExceedingStock = [];
  public $searchS = "";
  public $searchP = "";

  public function init($reservationId = null)
  {
    $this->selectedProducts = collect();
    $this->selectedAreas = collect();
    if (!$reservationId) {
      $this->order_date = now()->format('Y-m-d\TH:i'); // Fecha y hora actual
      $this->execution_date = now()->format('Y-m-d\TH:i'); // Fecha y hora actual
    }
  }

  public function rules()
  {
    return [
      'company_name' => 'required|string|max:150',
      'description' => 'nullable|string',
      'status' => 'required|integer|in:1,2,3,4',
      'payment_status' => 'required|integer|in:1,2',
      'order_date' => 'required|date',
      'type' => 'required|integer',
      'execution_date' => 'required|date',
      'total_cost' => 'required|numeric|min:0',
      'people_count' => 'required|integer|min:1',
      'total_products' => 'required|numeric|min:0',
    ];
  }

  public function setArea($areaId)
  {
    $this->selectedAreas->push([
      "area_id" => $areaId
    ]);
    $areaProducts = AreaProduct::where('area_id', $areaId)->get();
    foreach ($areaProducts as $areaProduct) {
      $variation = Variation::where('product_id', $areaProduct->product_id)
        ->where('unit_id', $areaProduct->unit_id)
        ->first();
      $this->addProductToSelected($variation, $areaProduct->price, $areaProduct->quantity);
    }
  }

  public function addProduct($variationId)
  {
    $variation = Variation::findOrFail($variationId);
    $price = $variation->price_base;
    $quantity = 1;
    $this->addProductToSelected($variation, $price, $quantity);
  }

  private function addProductToSelected(Variation $variation, $price, $quantity)
  {
    $existingProduct = $this->selectedProducts->firstWhere('variation_id', $variation->id);

    // Definir initialStock y variationStock dependiendo de si es una edición
    if ($this->id && $existingProduct) {
      $initialStock = $existingProduct['initial_stock'];
      $variationStock = $initialStock - $existingProduct['quantity'];
    } else {
      $initialStock = $variation->quantity_base;
      $variationStock = $variation->quantity_base;
    }

    if ($existingProduct) {
      $newQuantity = $existingProduct['quantity'] + $quantity;
      if ($newQuantity > $initialStock) {
        $this->addProductExceedingStock($variation, $newQuantity - $initialStock);
      } else {
        $this->selectedProducts = $this->selectedProducts->map(function ($product) use ($variation, $newQuantity, $variationStock) {
          if ($product['variation_id'] === $variation->id) {
            $product['quantity'] = $newQuantity;
            $product['variation_stock'] = $product['initial_stock'] - $newQuantity;
          }
          return $product;
        });
      }
    } else {
      if ($quantity > $variationStock) {
        $this->addProductExceedingStock($variation, $quantity - $variationStock);
      } else {
        $this->selectedProducts->push([
          "variation_id" => $variation->id,
          "product_id" => $variation->product->id,
          "product_name" => $variation->product->name,
          "unit_id" => $variation->unit->id,
          "unit_name" => $variation->unit->name,
          "unit_abbreviation" => $variation->unit->abbreviation,
          "quantity" => $quantity,
          "variation_price" => $price,
          "variation_stock" => $variationStock - $quantity, //cambio
          "price_edit" => true,
          "quantity_edit" => true,
          "index" => $this->uniqueIndex,
          "initial_stock" => $initialStock // Agregar initial_stock
        ]);
        $this->uniqueIndex += 1;
      }
    }

    $this->reindexProducts();
    $this->calculateTotals();
  }

  private function addProductExceedingStock($variation, $exceededQuantity)
  {
    $existingExceededProduct = collect($this->productsExceedingStock)->firstWhere('product_name', $variation->product->name);
    if (!$existingExceededProduct) {
      $this->productsExceedingStock[] = [
        "product_name" => $variation->product->name,
        "exceeded_quantity" => $exceededQuantity
      ];
    }
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
    $this->calculateTotals();
  }

  public function editProduct($variationId)
  {
    $this->selectedProducts = $this->selectedProducts->map(function ($product) use ($variationId) {
      if ($product['variation_id'] === $variationId) {
        if (!$product['price_edit'] && !$product['quantity_edit']) {
          if ($product['quantity'] <= 0) {
            return null;
          } elseif ($product['quantity'] > $product['initial_stock']) {
            $product['quantity'] = $product['initial_stock'];
          }
          if ($product['variation_price'] <= 0) {
            $product['variation_price'] = 1;
          }
        }
        $product['price_edit'] = !$product['price_edit'];
        $product['quantity_edit'] = !$product['quantity_edit'];
      }
      $product['variation_stock'] = $product['initial_stock'] - $product['quantity'];
      return $product;
    })->filter();
    $this->reindexProducts();
    $this->calculateTotals();
  }

  public function incrementQuantityVariation($variationId)
  {
    $existingVariation = $this->selectedProducts->firstWhere('variation_id', $variationId);
    if ($existingVariation) {
      $this->selectedProducts = $this->selectedProducts->map(function ($product) use ($variationId) {
        if ($product['variation_id'] === $variationId) {
          if ($product['quantity'] < $product['initial_stock']) {
            $product['quantity'] += 1;
          } else {
            $product['quantity'] = $product['initial_stock'];
          }
          $product['variation_stock'] = $product['initial_stock'] - $product['quantity'];
        }
        return $product;
      });
    } else {
      throw new Exception('La variación no existe para su edición');
    }
    $this->calculateTotals();
  }

  public function decrementQuantityVariation($variationId)
  {
    $this->selectedProducts = $this->selectedProducts->map(function ($product) use ($variationId) {
      if ($product['variation_id'] === $variationId) {
        if ($product['quantity'] > 1) {
          $product['quantity'] -= 1;
        } else {
          return null;
        }
        $product['variation_stock'] = $product['initial_stock'] - $product['quantity'];
      }
      return $product;
    })->filter()->values();
    $this->reindexProducts();
    $this->calculateTotals();
  }

  public function reindexProducts()
  {
    $this->selectedProducts = $this->selectedProducts->map(function ($product, $index) {
      $product['index'] = $index;
      return $product;
    });
    $this->uniqueIndex = $this->selectedProducts->count();
  }

  public function setReservation($reservationId)
  {
    $this->reservation = Reservation::findOrFail($reservationId);
    $this->id = $this->reservation->id;
    $this->company_name = $this->reservation->company_name;
    $this->description = $this->reservation->description;
    $this->status = $this->reservation->status;
    $this->payment_status = $this->reservation->payment_status;
    $this->type = $this->reservation->type;
    $this->order_date = Carbon::parse($this->reservation->order_date)->format('Y-m-d\TH:i'); // Formatear la fecha correctamente
    $this->execution_date = Carbon::parse($this->reservation->execution_date)->format('Y-m-d\TH:i'); // Formatear la fecha correctamente
    $this->total_cost = $this->reservation->total_cost;
    $this->people_count = $this->reservation->people_count;
    $this->total_products = $this->reservation->total_products;

    foreach ($this->reservation->inventories as $inventory) {
      $variation = Variation::where('product_id', $inventory->product_id)
        ->where('unit_id', $inventory->unit_id)
        ->first();
      $this->selectedProducts->push([
        "variation_id" => $variation->id,
        "product_id" => $variation->product->id,
        "product_name" => $variation->product->name,
        "unit_id" => $variation->unit->id,
        "unit_name" => $variation->unit->name,
        "unit_abbreviation" => $variation->unit->abbreviation,
        "quantity" => $inventory->quantity,
        "variation_price" => $inventory->unit_price,
        "variation_stock" => $variation->quantity_base,
        "price_edit" => true,
        "quantity_edit" => true,
        "index" => $this->uniqueIndex++,
        "initial_stock" => $variation->quantity_base + $inventory->quantity // Definir initial_stock
      ]);
    }
    $this->calculateTotals();
  }

  public function store()
  {
    $this->validate();
    $this->calculateTotals();
    if ($this->selectedProducts->isEmpty()) {
      throw new Exception('Debe agregar al menos un producto a la reserva.');
    }

    DB::transaction(function () {
      $reservation = Reservation::create($this->only([
        'company_name',
        'description',
        'status',
        'payment_status',
        'type',
        'order_date',
        'execution_date',
        'total_cost',
        'people_count',
        'total_products'
      ]));

      foreach ($this->selectedProducts as $product) {
        $variation = Variation::findOrFail($product['variation_id']);

        if ($product['quantity'] > $variation->quantity_base) {
          throw new Exception('El producto ' . $variation->product->name . ' excede el stock disponible.');
        }

        $variation->quantity_base -= $product['quantity'];
        $variation->save();

        DB::table('inventories')->insert([
          'product_id' => $product['product_id'],
          'unit_id' => $product['unit_id'],
          'reservation_id' => $reservation->id,
          'movement_type' => 2,
          'quantity' => $product['quantity'],
          'unit_price' => $product['variation_price'],
          'type_action' => 2,
          'description' => $this->description,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }
    });
  }

  private function calculateTotals()
  {
    $totalCost = 0;
    $totalProducts = 0;

    foreach ($this->selectedProducts as $product) {
      $totalCost += $product['variation_price'] * $product['quantity'];
      $totalProducts += $product['quantity'];
    }

    $this->total_cost = $totalCost;
    $this->total_products = $totalProducts;
  }

  public function update()
  {
    $this->validate();
    $this->calculateTotals();
    if ($this->selectedProducts->isEmpty()) {
      throw new Exception('Debe agregar al menos un producto a la reserva.');
    }

    DB::transaction(function () {

      // Obtener los productos actuales en la reserva
      $currentProducts = $this->reservation->inventories()->get();

      // Actualizar la reserva
      $this->reservation->update($this->only([
        'company_name',
        'description',
        'status',
        'payment_status',
        'order_date',
        'execution_date',
        'total_cost',
        'people_count',
        'total_products'
      ]));

      // Reponer el stock de los productos que ya no están en la lista de selectedProducts
      foreach ($currentProducts as $currentProduct) {
        if (!$this->selectedProducts->firstWhere('variation_id', $currentProduct->variation_id)) {
          $variation = Variation::where('product_id', $currentProduct->product_id)
            ->where('unit_id', $currentProduct->unit_id)
            ->first();
          $variation->quantity_base += $currentProduct->quantity;
          $variation->save();
        }
      }

      // Eliminar productos antiguos en la tabla de inventarios para esta reserva
      DB::table('inventories')->where('type_action', 2)
        ->where('reservation_id', $this->id)
        ->delete();

      foreach ($this->selectedProducts as $product) {
        $variation = Variation::findOrFail($product['variation_id']);

        // Reponer la cantidad inicial del stock antes de cualquier modificación
        $currentProduct = $currentProducts->firstWhere('variation_id', $product['variation_id']);
        if ($currentProduct) {
          $variation->quantity_base += $currentProduct->quantity;
        }

        if ($product['quantity'] > $variation->quantity_base) {
          throw new Exception('El producto ' . $variation->product->name . ' excede el stock disponible.');
        }

        // Actualizar el stock con la nueva cantidad
        $variation->quantity_base -= $product['quantity'];
        $variation->save();

        DB::table('inventories')->insert([
          'product_id' => $product['product_id'],
          'unit_id' => $product['unit_id'],
          'reservation_id' => $this->id,
          'movement_type' => 2,
          'quantity' => $product['quantity'],
          'unit_price' => $product['variation_price'],
          'type_action' => 2,
          'description' => $this->description,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }
    });
  }
  public function delete($reservationId)
  {
    $this->reservation = Reservation::findOrFail($reservationId);
    DB::transaction(function () {
      $currentProducts = $this->reservation->inventories()->get();
      foreach ($currentProducts as $currentProduct) {
        $variation = Variation::where('product_id', $currentProduct->product_id)
          ->where('unit_id', $currentProduct->unit_id)
          ->first();
        $variation->quantity_base += $currentProduct->quantity;
        $variation->save();
      }
      DB::table('inventories')->where('type_action', 2)
        ->where('reservation_id', $this->id)
        ->delete();
      $this->reservation->delete();
    });
  }
}
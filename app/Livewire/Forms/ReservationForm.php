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
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Reservation;
use Illuminate\Support\Collection;

class ReservationForm extends Form
{
  public ?Reservation $reservation = null;
  #[Locked]
  public $id = null;
  public $company_name = 'NOT FOUND';
  public $description = '';
  public $status = 1;
  public $payment_status = 1;
  public $type = 1;
  public $order_date = null;
  public $execution_date = null;
  public $total_cost = 0.00;
  public $people_count = 0;
  public $total_products = 0;
  public $cost_pack = 0;
  public $total_pack = 0;
  public $variations = [];
  public $selectedVariation = null;
  public $selectedProducts; // Ahora será un array de áreas con productos
  public $uniqueIndex = 0;
  public $selectedAreas = [];
  public $selectedCompanies = [];
  public $area = null;
  public $productsExceedingStock = [];
  public $searchS = "";
  public $searchP = "";

  public function init($reservationId = null)
  {
    $this->selectedProducts = collect(); // Ahora será una colección de áreas
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
      'cost_pack' => 'required|numeric|min:0',
      'total_pack' => 'required|numeric|min:0',
      'people_count' => 'required|integer|min:1',
      'total_products' => 'required|numeric|min:0',
    ];
  }
  public function recargar_precios()
  {
    $this->selectedProducts = $this->selectedProducts->map(function ($area) {
      $area['products'] = $area['products']->map(function ($product) {
        $variation = Variation::where("product_id", $product["product_id"])
          ->where("unit_id", $product["unit_id"])
          ->first();

        if ($variation) {
          $product['variation_price'] = $variation->price_base;
        }

        return $product;
      });
      return $area;
    });
    $this->calculateTotals();
  }
  public function setArea($areaId)
  {
    $areaProducts = AreaProduct::where('area_id', $areaId)->get();

    foreach ($areaProducts as $areaProduct) {
      $variation = Variation::where('product_id', $areaProduct->product_id)
        ->where('unit_id', $areaProduct->unit_id)
        ->first();

      if ($variation) {
        $this->addProductToSelected($variation, $variation->price_base, $areaProduct->quantity, $areaId);
      }
    }

    $this->reindexProducts();
    $this->calculateTotals();
  }
  public function addProduct($variationId, $areaId)
  {
    $variation = Variation::findOrFail($variationId);
    $price = $variation->price_base;
    $quantity = 0.01;
    $this->addProductToSelected($variation, $price, $quantity, $areaId);
  }
  private function addProductToSelected(Variation $variation, $price, $quantity, $areaId)
  {
    // Verificar el stock total del producto en todas las áreas
    $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($variation) {
      $product = $area['products']->firstWhere('variation_id', $variation->id);
      if ($product) {
        $carry += $product['quantity'];
      }
      return $carry;
    }, 0);

    // Si el área ya existe
    $existingAreaIndex = $this->selectedProducts->search(function ($area) use ($areaId) {
      return $area['area_id'] == $areaId;
    });

    if ($existingAreaIndex !== false) {
      $existingArea = $this->selectedProducts->get($existingAreaIndex);
      $existingProductIndex = $existingArea['products']->search(function ($product) use ($variation) {
        return $product['variation_id'] == $variation->id;
      });

      if ($existingProductIndex !== false) {
        $existingProduct = $existingArea['products']->get($existingProductIndex);

        $newQuantity = $existingProduct['quantity'] + $quantity;
        if ($newQuantity + $totalQuantityInAllAreas - $existingProduct['quantity'] > $variation->quantity_base) {
          $this->addProductExceedingStock($variation, $newQuantity + $totalQuantityInAllAreas - $existingProduct['quantity'] - $variation->quantity_base, $areaId);
        } else {
          $existingProduct['quantity'] = $newQuantity;
          $existingProduct['variation_stock'] = round($existingProduct['initial_stock'] - $newQuantity, 2);
          $existingArea['products']->put($existingProductIndex, $existingProduct);
        }

        $this->selectedProducts->put($existingAreaIndex, $existingArea);
      } else {
        if ($quantity + $totalQuantityInAllAreas > $variation->quantity_base) {
          $this->addProductExceedingStock($variation, $quantity + $totalQuantityInAllAreas - $variation->quantity_base, $areaId);
        } else {
          // El producto no existe en esta área, agregarlo
          $existingArea['products']->push([
            "variation_id" => $variation->id,
            "product_id" => $variation->product->id,
            "product_name" => $variation->product->name,
            "unit_id" => $variation->unit->id,
            "unit_name" => $variation->unit->name,
            "unit_abbreviation" => $variation->unit->abbreviation,
            "quantity" => $quantity,
            "variation_price" => $price,
            "variation_stock" => round($variation->quantity_base - $quantity, 2),
            "price_edit" => true,
            "quantity_edit" => true,
            "index" => $this->uniqueIndex++,
            "initial_stock" => $variation->quantity_base
          ]);

          $this->selectedProducts->put($existingAreaIndex, $existingArea);
        }
      }
    } else {
      // Validar antes de agregar una nueva área
      if ($quantity + $totalQuantityInAllAreas > $variation->quantity_base) {
        $this->addProductExceedingStock($variation, $quantity + $totalQuantityInAllAreas - $variation->quantity_base, $areaId);
      } else {
        // El área no existe, agregarla
        $areaData = [
          "area_id" => $areaId,
          "index" => $this->uniqueIndex++, // Agregar índice único al área
          "products" => collect()
        ];

        $areaData['products']->push([
          "variation_id" => $variation->id,
          "product_id" => $variation->product->id,
          "product_name" => $variation->product->name,
          "unit_id" => $variation->unit->id,
          "unit_name" => $variation->unit->name,
          "unit_abbreviation" => $variation->unit->abbreviation,
          "quantity" => $quantity,
          "variation_price" => $price,
          "variation_stock" => round($variation->quantity_base - $quantity, 2),
          "price_edit" => true,
          "quantity_edit" => true,
          "index" => $this->uniqueIndex++,
          "initial_stock" => $variation->quantity_base
        ]);

        $this->selectedProducts->push($areaData);
      }
    }

    // Actualizar el variation_stock en todas las áreas
    $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($variation) {
      $product = $area['products']->firstWhere('variation_id', $variation->id);
      if ($product) {
        $carry += $product['quantity'];
      }
      return $carry;
    }, 0);

    foreach ($this->selectedProducts as &$area) {
      $productIndex = $area['products']->search(function ($product) use ($variation) {
        return $product['variation_id'] == $variation->id;
      });

      if ($productIndex !== false) {
        $product = $area['products'][$productIndex];
        $product['variation_stock'] = round($product['initial_stock'] - $totalQuantityInAllAreas, 2);
        $area['products']->put($productIndex, $product);
      }
    }
    $this->reindexProducts();
  }
  private function addProductExceedingStock($variation, $exceededQuantity, $areaId)
  {
    $existingExceededProduct = collect($this->productsExceedingStock)->firstWhere('variation_id', $variation->id);
    if (!$existingExceededProduct) {
      $this->productsExceedingStock[] = [
        "variation_id" => $variation->id,
        "variation_abbr" => $variation->unit->abbreviation,
        "product_name" => $variation->product->name,
        "exceeded_quantity" => $exceededQuantity,
        "area_id" => $areaId,
      ];
    } else {
      // Actualizamos la cantidad excedida si ya existe el producto para esa área
      foreach ($this->productsExceedingStock as &$product) {
        if ($product['variation_id'] == $variation->id && $product['area_id'] == $areaId) {
          $product['exceeded_quantity'] += $exceededQuantity;
          break;
        }
      }
    }
  }
  public function setProduct($productId)
  {
    $selectedProduct = Product::findOrFail($productId);
    $this->variations = $selectedProduct->variations()->get();
  }
  public function setVariation($variationId, $action, $areaId)
  {
    $this->selectedVariation = Variation::findOrFail($variationId);
    if ($action == "create") {
      $this->addProduct($variationId, $areaId);
    } elseif ($action == "edit") {
      $this->editProduct($variationId, $areaId);
    } elseif ($action == "delete") {
      $this->deleteProduct($variationId, $areaId);
    } else {
      throw new Exception('La acción no es válida');
    }
  }
  public function deleteProduct($variationId, $areaId)
  {
    // Eliminar el producto del área específica
    $this->selectedProducts = $this->selectedProducts->map(function ($area) use ($variationId, $areaId) {
      if ($area['area_id'] === $areaId) {
        $area['products'] = $area['products']->filter(function ($product) use ($variationId) {
          return $product['variation_id'] !== $variationId;
        })->values(); // Reindexar los productos
      }
      return $area;
    })->filter(function ($area) {
      return $area['products']->isNotEmpty();
    })->values(); // Reindexar las áreas

    // Actualizar el variation_stock en todas las áreas
    $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($variationId) {
      $product = $area['products']->firstWhere('variation_id', $variationId);
      if ($product) {
        $carry += $product['quantity'];
      }
      return $carry;
    }, 0);

    // Actualizar el stock en todas las áreas
    $this->selectedProducts = $this->selectedProducts->map(function ($area) use ($variationId, $totalQuantityInAllAreas) {
      $area['products'] = $area['products']->map(function ($product) use ($variationId, $totalQuantityInAllAreas) {
        if ($product['variation_id'] === $variationId) {
          $product['variation_stock'] = $product['initial_stock'] - $totalQuantityInAllAreas;
        }
        return $product;
      });
      return $area;
    });

    $this->reindexProducts();
    $this->calculateTotals();
  }
  public function editProduct($variationId, $areaId)
  {
    $this->selectedProducts = $this->selectedProducts->map(function ($area) use ($variationId, $areaId) {
      if ($area['area_id'] === $areaId) {
        $area['products'] = $area['products']->map(function ($product) use ($variationId, $areaId) {
          if ($product['variation_id'] === $variationId) {
            if (!$product['quantity_edit']) {
              $totalQuantityInOtherAreas = $this->selectedProducts->reduce(function ($carry, $otherArea) use ($variationId, $areaId) {
                if ($otherArea['area_id'] !== $areaId) {
                  $otherProduct = $otherArea['products']->firstWhere('variation_id', $variationId);
                  if ($otherProduct) {
                    $carry += $otherProduct['quantity'];
                  }
                }
                return $carry;
              }, 0);

              if ($product['quantity'] <= 0) {
                $product['quantity'] = 1;
              } elseif ($product['quantity'] > $product['initial_stock'] - $totalQuantityInOtherAreas) {
                $product['quantity'] = $product['initial_stock'] - $totalQuantityInOtherAreas;
              }

              if ($product['variation_price'] <= 0) {
                $product['variation_price'] = 1;
              }
            }
            $product['quantity_edit'] = !$product['quantity_edit'];
            $product['variation_stock'] = round($product['initial_stock'] - $product['quantity'], 2);
          }
          return $product;
        });
      } else {
        $area['products'] = $area['products']->map(function ($product) use ($variationId) {
          if ($product['variation_id'] === $variationId) {
            $product['quantity_edit'] = true;
          }
          return $product;
        });
      }
      return $area;
    });

    $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($variationId) {
      $product = $area['products']->firstWhere('variation_id', $variationId);
      if ($product) {
        $carry += $product['quantity'];
      }
      return $carry;
    }, 0);

    $this->selectedProducts = $this->selectedProducts->map(function ($area) use ($variationId, $totalQuantityInAllAreas) {
      $area['products'] = $area['products']->map(function ($product) use ($variationId, $totalQuantityInAllAreas) {
        if ($product['variation_id'] === $variationId) {
          $product['variation_stock'] = round($product['initial_stock'] - $totalQuantityInAllAreas, 2);
          // Evitar que el stock sea negativo
          if ($product['variation_stock'] < 0) {
            $product['variation_stock'] = 0;
          }
        }
        return $product;
      });
      return $area;
    });

    $this->reindexProducts();
    $this->calculateTotals();
  }

  public function incrementQuantityVariation($variationId, $areaId)
  {
    $variation = Variation::findOrFail($variationId);

    // Calcular la cantidad total del producto en todas las áreas
    $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($variationId) {
      $product = $area['products']->firstWhere('variation_id', $variationId);
      if ($product) {
        $carry += $product['quantity'];
      }
      return $carry;
    }, 0);

    // Modificar la cantidad y el stock en el área específica
    $this->selectedProducts = $this->selectedProducts->map(function ($area) use ($variation, $variationId, $areaId, $totalQuantityInAllAreas) {
      if ($area['area_id'] === $areaId) {
        $area['products'] = $area['products']->map(function ($product) use ($variation, $variationId, $totalQuantityInAllAreas, $areaId) {
          if ($product['variation_id'] === $variationId) {
            $newQuantity = round($product['quantity'] + 1, 2);
            // Asegurarse de que la cantidad no exceda el initial_stock
            if ($totalQuantityInAllAreas + 1 <= $product['initial_stock']) {
              $product['quantity'] = $newQuantity;
              $totalQuantityInAllAreas += 1;
              $product['variation_stock'] = round($product['initial_stock'] - $totalQuantityInAllAreas, 2);
            } else {
              $this->addProductExceedingStock($variation, 1, $areaId);
            }
          }
          return $product;
        });
      }
      return $area;
    });

    // Actualizar el variation_stock en todas las áreas
    $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($variationId) {
      $product = $area['products']->firstWhere('variation_id', $variationId);
      if ($product) {
        $carry += $product['quantity'];
      }
      return $carry;
    }, 0);

    // Actualizar el stock en todas las áreas
    $this->selectedProducts = $this->selectedProducts->map(function ($area) use ($variationId, $totalQuantityInAllAreas) {
      $area['products'] = $area['products']->map(function ($product) use ($variationId, $totalQuantityInAllAreas) {
        if ($product['variation_id'] === $variationId) {
          $product['variation_stock'] = round($product['initial_stock'] - $totalQuantityInAllAreas, 2);
          // Evitar que el stock sea negativo
          if ($product['variation_stock'] < 0) {
            $product['variation_stock'] = 0;
          }
        }
        return $product;
      });
      return $area;
    });

    $this->calculateTotals();
  }



  public function decrementQuantityVariation($variationId, $areaId)
  {
    $variation = Variation::findOrFail($variationId);

    // Modificar la cantidad y el stock en el área específica
    $this->selectedProducts = $this->selectedProducts->map(function ($area) use ($variation, $variationId, $areaId) {
      if ($area['area_id'] === $areaId) {
        $area['products'] = $area['products']->map(function ($product) use ($variation, $variationId) {
          if ($product['variation_id'] === $variationId) {
            if ($product['quantity'] > 1) {
              $product['quantity'] = round($product['quantity'] - 1, 2);
              $product['variation_stock'] = round($product['initial_stock'] - $product['quantity'], 2);
              // Evitar que el stock sea negativo
              if ($product['variation_stock'] < 0) {
                $product['variation_stock'] = 0;
              }
            } else {
              return null;
            }
          }
          return $product;
        })->filter(function ($product) {
          return $product !== null; // Filtrar productos eliminados
        })->values(); // Reindexar los productos
      }
      return $area;
    });

    // Actualizar el variation_stock en todas las áreas
    $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($variationId) {
      $product = $area['products']->firstWhere('variation_id', $variationId);
      if ($product) {
        $carry += $product['quantity'];
      }
      return $carry;
    }, 0);

    // Actualizar el stock en todas las áreas
    $this->selectedProducts = $this->selectedProducts->map(function ($area) use ($variationId, $totalQuantityInAllAreas) {
      $area['products'] = $area['products']->map(function ($product) use ($variationId, $totalQuantityInAllAreas) {
        if ($product['variation_id'] === $variationId) {
          $product['variation_stock'] = round($product['initial_stock'] - $totalQuantityInAllAreas, 2);
          // Evitar que el stock sea negativo
          if ($product['variation_stock'] < 0) {
            $product['variation_stock'] = 0;
          }
        }
        return $product;
      });
      return $area;
    });

    $this->reindexProducts();
    $this->calculateTotals();
  }


  public function reindexProducts()
  {
    $this->selectedProducts = $this->selectedProducts->map(function ($area, $areaIndex) {
      $area['index'] = $areaIndex; // Asignar el índice al área
      $area['products'] = $area['products']->map(function ($product, $productIndex) {
        $product['index'] = $productIndex; // Asignar el índice al producto
        return $product;
      });
      return $area;
    });
    // Actualizar el índice único basado en la cantidad total de productos
    $this->uniqueIndex = $this->selectedProducts->reduce(function ($carry, $area) {
      return $carry + $area['products']->count();
    }, 0);
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
    $this->order_date = Carbon::parse($this->reservation->order_date)->format('Y-m-d\TH:i');
    $this->execution_date = Carbon::parse($this->reservation->execution_date)->format('Y-m-d\TH:i');
    $this->total_cost = $this->reservation->total_cost;
    $this->cost_pack = $this->reservation->cost_pack;
    $this->total_pack = $this->reservation->total_pack;
    $this->people_count = $this->reservation->people_count;
    $this->total_products = $this->reservation->total_products;

    // Inicializar selectedProducts como colección de áreas
    $this->selectedProducts = collect();

    // Mapear las variaciones para calcular initial_stock y total_reserved correctamente
    $variationStocks = [];

    foreach ($this->reservation->inventories as $inventory) {
      $variation = Variation::where('product_id', $inventory->product_id)
        ->where('unit_id', $inventory->unit_id)
        ->first();

      if (!isset($variationStocks[$variation->id])) {
        $variationStocks[$variation->id] = [
          'initial_stock' => $variation->quantity_base,
          'total_reserved' => 0
        ];
      }

      // Actualizar el total reservado por la cantidad del inventario actual
      $variationStocks[$variation->id]['total_reserved'] += $inventory->quantity;

      // Encontrar o crear el área correspondiente
      $areaIndex = $this->selectedProducts->search(function ($area) use ($inventory) {
        return $area['area_id'] == $inventory->type_area;
      });

      if ($areaIndex === false) {
        $areaData = [
          "area_id" => $inventory->type_area,
          "index" => $this->uniqueIndex++, // Agregar índice único al área
          "products" => collect()
        ];
        $this->selectedProducts->push($areaData);
        $areaIndex = $this->selectedProducts->count() - 1;
      }

      // Agregar el producto al área correspondiente
      $this->selectedProducts[$areaIndex]['products']->push([
        "variation_id" => $variation->id,
        "product_id" => $variation->product->id,
        "product_name" => $variation->product->name,
        "unit_id" => $variation->unit->id,
        "unit_name" => $variation->unit->name,
        "unit_abbreviation" => $variation->unit->abbreviation,
        "quantity" => $inventory->quantity,
        "variation_price" => $inventory->unit_price,
        "variation_stock" => max(0, $variation->quantity_base - $variationStocks[$variation->id]['total_reserved']),
        "price_edit" => true,
        "quantity_edit" => true,
        "index" => $this->uniqueIndex++,
        "initial_stock" => 0 // Se inicializa en 0 y se calcula después
      ]);
    }

    // Recalcular el initial_stock para cada variación
    foreach ($this->selectedProducts as $area) {
      foreach ($area['products'] as $product) {
        $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($product) {
          $foundProduct = $area['products']->firstWhere('variation_id', $product['variation_id']);
          if ($foundProduct) {
            $carry += $foundProduct['quantity'];
          }
          return $carry;
        }, 0);

        $product['initial_stock'] = $variationStocks[$product['variation_id']]['initial_stock'] + $totalQuantityInAllAreas;

        // Actualizar el producto en el área
        $area['products']->transform(function ($p) use ($product) {
          return $p['variation_id'] === $product['variation_id'] ? $product : $p;
        });
      }
    }

    // Recalcular el variation_stock para cada variación en todas las áreas
    foreach ($this->selectedProducts as $area) {
      foreach ($area['products'] as $product) {
        $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($product) {
          $foundProduct = $area['products']->firstWhere('variation_id', $product['variation_id']);
          if ($foundProduct) {
            $carry += $foundProduct['quantity'];
          }
          return $carry;
        }, 0);

        $product['variation_stock'] = max(0, $product['initial_stock'] - $totalQuantityInAllAreas);

        // Actualizar el producto en el área
        $area['products']->transform(function ($p) use ($product) {
          return $p['variation_id'] === $product['variation_id'] ? $product : $p;
        });
      }
    }

    $this->reindexProducts(); // Llamar a reindexProducts para actualizar los índices
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
        'cost_pack',
        'total_pack',
        'people_count',
        'total_products'
      ]));

      $this->id = $reservation->id;

      foreach ($this->selectedProducts as $area) {
        foreach ($area['products'] as $product) {
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
            'type_area' => $area['area_id']
          ]);
        }
      }
    });
  }
  public function update()
  {
    $this->validate();
    $this->calculateTotals();
    if ($this->selectedProducts->isEmpty()) {
      throw new Exception('Debe agregar al menos un producto a la reserva.');
    }
    DB::transaction(function () {
      // Revertir stock de los productos actuales
      $currentProducts = $this->reservation->inventories()->get();
      foreach ($currentProducts as $currentProduct) {
        $variation = Variation::where('product_id', $currentProduct->product_id)
          ->where('unit_id', $currentProduct->unit_id)
          ->first();
        if ($variation) {
          $variation->quantity_base += $currentProduct->quantity;
          $variation->save();
        }
      }
      // Actualizar la reserva
      $this->reservation->update($this->only([
        'company_name',
        'description',
        'status',
        'payment_status',
        'order_date',
        'execution_date',
        'total_cost',
        'cost_pack',
        'total_pack',
        'people_count',
        'total_products'
      ]));

      // Eliminar inventarios actuales
      DB::table('inventories')->where('type_action', 2)
        ->where('reservation_id', $this->id)
        ->delete();

      // Recalcular el stock disponible antes de agregar nuevos productos
      $stockDisponible = Variation::pluck('quantity_base', 'id');

      // Agregar nuevos productos seleccionados
      foreach ($this->selectedProducts as $area) {
        foreach ($area['products'] as $product) {
          $variation = Variation::findOrFail($product['variation_id']);

          // Verificar stock disponible
          $totalQuantityInAllAreas = $this->selectedProducts->reduce(function ($carry, $area) use ($product) {
            $foundProduct = $area['products']->firstWhere('variation_id', $product['variation_id']);
            if ($foundProduct) {
              $carry += $foundProduct['quantity'];
            }
            return $carry;
          }, 0);

          if ($totalQuantityInAllAreas > $stockDisponible[$product['variation_id']]) {
            throw new Exception('El producto ' . $variation->product->name . ' excede el stock disponible.');
          }

          // Actualizar stock
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
            'type_area' => $area['area_id']
          ]);
        }
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
      DB::table('reservation_companies')
        ->where('reservation_id', $this->id)
        ->delete();
      $this->reservation->delete();
    });
  }
  private function calculateTotals()
  {
    $totalCost = 0;
    $totalProducts = 0;

    foreach ($this->selectedProducts as $area) {
      foreach ($area['products'] as $product) {
        $totalCost += $product['variation_price'] * $product['quantity'];
        $totalProducts += $product['quantity'];
      }
    }

    $this->total_cost = $totalCost;
    $this->total_products = $totalProducts;
  }
}
<?php

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
  public $movement_type = self::MOVEMENT_TYPE_ENTRY;
  public $unit_price = 0.0;
  public $unit_id = null;
  public $description = '';
  public $type = 1;
  const MOVEMENT_TYPE_ENTRY = 1;
  const MOVEMENT_TYPE_EXIT = 2;
  public function rules()
  {
    $rules = [
      'product_id' => 'required|integer|exists:products,id',
      'unit_id' => 'required|integer|exists:units,id',
      'type' => 'required|integer',
      'movement_type' => ['required', 'integer', Rule::in([self::MOVEMENT_TYPE_ENTRY, self::MOVEMENT_TYPE_EXIT])],
      'quantity' => 'required|numeric|min:0.01',
      'description' => 'nullable|string',
    ];
    if ($this->type == 1 && $this->movement_type == 1) {
      $rules['unit_price'] = 'required|numeric|min:0.01';
    } else {
      $rules['unit_price'] = 'nullable';
    }
    return $rules;
  }
  public function messages()
  {
    return [
      'unit_price.required' => 'El :attribute es necesario.',
      'unit_price.min' => 'El :attribute debe ser mayor a 0.01.',
      'unit_price.numeric' => 'El :attribute debe ser un número.',
      'quantity.required' => 'La :attribute es necesario.',
      'quantity.min' => 'La :attribute debe ser mayor a 0.01.',
      'quantity.numeric' => 'La :attribute debe ser un número.',
      'product_id.required' => 'El :attribute es requerido.',
      'unit_id.required' => 'La :attribute es requerida.',
    ];
  }
  public function validationAttributes()
  {
    return [
      'quantity' => 'cantidad',
      'unit_price' => 'precio',
      'product_id' => 'producto',
      'unit_id' => 'unidad de medida',
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
    $this->type = $inventory->type;
  }

  public function store()
  {
    /* Validamos si se ingresaron todos los valores en el formulario */
    $this->validate();

    if ($this->type == 1) {
      /* Validamos el preccio */
      $this->validateUnitPrice();
    }

    /* Crea o Encuentra un registro existente de variación */
    $variation = $this->getOrCreateVariation();

    /* Se crea la variación validando la cantidad */
    $this->updateProductQuantity($variation, 'create');

    /* Se crea el registro y se envía a la base de datos */
    Inventory::create($this->all());

    /* Se actualiza el precio base, sacando nuevamente el promedio conforme a la cantidad de inventarios */
    if ($this->movement_type == self::MOVEMENT_TYPE_ENTRY) {
      $this->updateProductPriceBase($variation);
    }

    /* Se resetea los campos en el formulario */
    $this->reset();
  }

  public function update()
  {
    /* Validamos si se ingresaron todos los valores en el formulario */
    $this->validate();

    if ($this->type == 1) {
      /* Validamos el preccio */
      $this->validateUnitPrice();
    }

    /* guardamos en una variable la cantidad inicial del inventario */
    $originalQuantity = $this->inventory->quantity;

    /* Guardamos en una variable el tipo de movimiento inicial del inventario */
    $originalMovementType = $this->inventory->movement_type;

    /* Guardamos en una varible el id de la unidad inicial del inventario */
    $originalUnitId = $this->inventory->unit_id;

    /* Si existe la variación se selecciona si no existe lo crea */
    $newVariation = $this->getOrCreateVariation();

    /* Se valida si la unidad que se tenía es igual a la unidad que se seleccionó */
    $isUnitChanged = $originalUnitId != $this->unit_id;

    /* Se valida si el tipo de movimiento inicial es igual a al tipo de movimiento   */
    $isMovementTypeChanged = $originalMovementType != $this->movement_type;

    /* Comienza */
    if ($isUnitChanged && $isMovementTypeChanged) {
      $originalVariation = $this->getVariation($this->product_id, $originalUnitId);
      $this->updateProductQuantity($originalVariation, 'delete', $originalQuantity, $originalMovementType);
      $this->updateProductQuantity($newVariation, 'create');
    } else if ($isUnitChanged) {
      $this->updateProductQuantity($newVariation, 'create');
      $originalVariation = $this->getVariation($this->product_id, $originalUnitId);
      $this->updateProductQuantity($originalVariation, 'delete', $originalQuantity);
    } else if ($isMovementTypeChanged) {
      $this->updateProductQuantity($newVariation, 'update', $originalQuantity, $originalMovementType);
    } else {
      $this->updateProductQuantity($newVariation, 'update', $originalQuantity, $originalMovementType);
    }
    /* Termina */

    if ($this->movement_type == self::MOVEMENT_TYPE_EXIT) {
      $this->unit_price = 0;
    }

    $this->inventory->update($this->all());

    if ($isUnitChanged) {
      $this->updateProductPriceBase($originalVariation);
    }
    $this->updateProductPriceBase($newVariation);

    $this->reset();
  }

  public function delete()
  {
    $variation = $this->getVariation($this->inventory->product_id, $this->inventory->unit_id);

    $this->updateProductQuantity($variation, 'delete', $this->inventory->quantity);

    $this->inventory->delete();

    if ($this->inventory->movement_type == self::MOVEMENT_TYPE_ENTRY) {
      $this->updateProductPriceBase($variation);
    }
  }

  private function validateUnitPrice()
  {
    if ($this->movement_type == self::MOVEMENT_TYPE_ENTRY && $this->unit_price < 0.01) {
      throw new Exception('El precio es necesario.');
    }
  }

  private function getOrCreateVariation()
  {
    /* Retorna un Registro de Variación existente o Un Registro Nuevo, Dependiendo si encontró el id de producto y unidad */
    return Variation::firstOrCreate(
      [
        'product_id' => $this->product_id,
        'unit_id' => $this->unit_id,
      ],
      [
        'quantity_base' => 0,
        'price_base' => 0,
      ]
    );
  }

  private function getVariation($productId, $unitId)
  {
    return Variation::where('product_id', $productId)
      ->where('unit_id', $unitId)
      ->firstOrFail();
  }

  private function updateProductQuantity(Variation $variation, string $action, float $originalQuantity = 0, int $originalMovementType = 0)
  {
    if ($action === 'create') {
      $this->updateQuantityForCreateAction($variation);
    } elseif ($action === 'update') {
      $this->updateQuantityForUpdateAction($variation, $originalQuantity, $originalMovementType);
    } elseif ($action === 'delete') {
      $this->updateQuantityForDeleteAction($variation, $originalQuantity);
    }

    if ($variation->quantity_base < 0) {
      throw new Exception('No puedes realizar esta acción, ocurrió algo inesperado.');
    }

    $variation->save();
  }

  private function updateQuantityForCreateAction(Variation $variation)
  {
    /* Se el movimiento es de tipo entrada */
    if ($this->movement_type == self::MOVEMENT_TYPE_ENTRY) {

      /* Se suma a la variación nueva si la cantidad nueva que se ingresó en el formulario */
      $variation->quantity_base += $this->quantity;
    } else {

      /* Si el movimiento es de tipo salida entonces se valida */
      /* si la cantidad base de la variación nueva es menor a la cantidad nueva que ingresó en el formulario */
      if ($variation->quantity_base < $this->quantity) {

        /* Ocurre un error */
        throw new Exception('No hay suficiente stock para realizar esta salida.');
      }

      /* De lo contrario, se prosigue a restar a la cantidad base la cantidad que se ingresó, ya que es una salida */
      $variation->quantity_base -= $this->quantity;
    }
  }

  private function updateQuantityForUpdateAction(Variation $variation, float $originalQuantity, int $originalMovementType)
  {
    /* Si el tipo de movimiento era de tipo entrada y no cambió entonce se prosigue con esto */
    if ($originalMovementType == self::MOVEMENT_TYPE_ENTRY && $this->movement_type == self::MOVEMENT_TYPE_ENTRY) {
      $variation->quantity_base += ($this->quantity - $originalQuantity);

      /* Si el tipo de movimiento era de tipo salida y no cambió entonces se prosigue con esto */
    } elseif ($originalMovementType == self::MOVEMENT_TYPE_EXIT && $this->movement_type == self::MOVEMENT_TYPE_EXIT) {

      /* Se verifica que la cantidad base de la variación siempre sea mayor al restar la cantidad nueva y la cantidad inicial */
      if ($variation->quantity_base < ($this->quantity - $originalQuantity)) {
        throw new Exception('No hay suficiente stock para realizar esta salida.');
      }

      /* Si cantidad nueva no es mayor que la cantidad base entonces, se resta la diferencia con la cantidad base de la variación */
      $variation->quantity_base -= ($this->quantity - $originalQuantity);

      /* Si el movimiento se cambió a un tipo de movimiento salida */
    } elseif ($originalMovementType == self::MOVEMENT_TYPE_ENTRY && $this->movement_type == self::MOVEMENT_TYPE_EXIT) {

      /* Se prosigue a restar la cantidad nueva a la cantidad base de la variación ya que se cambió a salida el tipo de movimiento */
      $newQuantityBase = $variation->quantity_base - ($this->quantity + $originalQuantity);
      if ($newQuantityBase < 0) {
        throw new Exception('No puedes realizar esta acción ya que las salidas sobrepasan las entradas.');
      }

      /* Se guarda la diferencia validando que no sea menor a cero */
      $variation->quantity_base = $newQuantityBase; // se agrega 5

      /* Si el movimiento se cambió a un tipo de movimiento entrada */
    } elseif ($originalMovementType == self::MOVEMENT_TYPE_EXIT && $this->movement_type == self::MOVEMENT_TYPE_ENTRY) {

      /* Se suma la cantidad nueva a la cantidad base de la variación */
      $variation->quantity_base += $this->quantity + $originalQuantity;
    }
  }

  private function updateQuantityForDeleteAction(Variation $variation, float $originalQuantity)
  {
    /* Cuando se envía la variación y la cantidad inicial, se debe de validar si es de tipo entrada o salida */
    if ($this->inventory->movement_type == self::MOVEMENT_TYPE_ENTRY) {

      /* Si es entrada entonces se debe de restar la cantidad inicial a la cantidad base de variación */
      $variation->quantity_base -= $originalQuantity;
    } else {

      /* Si es salida entonces se debe de sumar la cantidad inicial a la cantidad base de variación */
      $variation->quantity_base += $originalQuantity;
    }

    if ($variation->quantity_base < 0) {
      throw new Exception('No hay suficiente stock para realizar esta eliminación.');
    }
  }

  private function updateProductPriceBase(Variation $variation)
  {
    $totalEntries = Inventory::where('product_id', $variation->product_id)
      ->where('unit_id', $variation->unit_id)
      ->where('movement_type', self::MOVEMENT_TYPE_ENTRY)
      ->count();

    $totalUnitPrice = Inventory::where('product_id', $variation->product_id)
      ->where('unit_id', $variation->unit_id)
      ->where('movement_type', self::MOVEMENT_TYPE_ENTRY)
      ->sum('unit_price');

    $variation->price_base = $totalEntries ? $totalUnitPrice / $totalEntries : 0;
    $variation->save();
  }
}

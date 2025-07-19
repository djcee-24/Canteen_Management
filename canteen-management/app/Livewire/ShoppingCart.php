<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Session;

class ShoppingCart extends Component
{
    public $cartItems = [];
    public $cartTotal = 0;
    public $cartCount = 0;
    public $showCart = false;

    protected $listeners = [
        'addToCart' => 'addItem',
        'removeFromCart' => 'removeItem',
        'updateQuantity' => 'updateItemQuantity',
        'clearCart' => 'clearCart',
        'toggleCart' => 'toggleCart',
    ];

    public function mount()
    {
        $this->loadCartFromSession();
    }

    public function addItem($menuItemId, $quantity = 1, $customizations = [])
    {
        $menuItem = MenuItem::find($menuItemId);
        
        if (!$menuItem || !$menuItem->is_available) {
            session()->flash('error', 'Item is not available');
            return;
        }

        $cartItemKey = $menuItemId . '_' . md5(serialize($customizations));

        if (isset($this->cartItems[$cartItemKey])) {
            $this->cartItems[$cartItemKey]['quantity'] += $quantity;
        } else {
            $this->cartItems[$cartItemKey] = [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'image' => $menuItem->image,
                'quantity' => $quantity,
                'customizations' => $customizations,
                'preparation_time' => $menuItem->preparation_time,
            ];
        }

        $this->updateCartTotals();
        $this->saveCartToSession();
        
        $this->dispatch('cartUpdated', [
            'count' => $this->cartCount,
            'total' => $this->cartTotal
        ]);

        session()->flash('success', 'Item added to cart!');
    }

    public function removeItem($cartItemKey)
    {
        if (isset($this->cartItems[$cartItemKey])) {
            unset($this->cartItems[$cartItemKey]);
            $this->updateCartTotals();
            $this->saveCartToSession();
            
            $this->dispatch('cartUpdated', [
                'count' => $this->cartCount,
                'total' => $this->cartTotal
            ]);
        }
    }

    public function updateItemQuantity($cartItemKey, $quantity)
    {
        if (isset($this->cartItems[$cartItemKey])) {
            if ($quantity <= 0) {
                $this->removeItem($cartItemKey);
                return;
            }

            $this->cartItems[$cartItemKey]['quantity'] = $quantity;
            $this->updateCartTotals();
            $this->saveCartToSession();
            
            $this->dispatch('cartUpdated', [
                'count' => $this->cartCount,
                'total' => $this->cartTotal
            ]);
        }
    }

    public function clearCart()
    {
        $this->cartItems = [];
        $this->updateCartTotals();
        $this->saveCartToSession();
        
        $this->dispatch('cartUpdated', [
            'count' => $this->cartCount,
            'total' => $this->cartTotal
        ]);

        session()->flash('success', 'Cart cleared!');
    }

    public function toggleCart()
    {
        $this->showCart = !$this->showCart;
    }

    private function updateCartTotals()
    {
        $this->cartCount = array_sum(array_column($this->cartItems, 'quantity'));
        $this->cartTotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $this->cartItems));
    }

    private function saveCartToSession()
    {
        Session::put('cart', $this->cartItems);
    }

    private function loadCartFromSession()
    {
        $this->cartItems = Session::get('cart', []);
        $this->updateCartTotals();
    }

    public function getTotalPreparationTime()
    {
        return array_sum(array_map(function ($item) {
            return $item['preparation_time'] * $item['quantity'];
        }, $this->cartItems));
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
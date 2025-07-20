<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CheckoutProcess extends Component
{
    public $cartItems = [];
    public $customerName = '';
    public $customerPhone = '';
    public $customerEmail = '';
    public $orderType = 'dine_in';
    public $paymentMethod = 'cash';
    public $specialInstructions = '';
    public $agreedToTerms = false;

    // Cart totals
    public $subtotal = 0;
    public $taxRate = 0.08; // 8% tax
    public $taxAmount = 0;
    public $total = 0;

    // Guest checkout restrictions
    public $isGuest = false;

    protected $rules = [
        'customerName' => 'required|string|max:255',
        'customerPhone' => 'nullable|string|max:20',
        'customerEmail' => 'nullable|email|max:255',
        'orderType' => 'required|in:dine_in,takeaway,online',
        'paymentMethod' => 'required|in:cash,card,online,bank_transfer',
        'specialInstructions' => 'nullable|string|max:500',
        'agreedToTerms' => 'accepted',
    ];

    public function mount()
    {
        $this->loadCartFromSession();
        $this->calculateTotals();
        
        // Set customer info if logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->customerName = $user->name;
            $this->customerPhone = $user->phone ?? '';
            $this->customerEmail = $user->email;
            
            // Check if user is guest
            $this->isGuest = $user->user_type === 'guest';
        } else {
            $this->isGuest = true; // Assume guest if not logged in
        }

        // Guests can only place online orders
        if ($this->isGuest) {
            $this->orderType = 'online';
            $this->paymentMethod = 'online';
        }
    }

    public function updatedOrderType()
    {
        // Guests can only order online
        if ($this->isGuest && $this->orderType !== 'online') {
            $this->orderType = 'online';
            session()->flash('warning', 'Guests can only place online orders.');
        }
    }

    public function updatedPaymentMethod()
    {
        // Guests cannot use cash payment
        if ($this->isGuest && $this->paymentMethod === 'cash') {
            $this->paymentMethod = 'online';
            session()->flash('warning', 'Guests must use online payment methods.');
        }
    }

    public function removeItem($cartItemKey)
    {
        if (isset($this->cartItems[$cartItemKey])) {
            unset($this->cartItems[$cartItemKey]);
            $this->saveCartToSession();
            $this->calculateTotals();
            
            $this->dispatch('cartUpdated', [
                'count' => array_sum(array_column($this->cartItems, 'quantity')),
                'total' => $this->total
            ]);
        }
    }

    public function updateQuantity($cartItemKey, $quantity)
    {
        if (isset($this->cartItems[$cartItemKey])) {
            if ($quantity <= 0) {
                $this->removeItem($cartItemKey);
                return;
            }

            $this->cartItems[$cartItemKey]['quantity'] = $quantity;
            $this->saveCartToSession();
            $this->calculateTotals();
            
            $this->dispatch('cartUpdated', [
                'count' => array_sum(array_column($this->cartItems, 'quantity')),
                'total' => $this->total
            ]);
        }
    }

    public function placeOrder()
    {
        $this->validate();

        if (empty($this->cartItems)) {
            session()->flash('error', 'Your cart is empty!');
            return;
        }

        try {
            DB::beginTransaction();

            // Create the order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => auth()->user()?->getKey(),
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_email' => $this->customerEmail,
                'order_type' => $this->orderType,
                'payment_method' => $this->paymentMethod,
                'payment_status' => $this->paymentMethod === 'cash' ? 'pending' : 'pending',
                'status' => 'pending',
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'total_amount' => $this->total,
                'special_instructions' => $this->specialInstructions,
                'estimated_completion_time' => $this->calculateEstimatedCompletionTime(),
            ]);

            // Create order items
            foreach ($this->cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $cartItem['id'],
                    'menu_item_name' => $cartItem['name'],
                    'unit_price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'total_price' => $cartItem['price'] * $cartItem['quantity'],
                    'special_instructions' => $cartItem['customizations']['notes'] ?? '',
                    'customizations' => $cartItem['customizations'] ?? [],
                ]);
            }

            DB::commit();

            // Clear the cart
            Session::forget('cart');
            $this->cartItems = [];

            // Dispatch events for real-time updates
            $this->dispatch('orderPlaced', $order->id);
            $this->dispatch('cartUpdated', ['count' => 0, 'total' => 0]);

            // Redirect to order confirmation
            session()->flash('success', 'Order placed successfully! Order #' . $order->order_number);
            return redirect()->route('order.confirmation', $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to place order. Please try again.');
            \Log::error('Order placement failed: ' . $e->getMessage());
        }
    }

    private function loadCartFromSession()
    {
        $this->cartItems = Session::get('cart', []);
    }

    private function saveCartToSession()
    {
        Session::put('cart', $this->cartItems);
    }

    private function calculateTotals()
    {
        $this->subtotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $this->cartItems));

        $this->taxAmount = $this->subtotal * $this->taxRate;
        $this->total = $this->subtotal + $this->taxAmount;
    }

    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    private function calculateEstimatedCompletionTime()
    {
        $totalPrepTime = array_sum(array_map(function ($item) {
            return $item['preparation_time'] * $item['quantity'];
        }, $this->cartItems));

        // Add some buffer time and current queue
        $queueTime = Order::where('status', 'preparing')->count() * 5; // 5 min per order in queue
        
        return now()->addMinutes($totalPrepTime + $queueTime + 10); // +10 min buffer
    }

    public function render()
    {
        return view('livewire.checkout-process');
    }
}
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Events\OrderStatusUpdated;

class OrderTracking extends Component
{
    public $order;
    public $orderNumber;
    public $showDetails = false;

    protected $listeners = [
        'echo:orders,order.status.updated' => 'orderStatusUpdated',
        'echo-private:order.{order.id},order.status.updated' => 'orderStatusUpdated',
    ];

    public function mount($orderNumber = null)
    {
        if ($orderNumber) {
            $this->orderNumber = $orderNumber;
            $this->loadOrder();
        }
    }

    public function loadOrder()
    {
        if ($this->orderNumber) {
            $this->order = Order::with(['orderItems.menuItem'])
                ->where('order_number', $this->orderNumber)
                ->first();

            if (!$this->order) {
                session()->flash('error', 'Order not found!');
            }
        }
    }

    public function searchOrder()
    {
        $this->validate([
            'orderNumber' => 'required|string'
        ]);

        $this->loadOrder();
    }

    public function orderStatusUpdated($event)
    {
        if ($this->order && $this->order->id == $event['order_id']) {
            $this->order->refresh();
            
            // Show notification
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Order status updated to: ' . $event['status']
            ]);
        }
    }

    public function toggleDetails()
    {
        $this->showDetails = !$this->showDetails;
    }

    public function getStatusColorProperty()
    {
        if (!$this->order) return 'gray';

        return match($this->order->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'preparing' => 'orange',
            'ready' => 'green',
            'completed' => 'emerald',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getStatusStepsProperty()
    {
        $steps = [
            'pending' => 'Order Placed',
            'confirmed' => 'Order Confirmed',
            'preparing' => 'Being Prepared',
            'ready' => 'Ready for Pickup',
            'completed' => 'Completed',
        ];

        if (!$this->order) return $steps;

        $currentStep = $this->order->status;
        $stepKeys = array_keys($steps);
        $currentIndex = array_search($currentStep, $stepKeys);

        $result = [];
        foreach ($steps as $key => $label) {
            $index = array_search($key, $stepKeys);
            $result[$key] = [
                'label' => $label,
                'completed' => $index <= $currentIndex,
                'current' => $key === $currentStep,
            ];
        }

        return $result;
    }

    public function getEstimatedTimeRemainingProperty()
    {
        if (!$this->order || !$this->order->estimated_completion_time) {
            return null;
        }

        $remaining = $this->order->estimated_completion_time->diffInMinutes(now());
        
        if ($remaining <= 0) {
            return 'Ready!';
        }

        return $remaining . ' minutes';
    }

    public function render()
    {
        return view('livewire.order-tracking');
    }
}
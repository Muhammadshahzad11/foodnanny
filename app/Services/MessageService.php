<?php

namespace App\Services;


use Exception;
use App\Enums\Ask;
use App\Models\Order;
use App\Models\Message;
use App\Enums\OrderType;
use App\Enums\OrderStatus;
use App\Events\NewChatMessage;
use App\Enums\MessageChannelType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MessageRequest;
use App\Libraries\QueryExceptionLibrary;

class MessageService
{


    /**
     * @throws Exception
     */
    public function list(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return Order::with('user')
                ->where(['delivery_boy_id' => Auth::user()->id, 'order_type' => OrderType::DELIVERY])
                ->where('status', '!=', OrderStatus::DELIVERED)
                ->where('status', '!=', OrderStatus::CANCELED)
                ->where('status', '!=', OrderStatus::REJECTED)
                ->where('status', '!=', OrderStatus::RETURNED)
                ->orderBy('id')->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(MessageRequest $request)
    {
        try {
            $message = Message::create([
                'order_id'     => $request->order_id,
                'user_id'      => auth()->id(),
                'text'         => $request->text,
                'is_read'      => Ask::NO,
                'channel_type' => MessageChannelType::DELIVERY
            ]);

            if (!empty(env('PUSHER_APP_ID')) && !empty(env('PUSHER_APP_KEY')) && !empty(env('PUSHER_APP_SECRET')) && !empty(env('PUSHER_APP_CLUSTER'))) {
                broadcast(new NewChatMessage($message));
            }

            return $message;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function show(Order $order, $channel = MessageChannelType::DELIVERY)
    {
        try {
            $unreadMessage = DB::table('messages')->where('user_id', '!=', auth()->id())->where(['order_id' => $order->id, 'channel_type' => $channel, 'is_read' => Ask::NO]);
            if ($unreadMessage->exists()) {
                $unreadMessage->update(['is_read' => Ask::YES]);
            }

            return Message::where(['order_id' => $order->id, 'channel_type' => $channel])->orderBy('id', 'asc')->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}

<?php

namespace App\Services;

use Exception;
use App\Mail\Subscriber;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\SubscriberRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\SubscriberEmailRequest;
use App\Models\Subscriber as ModelSubscriber;

class SubscriberService
{
    protected array $subscriberFilter = [
        'email'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return ModelSubscriber::where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = Date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = Date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('created_at', '>=', $first_date)->whereDate('created_at','<=',$last_date);
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->subscriberFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(ModelSubscriber $subscriber): void
    {
        try {
            $subscriber->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(SubscriberRequest $request): ModelSubscriber
    {
        try {
            $subscriber        = new ModelSubscriber;
            $subscriber->email = $request->email;
            $subscriber->save();
            return $subscriber;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function sendEmail(SubscriberEmailRequest $request): void
    {
        try {
            $subscribers = ModelSubscriber::select('email')->get();
            Mail::bcc($subscribers)->send(new Subscriber($request->subject, $request->message));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}

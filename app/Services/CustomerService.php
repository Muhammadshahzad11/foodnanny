<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Enums\Status;
use App\Models\User;
use App\Models\Address;
use App\Enums\Role as EnumRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\UserChangePasswordRequest;


class CustomerService
{
    public object $user;
    public array $phoneFilter = ['phone'];
    public array $roleFilter = ['role_id'];
    public array $userFilter = ['name', 'email', 'username', 'restaurant_id', 'status', 'phone'];
    public array $blockRoles = [EnumRole::ADMIN];


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

            return User::with('media', 'addresses')->role(EnumRole::CUSTOMER)->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->userFilter)) { 
                        if ($key == 'phone'){
                            $query->whereRaw("CONCAT(country_code, phone) LIKE ?", ["%{$request}%"]);
                        }else{
                            $query->where($key, 'like', '%' . $request . '%');
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
    public function store(CustomerRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->user = User::create([
                    'name'                 => $request->name,
                    'email'                => $request->email,
                    'phone'                => $request->phone,
                    'username'             => $this->username($request->email),
                    'password'             => bcrypt($request->password),
                    'restaurant_id'        => 0,
                    'email_verified_at'    => now(),
                    'status'               => $request->status,
                    'country_code'         => $request->country_code,
                    'is_guest'             => Ask::NO,
                    'terms_and_conditions' => Ask::YES
                ]);
                $this->user->assignRole(EnumRole::CUSTOMER);
            });
            return $this->user;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(CustomerRequest $request, User $customer)
    {
        try {
            if (!in_array(EnumRole::CUSTOMER, $this->blockRoles)) {
                DB::transaction(function () use ($customer, $request) {
                    $this->user               = $customer;
                    $this->user->name         = $request->name;
                    $this->user->email        = $request->email;
                    $this->user->phone        = $request->phone;
                    $this->user->status       = $request->status;
                    $this->user->country_code = $request->country_code;
                    if ($request->password) {
                        $this->user->password = Hash::make($request->password);
                    }
                    $this->user->save();
                });
                return $this->user;
            } else {
                throw new Exception(trans('all.message.permission_denied'), 422);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(User $customer): User
    {
        try {
            if (!in_array(EnumRole::CUSTOMER, $this->blockRoles)) {
                return $customer;
            } else {
                throw new Exception(trans('all.message.permission_denied'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(User $customer)
    {
        try {
            if (!in_array(EnumRole::CUSTOMER, $this->blockRoles) && $customer->id != 2) {
                if ($customer->hasRole(EnumRole::CUSTOMER)) {
                    DB::transaction(function () use ($customer) {
                        $customer->addresses()->delete();
                        $customer->delete();
                    });
                } else {
                    throw new Exception(trans('all.message.permission_denied'), 422);
                }
            } else {
                throw new Exception(trans('all.message.permission_denied'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * Lightweight customer create/update for POS Delivery (name/phone/address only).
     *
     * @throws Exception
     */
    public function storeFromPos(array $data): User
    {
        $name        = trim((string) ($data['name'] ?? ''));
        $phone       = trim((string) ($data['phone'] ?? ''));
        $addressText = trim((string) ($data['address'] ?? ''));
        $countryCode = trim((string) ($data['country_code'] ?? '+91')) ?: '+91';

        if ($name === '') {
            throw new Exception('Customer name is required.', 422);
        }

        try {
            return DB::transaction(function () use ($name, $phone, $addressText, $countryCode) {
                $user = null;
                if ($phone !== '') {
                    $user = User::role(EnumRole::CUSTOMER)->where('phone', $phone)->first();
                }

                if ($user) {
                    $user->name = $name;
                    if ($countryCode !== '') {
                        $user->country_code = $countryCode;
                    }
                    $user->save();
                } else {
                    $slug = $phone !== '' ? preg_replace('/\D+/', '', $phone) : '';
                    if ($slug === '') {
                        $slug = (string) Str::lower(Str::random(8));
                    }
                    $email = 'pos.' . $slug . '.' . time() . '@foodnanny.local';
                    while (User::withTrashed()->where('email', $email)->exists()) {
                        $email = 'pos.' . $slug . '.' . uniqid() . '@foodnanny.local';
                    }

                    $user = User::create([
                        'name'                 => $name,
                        'email'                => $email,
                        'phone'                => $phone !== '' ? $phone : null,
                        'username'             => $this->username($email),
                        'password'             => bcrypt(Str::random(16)),
                        'restaurant_id'        => 0,
                        'email_verified_at'    => now(),
                        'status'               => Status::ACTIVE,
                        'country_code'         => $countryCode,
                        'is_guest'             => Ask::NO,
                        'terms_and_conditions' => Ask::YES,
                    ]);
                    $user->assignRole(EnumRole::CUSTOMER);
                }

                if ($addressText !== '') {
                    $existing = $user->addresses()->first();
                    if ($existing) {
                        $existing->update(['address' => $addressText]);
                    } else {
                        Address::create([
                            'user_id'   => $user->id,
                            'label'     => 'Home',
                            'address'   => $addressText,
                            'apartment' => null,
                            'latitude'  => '0',
                            'longitude' => '0',
                        ]);
                    }
                }

                return $user->load('addresses');
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    private function username($email): string
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }

    /**
     * @throws Exception
     */
    public function changePassword(UserChangePasswordRequest $request, User $customer): User
    {
        try {
            if (!in_array(EnumRole::CUSTOMER, $this->blockRoles)) {
                $customer->password = Hash::make($request->password);
                $customer->save();
                return $customer;
            } else {
                throw new Exception(trans('all.message.permission_denied'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeImage(ChangeImageRequest $request, User $customer): User
    {
        try {
            if (!in_array(EnumRole::CUSTOMER, $this->blockRoles)) {
                if ($request->image) {
                    $customer->clearMediaCollection('profile');
                    $customer->addMediaFromRequest('image')->toMediaCollection('profile');
                }
                return $customer;
            } else {
                throw new Exception(trans('all.message.permission_denied'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function allCustomer(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return User::role(EnumRole::CUSTOMER)->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->userFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}

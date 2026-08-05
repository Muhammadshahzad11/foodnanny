<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Models\User;
use App\Models\Restaurant;
use App\Enums\Role as EnumRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\RestaurantOwnerRequest;
use App\Http\Requests\UserChangePasswordRequest;

class RestaurantOwnerService
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

            return User::with('media', 'addresses')->role(EnumRole::RESTAURANT_OWNER)->where(function ($query) use ($requests) {
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
    public function store(RestaurantOwnerRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->user = User::create([
                    'name'                 => $request->name,
                    'email'                => $request->email,
                    'phone'                => $request->phone,
                    'username'             => $this->username($request->email),
                    'password'             => bcrypt($request->password),
                    'restaurant_id'        => $request->restaurant_id,
                    'email_verified_at'    => now(),
                    'status'               => $request->status,
                    'country_code'         => $request->country_code,
                    'is_guest'             => Ask::NO,
                    'terms_and_conditions' => Ask::YES
                ]);
                $this->user->assignRole(EnumRole::RESTAURANT_OWNER);
                $restaurant = Restaurant::findOrFail($request->restaurant_id);
                $restaurant->user_id = $this->user->id;
                $restaurant->save();
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
    public function update(RestaurantOwnerRequest $request, User $restaurantOwner)
    {
        try {
            if (!in_array(EnumRole::RESTAURANT_OWNER, $this->blockRoles)) {
                DB::transaction(function () use ($restaurantOwner, $request) {
                    $this->user               = $restaurantOwner;
                    $this->user->name         = $request->name;
                    $this->user->email        = $request->email;
                    $this->user->phone        = $request->phone;
                    $this->user->status       = $request->status;
                    $this->user->country_code = $request->country_code;
                    if ($request->password) {
                        $this->user->password = Hash::make($request->password);
                    }
                    if ($request->restaurant_id) {
                        $this->user->restaurant_id = $request->restaurant_id;
                        $restaurant = Restaurant::findOrFail($request->restaurant_id);
                        $restaurant->user_id = $this->user->id;
                        $restaurant->save();
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
    public function show(User $restaurantOwner): User
    {
        try {
            if (!in_array(EnumRole::RESTAURANT_OWNER, $this->blockRoles)) {
                return $restaurantOwner;
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
    public function destroy(User $restaurantOwner)
    {
        try {
            if (!in_array(EnumRole::RESTAURANT_OWNER, $this->blockRoles) && $restaurantOwner->id != 2) {
                if ($restaurantOwner->hasRole(EnumRole::RESTAURANT_OWNER)) {
                    DB::transaction(function () use ($restaurantOwner) {
                        $restaurantOwner->addresses()->delete();
                        $restaurantOwner->delete();
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

    private function username($email): string
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }

    /**
     * @throws Exception
     */
    public function changePassword(UserChangePasswordRequest $request, User $restaurantOwner): User
    {
        try {
            if (!in_array(EnumRole::RESTAURANT_OWNER, $this->blockRoles)) {
                $restaurantOwner->password = Hash::make($request->password);
                $restaurantOwner->save();
                return $restaurantOwner;
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
    public function changeImage(ChangeImageRequest $request, User $restaurantOwner): User
    {
        try {
            if (!in_array(EnumRole::RESTAURANT_OWNER, $this->blockRoles)) {
                if ($request->image) {
                    $restaurantOwner->clearMediaCollection('profile');
                    $restaurantOwner->addMediaFromRequest('image')->toMediaCollection('profile');
                }
                return $restaurantOwner;
            } else {
                throw new Exception(trans('all.message.permission_denied'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}

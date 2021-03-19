<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Payment\Order;
use Carbon\Carbon;
use App\Helpers\Enum\MonthList;
use Illuminate\Support\Facades\Auth;
use App\Models\Proxy\Proxy;
use App\Models\Proxy\ProxyNote;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'first_name', 'last_name', 'country', 'zip', 'city',
        'state', 'address', 'phone', 'avatar', 'ip', 'ip_country', 'abusing_score', 'abusing_reason',
        'notes'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check is user admin
     * 
     * @return bool
     */
    public function isAdmin()
    {
        $isAdmin = false;

        foreach ($this->roles as $role) {
            $isAdmin = $role->name == 'admin' ? true : false;
            if ($isAdmin)
                break;
        }

        return $isAdmin;
    }

    public function timelines() 
    {
        return $this->belongsToMany('App\Models\Info\Timeline');
    }

    public function tickets() 
    {
        return $this->hasMany('App\Models\Contact\Ticket', 'creator_id');
    }

    public function payPalSubscriptions()
    {
        return $this->belongsToMany('App\Models\Payment\PayPalSubscription');
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Payment\Invoice', 'creator_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Group user registrations by month for current year
     */
    public static function getRegsGroupByMonth() 
    {
        $usersGroupByMonth = self::select('id', 'created_at')
            ->whereRaw('year(`created_at`) = ?', array(date('Y')))
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $usersCount = [];
        $userArr = [];
        foreach ($usersGroupByMonth as $key => $value) {
            $usersCount[(int)$key] = count($value);
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($usersCount[$i])) {
                $userArr[$i]['count'] = $usersCount[$i];
            } else {
                $userArr[$i]['count'] = 0;
            }
            $userArr[$i]['month'] = MonthList::$months[$i - 1];
        }

        return $userArr;
    }

    /**
     * Get users trial proxies
     */
    public function testProxies() 
    {
        return Proxy::where('is_trial', true)->where('user_id', Auth::id())->get();
    }

    /**
     * Check is user has filled bussiness info 
     * 
     * @return array
     */
    public function validateBussinessInfo()
    {
        $errors = [];
        if (empty($this->first_name))
            $errors['first_name'] = 'To receive invoices, fill your first name field';

        if (empty($this->last_name)) 
            $errors['last_name'] = 'To receive invoices, fill your last name field';

        if (empty($this->country))
            $errors['country'] = 'To receive invoices, fill your country field';

        if (empty($this->zip))
            $errors['zip'] = 'To receive invoices, fill your zip-code field';

        if (empty($this->city))
            $errors['city'] = 'To receive invoices, fill your city field';

        if (empty($this->state))
            $errors['state'] = 'To receive invoices, fill your state field';

        if (empty($this->address))
            $errors['address'] = 'To receive invoices, fill your address field';

        if (empty($this->phone))
            $errors['phone'] = 'To receive invoices, fill your phone field';

        return $errors;
    }

    /**
     * Add admin rights to user
     * 
     * @return void
     */
    public function addAdminRights()
    {
        $this->roles()->save(Role::adminRole());
    }

    /**
     * Find user and create string from bussiness info for file
     * 
     * @param int  $userId
     * @return string
     */
    public static function findUserBussinessInfoToString($userId)
    {
        $user = self::find($userId);

        return $user->userBussinessInfoToString();
    }

    /**
     * Create string from bussiness info for file
     * 
     * @return string
     */
    public function userBussinessInfoToString()
    {
        $content = $this->name . ':';
        $content .= $this->email . ':';
        $content .= $this->password . ':';
        $content .= $this->first_name ?? 'N/A' . ':';
        $content .= $this->last_name ?? 'N/A' . ':';
        $content .= $this->country ?? 'N/A' . ':';
        $content .= $this->city ?? 'N/A' . ':';
        $content .= $this->state ?? 'N/A' . ':';
        $content .= $this->address ?? 'N/A' . ':';
        $content .= $this->zip ?? 'N/A' . ':';
        $content .= $this->phone ?? 'N/A';
        $content .= "\r\n";

        return $content;
    }

    /**
     * Add trial proxy to user for selected duration
     * 
     * @param int  $proxyId
     * @param int  $durationValue
     * @param string $durationType
     * @return void
     */
    public function addTrialProxy($proxyId, $durationValue, $durationType)
    {
        $trialEndsAt = strtotime("+" . $durationValue . " " . $durationType);
        Proxy::find($proxyId)->update([
            'is_trial'  => true,
            'user_id'   => $this->id,
            'trial_ends_at' => date('Y-m-d H:i:s', $trialEndsAt)
        ]);
    }

    /**
     * Remove trial proxy from user 
     * 
     * @param \App\Models\Proxy\Proxy  $proxy
     * @return void
     */
    public function removeTrialProxy(Proxy $proxy)
    {
        $proxy->update([
            'is_trial'              => false,
            'trial_ends_at'         => null,
            'user_id'               => null,
            'is_action_required'    => true
        ]);
    }

    /**
     * Collect all subscribed proxies to one collection
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collectAllProxies()
    {
        $subscriptions = Auth::user()->payPalSubscriptions;
        $proxies = collect();
        foreach ($subscriptions as $subscription) {
            if (!$subscription->isExpired()) {
                $proxies = $proxies->merge($subscription->proxies);
            }
        }

        return $proxies;
    }

    /**
     * Add user notes to proxy
     * 
     * @param \App\Models\Proxy\Proxy  $proxy
     * @param string  $notes
     * @return void
     */
    public function addNotes(Proxy $proxy, $notes)
    {
        if (!empty($proxy->notes->where('user_id', $this->id)->first())) {
            $note = $proxy->notes->where('user_id', $this->id)->first();
            $note->note = $notes;
            $note->save();
        } 
        else {
            ProxyNote::create([
                'note' => $notes,
                'proxy_id' => $proxy->id,
                'user_id' => $this->id,
            ]);
        }
    }
}

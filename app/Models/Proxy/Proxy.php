<?php

namespace App\Models\Proxy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\Subscription;
use App\Models\ProxyNote;
use App\Models\Setting;
use App\Models\User;

class Proxy extends Model
{
    use HasFactory;

    const TYPE_LIMITED      = 'limited';
    const TYPE_UNLIMITED    = 'unlimited';

    protected $fillable = [
        'ip_port', 'login', 'password', 'type', 'rotation_time', 'subscription_id', 'user_id', 
        'check_status', 'latency', 'is_action_required', 'http_port', 'socks_port', 'real_ip',
        'is_trial', 'trial_ends_at'
    ];

    /**
     * Check is proxy availible for selling
     * 
     * @return bool
     */
    public function isAvailible() 
    {
        return empty($this->subscription_id) 
            && !$this->is_action_required 
            && empty($this->user_id);
    }

    /**
     * Check is proxy availible by type for selling
     * 
     * @param string  $proxyType
     * @return bool
     */
    public function isAvailibleByType($proxyType)
    {
        return empty($this->subscription_id) 
            && empty($this->user_id)
            && !$this->is_action_required 
            && $proxyType == $this->type;
    }

    /**
     * Check is proxy availible by type and valid for selling
     * 
     * @param string  $proxyType
     * @return bool
     */
    public function isAvailibleValidByType($proxyType)
    {
        return empty($this->subscription_id) 
            && $this->is_action_required == false 
            && empty($this->user_id)
            && $proxyType === $this->type;
    }

    /**
     * Check is proxy sold
     * 
     * @return bool
     */
    public function isSold() 
    {
        return !empty($this->subscription_id) && !empty($this->user_id);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }  

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notes() 
    {
        return $this->hasMany(ProxyNote::class);
    }

    /**
     * Check is trial proxy expired
     * 
     * @return bool
     */
    public function isExpired() 
    {
        return time() > strtotime($this->trial_ends_at);
    }

    /**
     * Validate proxy
     * 
     * @return void
     */
    public function check() 
    {
        $this->update([
            'check_status'  => 'Valid',
            'latency'       => 300 
        ]);
    }

    /**
     * Validate all proxies
     * 
     * @return void
     */
    public static function checkAll()
    {
        self::all()->each(function ($proxy) {
            $proxy->update([
                'check_status'  => 'Valid',
                'latency'       => 300 
            ]);
        });
    }

    /**
     * Import file and store proxies to DB
     * 
     * @param string  $content
     * @return bool
     */
    public static function importFile($content)
    {
        $contentLines = explode("\r\n", $content);

        foreach ($contentLines as $line) {
            $lineExploded = explode(':', $line);

            self::create([
                'ip_port'   => $lineExploded[0] . ':' . $lineExploded[1],
                'login'     => $lineExploded[2],
                'password'  => $lineExploded[3],
                'type'      => $lineExploded[4],
                'rotation_time' => 0,
                'latency'       => 0,
                'check_status'  => 'Not valid'
            ]);
        }
    }

    /**
     * Find object and create string from creditenials. Format: ip:port:login:password
     * 
     * @param int  $id
     * @return string
     */
    public static function findAndCreditenialsToString($id)
    {
        $proxy = self::find($id);

        return $proxy->creditenialsToString();
    }

    /**
     * Create string from creditenials. Format: ip:port:login:password
     * 
     * @param bool  $returnSocks5
     * @return string
     */
    public function creditenialsToString($returnSocks5 = false) 
    {
        if ($returnSocks5) {
            $ip = explode(':', $this->ip_port)[1];
            $content = $ip . ':';
            $content .= $this->socks_port . ':';
        } else {
            $content = $this->ip_port . ':';
        }
        $content .= $this->login . ':';
        $content .= $this->password . ':';
        $content .= $this->type . "\r\n";

        return $content;
    }

    /**
     * Compact proxy creditenials to show for user
     * 
     * @return string
     */
    public function creditenialsToShow()
    {
        $content = "http(s)\r\n";
        $content .= $this->creditenialsToString();

        if (!empty($this->socks_port)) {
            $content .= "socks\r\n";
            $content .= $this->creditenialsToString(true);
        }

        return $content;
    }

    /**
     * Format proxies creditenials according to template
     * 
     * @param array  $proxyIds
     * @param bool  $isDefault
     * @param int  $exportType
     * @param string  $template
     * @return string
     */
    public static function formatProxiesCreditenialsToExport($proxyIds, $isDefault, $exportType, $template)
    {
        $content = '';
        $proxies = [];
        foreach ($proxyIds as $proxyId) {
            $proxy = self::find($proxyId);

            if (empty($proxy->subscription->user[0]->id) || $proxy->subscription->user[0]->id != Auth::id())
                continue;
            else
                $proxies[] = $proxy; 
        }

        if ($isDefault) {
            if ($exportType === "0" || $exportType === "1") {
                $content .= "http(s)\r\n";
                
                foreach ($proxies as $proxy) {
                    $content .= $proxy->creditenialsToString();
                }
            }

            if ($exportType === "0" || $exportType === '2') {
                $content .= "socks\r\n";

                foreach ($proxies as $proxy) {
                    $content .= $proxy->creditenialsToString(true);
                }
            }
        } 
        else {
            $pattern = '/(\W)/';
            $parameters = preg_split($pattern, $template);

            $pattern = '/(\w)+/';
            $delimeters = preg_split($pattern, $template);

            if ($exportType === "0" || $exportType === "1") {
                $content .= "http(s)\r\n";

                foreach ($proxies as $proxy) { 
                    $ip = explode(':', $proxy->ip_port)[0];
                    $port = explode(':', $proxy->ip_port)[1];

                    foreach ($parameters as $i => $parameter) {
                        if ($parameter == 'host') {
                            $content .= $ip;
                        }
                        elseif ($parameter == 'port')  {
                            $content .= $port;
                        }
                        elseif ($parameter == 'user') {
                            $content .= $proxy->login;
                        }
                        elseif ($parameter == 'pass') {
                            $content .= $proxy->password;
                        } 
                        else {
                            $content .= '';
                        }

                        $content .= $delimeters[$i + 1];
                    }

                    $content .= "\r\n";
                }
            }
            if ($exportType === "0" || $exportType === "2") {
                $content .= "socks\r\n";

                foreach ($proxies as $proxy) { 
                    $ip = explode(':', $proxy->ip_port)[0];

                    if (!empty($proxy->socks_port)) {
                        foreach ($parameters as $i => $parameter) {
                            if ($parameter == 'host') {
                                $content .= $ip;
                            }
                            elseif ($parameter == 'port')  {
                                $content .= $proxy->socks_port;
                            }
                            elseif ($parameter == 'user') {
                                $content .= $proxy->login;
                            }
                            elseif ($parameter == 'pass') {
                                $content .= $proxy->password;
                            } 
                            else {
                                $content .= '';
                            }
    
                            $content .= $delimeters[$i + 1];
                        }
    
                        $content .= "\r\n";
                    }
                }
            }
        }
    }
}

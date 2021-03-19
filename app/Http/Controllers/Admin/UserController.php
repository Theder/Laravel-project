<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment\Order;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Models\Proxy;
use App\Models\Contact\Ticket;
use App\Models\Contact\Message;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Common\AdminUserStore;
use App\Http\Requests\Common\AdminUserExport;
use App\Http\Requests\Common\AdminUserBulkDestroy;
use App\Http\Requests\Common\AdminUserNotesSave;
use App\Http\Requests\Common\AdminUserProfileUpdate;
use App\Http\Requests\Common\AdminUserBussinessUpdate;
use App\Http\Requests\Common\AdminUserReasignProxies;
use App\Http\Requests\Common\AdminUserAddProxy;
use App\Http\Requests\Common\AdminUserCreateTicket;
use App\Http\Requests\Common\AdminUserAddTestProxy;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $usersGroupByMonth = User::getRegsGroupByMonth();
        $ordersGroupedByMonth = Order::getOrdersGroupByMonth();

        return view('admin.user.index', compact('users', 'usersGroupByMonth', 'ordersGroupedByMonth'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Common\AdminUserStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserStore $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'              => $data['name'],
            'password'          => Hash::make($data['password']),
            'email'             => $data['email'],
            'first_name'        => $data['first_name'],
            'last_name'         => $data['last_name'],
            'phone'             => $data['phone'],
            'country'           => $data['country'],
            'state'             => $data['state'],
            'zip'               => $data['zip'],
            'city'              => $data['city'],
            'address'           => $data['address']
        ]);

        if (isset($data['is_admin']) && !empty($data['is_admin'])) {
            $user->addAdminRights();
        }

        return redirect()->route('users.index')->with(['status' => 'New user successfully created']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $proxies = Proxy::all();
        $tickets = Ticket::where('creator_id', $user->id)->orderBy('updated_at', 'DESC')->get();

        return view('admin.user.edit', compact('user', 'proxies', 'tickets'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with(['status' => "User #{$user->id} successfully delete"]);
    }


    /**
     * Export selected users to txt file
     * 
     * @param \App\Http\Requests\Common\AdminUserExport  $request
     * @return \Illuminate\Http\Response
     */
    public function export(AdminUserExport $request) 
    {
        $data = $request->validated();

        $user_ids = explode(',', $data['user_ids']);

        $content = '';
        $content .= "NAME:EMAIL:PASSWORD_HASHED:FIRST_NAME:LAST_NAME:COUNTRY:CITY:STATE:ADDRESS:ZIP:PHONE\r\n";
        foreach ($user_ids as $userId) {
            $content .= User::findUserBussinessInfoToString($userId);
        }

        return response($content)->withHeaders([
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="export.txt',
        ]);
    }

    /**
     * Export all users to txt file
     * 
     * @return \Illuminate\Http\Response
     */
    public function exportAll()
    {
        $users = User::all();

        $content = '';
        $content .= "NAME:EMAIL:PASSWORD_HASHED:FIRST_NAME:LAST_NAME:COUNTRY:CITY:STATE:ADDRESS:ZIP:PHONE\r\n";

        User::all()->each(function ($user) use ($content) {
            $content .= $user->userBussinessInfoToString();
        });

        return response($content)->withHeaders([
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="export.txt',
        ]);
    }


    /**
     * Delete selected users
     * 
     * @param  \App\Http\Requests\Common\AdminUserBulkDestroy  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(AdminUserBulkDestroy $request)
    {
        $data = $request->validated();

        $user_ids = explode(',', $data['user_ids']);

        foreach ($user_ids as $userId) {
            $user = User::find($userId);

            if (!$user->isAdmin())
                $user->delete();
        }

        return redirect()->route('users.index')->with(['status' => 'Selected users successfully deleted']);
    }

    /**
     * Save note about user
     * 
     * @param \App\Http\Requests\Common\AdminUserNotesSave  $request
     * @param \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function notesSave(AdminUserNotesSave $request, User $user)
    {
        $data = $request->validated();
        $user->notes = $data['notes'];
        $user->save();

        return redirect()->route('users.edit', ['user' => $user->id])
            ->with(['status' => 'Notes saved']);
    }

    /**
     * Update user's profile settings
     * 
     * @param \App\Http\Requests\Common\AdminProfileUpdate  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function profileUpdate(AdminUserProfileUpdate $request, User $user)
    {
        $data = $request->validated();

        if($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $photo = Image::make($request->file('avatar'))
                ->resize(400, null, function ($constraint) { $constraint->aspectRatio(); } )
                ->encode('jpg',80);

            $filename = time() . '.' . $avatar->getClientOriginalExtension();                

            Storage::disk('public')->put($filename, $photo);

    		$user->avatar = $filename;
    		$user->save();
        }

        if (!empty($data['password'])) {
            $user->fill([
                'password'  => Hash::make($data['password'])    
            ]);    
        }

        $user->fill([
            'name' => $data['name']
        ]);

        $user->save();

        return redirect()->back()->with(['status' => 'Account settings successfuly saved.']);
    }

    /**
     * Update user's bussiness settings
     * 
     * @param \App\Http\Requests\Common\AdminBussinessUpdate  $request
     * @param \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function bussinessUpdate(AdminUserBussinessUpdate $request, User $user)
    {
        $data = $request->validated();

        $user->fill([
            'email'         => $data['email'],
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'phone'         => $data['phone'],
            'country'       => $data['country'],
            'state'         => $data['state'],
            'zip'           => $data['zip'],
            'city'          => $data['city'],
            'address'       => $data['address']
        ]);

        $user->save();

        return redirect()->back()->with(['status' => 'Bussiness settings successfully saved.']);
    }

    /**
     * Remove related proxy from the specific order
     * 
     * @param  \App\Models\Payment\Order  $order
     * @param  \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Response
     */
    public function removeProxy(Order $order, Proxy $proxy)
    {
        $order->subscription->removeProxy($proxy);
        $order->subscription->updateProxiesHistory();

        return redirect()->back()->with(['status' => 'Proxy successfully removed from order']);
    }

    /**
     * Reasign related proxy to another in the specific order
     * 
     * @param  \App\Http\Requests\Common\AdminReasignProxies  $request
     * @param \App\Models\Payment\Order  $order
     * @param  \App\Models\Proxy\Proxy  $proxy
     */
    public function reasignProxies(AdminUserReasignProxies $request, Order $order, Proxy $proxy)
    {
        $data = $request->validated();

        $order->subscription->removeProxy($proxy);
        $order->subscription->addProxy($data['new_proxy_id']);
        $order->subscription->updateProxiesHistory();

        return redirect()->back()->with(['status' => 'Proxy successfully reasigned to order.']);
    }

    /**
     * Add new proxy to specific order
     * 
     * @param \App\Http\Requests\Common\AdminUserAddProxy  $request
     * @param \App\Models\Payment\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function addProxy(AdminUserAddProxy $request, Order $order)
    {
        $data = $request->validated();

        $proxyIds = explode(',', $data['proxy_ids']);
        foreach ($proxyIds as $proxyId) {
            $order->subscription->addProxy($proxyId);
        }

        $order->subscription->updateProxiesHistory();

        return redirect()->back()->with(['status' => 'Proxy successfully added to order.']);
    }

    /**
     * Create ticket in user area
     * 
     * @param \App\Http\Requests\Common\AdminUserCreateTicket  $request
     * @param \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function createTicket(AdminUserCreateTicket $request, User $user)
    {
        $data = $request->validated();

        $ticket = Ticket::create([
            'title'         => $data['title'],
            'status'        => 'answered',
            'creator_id'    => $user->id,
        ]);

        Message::create([
            'message'               => $data['message'],
            'is_unread_by_user'     => true,
            'is_unread_by_admin'    => false,
            'creator_id'            => Auth::id(),
            'ticket_id'             => $ticket->id,
        ]);


        return redirect()->back()
            ->with(['status' => 'New Ticket Successfully created']);
    }

    /**
     * Add trial proxy to user
     * 
     * @param \App\Http\Requests\Common\AdminUserAddTestProxy  $request
     * @param \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function addTestProxy(AdminUserAddTestProxy $request, User $user)
    {   
        $data = $request->validated();

        $proxyIds = explode(',', $data['proxy_ids']);
        foreach ($proxyIds as $proxyId) {
            $user->addTrialProxy($proxyId, $data['duration_value'], $data['duration_type']);
        }

        return redirect()->back()
            ->with(['status' => 'Test proxies assigned']);
    }
    
    /**
     * Remove trial proxy from user
     * 
     * @param \App\Models\User  $user
     * @param \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Response
     */
    public function removeTestProxy(User $user, Proxy $proxy) 
    {
        $user->removeTrialProxy($proxy);

        return redirect()->back()
            ->with(['status' => 'Test proxy removed']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * The address service instance.
     *
     * @var \App\Services\AddressService
     */
    protected $addressService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\AddressService $addressService
     * @return void
     */
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $addresses = Auth::user()->addresses;
        return view('profile.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $data = $this->addressService->getAddressFormData();
        return view('profile.addresses.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param \App\Http\Requests\StoreAddressRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAddressRequest $request)
    {
        $this->addressService->createAddress($request->validated());
        
        return redirect()->route('addresses.index')
            ->with('toast', 'Address added successfully.');
    }

    /**
     * Display the specified resource.
     * 
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $data = $this->addressService->getAddressEditData($id);
        return view('profile.addresses.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param \App\Http\Requests\UpdateAddressRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAddressRequest $request, string $id)
    {
        $this->addressService->updateAddress($id, $request->validated());
        
        return redirect()->route('addresses.index')
            ->with('toast', 'Address updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $this->addressService->deleteAddress($id);
        
        return redirect()->route('addresses.index')
            ->with('toast', 'Address deleted successfully.');
    }
    
    /**
     * Set an address as the default.
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setDefault(string $id)
    {
        $this->addressService->setDefaultAddress($id);
        
        return redirect()->route('addresses.index')
            ->with('toast', 'Default address updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCurrencyRequest;
use App\Http\Requests\Admin\SetCurrencyRequest;
use App\Models\Currency;
use App\Models\Option;

class CurrencyController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:admin_permission');
    }

    public function index()
    {
        $data['title'] = 'Currencies';
        $data['currencies'] = Currency::all();
        $data['currentCurrency'] = Option::get('app_currency', '$');
        return view('admin.currency.index', $data)->with('menu', 'currency');
    }

    public function store(StoreCurrencyRequest $request)
    {
        Currency::create([
            'name' => strtoupper($request->name),
            'symbol' => $request->symbol,
        ]);

        notyf()->success('Currency added successfully');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);

        // Check if this currency is currently selected
        $currentCurrency = Option::get('app_currency');
        if ($currentCurrency === $currency->symbol) {
            notyf()->error('Cannot delete the currently selected currency!');
            return redirect()->back();
        }

        $currency->delete();
        notyf()->success('Currency deleted successfully!');
        return redirect()->back();
    }

    public function setCurrency(SetCurrencyRequest $request)
    {
        Option::set('app_currency', $request->currency_symbol);

        notyf()->success('Currency updated successfully');

        return redirect()->back();
    }
}

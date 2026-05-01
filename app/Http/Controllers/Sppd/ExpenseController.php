<?php

namespace App\Http\Controllers\Sppd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sppd\StoreExpenseRequest;
use App\Models\Sppd\SppdExpense;
use App\Models\Sppd\SppdRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(StoreExpenseRequest $request, SppdRequest $sppd): RedirectResponse
    {
        $this->authorize('update', $sppd);
        $payload = $request->validated();
        $payload['mata_uang'] = $payload['mata_uang'] ?? 'IDR';
        $sppd->expenses()->create($payload);
        return redirect()->route('sppd.show', $sppd);
    }

    public function update(StoreExpenseRequest $request, SppdRequest $sppd, SppdExpense $expense): RedirectResponse
    {
        $this->authorize('update', $sppd);
        if ($expense->sppd_id !== $sppd->id) {
            abort(404);
        }
        $payload = $request->validated();
        $payload['mata_uang'] = $payload['mata_uang'] ?? 'IDR';
        $expense->update($payload);
        return redirect()->route('sppd.show', $sppd);
    }

    public function destroy(SppdRequest $sppd, SppdExpense $expense): RedirectResponse
    {
        $this->authorize('update', $sppd);
        if ($expense->sppd_id !== $sppd->id) {
            abort(404);
        }
        $expense->delete();
        return redirect()->route('sppd.show', $sppd);
    }
}


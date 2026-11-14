<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $plans = Plan::withCount('tenants')->get();
        return $this->successResponse($plans);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_clinics' => 'required|integer',
            'free_whatsapp_quota_monthly' => 'nullable|integer|min:0',
            'free_sms_quota_monthly' => 'nullable|integer|min:0',
            'allow_custom_domain' => 'boolean',
            'allow_custom_website' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $plan = Plan::create($data);
        return $this->successResponse($plan, 'تم إضافة الباقة بنجاح');
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $plan = Plan::findOrFail($id);

        $data = $request->validate([
            'name_ar' => 'sometimes|required|string|max:255',
            'price_monthly' => 'sometimes|required|numeric|min:0',
            'price_yearly' => 'sometimes|required|numeric|min:0',
            'max_clinics' => 'sometimes|required|integer',
            'free_whatsapp_quota_monthly' => 'nullable|integer|min:0',
            'free_sms_quota_monthly' => 'nullable|integer|min:0',
            'allow_custom_domain' => 'boolean',
            'allow_custom_website' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $plan->update($data);
        return $this->successResponse($plan, 'تم تحديث الباقة بنجاح');
    }

    public function destroy(int $id): JsonResponse
    {
        $plan = Plan::findOrFail($id);
        if ($plan->tenants()->count() > 0) {
            return $this->errorResponse('لا يمكن حذف باقة مرتبطة بحسابات أطباء نشطة');
        }
        $plan->delete();
        return $this->successResponse(null, 'تم حذف الباقة بنجاح');
    }
}

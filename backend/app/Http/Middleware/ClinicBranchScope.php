<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClinicBranchScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'receptionist') {
            $requestedClinicId = $request->input('clinic_id') ?? $request->query('clinic_id') ?? $request->route('clinicId');

            if ($requestedClinicId && (int)$requestedClinicId !== (int)$user->clinic_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بالوصول لبيانات هذا الفرع',
                    'data' => null,
                    'errors' => null,
                ], 403);
            }

            // Always enforce receptionist's assigned clinic_id
            $request->merge(['clinic_id' => $user->clinic_id]);

            $appointmentId = $request->route('appointmentId');
            if ($appointmentId) {
                $appt = \App\Models\Appointment::where('tenant_id', $user->tenant_id)->find($appointmentId);
                if ($appt && (int)$appt->clinic_id !== (int)$user->clinic_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'غير مصرح لك بتعديل مواعيد فرع آخر',
                        'data' => null,
                        'errors' => null,
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}

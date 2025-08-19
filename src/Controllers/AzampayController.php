<?php

namespace Taitech\Azampay\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Taitech\Azampay\Modules\CallbackModule;
use Taitech\Azampay\Services\ActivityLoggerService;
use Exception;

class AzampayController extends Controller
{
    /**
     * Handle AzamPay webhook callbacks
     */
    public function webhook(Request $request): JsonResponse
    {
        $activityLogger = new ActivityLoggerService();
        $userId = auth()->id(); // Get current user ID if authenticated
        
        try {
            $callback = app('azampay.callback');
            $callback->callback();
            
            // Log successful webhook processing
            $activityLogger->logWebhookEvent(
                $request->headers->all(),
                $request->getContent(),
                'success',
                $userId
            );
            
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            // Log webhook error
            $activityLogger->logError($e->getMessage(), [
                'exception' => $e->getMessage(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ], $userId);
            
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process payment checkout
     */
    public function checkout(Request $request): JsonResponse
    {
        $activityLogger = new ActivityLoggerService();
        $userId = auth()->id(); // Get current user ID if authenticated
        
        try {
            $payload = $request->validate([
                'amount' => 'required|numeric|min:1',
                'accountNumber' => 'required|string',
                'provider' => 'required|string',
                'additionalProperties' => 'sometimes|array'
            ]);

            $response = app('azampay')->checkout($payload);
            
            // Log successful payment checkout
            $activityLogger->logPaymentCheckout($payload, $response, $userId);
            
            return response()->json($response);
        } catch (Exception $e) {
            // Log payment checkout error
            $activityLogger->logError('Payment checkout error: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString()
            ], $userId);
            
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Check payment status
     */
    public function status(string $reference): JsonResponse
    {
        $activityLogger = new ActivityLoggerService();
        $userId = auth()->id(); // Get current user ID if authenticated
        
        try {
            // This is a placeholder - implement your status checking logic
            // You might want to call AzamPay's status API or check your database
            
            $status = 'pending'; // Placeholder status
            
            // Log status check
            $activityLogger->logStatusCheck($reference, $status, $userId);
            
            return response()->json([
                'reference' => $reference,
                'status' => $status,
                'message' => 'Status check endpoint - implement your logic here'
            ]);
        } catch (Exception $e) {
            // Log status check error
            $activityLogger->logError('Status check error: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'reference' => $reference,
                'trace' => $e->getTraceAsString()
            ], $userId);
            
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

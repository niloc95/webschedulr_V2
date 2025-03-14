<?php
namespace WebSchedulr\Services;

class PaymentService {
    private $merchantId;
    private $merchantKey;
    private $passphrase;
    private $testMode;
    private $returnUrl;
    private $cancelUrl;
    private $notifyUrl;
    
    public function __construct() {
        $this->merchantId = Config::PAYFAST_MERCHANT_ID ?? '';
        $this->merchantKey = Config::PAYFAST_MERCHANT_KEY ?? '';
        $this->passphrase = Config::PAYFAST_PASSPHRASE ?? '';
        $this->testMode = Config::PAYFAST_TEST_MODE ?? true;
        $this->returnUrl = Config::BASE_URL . '/appointments/payment-success';
        $this->cancelUrl = Config::BASE_URL . '/appointments/payment-cancel';
        $this->notifyUrl = Config::BASE_URL . '/appointments/payment-notify';
    }
    
    /**
     * Generate PayFast payment form data
     * 
     * @param array $appointmentData Appointment data
     * @return array Form data for payment
     */
    public function generatePaymentForm($appointmentData) {
        if (!(Config::PAYMENT_GATEWAY_ACTIVE ?? false)) {
            return null;
        }
        
        // Required fields
        $data = [
            // Merchant details
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey,
            
            // Transaction details
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'notify_url' => $this->notifyUrl,
            
            // Customer details
            'name_first' => $appointmentData['client_first_name'] ?? '',
            'name_last' => $appointmentData['client_last_name'] ?? '',
            'email_address' => $appointmentData['client_email'] ?? '',
            
            // Payment details
            'item_name' => $appointmentData['title'],
            'item_description' => substr($appointmentData['description'] ?? 'Appointment Booking', 0, 255),
            'm_payment_id' => $appointmentData['id'],
            'amount' => number_format($appointmentData['price'], 2, '.', ''),
        ];
        
        // Generate signature
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($key !== 'signature') {
                $pfOutput .= $key . '=' . urlencode($val) . '&';
            }
        }
        
        // Remove last '&'
        $pfOutput = substr($pfOutput, 0, -1);
        
        if (!empty($this->passphrase)) {
            $pfOutput .= '&passphrase=' . urlencode($this->passphrase);
        }
        
        $data['signature'] = md5($pfOutput);
        
        // Test mode
        if ($this->testMode) {
            $data['testing'] = 'true';
        }
        
        return [
            'data' => $data,
            'url' => $this->testMode ? 'https://sandbox.payfast.co.za/eng/process' : 'https://www.payfast.co.za/eng/process'
        ];
    }
}

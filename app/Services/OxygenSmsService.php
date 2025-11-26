<?php

namespace Oxygen\Services;

use Twilio\Rest\Client;

/**
 * OxygenSmsService - SMS Service
 * 
 * Professional SMS sending service using Twilio.
 * 
 * @package    Oxygen\Services
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenSmsService
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        $sid = $_ENV['TWILIO_SID'] ?? '';
        $token = $_ENV['TWILIO_TOKEN'] ?? '';
        $this->fromNumber = $_ENV['TWILIO_PHONE_NUMBER'] ?? '';

        $this->client = new Client($sid, $token);
    }

    /**
     * Send SMS
     * 
     * @param string $to Recipient phone number
     * @param string $message Message content
     * @return bool
     */
    public function send($to, $message)
    {
        try {
            $this->client->messages->create($to, [
                'from' => $this->fromNumber,
                'body' => $message
            ]);
            return true;
        } catch (\Exception $e) {
            error_log("SMS send failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send WhatsApp message
     * 
     * @param string $to Recipient phone number
     * @param string $message Message content
     * @return bool
     */
    public function sendWhatsApp($to, $message)
    {
        $whatsappNumber = $_ENV['TWILIO_WHATSAPP_NUMBER'] ?? '';

        try {
            $this->client->messages->create("whatsapp:$to", [
                'from' => "whatsapp:$whatsappNumber",
                'body' => $message
            ]);
            return true;
        } catch (\Exception $e) {
            error_log("WhatsApp send failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Make a voice call
     * 
     * @param string $to Recipient phone number
     * @param string $message Message to speak
     * @return bool
     */
    public function call($to, $message)
    {
        try {
            $this->client->calls->create($to, $this->fromNumber, [
                'twiml' => "<Response><Say>{$message}</Say></Response>"
            ]);
            return true;
        } catch (\Exception $e) {
            error_log("Call failed: " . $e->getMessage());
            return false;
        }
    }
}

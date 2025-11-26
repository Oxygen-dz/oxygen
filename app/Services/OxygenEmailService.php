<?php

namespace Oxygen\Services;

use SendGrid;
use SendGrid\Mail\Mail;

/**
 * OxygenEmailService - Email Service
 * 
 * Professional email sending service using SendGrid.
 * 
 * @package    Oxygen\Services
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenEmailService
{
    protected $sendgrid;
    protected $fromEmail;
    protected $fromName;

    public function __construct()
    {
        $apiKey = $_ENV['SENDGRID_API_KEY'] ?? '';
        $this->sendgrid = new SendGrid($apiKey);
        $this->fromEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@example.com';
        $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? 'OxygenFramework';
    }

    /**
     * Send an email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string|null $plainText Plain text version
     * @return bool
     */
    public function send($to, $subject, $body, $plainText = null)
    {
        $email = new Mail();
        $email->setFrom($this->fromEmail, $this->fromName);
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent("text/html", $body);

        if ($plainText) {
            $email->addContent("text/plain", $plainText);
        }

        try {
            $response = $this->sendgrid->send($email);
            return $response->statusCode() >= 200 && $response->statusCode() < 300;
        } catch (\Exception $e) {
            error_log("Email send failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email with template
     * 
     * @param string $to Recipient email
     * @param string $templateId SendGrid template ID
     * @param array $data Template data
     * @return bool
     */
    public function sendTemplate($to, $templateId, $data = [])
    {
        $email = new Mail();
        $email->setFrom($this->fromEmail, $this->fromName);
        $email->addTo($to);
        $email->setTemplateId($templateId);
        $email->addDynamicTemplateDatas($data);

        try {
            $response = $this->sendgrid->send($email);
            return $response->statusCode() >= 200 && $response->statusCode() < 300;
        } catch (\Exception $e) {
            error_log("Template email send failed: " . $e->getMessage());
            return false;
        }
    }
}

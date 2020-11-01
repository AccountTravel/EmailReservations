<?php

namespace AccountTravel\EmailReservations;

use ZBateson\MailMimeParser\Message;

class AbstractParser implements ParserInterface
{
    public bool $checkForwarded = true;
    public array $providerEmailAddresses = [];
    public array $providerEmailSubjects = [];

    protected Message $message;
    protected ?string $html;

    public function __construct(Message $message)
    {
//        if (!$this->providerEmailAddresses) {
//            throw new \Exception('Empty provider email addresses');
//        }

        $this->message = $message;
        $this->html = $message->getHtmlContent();

        return $this;
    }

    public function getResult(): array
    {
        return [];
    }

    public function isValid(): bool
    {
        // письмо должно быть от нужного адреса
        $fromValidation = in_array($this->message->getHeaderValue('from'), $this->providerEmailAddresses);
        if (!$fromValidation && $this->checkForwarded) {
            foreach ($this->providerEmailAddresses as $address) {
                if (strpos($this->html, $address) !== false) {
                    $fromValidation = true;
                    break;
                }
            }
        }

        // в теме должны содержаться указанные подстроки
        $subjectValidation = empty($this->providerEmailSubjects);
        if ($this->providerEmailSubjects) {
            $subject = $this->message->getHeaderValue('subject');
            foreach ($this->providerEmailSubjects as $substring) {
                if (strpos($subject, $substring) !== false) {
                    $subjectValidation = true;
                    break;
                }
            }
        }

        return $fromValidation && $subjectValidation;
    }

    public function isSuccess(): bool
    {
        return false;
    }
}
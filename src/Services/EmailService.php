<?php

namespace App\Services;

use Mailjet\Client;
use App\Entity\User;
use Mailjet\Resources;
use App\Entity\EmailModel;

class EmailService
{
    public function sendNotification(User $user, EmailModel $email)
    {
        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'],true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "zoundai@outlook.com",
                        'Name' => "KemShop contact"
                    ],
                    'To' => [
                    [
                        'Email' => $user->getEmail(),
                        'Name' => $user->getFullName()
                    ]
                    ],
                    'TemplateID' => 3470093,
                    'TemplateLanguage' => true,
                    'Subject' => $email->getSubject(),
                    'Variables' => [
                        "title" => $email->getTitle(),
                        "content" => $email->getContent()
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        // $response->success() && var_dump($response->getData());
    }
}
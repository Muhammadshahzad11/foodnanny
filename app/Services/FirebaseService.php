<?php

namespace App\Services;


use Dipokhalder\Settings\Facades\Settings;
use Exception;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    public string $filePath;

    public function sendNotification($data, $fcmTokens, $topicName = "notification", $orderStatus = 0, $redirectUrl = ''): void
    {
        try {
            $notification = Settings::group('notification')->all();
            $url          = 'https://fcm.googleapis.com/v1/projects/' . $notification['notification_fcm_project_id'] . '/messages:send';
            $accessToken  = $this->getAccessToken();

            $client  = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ];
            foreach ($fcmTokens as $fcmToken) {
                $payload = [
                    'message' => [
                        'token'        => $fcmToken,
                        'notification' => [
                            'title' => (string)$data->title,
                            'body'  => (string)$data->description,
                            'image' => $data->image ?? null
                        ],
                        'data'         => [
                            'title'        => (string)$data->title,
                            'body'         => (string)$data->description,
                            'sound'        => (string)'default',
                            'image'        => $data->image ?? null,
                            'topic_name'   => (string)$topicName,
                            "order_status" => (string)$orderStatus,
                            "url"          => (string)$redirectUrl
                        ],
                        'webpush'      => [
                            "headers" => [
                                "Urgency" => "high"
                            ]
                        ]
                    ]
                ];

                $client->post($url, [
                    'headers' => $headers,
                    'body'    => json_encode($payload),
                    'verify'  => config('services.http.verify_ssl')
                ]);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function getAccessToken()
    {
        $keyFilePath = NotificationSetting::where(['key' => 'notification_fcm_json_file'])->first()->file;
        $parsed_url  = parse_url($keyFilePath);

        if (isset($parsed_url['path'])) {
            $relative_path  = ltrim($parsed_url['path'], '/storage');
            $this->filePath = storage_path('app/public/' . $relative_path);
        } else {
            throw new Exception('No file found in the URL');
        }

        $scopes = ['https://www.googleapis.com/auth/cloud-platform'];

        if (!file_exists($this->filePath)) {
            throw new Exception('Service account key file not found');
        }

        $credentials = new ServiceAccountCredentials($scopes, $this->filePath);
        $token       = $credentials->fetchAuthToken();

        if (isset($token['access_token'])) {
            return $token['access_token'];
        } else {
            throw new Exception('Failed to fetch access token');
        }
    }
}

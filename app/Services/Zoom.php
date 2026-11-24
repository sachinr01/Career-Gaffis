<?php

namespace App\Services;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Small Zoom REST client that replaces macsidigital/laravel-zoom.
 * That package has no Laravel 12 / PHP 8.4 release.
 */
class Zoom
{
    public static function user(): ZoomUserApi
    {
        return new ZoomUserApi();
    }

    public static function meeting(): ZoomMeetingApi
    {
        return new ZoomMeetingApi();
    }

    public static function token(): string
    {
        $key = (string) config('zoom.api_key');
        $secret = (string) config('zoom.api_secret');

        if ($key === '' || $secret === '') {
            throw new RuntimeException('Zoom API key and secret are not configured.');
        }

        $header = self::base64Url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = self::base64Url(json_encode([
            'iss' => $key,
            'exp' => time() + 60,
        ]));
        $signature = self::base64Url(hash_hmac('sha256', $header.'.'.$payload, $secret, true));

        return $header.'.'.$payload.'.'.$signature;
    }

    public static function request(string $method, string $path, ?array $body = null, bool $allowNotFound = false): array
    {
        $pending = Http::withToken(self::token())->acceptJson();
        $url = 'https://api.zoom.us/v2'.$path;
        $response = $body === null
            ? $pending->send($method, $url)
            : $pending->send($method, $url, ['json' => $body]);

        if ($allowNotFound && $response->status() === 404) {
            return [];
        }

        if ($response->failed()) {
            throw new RuntimeException('Zoom API error: '.$response->body());
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    public static function toApiBody(array $settings): array
    {
        $start = $settings['start_time'] ?? null;
        if ($start instanceof DateTimeInterface) {
            $start = Carbon::instance($start)->format('Y-m-d\TH:i:s');
        }

        $body = [
            'topic' => $settings['topic'] ?? 'Meeting',
            'type' => (int) ($settings['type'] ?? 2),
            'start_time' => $start,
            'duration' => (int) ($settings['duration'] ?? 30),
            'timezone' => $settings['timezone'] ?? config('app.timezone'),
            'agenda' => $settings['agenda'] ?? '',
            'settings' => [
                'host_video' => (bool) ($settings['settings']['host_video'] ?? false),
                'participant_video' => (bool) ($settings['settings']['participant_video'] ?? false),
            ],
        ];

        if (! empty($settings['alternative_host'])) {
            $hosts = $settings['alternative_host'];
            $body['settings']['alternative_hosts'] = is_array($hosts) ? implode(',', $hosts) : $hosts;
        }

        return $body;
    }

    private static function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}

class ZoomUserApi
{
    public function find(string $id = 'me'): ZoomUser
    {
        return new ZoomUser($id);
    }
}

class ZoomUser
{
    public function __construct(public string $id)
    {
    }

    public function meetings(): ZoomUserMeetings
    {
        return new ZoomUserMeetings($this->id);
    }
}

class ZoomUserMeetings
{
    public function __construct(public string $userId)
    {
    }

    public function make(array $attributes): ZoomMeetingDraft
    {
        return new ZoomMeetingDraft($attributes);
    }

    public function save(ZoomMeetingDraft $draft): ZoomMeetingRecord
    {
        $body = Zoom::toApiBody($draft->attributes);
        if ($draft->recurrence) {
            $body['recurrence'] = $draft->recurrence;
        }

        $data = Zoom::request('POST', '/users/'.$this->userId.'/meetings', $body);

        return ZoomMeetingRecord::fromApi($data);
    }

    public function find($meetingId): ?ZoomMeetingRecord
    {
        if (! $meetingId) {
            return null;
        }

        $data = Zoom::request('GET', '/meetings/'.$meetingId, null, true);

        return $data === [] ? null : ZoomMeetingRecord::fromApi($data);
    }
}

class ZoomMeetingApi
{
    public function find($meetingId): ?ZoomMeetingRecord
    {
        if (! $meetingId) {
            return null;
        }

        $data = Zoom::request('GET', '/meetings/'.$meetingId, null, true);

        return $data === [] ? null : ZoomMeetingRecord::fromApi($data);
    }

    public function make(array $attributes): ZoomMeetingDraft
    {
        return new ZoomMeetingDraft($attributes);
    }
}

class ZoomMeetingDraft
{
    public ?array $recurrence = null;

    public function __construct(public array $attributes)
    {
    }

    public function recurrence(): ZoomRecurrenceBuilder
    {
        return new ZoomRecurrenceBuilder($this);
    }
}

class ZoomRecurrenceBuilder
{
    public function __construct(private ZoomMeetingDraft $draft)
    {
    }

    public function make(array $data): void
    {
        $recurrence = [
            'type' => (int) ($data['type'] ?? 1),
            'repeat_interval' => (int) ($data['repeat_interval'] ?? 1),
            'end_times' => (int) ($data['end_times'] ?? 1),
        ];

        if (isset($data['weekly_days'])) {
            $recurrence['weekly_days'] = (string) $data['weekly_days'];
        }

        $this->draft->recurrence = $recurrence;
    }
}

class ZoomMeetingRecord
{
    public $id;

    public $start_url;

    public $join_url;

    public $password;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->start_url = $data['start_url'] ?? null;
        $this->join_url = $data['join_url'] ?? null;
        $this->password = $data['password'] ?? null;
    }

    public static function fromApi(array $data): self
    {
        return new self($data);
    }

    public function update(array $settings): void
    {
        Zoom::request('PATCH', '/meetings/'.$this->id, Zoom::toApiBody($settings));
    }

    public function delete(): void
    {
        Zoom::request('DELETE', '/meetings/'.$this->id);
    }

    public function endMeeting(): void
    {
        Zoom::request('PUT', '/meetings/'.$this->id.'/status', ['action' => 'end']);
    }

    public function occurrences(): ZoomOccurrences
    {
        return new ZoomOccurrences((string) $this->id);
    }
}

class ZoomOccurrences
{
    public function __construct(private string $meetingId)
    {
    }

    public function delete(): void
    {
        Zoom::request('DELETE', '/meetings/'.$this->meetingId);
    }

    public function find($occurrenceId): ZoomOccurrence
    {
        return new ZoomOccurrence($this->meetingId, (string) $occurrenceId);
    }
}

class ZoomOccurrence
{
    public $start_time;

    public function __construct(private string $meetingId, private string $occurrenceId)
    {
    }

    public function delete(): void
    {
        Zoom::request('DELETE', '/meetings/'.$this->meetingId.'?occurrence_id='.$this->occurrenceId);
    }

    public function save(): void
    {
        $start = $this->start_time;
        if ($start instanceof DateTimeInterface) {
            $start = Carbon::instance($start)->format('Y-m-d\TH:i:s');
        }

        Zoom::request('PATCH', '/meetings/'.$this->meetingId.'?occurrence_id='.$this->occurrenceId, [
            'start_time' => $start,
        ]);
    }
}

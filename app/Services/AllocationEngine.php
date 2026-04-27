<?php

namespace App\Services;

use App\Models\AllocationMatch;
use App\Models\Donor;
use App\Models\Recipient;
use App\Models\SystemSetting;
use Illuminate\Support\Carbon;

class AllocationEngine
{
    public function __construct(
        private readonly BloodCompatibilityService $bloodCompatibilityService,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function findBestMatch(Donor $donor): ?AllocationMatch
    {
        $donorOrgans = $donor->organs()->pluck('organ_type')->all();
        if ($donorOrgans === []) {
            $donorOrgans = [$donor->organ_type];
        }

        if (
            ! $donor->is_available ||
            $donor->medical_status !== 'VERIFIED' ||
            ! $donor->identity_verified ||
            ($donor->available_until && Carbon::parse($donor->available_until)->isPast())
        ) {
            return null;
        }

        $hasActiveMatch = AllocationMatch::where('donor_id', $donor->id)
            ->whereNotIn('status', ['REJECTED', 'COMPLETED'])
            ->exists();

        if ($hasActiveMatch) {
            return null;
        }

        $organCandidates = collect($donorOrgans)
            ->flatMap(fn (string $organ) => [$organ, strtolower($organ), ucfirst(strtolower($organ))])
            ->unique()
            ->values()
            ->all();

        $recipient = Recipient::query()
            ->whereIn('organ_needed', $organCandidates)
            ->where('status', 'VERIFIED')
            ->where('identity_verified', true)
            ->get()
            ->filter(fn (Recipient $recipient): bool => $this->isCompatible($donor->blood_group, $recipient->blood_group))
            ->filter(function (Recipient $recipient): bool {
                return ! AllocationMatch::where('recipient_id', $recipient->id)
                    ->whereNotIn('status', ['REJECTED', 'COMPLETED'])
                    ->exists();
            })
            ->map(function (Recipient $recipient) use ($donor): array {
                $breakdown = $this->scoreBreakdown($recipient, $donor);

                return [
                    'recipient' => $recipient,
                    'score' => $breakdown['total'],
                    'breakdown' => $breakdown,
                ];
            })
            ->sortByDesc('score')
            ->first();

        if (! $recipient) {
            return null;
        }

        $match = AllocationMatch::create([
            'donor_id' => $donor->id,
            'recipient_id' => $recipient['recipient']->id,
            'score' => $recipient['score'],
            'match_score' => min(100, max(0, $recipient['score'])),
            'reason' => $this->buildReason($recipient['breakdown']),
            'score_breakdown' => $recipient['breakdown'],
            'status' => 'MATCHED',
        ]);

        $donor->update(['medical_status' => 'MATCHED']);
        $recipient['recipient']->update(['status' => 'MATCHED']);
        if ($donor->user) {
            $this->notificationService->notify(
                $donor->user,
                'match',
                'New donor match found',
                "Matched with recipient {$recipient['recipient']->id} at {$match->match_score}%.",
                AllocationMatch::class,
                $match->id
            );
        }
        if ($recipient['recipient']->user) {
            $this->notificationService->notify(
                $recipient['recipient']->user,
                'match',
                'A donor is available for you',
                "A compatible donor match was created with score {$match->match_score}%.",
                AllocationMatch::class,
                $match->id
            );
        }

        return $match;
    }

    public function isCompatible(string $donorBloodGroup, string $recipientBloodGroup): bool
    {
        return $this->bloodCompatibilityService->canDonateTo($donorBloodGroup, $recipientBloodGroup);
    }

    public function calculateScore(Recipient $recipient, ?Donor $donor = null): int
    {
        return $this->scoreBreakdown($recipient, $donor)['total'];
    }

    public function scoreBreakdown(Recipient $recipient, ?Donor $donor = null): array
    {
        $settings = $this->resolveSettings();

        $urgencyBand = match (strtolower($recipient->urgency_level)) {
            'high' => 1.0,
            'medium' => 0.65,
            default => 0.35,
        };
        $urgencyScore = (int) round($settings->urgency_weight * $urgencyBand);

        $waitingProgress = min(1, ((int) $recipient->waiting_time) / 365);
        $waitingScore = (int) round($settings->waiting_weight * $waitingProgress);
        $compatibilityScore = (int) $settings->compatibility_weight;
        $distanceScore = $donor && $recipient->region && $donor->region
            ? ($recipient->region === $donor->region ? 15 : 5)
            : 8;
        $timeConstraintScore = $donor && $donor->available_until
            ? max(0, min(10, (int) now()->diffInDays($donor->available_until, false)))
            : 6;
        $criticalBoost = (int) $recipient->waiting_time >= $settings->emergency_threshold ? 15 : 0;
        $emergencyBoost = $recipient->is_emergency ? 15 : 0;

        $total = $urgencyScore
            + $waitingScore
            + $compatibilityScore
            + $distanceScore
            + $timeConstraintScore
            + $criticalBoost
            + $emergencyBoost;

        return [
            'urgency' => $urgencyScore,
            'waiting' => $waitingScore,
            'compatibility' => $compatibilityScore,
            'distance' => $distanceScore,
            'time_constraint' => $timeConstraintScore,
            'critical_boost' => $criticalBoost,
            'emergency_boost' => $emergencyBoost,
            'total' => $total,
        ];
    }

    private function resolveSettings(): SystemSetting
    {
        return SystemSetting::firstOrCreate(
            ['id' => 1],
            [
                'urgency_weight' => 40,
                'waiting_weight' => 30,
                'compatibility_weight' => 20,
                'emergency_threshold' => 180,
                'max_daily_surgeries' => 6,
            ]
        );
    }

    public function resolvePriorityLevel(int $score): string
    {
        return match (true) {
            $score >= 90 => 'Critical',
            $score >= 65 => 'High',
            $score >= 40 => 'Medium',
            default => 'Standard',
        };
    }

    private function buildReason(array $breakdown): string
    {
        $parts = [];
        if (($breakdown['compatibility'] ?? 0) > 0) {
            $parts[] = 'compatible blood group';
        }
        if (($breakdown['urgency'] ?? 0) >= 25) {
            $parts[] = 'high urgency';
        }
        if (($breakdown['distance'] ?? 0) >= 10) {
            $parts[] = 'favorable distance';
        }
        if (($breakdown['waiting'] ?? 0) >= 20) {
            $parts[] = 'long waiting time';
        }

        if ($parts === []) {
            return 'Matched by baseline eligibility rules.';
        }

        return 'Matched due to '.implode(' and ', $parts).'.';
    }
}

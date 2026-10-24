<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * @return array{total: int, new_this_month: int, verified: int, two_factor: int}
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'total' => User::count(),
            'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'two_factor' => User::whereNotNull('two_factor_confirmed_at')->count(),
        ];
    }

    /**
     * The last 12 months, oldest first.
     *
     * @return array<int, CarbonInterface>
     */
    #[Computed]
    public function months(): array
    {
        return collect(range(11, 0))->map(fn (int $ago) => now()->startOfMonth()->subMonths($ago))->all();
    }

    /**
     * New users per month over the last 12 months (one count query per month keeps it database agnostic).
     *
     * @return array{categories: array<int, string>, series: array<int, array{name: string, data: array<int, int>}>}
     */
    #[Computed]
    public function newUsersChart(): array
    {
        return [
            'categories' => $this->monthLabels(),
            'series' => [[
                'name' => __('New users'),
                'data' => array_map(
                    fn (CarbonInterface $month) => User::whereBetween('created_at', [$month, $month->copy()->endOfMonth()])->count(),
                    $this->months,
                ),
            ]],
        ];
    }

    /**
     * Users split into three mutually exclusive security groups.
     *
     * @return array{labels: array<int, string>, series: array<int, int>}
     */
    #[Computed]
    public function securityChart(): array
    {
        $twoFactor = User::whereNotNull('two_factor_confirmed_at')->count();
        $verified = User::whereNotNull('email_verified_at')->whereNull('two_factor_confirmed_at')->count();
        $unverified = User::whereNull('email_verified_at')->whereNull('two_factor_confirmed_at')->count();

        return [
            'labels' => [__('2FA enabled'), __('Verified, no 2FA'), __('Unverified')],
            'series' => [$twoFactor, $verified, $unverified],
        ];
    }

    /**
     * Sample revenue data — replace with real figures once there is a data source.
     *
     * @return array{categories: array<int, string>, series: array<int, array{name: string, data: array<int, int>}>}
     */
    #[Computed]
    public function revenueChart(): array
    {
        return [
            'categories' => $this->monthLabels(),
            'series' => [
                ['name' => __('This year'), 'data' => [42, 47, 45, 53, 58, 55, 63, 68, 66, 74, 79, 86]],
                ['name' => __('Last year'), 'data' => [35, 38, 41, 39, 44, 48, 46, 51, 55, 53, 58, 61]],
            ],
        ];
    }

    public function render(): View
    {
        return view('admin.dashboard');
    }

    /**
     * @return array<int, string>
     */
    private function monthLabels(): array
    {
        return array_map(fn (CarbonInterface $month) => $month->translatedFormat('M'), $this->months);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Actions\Home\ResolvePeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomePeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'period' => ['sometimes', Rule::in(['this_month', 'last_month', 'custom'])],
            'from' => ['required_if:period,custom', 'nullable', 'date_format:Y-m-d'],
            'to' => ['required_if:period,custom', 'nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ];
    }

    /**
     * @return array{start: string, end: string, label: string}
     */
    public function range(): array
    {
        return app(ResolvePeriod::class)->handle(
            $this->validated('period', 'this_month'),
            $this->validated('from'),
            $this->validated('to'),
        );
    }
}

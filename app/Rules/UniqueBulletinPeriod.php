<?php

namespace App\Rules;

use App\Models\Bulletin;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueBulletinPeriod implements ValidationRule
{
    public function __construct(
        private readonly ?int $churchId,
        private readonly ?int $year,
        private readonly mixed $ignoreId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->churchId || ! $this->year || ! is_numeric($value)) {
            return;
        }

        $exists = Bulletin::query()
            ->where('church_id', $this->churchId)
            ->where('year', $this->year)
            ->where('month', (int) $value)
            ->when($this->ignoreId, fn ($query) => $query->whereKeyNot($this->ignoreId))
            ->exists();

        if ($exists) {
            $fail('Já existe um boletim para esta congregação neste mês.');
        }
    }
}

<?php

namespace App\Services;

use App\Models\Material;
use Illuminate\Support\Facades\DB;

class MaterialCodeService
{
    public function generateCode(string $name, ?string $type = null): string
    {
        return DB::transaction(function () use ($name, $type) {
            $baseCode = $this->sanitizeCode($name);

            if ($type) {
                $typeCode = $this->sanitizeCode($type);
                $baseCode = $baseCode . '-' . $typeCode;
            }

            $sequence = $this->getGlobalNextSequence();

            return 'MAT-' . $baseCode . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
        });
    }

    protected function getGlobalNextSequence(): int
    {
        $codes = Material::pluck('code')->toArray();

        if (empty($codes)) {
            return 1;
        }

        $maxSequence = 0;
        foreach ($codes as $code) {
            if (preg_match('/(\d{3})$/', $code, $matches)) {
                $sequence = (int)$matches[1];
                $maxSequence = max($maxSequence, $sequence);
            }
        }

        return $maxSequence + 1;
    }

    protected function sanitizeCode(string $input): string
    {
        $textInParentheses = '';
        if (preg_match('/\(([^)]+)\)/', $input, $matches)) {
            $textInParentheses = ' ' . $matches[1];
        }

        $combined = $input . $textInParentheses;

        $sanitized = strtoupper($combined);
        $sanitized = preg_replace('/[^A-Z0-9]/', '_', $sanitized);
        $sanitized = preg_replace('/_+/', '_', $sanitized);
        $sanitized = trim($sanitized, '_');

        return substr($sanitized, 0, 25);
    }
}

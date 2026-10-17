<?php

namespace App\Services;

class UnitConverter
{
    /**
     * Grouping unit categories with standard base multipliers.
     *
     * @var array<string, array<string, float>>
     */
    protected const UNIT_GROUPS = [
        'mass' => [
            // Standard base: gram (g)
            'kg' => 1000.0,
            'kilogram' => 1000.0,
            'kilograms' => 1000.0,
            'kilo' => 1000.0,
            'ons' => 100.0,
            'hg' => 100.0,
            'g' => 1.0,
            'gram' => 1.0,
            'gramme' => 1.0,
            'grams' => 1.0,
            'gr' => 1.0,
            'grm' => 1.0,
            'mg' => 0.001,
            'miligram' => 0.001,
            'milligram' => 0.001,
        ],
        'volume' => [
            // Standard base: milliliter (ml)
            'l' => 1000.0,
            'liter' => 1000.0,
            'litre' => 1000.0,
            'liters' => 1000.0,
            'ltr' => 1000.0,
            'ml' => 1.0,
            'mililiter' => 1.0,
            'milliliter' => 1.0,
            'milliliters' => 1.0,
            'cc' => 1.0,
            'sdm' => 15.0,
            'sendok makan' => 15.0,
            'tbsp' => 15.0,
            'sdt' => 5.0,
            'sendok teh' => 5.0,
            'tsp' => 5.0,
            'cup' => 240.0,
            'gelas' => 240.0,
        ],
        'discrete' => [
            'pcs' => 1.0,
            'pc' => 1.0,
            'piece' => 1.0,
            'pieces' => 1.0,
            'buah' => 1.0,
            'biji' => 1.0,
            'butir' => 1.0,
            'lembar' => 1.0,
            'ikat' => 1.0,
            'batang' => 1.0,
            'siung' => 1.0,
            'bungkus' => 1.0,
            'bks' => 1.0,
            'pack' => 1.0,
            'sachet' => 1.0,
            'kaleng' => 1.0,
            'can' => 1.0,
            'porsi' => 1.0,
            'portion' => 1.0,
            'pax' => 1.0,
            'box' => 1.0,
        ],
    ];

    /**
     * Konversi nilai dari satuan asal ke satuan tujuan.
     * Mengembalikan null jika satuan tidak kompatibel / tidak dapat dikonversi.
     */
    public function convert(float|int|string $amount, string $fromUnit, string $toUnit): ?float
    {
        $amount = (float) $amount;
        $cleanFrom = $this->normalize($fromUnit);
        $cleanTo = $this->normalize($toUnit);

        // Jika satuan sama persis setelah normalisasi
        if ($cleanFrom === $cleanTo) {
            return $amount;
        }

        // Cari grup satuan untuk $fromUnit dan $toUnit
        $fromGroup = $this->findGroup($cleanFrom);
        $toGroup = $this->findGroup($cleanTo);

        if (! $fromGroup || ! $toGroup) {
            return null;
        }

        // Jika berbeda grup (misal mass ke volume atau mass ke discrete)
        if ($fromGroup !== $toGroup) {
            return null;
        }

        // Untuk grup discrete, jika nama dasarnya berbeda (misal 'butir' vs 'ikat')
        if ($fromGroup === 'discrete' && $this->resolveDiscreteCanonical($cleanFrom) !== $this->resolveDiscreteCanonical($cleanTo)) {
            return null;
        }

        $fromMultiplier = self::UNIT_GROUPS[$fromGroup][$cleanFrom] ?? null;
        $toMultiplier = self::UNIT_GROUPS[$toGroup][$cleanTo] ?? null;

        if ($fromMultiplier === null || $toMultiplier === null || $toMultiplier == 0) {
            return null;
        }

        // Nilai dalam basis = amount * fromMultiplier
        // Nilai dalam tujuan = basis / toMultiplier
        $baseValue = $amount * $fromMultiplier;

        return $baseValue / $toMultiplier;
    }

    /**
     * Cek apakah dua satuan dapat dikonversi satu sama lain.
     */
    public function canConvert(string $fromUnit, string $toUnit): bool
    {
        $cleanFrom = $this->normalize($fromUnit);
        $cleanTo = $this->normalize($toUnit);

        if ($cleanFrom === $cleanTo) {
            return true;
        }

        $fromGroup = $this->findGroup($cleanFrom);
        $toGroup = $this->findGroup($cleanTo);

        if (! $fromGroup || ! $toGroup || $fromGroup !== $toGroup) {
            return false;
        }

        if ($fromGroup === 'discrete') {
            return $this->resolveDiscreteCanonical($cleanFrom) === $this->resolveDiscreteCanonical($cleanTo);
        }

        return true;
    }

    /**
     * Normalisasi string satuan (lowercase, trim).
     */
    public function normalize(string $unit): string
    {
        return mb_strtolower(trim($unit));
    }

    /**
     * Mencari grup kategori satuan (mass, volume, discrete).
     */
    public function findGroup(string $unit): ?string
    {
        $normalized = $this->normalize($unit);

        foreach (self::UNIT_GROUPS as $groupName => $units) {
            if (array_key_exists($normalized, $units)) {
                return $groupName;
            }
        }

        return null;
    }

    /**
     * Memetakan sinonim satuan diskrit ke bentuk kanonikalnya.
     */
    protected function resolveDiscreteCanonical(string $unit): string
    {
        return match ($unit) {
            'pcs', 'pc', 'piece', 'pieces', 'buah', 'biji' => 'pcs',
            'bungkus', 'bks', 'pack', 'sachet' => 'bungkus',
            'kaleng', 'can' => 'kaleng',
            'porsi', 'portion', 'pax', 'box' => 'porsi',
            default => $unit,
        };
    }

    /**
     * Format nilai kuantitas dengan satuannya agar rapi untuk tampilan antarmuka.
     */
    public function formatQuantity(float|int|string $quantity, string $unit, int $decimals = 2): string
    {
        $floatVal = (float) $quantity;
        $formatted = number_format($floatVal, $decimals, ',', '.');
        $cleanNumber = rtrim(rtrim($formatted, '0'), ',');

        if ($cleanNumber === '' || $cleanNumber === '-0') {
            $cleanNumber = '0';
        }

        return "{$cleanNumber} {$unit}";
    }
}

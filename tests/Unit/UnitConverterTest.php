<?php

namespace Tests\Unit;

use App\Services\UnitConverter;
use PHPUnit\Framework\TestCase;

class UnitConverterTest extends TestCase
{
    private UnitConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new UnitConverter;
    }

    public function test_it_converts_mass_units_correctly(): void
    {
        // 1.5 kg = 1500 gram
        $this->assertEquals(1500.0, $this->converter->convert(1.5, 'kg', 'gram'));

        // 250 gram = 0.25 kg
        $this->assertEquals(0.25, $this->converter->convert(250, 'gram', 'kg'));

        // 500 mg = 0.5 gram
        $this->assertEquals(0.5, $this->converter->convert(500, 'mg', 'g'));

        // 10 ons = 1 kg
        $this->assertEquals(1.0, $this->converter->convert(10, 'ons', 'kg'));
    }

    public function test_it_converts_volume_units_correctly(): void
    {
        // 2 liter = 2000 ml
        $this->assertEquals(2000.0, $this->converter->convert(2, 'liter', 'ml'));

        // 750 ml = 0.75 liter
        $this->assertEquals(0.75, $this->converter->convert(750, 'ml', 'liter'));

        // 2 sdm (sendok makan) = 30 ml
        $this->assertEquals(30.0, $this->converter->convert(2, 'sdm', 'ml'));

        // 2 sdt (sendok teh) = 10 ml
        $this->assertEquals(10.0, $this->converter->convert(2, 'sdt', 'ml'));
    }

    public function test_it_handles_discrete_units_and_synonyms(): void
    {
        // Identical discrete unit
        $this->assertEquals(12.0, $this->converter->convert(12, 'butir', 'butir'));
        $this->assertEquals(5.0, $this->converter->convert(5, 'ikat', 'ikat'));

        // Synonyms in discrete: buah to pcs
        $this->assertEquals(10.0, $this->converter->convert(10, 'buah', 'pcs'));
    }

    public function test_it_normalizes_case_and_whitespaces(): void
    {
        $this->assertEquals(500.0, $this->converter->convert(0.5, '  KG  ', 'Gram'));
    }

    public function test_it_returns_null_for_incompatible_units(): void
    {
        $this->assertNull($this->converter->convert(10, 'kg', 'liter'));
        $this->assertNull($this->converter->convert(5, 'pcs', 'kg'));
        $this->assertNull($this->converter->convert(3, 'butir', 'ikat'));
    }

    public function test_can_convert_evaluates_compatibility(): void
    {
        $this->assertTrue($this->converter->canConvert('kg', 'gram'));
        $this->assertTrue($this->converter->canConvert('ml', 'liter'));
        $this->assertTrue($this->converter->canConvert('buah', 'pcs'));

        $this->assertFalse($this->converter->canConvert('kg', 'liter'));
        $this->assertFalse($this->converter->canConvert('pcs', 'gram'));
    }

    public function test_format_quantity_formats_nicely(): void
    {
        $this->assertSame('2,5 kg', $this->converter->formatQuantity(2.5, 'kg'));
        $this->assertSame('150 gram', $this->converter->formatQuantity(150.00, 'gram'));
        $this->assertSame('0 pcs', $this->converter->formatQuantity(0, 'pcs'));
    }
}

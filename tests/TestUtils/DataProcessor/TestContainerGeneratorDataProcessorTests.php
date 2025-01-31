<?php
declare(strict_types=1);

namespace RaptorTests\TestUtils\DataProcessor;

use PHPUnit\Framework\TestCase;
use Raptor\TestUtils\DataProcessor\TestContainerGeneratorDataProcessor;
use Raptor\TestUtils\ExtraAssertions;

/**
 * @author Mikhail Kamorin aka raptor_MVK
 *
 * @copyright 2019, raptor_MVK
 */
class TestContainerGeneratorDataProcessorTests extends TestCase
{
    use ExtraAssertions;

    /**
     * Checks that method _process_ returns correct result.
     *
     * @param string $json
     * @param array $expected
     *
     * @dataProvider correctDataProvider
     */
    public function testProcessReturnsCorrectResult(string $json, array $expected): void
    {
        $dataProcessor = new TestContainerGeneratorDataProcessor();

        $actual = $dataProcessor->process($json);

        static::assertArraysAreSame($expected, $actual);
    }

    /**
     * Provides correct test data for testing method _process_.
     *
     * @return array [ [ json, expected ], ... ]
     */
    public function correctDataProvider(): array
    {
        return [
            'single occurrence' => $this->prepareSingleOccurrenceTestData(),
            'multi occurrence with same type' => $this->prepareMultiOccurrenceWithSameTypeTestData(),
            'multi occurrence with different types' => $this->prepareMultiOccurrenceWithDifferentTypeTestData(),
            'float => int and int => float' => $this->prepareMultiOccurrenceWithFloatAndIntTestData()
        ];
    }

    /**
     * Prepares test data, where each field appears only once.
     *
     * @return array [ [ json, expected ], ... ]
     */
    private function prepareSingleOccurrenceTestData(): array
    {
        $jsonData = array_merge(
            ['int_field' => 3, 'string_field' => 'test', 'float_field' => 56.35, 'bool_field' => true],
            ['associative_array_field' => ['a' => 23, 'b' => 7], 'simple_array_field' => [46, 764]],
            ['null_field' => null]
        );
        $json = json_encode([array_merge($jsonData, ['_name' => 'some_test'])]);
        $expected = [
            'int_field' => TestContainerGeneratorDataProcessor::INT_TYPE,
            'string_field' => TestContainerGeneratorDataProcessor::STRING_TYPE,
            'float_field' => TestContainerGeneratorDataProcessor::FLOAT_TYPE,
            'bool_field' => TestContainerGeneratorDataProcessor::BOOL_TYPE,
            'associative_array_field' => TestContainerGeneratorDataProcessor::ARRAY_TYPE,
            'simple_array_field' => TestContainerGeneratorDataProcessor::ARRAY_TYPE,
            'null_field' => TestContainerGeneratorDataProcessor::MIXED_TYPE
        ];
        return [$json, $expected];
    }

    /**
     * Prepares test data, where fields appear several times with same type.
     *
     * @return array [ [ json, expected ], ... ]
     */
    private function prepareMultiOccurrenceWithSameTypeTestData(): array
    {
        $data = array_merge(
            ['int_field' => 3, 'string_field' => 'test', 'float_field' => 56.35, 'bool_field' => true],
            ['associative_array_field' => ['a' => 23, 'b' => 7], 'simple_array_field' => [46, 764]],
        );
        $jsonData = [array_merge($data, ['_name' => 'some_test']), array_merge($data, ['_name' => 'other_test'])];
        $json = json_encode($jsonData);
        $expected = [
            'int_field' => TestContainerGeneratorDataProcessor::INT_TYPE,
            'string_field' => TestContainerGeneratorDataProcessor::STRING_TYPE,
            'float_field' => TestContainerGeneratorDataProcessor::FLOAT_TYPE,
            'bool_field' => TestContainerGeneratorDataProcessor::BOOL_TYPE,
            'associative_array_field' => TestContainerGeneratorDataProcessor::ARRAY_TYPE,
            'simple_array_field' => TestContainerGeneratorDataProcessor::ARRAY_TYPE
        ];
        return [$json, $expected];
    }

    /**
     * Prepares test data, where fields appear several times with different types.
     *
     * @return array [ [ json, expected ], ... ]
     */
    private function prepareMultiOccurrenceWithDifferentTypeTestData(): array
    {
        $firstData = array_merge(
            ['int_field' => 3, 'string_field' => 'test', 'float_field' => 56.35, 'bool_field' => true],
            ['associative_array_field' => ['a' => 23, 'b' => 7], 'simple_array_field' => [46, 764]],
        );
        $secondData = array_merge(
            ['int_field' => 'text', 'string_field' => [35, 646], 'float_field' => ['a' => 5], 'bool_field' => 5.36],
            ['associative_array_field' => 1324, 'simple_array_field' => true],
        );
        $jsonData = [array_merge($firstData, ['_name' => 'test1']), array_merge($secondData, ['_name' => 'test2'])];
        $json = json_encode($jsonData);
        $fields =
            ['int_field', 'string_field', 'float_field', 'bool_field', 'associative_array_field', 'simple_array_field'];
        $expected = array_fill_keys($fields, TestContainerGeneratorDataProcessor::MIXED_TYPE);
        return [$json, $expected];
    }

    /**
     * Prepares test data, where fields appear several times with types float and int in a different order.
     *
     * @return array [ [ json, expected ], ... ]
     */
    private function prepareMultiOccurrenceWithFloatAndIntTestData(): array
    {
        $firstData = ['int_field' => 3, 'float_field' => 56.35];
        $secondData = ['int_field' => 64.14, 'float_field' => 43];
        $jsonData = [array_merge($firstData, ['_name' => 'test1']), array_merge($secondData, ['_name' => 'test2'])];
        $json = json_encode($jsonData);
        $expected = [
            'int_field' => TestContainerGeneratorDataProcessor::FLOAT_TYPE,
            'float_field' => TestContainerGeneratorDataProcessor::FLOAT_TYPE
        ];
        return [$json, $expected];
    }
}

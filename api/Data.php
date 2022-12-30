<?php

class Data
{
    private static string $file = 'data.json';
    private static int $slice_count = 5;

    public function get(): string
    {
        [$sortItem, $sortBy, $page] = self::getQueryData([
            'sortItem' => 'id',
            'sortBy' => 'asc',
            'page' => 0,
        ]);

        $data = self::getJsonData();

        usort($data, function($_a, $_b) use ($sortItem) {
            $a = $_a->$sortItem;
            $b = $_b->$sortItem;

            if ($a === $b) {
                return 0;
            }

            if (is_numeric($a)) {
                return ((float) $a < (float) $b) ? -1 : 1;
            } else {
                $arString = $arBaseString = [$a, $b];
                sort($arString, SORT_STRING);
                return $arString[0] === $arBaseString[0] ? -1 : 1;
            }
        });

        $page_count = ceil(((float) count($data)) / self::$slice_count);

        if (strtolower($sortBy) === 'desc') {
            $data = array_reverse($data);
        }

        $data = array_slice($data, self::$slice_count * ((int) $page), self::$slice_count);

        return json_encode([
            'error' => 0,
            'data' => [
                'items' => $data,
                'pages' => $page_count
            ]
        ]);
    }

    public function update(): string
    {
        [$id, $name, $quantity, $price] = self::getQueryData([
            'id',
            'name',
            'quantity',
            'price',
        ]);

        $data = self::getJsonData();

        if ((int) $id === -1) {
            $maxId = 0;
            foreach ($data as $datum) {
                if ((int) $datum->id > $maxId) {
                    $maxId = (int) $datum->id;
                }
            }

            $data[] = [
                'id' => $maxId + 1,
                'name' => $name,
                'quantity' => $quantity,
                'price' => $price,
            ];
        } else {
            $changed = false;
            foreach ($data as &$datum) {
                if ((int) $datum->id === (int) $id) {
                    $datum->name = $name;
                    $datum->quantity = $quantity;
                    $datum->price = $price;
                    $changed = true;
                    break;
                }
            }
            if (! $changed) {
                return json_encode([
                    'error' => 1,
                    'message' => 'Id not found',
                ]);
            }
        }

        self::saveJsonData($data);

        return json_encode([
            'error' => 0,
        ]);
    }

    public function delete(): string
    {

        [$id] = self::getQueryData(['id']);

        $data = self::getJsonData();

        foreach ($data as $index => $datum) {
            if ((int) $datum->id === (int) $id) {
                array_splice($data, $index, 1);
                break;
            }
        }

        self::saveJsonData($data);

        return json_encode([
            'error' => 0,
        ]);
    }

    private static function getQueryData($array): array
    {
        $rawData = $_GET['data'] ?? null;
        $query = json_decode($rawData);

        $result = [];

        foreach ($array as $key => $item) {
            if (is_int($key)) {
                $result[] = $query->$item;
            } else {
                $result[] = $query->$key ?? $item;
            }
        }

        return $result;
    }

    private static function saveJsonData(array $data): void
    {
        file_put_contents(self::$file, json_encode($data));
    }

    private static function getJsonData(): array
    {
        return json_decode(file_get_contents(self::$file));
    }
}
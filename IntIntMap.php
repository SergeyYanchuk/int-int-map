<?php
require_once 'MemDriver.php';
require_once 'Bucket.php';
require_once 'IntIntMapException.php';

/**
 * Требуется написать IntIntMap, который по произвольному int ключу хранит произвольное int значение
 * Важно: все данные (в том числе дополнительные, если их размер зависит от числа элементов) требуется хранить в выделенном заранее блоке в разделяемой памяти
 * для доступа к памяти напрямую необходимо (и достаточно) использовать следующие два метода:
 * \shmop_read и \shmop_write
 */
class IntIntMap
{
    private int $maxCount;

    private int $actualCount = 0;

    private MemDriver $driver;

    /**
     * IntIntMap constructor.
     *
     * @param \Shmop $shm_id результат вызова \shmop_open
     * @param int $size размер зарезервированного блока в разделяемой памяти (~100GB)
     */
    public function __construct(\Shmop $shm_id, int $size)
    {
        $this->maxCount = floor($size / (PHP_INT_SIZE * 5));
        $this->driver = new MemDriver($shm_id, $size);
    }

    /**
     * Метод должен работать со сложностью O(1) при отсутствии коллизий, но может деградировать при их появлении
     *
     * @param int $key произвольный ключ
     * @param int $value произвольное значение
     *
     * @return int|null предыдущее значение
     * @throws IntIntMapException
     */
    public function put(int $key, int $value): ?int
    {
        $startPosition = $this->driver->read($this->getHash($key));
        $bucket = null;
        if ($startPosition > 0) {
            $bucket = new Bucket($startPosition, $this->driver);
            $bucket = $bucket->getEqualsOrLast($key);

            if ($bucket->isEquals($key)) {
                $oldValue = $bucket->getValue();
                $bucket->setValue($value);
                return $oldValue;
            }
        }

        if ($this->actualCount >= $this->maxCount) {
            throw new IntIntMapException('Can\'t add value. Hashmap  is full');
        }

        $needAdd = $startPosition == 0;
        $startPosition = $this->maxCount + $this->actualCount * 4;
        if ($needAdd) {
            $this->driver->write($startPosition, $this->getHash($key));
        } elseif ($bucket) {
            $bucket->setNext($startPosition);
        }

        $newBucket = new Bucket($startPosition, $this->driver);
        $newBucket->setValue($value);
        $newBucket->setKey($key);
        $newBucket->setHash($this->getHash($key));
        $this->actualCount++;
        return null;
    }

    private function getHash($key): int
    {
        return floor($this->maxCount / $key);
    }

    /**
     * Метод должен работать со сложностью O(1) при отсутствии коллизий, но может деградировать при их появлении
     *
     * @param int $key ключ
     *
     * @return int|null значение, сохраненное ранее по этому ключу
     */
    public function get(int $key): ?int
    {
        $startPosition = $this->driver->read($this->getHash($key));
        $bucket = new Bucket($startPosition, $this->driver);
        $bucket = $bucket->getEqualsOrLast($key);
        if ($bucket->isEquals($key)) {
            return $bucket->getValue();
        }

        return null;
    }
}
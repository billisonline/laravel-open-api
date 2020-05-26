<?php


namespace BYanelli\OpenApiLaravel\Support;


trait Tappable
{
    use \Illuminate\Support\Traits\Tappable;

    /**
     * @param $condition
     * @param callable $callback
     * @return $this
     */
    public function when($condition, callable $callback)
    {
        if ($condition) {
            return $this->tap($callback);
        }

        return $this;
    }

    /**
     * @param $condition
     * @param callable $callback
     * @return $this
     */
    public function unless($condition, callable $callback)
    {
        return $this->when(!$condition, $callback);
    }
}
<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\Cache\Storages;

/**
 * @package Cache_Storages
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Storage implements \ArrayAccess
{
    /**
     * Determine if an item exists in the cache.
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return (!is_null($this->get($key)));
    }

    /**
     * Determine if an item exists in the cache.
     *
     * Enables you to use: isset($storage[$key])
     *
     * @param $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Get an item from the cache.
     *
     * <code>
     *    // Get an item from the cache storage
     *    $name = Cache::storage('name');
     *
     *    // Return a default value if the requested item isn't cached
     *    $name = Cache::get('name', 'Robin');
     * </code>
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $item = $this->retrieve($key);
        return (isset($item)) ? $item : $default;
    }

    /**
     * Get an item from the cache.
     *
     * Enables you to use: $storage[$key]
     *
     * <code>
     *    // Get an item from the cache storage
     *    $name = Cache::storage()['name'];
     * </code>
     *
     * @param      $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Retrieve an item from the cache storage.
     *
     * @param string $key
     *
     * @return mixed
     */
    abstract protected function retrieve($key);

    /**
     * Write an item to the cache for a given number of minutes.
     *
     * <code>
     *    // Put an item in the cache for 15 minutes
     *    Cache::put('name', 'Robin', 15);
     * </code>
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $minutes
     *
     * @return void
     */
    abstract public function put($key, $value, $minutes);

    /**
     * Write an item to the cache for indefinite-term storage.
     *
     * Enables you to use: $storage[$key] = $value;
     *
     * @param $key
     * @param $value
     */
    public function offsetSet($key, $value)
    {
        $this->forever($key, $value);
    }

    /**
     * Get an item from the cache, or cache and return the default value.
     *
     * <code>
     *    // Get an item from the cache, or cache a value for 15 minutes
     *    $name = Cache::remember('name', 'Robin', 15);
     *
     *    // Use a closure for deferred execution
     *    $count = Cache::remember('count', function () { return User::count(); }, 15);
     * </code>
     *
     * @param string $key
     * @param mixed  $default
     * @param int    $minutes
     * @param string $function
     *
     * @return mixed
     */
    public function remember($key, $default, $minutes, $function = 'put')
    {
        $item = $this->get($key, null);

        if (isset($item)) {
            return $item;
        }

        $this->$function($key, $default, $minutes);

        return $default;
    }

    /**
     * Write an item to the cache for indefinite-term storage.
     *
     * @param $key
     * @param $value
     *
     * @return mixed Depends on implementation
     */
    abstract public function forever($key, $value);

    /**
     * Get an item from the cache, or cache the default value forever.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function sear($key, $default)
    {
        return $this->remember($key, $default, null, 'forever');
    }

    /**
     * Delete an item from the cache.
     *
     * @param string $key
     *
     * @return boolean
     */
    abstract public function forget($key);

    /**
     * Delete an item from the cache.
     *
     * Enables you to use: unset($storage[$key]);
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        $this->forget($key);
    }

    /**
     * Get the expiration time as a UNIX timestamp.
     *
     * @param int $minutes
     *
     * @return int
     */
    protected static function expiration($minutes)
    {
        return time() + ($minutes * 60);
    }
}

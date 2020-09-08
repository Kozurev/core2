<?php

class Core_Access_Cache
{
    /**
     * @var array
     */
    private array $stash = [];

    /**
     * Core_Access_Cache constructor.
     * @param User|null $user
     * @param array|null $capabilities
     */
    public function __construct(User $user = null, array $capabilities = null)
    {
        if (!is_null($user) && is_array($capabilities)) {
            /** @var Core_Access_Capability $capability */
            foreach ($capabilities as $capability) {
                $this->put($user->getId(), $capability->name(), $capability->access());
            }
        }
    }

    /**
     * @param int $userId
     * @param string $capability
     * @return bool|null
     */
    public function get(int $userId, string $capability)
    {
        return $this->stash[$userId][$capability] ?? null;
    }


    /**
     * @param int $userId
     * @param string $capability
     * @param bool $access
     */
    public function put(int $userId, string $capability, bool $access)
    {
        $this->stash[$userId][$capability] = $access;
    }


    /**
     * @param int $userId
     * @param string $capability
     */
    public function remove(int $userId, string $capability)
    {
        if (!is_null($this->get($userId, $capability))) {
            unset($this->stash[$userId][$capability]);
        }
    }


    /**
     *
     */
    public function clear()
    {
        $this->stash = [];
    }

}
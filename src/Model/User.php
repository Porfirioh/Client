<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $email
 * @property-read string $password
 * @property-read string $username
 * @property-read string $name
 * @property-read string $bio
 * @property-read string $skype
 * @property-read string $linkedin
 * @property-read string $twitter
 * @property-read bool $dark_scheme
 * @property-read int $theme_id
 * @property-read int $color_scheme_id
 * @property-read bool $blocked
 * @property-read int|null $project_limit
 * @property-read int $access_level
 * @property-read string $created_at
 * @property-read string $extern_uid
 * @property-read string $provider
 * @property-read string $state
 * @property-read bool $is_admin
 * @property-read bool $can_create_group
 * @property-read bool $can_create_project
 * @property-read string $avatar_url
 * @property-read string $current_sign_in_at
 * @property-read bool $two_factor_enabled
 */
final class User extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'email',
        'password',
        'username',
        'name',
        'bio',
        'skype',
        'linkedin',
        'twitter',
        'dark_scheme',
        'theme_id',
        'color_scheme_id',
        'blocked',
        'projects_limit',
        'access_level',
        'created_at',
        'extern_uid',
        'provider',
        'state',
        'is_admin',
        'can_create_group',
        'can_create_project',
        'avatar_url',
        'current_sign_in_at',
        'two_factor_enabled',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return User
     */
    public static function fromArray(Client $client, array $data)
    {
        $id = $data['id'] ?? 0;

        $user = new self($id, $client);

        return $user->hydrate($data);
    }

    /**
     * @param Client $client
     * @param string $email
     * @param string $password
     * @param array  $params
     *
     * @return User
     */
    public static function create(Client $client, $email, $password, array $params = [])
    {
        $data = $client->users()->create($email, $password, $params);

        return self::fromArray($client, $data);
    }

    /**
     * @param int|null    $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct($id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }

    /**
     * @return User
     */
    public function show()
    {
        $data = $this->client->users()->show($this->id);

        return self::fromArray($this->getClient(), $data);
    }

    /**
     * @param array $params
     *
     * @return User
     */
    public function update(array $params)
    {
        $data = $this->client->users()->update($this->id, $params);

        return self::fromArray($this->getClient(), $data);
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $this->client->users()->remove($this->id);

        return true;
    }

    /**
     * @return bool
     */
    public function block()
    {
        $this->client->users()->block($this->id);

        return true;
    }

    /**
     * @return bool
     */
    public function unblock()
    {
        $this->client->users()->unblock($this->id);

        return true;
    }

    /**
     * @return Key[]
     */
    public function keys()
    {
        $data = $this->client->users()->keys();

        $keys = [];
        foreach ($data as $key) {
            $keys[] = Key::fromArray($this->getClient(), $key);
        }

        return $keys;
    }

    /**
     * @param string $title
     * @param string $key
     *
     * @return Key
     */
    public function createKey($title, $key)
    {
        $data = $this->client->users()->createKey($title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    /**
     * @param int    $user_id
     * @param string $title
     * @param string $key
     *
     * @return Key
     */
    public function createKeyForUser($user_id, $title, $key)
    {
        $data = $this->client->users()->createKeyForUser($user_id, $title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function removeKey($id)
    {
        $this->client->users()->removeKey($id);

        return true;
    }

    /**
     * @param int $group_id
     * @param int $access_level
     *
     * @return User
     */
    public function addToGroup($group_id, $access_level)
    {
        $group = new Group($group_id, $this->getClient());

        return $group->addMember($this->id, $access_level);
    }

    /**
     * @param int $group_id
     *
     * @return bool
     */
    public function removeFromGroup($group_id)
    {
        $group = new Group($group_id, $this->getClient());

        return $group->removeMember($this->id);
    }
}

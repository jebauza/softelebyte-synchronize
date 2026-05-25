<?php


namespace Softelebyte\Synchronize\Base\Helper;


use Softelebyte\Synchronize\Base\Contracts\Row\GroupRowClass;
use Softelebyte\Synchronize\Base\Exceptions\GroupIncorrectTypeException;

class GroupRowClassHelper
{
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return GroupRowClass
     * @throws GroupIncorrectTypeException
     */
    public function groupRowClass(): GroupRowClass
    {
        $defaultGroup = config('synchronize.default_group');
        if ($defaultGroup && !$this->options['group']) {
            return new $defaultGroup();
        }
        $group = $this->options['group'];
        $group = config('synchronize.alias.' . $group) ?? null;
        if ($group) {
            return new $group();
        }
        throw new GroupIncorrectTypeException('GroupTable incorrecto ' . $group);
    }
}

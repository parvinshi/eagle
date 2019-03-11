<?php
declare(strict_types=1);

namespace Eagle\DI\Definition;

use Eagle\DI\ContainerSourceInterface;

interface DefinitionCollectionInterface extends ContainerSourceInterface, \IteratorAggregate
{
    /**
     * 添加对象依赖关系的定义
     *
     * @param string $id
     * @param $definition
     * @param bool $shared
     * @return DefinitionInterface
     */
    public function set(string $id, $definition, bool $shared = false) : DefinitionInterface;

    /**
     * 判断对象依赖的定义是否已添加
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id) : bool;

    /**
     * 获取对象依赖关系中的定义数据
     *
     * @param string $id
     * @return DefinitionInterface
     */
    public function getDefinition(string $id) : DefinitionInterface;

    /**
     * 解析所添加的对象关系依赖的定义
     *
     * @param string $id
     * @param bool $isNew
     * @return mixed
     */
    public function resolve(string $id, bool $isNew = false);
}
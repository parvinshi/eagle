<?php
declare(strict_types=1);

namespace Eagle\DI\Definition;

use Eagle\DI\ContainerSourceInterface;

interface DefinitionInterface extends ContainerSourceInterface
{
    /**
     * @param string $id
     * @return DefinitionInterface
     */
    public function setName(string $id) : DefinitionInterface;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @param $definition
     * @return DefinitionInterface
     */
    public function setEntry($definition) : DefinitionInterface;

    /**
     * @return mixed
     */
    public function getEntry();

    /**
     * @param bool $shared
     * @return DefinitionInterface
     */
    public function setShared(bool $shared) : DefinitionInterface;

    /**
     * @return bool
     */
    public function isShared() : bool;

    /**
     * 添加对象依赖关系的定义
     *
     * @param string|array $args
     * @return self
     */
    public function addArguments($args) : DefinitionInterface;

    /**
     * 添加一个类方法来实现注入
     *
     * @param string $method
     * @param array $args
     * @return DefinitionInterface
     */
    public function addInvokeMethod(string $method, array $args = []) : DefinitionInterface;

    /**
     * 添加多个实现注入的类方法
     *
     * @param array $methods
     * @return DefinitionInterface
     */
    public function addInvokeMethods(array $methods) : DefinitionInterface;

    /**
     * 解析所添加的对象关系依赖的定义
     *
     * @param bool $isNew
     * @return mixed
     */
    public function resolve(bool $isNew = false);
}
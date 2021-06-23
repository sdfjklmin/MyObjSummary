<?php
/*
 * 业务说明:
 *  我们要预定一艘货船(Voyage)来装载货物(Cargo)
 *
 * 业务规则图:
 *  Voyage.capacity <------> Cargo.size
 *
 * 领域知识:
 *  一般来说总会有人在最后一刻取消订单，因此货船可以接受"预定"，即"超定"。
 *  有时可以用个百分比容量来表示 110%，有时还会有更复杂的考虑:客户和特定物品优先等。
 *
 * 领域规则图-1:
 *  Voyage.capacity <------> Cargo.size
 *                 内置规则:110%
 *
 * 领域规则图-2:
 *  Voyage.capacity ------ Cargo.size
 *                    ↑
 *                    | sum(Cargo.size) < Voyage.capacity * 1.1
 *                    |
 *              Overbooking Policy
 * 说明:
 *  这里并不建议将这样的精细设计应用到领域的每个细节中。
 *  这个例子的目的是说明领域模型和相应的设计，可用来保护和共享知识。
 *
 * 更多:
 *  在运输过程中，可能会有转运、暂时仓储、责任承当等更复杂的业务。
 */

/** 货轮
 * Class Voyage
 */
class Voyage
{
    /** 可用容量
     * @var
     */
    public $capacity;

    public function addCargo(Cargo $cargo): bool
    {
        return true;
    }
}

/** 货物
 * Class Cargo
 */
class Cargo
{
    /** 货物总量
     * @var
     */
    public $size;
}

/** 普通方法
 * @param Voyage $voyage
 * @param Cargo $cargo
 * @return false
 */
function busOne(Voyage $voyage, Cargo $cargo): bool
{
    if ($cargo->size > $voyage->capacity) {
        return false;
    }
    return $voyage->addCargo($cargo);
}

/** 领域方法一
 * @param Voyage $voyage
 * @param Cargo $cargo
 * @return bool
 */
function domainOne(Voyage $voyage, Cargo $cargo): bool
{
    if ($cargo->size > $voyage->capacity * 1.1) {
        return false;
    }
    return $voyage->addCargo($cargo);
}

/** 领域二的规则-策略模式
 * Class Overbooking
 */
class Overbooking
{
    public function isAllowed($capacity, $size): bool
    {
        if ($size > $capacity * 1.1) {
            return false;
        } else {
            return true;
        }
    }
}

/** 领域二的规则
 * @param Voyage $voyage
 * @param Cargo $cargo
 * @param Overbooking $overbooking
 * @return bool
 */
function domainTwo(Voyage $voyage, Cargo $cargo, Overbooking $overbooking): bool
{
    if (!$overbooking->isAllowed($voyage->capacity, $cargo->size)) {
        return false;
    }
    return $voyage->addCargo($cargo);
}

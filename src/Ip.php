<?php

/**
 * @copyright Copyright &copy; Budi Pratama
 * @package yii2-date-range
 * @version 1.7.3
 */

namespace budipratama\user;

use yii\base\Behavior;
use yii\base\InvalidCallException;
use yii\db\BaseActiveRecord;

class Ip extends Behavior
{
    /**
     * @var string the attribute that will receive User Ip value
     * Set this property to false if you do not want to record the Firt Ip.
     */
    public $createdAtAttribute = 'first_ip';
    /**
     * @var string the attribute that will receive User Ip value.
     * Set this property to false if you do not want to record the Last Ip.
     */
    public $updatedAtAttribute = 'last_ip';
    /**
     * {@inheritdoc}
     *
     * In case, when the value is `null`, the result of the User Ip
     * will be used as value.
     */
    public $value;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdAtAttribute, $this->updatedAtAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedAtAttribute,
            ];
        }
    }

    /**
     * {@inheritdoc}
     *
     * In case, when the [[value]] is `null`, the result of the PHP function [time()](https://www.php.net/manual/en/function.time.php)
     * will be used as value.
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return Yii::$app->user->getIp();
        }

        return parent::getValue($event);
    }

    /**
     * Updates a timestamp attribute to the current timestamp.
     *
     * ```php
     * $model->touch('lastVisit');
     * ```
     * @param string $attribute the name of the attribute to update.
     * @throws InvalidCallException if owner is a new record (since version 2.0.6).
     */
    public function touch($attribute)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Updating the timestamp is not possible on a new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }

}
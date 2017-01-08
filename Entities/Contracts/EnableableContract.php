<?php
namespace Laracraft\Core\Entities\Contracts;

interface EnableableContract
{
	/**
	 * @return string
	 */
    public function getEnabledColumn();

	/**
	 * @return string
	 */
    public function getQualifiedEnabledColumn();

    public function fireEnablingEvent();

    public function fireEnabledEvent();

    public function fireDisablingEvent();

    public function fireDisabledEvent();

	/**
	 * @return boolean
	 */
	public function getEnabledAttribute();

	/**
	 * @param boolean $bool
	 *
	 * @return void
	 */
	public function setEnabledAttribute($bool);

	/**
	 * @return boolean
	 */
	public function isEnabling();

	/**
	 * @param boolean $isEnabling
	 */
	public function setEnabling($isEnabling);
	/**
	 * @return boolean
	 */
	public function isDisabling();

	/**
	 * @param boolean $isDisabling
	 */
	public function setDisabling($isDisabling);
}
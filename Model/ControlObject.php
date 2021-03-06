<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('Aco', 'Model');

class ControlObject extends Aco {

	/**
	 * Overwrite Aco name.
	 *
	 * @var string
	 */
	public $name = 'ControlObject';

	/**
	 * Use alias as display.
	 *
	 * @var string
	 */
	public $displayField = 'alias';

	/**
	 * Use acos table.
	 *
	 * @var string
	 */
	public $useTable = 'acos';

	/**
	 * Disable recursion.
	 *
	 * @var int
	 */
	public $recursive = -1;

	/**
	 * Force to admin plugin.
	 *
	 * @var string
	 */
	public $plugin = 'Admin';

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public $admin = array(
		'iconClass' => 'icon-wrench',
		'hideFields' => array('lft', 'rght'),
		'editable' => false
	);

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Parent' => array(
			'className' => 'Admin.ControlObject',
			'foreignKey' => 'parent_id'
		),
		'User' => array(
			'className' => USER_MODEL,
			'foreignKey' => 'foreign_key',
			//'conditions' => array('ControlObject.model' => USER_MODEL)
		)
	);

	/**
	 * Has many.
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Children' => array(
			'className' => 'Admin.ControlObject',
			'foreignKey' => 'parent_id',
			'dependent' => true,
			'exclusive' => true
		)
	);

	/**
	 * Has and belongs to many.
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = array(
		'RequestObject' => array(
			'className' => 'Admin.RequestObject',
			'with' => 'Admin.ObjectPermission',
			'joinTable' => 'aros_acos',
			'showInForm' => false
		)
	);

	/**
	 * Behaviors.
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Tree' => array('type' => 'nested'),
		'Containable',
		'Utility.Cacheable'
	);

	/**
	 * Add an object if it does not exist.
	 *
	 * @param string $alias
	 * @param int $parent_id
	 * @return int
	 */
	public function addObject($alias, $parent_id = null) {
		$query = array(
			'alias' => $alias,
			'parent_id' => $parent_id
		);

		$result = $this->find('first', array(
			'conditions' => $query
		));

		if ($result) {
			return $result['ControlObject']['id'];
		}

		$this->create();

		if ($this->save($query)) {
			return $this->id;
		}

		return null;
	}

	/**
	 * Return all records.
	 *
	 * @return array
	 */
	public function getAll() {
		$this->recursive = 0;

		return $this->find('all', array(
			'order' => array('ControlObject.alias' => 'ASC'),
			'cache' => __METHOD__,
			'cacheExpires' => '+1 hour'
		));
	}

	/**
	 * Return a record based on ID.
	 *
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('ControlObject.id' => $id),
			'contain' => array('Parent', 'User'),
			'cache' => array(__METHOD__, $id)
		));
	}

	/**
	 * Check if an alias already exists.
	 *
	 * @param string $alias
	 * @return bool
	 */
	public function hasAlias($alias) {
		return (bool) $this->find('count', array(
			'conditions' => array('ControlObject.alias' => $alias),
			'cache' => array(__METHOD__, $alias),
			'cacheExpires' => '+24 hours'
		));
	}

}
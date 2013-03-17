<?php

class ReportsController extends AdminAppController {

	public function index() {
		$this->paginate = array_merge(array(
			'limit' => 25,
			'order' => array($this->Model->alias . '.' . $this->Model->displayField => 'ASC'),
			'contain' => array_keys($this->Model->belongsTo),
			'conditions' => array($this->Model->alias . '.status' => ItemReport::PENDING)
		), $this->Model->admin['paginate']);

		$this->AdminToolbar->setBelongsToData($this->Model);
		$this->request->data[$this->Model->alias]['status'] = ItemReport::PENDING;

		// Filters
		if (!empty($this->request->params['named'])) {
			$this->paginate['conditions'] = $this->AdminToolbar->parseFilterConditions($this->Model, $this->request->params['named']);
		}

		$this->set('model', $this->Model);
		$this->set('results', $this->paginate($this->Model));
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Model = Admin::introspectModel('Admin.ItemReport');
	}

}
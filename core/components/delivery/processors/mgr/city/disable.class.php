<?php

/**
 * Disable an Item
 */
class DeliveryCityDisableProcessor extends modObjectProcessor {
	public $objectType = 'extDeliveryCity';
	public $classKey = 'extDeliveryCity';
	public $languageTopics = array('delivery');
	//public $permission = 'save';


	/**
	 * @return array|string
	 */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		$ids = $this->modx->fromJSON($this->getProperty('ids'));
		if (empty($ids)) {
			return $this->failure($this->modx->lexicon('delivery_city_err_ns'));
		}

		foreach ($ids as $id) {
			/** @var DeliveryItem $object */
			if (!$object = $this->modx->getObject($this->classKey, $id)) {
				return $this->failure($this->modx->lexicon('delivery_city_err_nf'));
			}

			$object->set('active', false);
			$object->save();
		}

		return $this->success();
	}

}

return 'DeliveryCityDisableProcessor';

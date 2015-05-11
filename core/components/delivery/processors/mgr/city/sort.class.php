<?php
/**
 * Created by Delivery.
 * User: Vitaly
 * Date: 11.05.15
 * Time: 16:25
 */

class DeliveryCitySortProcessor extends modObjectProcessor {
    public $objectType = 'extDeliveryCity';
    public $classKey = 'extDeliveryCity';
    public $languageTopics = array('delivery');


    /** {@inheritDoc} */
    //public function initialize() {
    //    if (!$this->modx->hasPermission($this->permission)) {
    //        return $this->modx->lexicon('access_denied');
    //    }
    //    return parent::initialize();
    //}


    /** {@inheritDoc} */
    public function process() {
        /* @var msPayment $source */
        $source = $this->modx->getObject($this->classKey, $this->getProperty('source'));
        /* @var msPayment $target */
        $target = $this->modx->getObject($this->classKey, $this->getProperty('target'));

        if (empty($source) || empty($target)) {
            return $this->modx->error->failure();
        }

        if ($source->get('rank') < $target->get('rank')) {
            $this->modx->exec("UPDATE {$this->modx->getTableName($this->classKey)}
				SET rank = rank - 1 WHERE
					rank <= {$target->get('rank')}
					AND rank > {$source->get('rank')}
					AND rank > 0
			");

        } else {
            $this->modx->exec("UPDATE {$this->modx->getTableName($this->classKey)}
				SET rank = rank + 1 WHERE
					rank >= {$target->get('rank')}
					AND rank < {$source->get('rank')}
			");
        }
        $newRank = $target->get('rank');
        $source->set('rank',$newRank);
        $source->save();

        if (!$this->modx->getCount($this->classKey, array('rank' => 0))) {
            $this->setRanks();
        }
        return $this->modx->error->success();
    }


    /** {@inheritDoc} */
    public function setRanks() {
        $q = $this->modx->newQuery($this->classKey);
        $q->select('id');
        $q->sortby('rank ASC, id', 'ASC');

        if ($q->prepare() && $q->stmt->execute()) {
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            $sql = '';
            $table = $this->modx->getTableName($this->classKey);
            foreach ($ids as $k => $id) {
                $sql .= "UPDATE {$table} SET `rank` = '{$k}' WHERE `id` = '{$id}';";
            }
            $this->modx->exec($sql);
        }
    }

}

return 'DeliveryCitySortProcessor';
<?php

class MovableActiveRecordBehavior extends CActiveRecordBehavior
{
	public $groupDetect = array();

	public $indexField = 'index';

	/**
	* @return CActiveRecord
	*/
	function getOwner()
	{
		$ar = parent::getOwner();
		assert('$ar instanceof CActiveRecord');
		return $ar;
	}

	/**
	* get a string that represent where statement to locate the items that are in same group
	* with the owner of this behavior
	*
	* @return string
	*/
	protected function getGroupWhere()
	{
		$ar = $this->getOwner();

		$where = '';
		if (!is_array($this->groupDetect)) $this->groupDetect = array($this->groupDetect);

		foreach ($this->groupDetect as $fieldName) {
			assert('is_string($fieldName)');

			$value = $ar->$fieldName;

			if (is_null($value)) {
				$where[] = "({$fieldName} IS NULL)";
				continue;
			}

			$where[] = sprintf("{$fieldName} = '%s'", addslashes($value));
		}

		return '(' . implode(' AND ', $where) . ')';
	}

	protected function moveWithRebuildIndexCallback($step, $callback)
	{
		$ar = $this->getOwner();

		$table = $ar->tableName();
		$where = $this->getGroupWhere();

		$cmd = $ar->getDbConnection()->createCommand("LOCK TABLES `{$table}`;");
		$indexField = $this->indexField;
		$idField = $ar->getTableSchema()->primaryKey;

		try {
			$curIndex = $ar->$indexField;

			$cmd->text = "SELECT `{$idField}`, `{$indexField}` FROM `{$table}` WHERE {$where} ORDER BY `{$indexField}`, `{$idField}`";
			$rows = $cmd->queryAll();

			$gap = 0;
			$lastIndex = 0;
			foreach ($rows as $i => & $row) {
				$row = array(
					'id'	=>	$row[$idField],
					'index'	=>	$row[$indexField],
				);
				$row['index'] = ++$lastIndex;
				if ($row['id'] == $ar->$idField) $idx = $i;
			}

			call_user_func_array($callback, array(&$rows, $step, $idx));

			$value = "CASE `{$idField}`";
			foreach ($rows as & $row) {
				$value .= sprintf(' WHEN "%s" THEN "%s"', addslashes($row['id']), $row['index']);
			}
			$value .= ' ELSE 0 END';

			$cmd->text = "UPDATE `{$table}` SET `{$indexField}` = {$value} WHERE {$where}";
			$cmd->execute();

		} catch (Exception $e) {
			$cmd->text = 'UNLOCK TABLES;';
			$cmd->execute();
			throw $e;
		}

		$cmd->text = 'UNLOCK TABLES;';
		$cmd->execute();

		return true;
	}

	protected function moveUpRebuildIndex(& $rows, $step, $idx)
	{
		$i = $idx-1;
		while ($step > 0 && $i >= 0) {
			$rows[$i]['index']++;
			$rows[$idx]['index']--;
			$step--; $i--;
		}
	}

	protected function moveDownRebuildIndex(& $rows, $step, $idx)
	{
		$i = $idx+1;
		$count = count($rows);
		while ($step > 0 && $i < $count) {
			$rows[$i]['index']--;
			$rows[$idx]['index']++;
			$step--; $i++;
		}
	}

	protected function moveFirstRebuildIndex(& $rows, $step, $idx)
	{
		for ($i=0; $i<$idx; $i++) $rows[$i]['index']++;
		$rows[$idx]['index'] = 1;
	}

	protected function moveLastRebuildIndex(& $rows, $step, $idx)
	{
		$last = count($rows);
		for ($i=$idx+1; $i<$last; $i++) $rows[$i]['index']--;
		$rows[$idx]['index'] = $last;
	}

	function moveUp($step = 1)
	{
		return $this->moveWithRebuildIndexCallback($step, array($this, 'moveUpRebuildIndex'));
	}

	function moveDown($step = 1)
	{
		return $this->moveWithRebuildIndexCallback($step, array($this, 'moveDownRebuildIndex'));
	}

	function moveToFirst()
	{
		return $this->moveWithRebuildIndexCallback(null, array($this, 'moveFirstRebuildIndex'));
	}

	function moveToEnd()
	{
		return $this->moveWithRebuildIndexCallback(null, array($this, 'moveLastRebuildIndex'));
	}
}

?>

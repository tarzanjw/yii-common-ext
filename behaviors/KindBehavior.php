<?php
/**
* 
* 
* 
* 
*/
  class KindBehavior extends CActiveRecordBehavior {  
    public function getValue($id=null,$uk=null,$name=null,$last_modified_time=null){
        return Yii::app()->Kind->getKind($id,$uk,$name,$last_modified_time); 
    }
    public function createValue($uk=null,$name=null){
        return Yii::app()->Kind->createKind($uk,$name); 
    }
    public function deleteValue($id=null){
         return Yii::app()->Kind->deleteKind($id);
    }
    public function updateValue($id=null,$uk=null,$name=null){
         return Yii::app()->Kind->updateKind($id,$uk,$name);
    }
  }
?>

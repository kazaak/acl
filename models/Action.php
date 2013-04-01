<?php

/**
 * This is the model class for table "{{action}}".
 *
 * Actions represent the things you can do with an Acess control object: for 
 * example you can most often create, read, update and delete objects (also called CRUD)
 * The actions which can be performed on a given object can be explicitely
 * denoted by RestrictedActiveRecord::$possibleActions. Actions which are not
 * in this list, if it is defined, will never be granted.
 * @see RestrictedActivgeRecord::possibleActions
 * 
 * @author dispy <dispyfree@googlemail.com>
 * @package acl.base
 * @license LGPLv2
 * 
 * The followings are the available columns in table '{{action}}':
 * @property integer $id
 * @property string $name
 * @property integer $created
 */
class Action extends CActiveRecord
{
    protected $sequenceFields = array(
        'id' => '{{seq_action_id}}'
    );
    
    public $id;
    public $name;
    
    /**
     * Translates the gibven actions into valid actions
     * @param mixed $obj the object to perform the permissions on (either CActiveRecord or a string or an ACL-Object
     * @param mixed $actions may be a string or an array 
     */
    public static function translateActions($obj, $actions){
        if(is_string($actions))
            $actions = static::translateStringActions($actions);

        //If it's an Action-Obj 
        elseif(is_object($actions)){
            
            //If we get a single action
            if($actions instanceof Action)
                return array($actions->name);
            //Well... what the hell is this??
            else
                throw new RuntimeException('Invalid Action specified');
            
        }
        //If it's an array of action-objects
        elseif(is_array($actions) && $actions[0] instanceof Action){
            $newActions = array();
            foreach($actions as $action){
                $newActions[] = $action->name;
            }
            return $newActions;
        }
        
        //If nothing has applied, we have the actions in plain form - a list of strings

        //Now, check if the object restricts the actions
        $class = NULL;
        
        if($obj instanceof CActiveRecord)
            $class = get_class($class);
        //this is for the general permissions
        elseif(is_string($obj))
            $class = $obj;
        elseif($obj->model !== NULL){
            $class = $obj->model;
        }
        
        if($class === NULL)
            return array();
            
        
        if(isset($class::$possibleActions)){
            $newActions = array();
            foreach($actions as $action){
                if(in_array($action, $class::$possibleActions))
                        $newActions[] = $action;
            }
            $actions = $newActions;
        }
            
        
        
        return $actions;
    }
    
    /**
     * Processes the given actions
     * @param mixed $actions string or array of actions
     */
    protected static function translateStringActions($actions){
        //Nothing more to do
        if(is_array($actions)){
            return $actions;
        }
        
        //Now, it is a string
        //Search the first occurence of a modificator (+ or -)
        $posMinus = strpos($actions, '-');
        $posPlus  = strpos($actions, '+');
        
        //If none is found, we can split it up into the actions
        if($posMinus === false && $posPlus === false){
            $actions = str_replace(",", " ",$actions);
            $actions = explode(" ", $actions);
            
            $completedActions = array();
            foreach($actions as $action){
                $action = trim($action);
                if(strlen($action) > 0){
                    
                    if($action == '*')
                        $completedActions = array_merge($completedActions, static::getAllStringActions());
                    else
                        $completedActions[] = $action;
                }
            }
            
            return $completedActions;
        }
        
        else{
            return static::processActionOperation($posMinus, $posPlus, $actions);
        }
    }
    
    /**
     * Processes the next operation on the actions and returns them
     * @param int $posMinus pos of the next minus-symbol in the string
     * @param int $posPlus pos of the next plus-symbol in the string
     * @param string $actions the action-string
     * @return array[string] the actions 
     */
    protected static function processActionOperation($posMinus, $posPlus, $actions){
        $firstPos = NULL;
            if($posMinus !== false && $posPlus !== false)
                $firstPos = min($posMinus, $posPlus);
            elseif($posMinus === false)
                $firstPos = $posPlus;
            else
                $firstPos = $posMinus;

            $operation = $firstPos == $posMinus ? '-' : '+';

            $startStr = substr($actions, 0, $firstPos);
            $endStr = substr($actions, $firstPos + 1);

            $startActions = static::translateStringActions($startStr);
            $endActions = static::translateStringActions($endStr);
            
            if($operation == '+'){
                return array_merge($startActions, $endActions);
            }
            else{
                return array_diff($startActions, $endActions);
            }
    }
    
    /**
     * Fetches all actions from the database and returns them in an indexed array
     * @return array[string] the actinos 
     */
    protected static function getAllStringActions(){
        $actions = Util::enableCaching(Action::model(), 'action')->findAll();
        $sActions = array();
        
        foreach($actions as $action){
            $sActions[] = $action->name;
        }
        return $sActions;
    }
    
    public function __toString(){
        $obj = new stdClass();
        $obj->id    = $this->id;
        $obj->name  = $this->name;
        
        return json_encode($obj);
    }
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Action the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{action}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, created', 'required'),
            array('created', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>15),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, created', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'created' => 'Created',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('created',$this->created);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
} 
?>

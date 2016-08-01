<?php

/**
 * This is the model class for table "tbl_category".
 *
 * The followings are the available columns in table 'tbl_category':
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $update_time
 *
 * The followings are the available model relations:
 * @property TblNewsCategory[] $tblNewsCategories
 */
class Category extends CActiveRecord
{        
        /**
         * @var array categories in which there is news 
         */
        private $_idCategoryHasNews = array();
        
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('parent_id, update_time', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, parent_id, update_time', 'safe', 'on'=>'search'),
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
			'NewsCategories' => array(self::HAS_MANY, 'NewsCategory', 'category_id'),
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
			'parent_id' => 'Parent',
			'update_time' => 'Update Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('update_time',$this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Category the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        /**
	 * Return children categories array
	 * @param integer $parent id parent category
         * @param integer $level nesting level
	 * @return array id children categories
	 */
        
        public function getChildrenArray($parent, $level=0)
        { 
                $criteria = new CDbCriteria;
                $criteria->condition='parent_id=:id';
                $criteria->params=array(':id'=>$parent);
                $model = $this->findAll($criteria);
                $categories = [];
                foreach ($model as $category) {
                        $categories[] = $category->id;
                        foreach ($this->getChildrenArray($category->id) as $cat) {
                                array_push($categories, $cat);
                        }
                }
                return $categories;
        }

        
        /**
	 * Add children categories to array
         * @param array &$categories array categories
	 * @param integer $parent id parent category
         * @param integer $level nesting level
	 * @return null
	 */
        public function setChildrenForSelect(&$categories, $parent, $level=1)
        { 
                $criteria = new CDbCriteria;
                $criteria->condition='parent_id=:id';
                $criteria->params=array(':id'=>$parent);
                $model = $this->findAll($criteria);
                foreach ($model as $category) {
                        
                        $categories[$category->id] = str_repeat('-', $level) . $category->name;
                        $this->setChildrenForSelect($categories, $category->id, $level+1);
                }
        }
        
        /**
         * Return array for CHtml::dropDownList().
         * Cached array depends on  update_time FROM tbl_category
         * @return array for CHtml::dropDownList() 
         */
        
        public function getCategoriesForSelect()
        { 
            $categories = Yii::app()->cache->get('categoriesForSelect');

            if($categories === false) {
                    $criteria = new CDbCriteria;
                    $criteria->condition='parent_id=0';
                    $model = $this->findAll($criteria);
                    $categories = array(0=>'');
                    foreach ($model as $category) {
                            $categories[$category->id] = $category->name;
                            $this->setChildrenForSelect($categories, $category->id);
                    }
                    $dependency = new CDbCacheDependency('SELECT COUNT(cat.id), SUM(cat.update_time), MAX(news.update_time) FROM tbl_category as cat, tbl_news as news');
                    Yii::app()->cache->set('categoriesForSelect', $categories, 86400, $dependency);
            }
            return $categories;
        }
        
        /**
	 * Return childrens category array for menu
	 * @param integer $parent id parent category
         * @param integer $level nesting level
	 * @return array childrens category
	 */
        
        public function getChildrenCategory($parent, $level=0)
        { 
            $criteria = new CDbCriteria;
            $criteria->condition='parent_id=:id';
            $criteria->params=array(':id'=>$parent);
            $model = $this->findAll($criteria);
            $categories = [];
            $i = 0;
            foreach ($model as $category) {
                    $childrensCategory = $this->getChildrenCategory($category->id, $level+1);
                    if (!empty($childrensCategory)) {
                            $categories[$i] = array(
                                    'label'=>$category->name,
                                    'url'=>array('route'=>'/category/'.$category->id),
                                    $childrensCategory,
                            );

                            foreach ($childrensCategory as $chld) {
                                    array_push($categories[$i], $chld);
                            }
                    } else {
                        if (in_array($category->id, $this->_idCategoryHasNews)) {
                            $categories[$i] = array(
                                    'label'=>$category->name,
                                    'url'=>array('route'=>'/category/'.$category->id),
                            );
                        }
                    }
                    $i++;
            }
            return $categories;
        }
        
        /**
         * Return array for zii.widgets.CMenu.
         * Cached array depends on  update_time FROM tbl_category
         * @return array for zii.widgets.CMenu 
         */
        public function getCategories()
        {
            $categories = Yii::app()->cache->get('categoriesForMenu');
            
                if($categories === false) {
                        $categories_id = Yii::app()->db->createCommand()->select('category_id')->from('tbl_news_category')->queryAll();
                        $idCategoriesHasNews = [];
                        foreach ($categories_id as $id) {
                                $idCategoriesHasNews[] = $id['category_id'];
                        }
                        $this->_idCategoryHasNews = $idCategoriesHasNews;

                        $criteria = new CDbCriteria;
                        $criteria->condition='parent_id=0';
                        $model = $this->findAll($criteria);
                        $categories = [];
                        $i = 0;
                        foreach ($model as $category) {
                                $childrensCategory = $this->getChildrenCategory($category->id);
                                if (!empty($childrensCategory)) {
                                        $categories[$i] = array(
                                            'label'=>$category->name,
                                            'url'=>array('route'=>'/category/'.$category->id),
                                            );
                                        foreach ($childrensCategory as $chld) {
                                                array_push($categories[$i], $chld);
                                        }
                                } else {
                                        if (in_array($category->id, $this->_idCategoryHasNews)) {
                                                $categories[$i] = array(
                                                    'label'=>$category->name,
                                                    'url'=>array('route'=>'/category/'.$category->id),
                                                );
                                        }
                                }
                                $i++;
                        }
                        $dependency = new CDbCacheDependency('SELECT COUNT(cat.id), SUM(cat.update_time), MAX(news.update_time) FROM tbl_category as cat, tbl_news as news');
                        Yii::app()->cache->set('categoriesForMenu', $categories, 86400, $dependency);
                }
            return $categories;
        }
        
        /**
         * This method is invoked before saving a record (after validation, if any).
         * @return boolean
         */
        protected function beforeSave()
        {
                $this->update_time = time();
                
                return true;
        }
}

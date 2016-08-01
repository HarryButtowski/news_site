<?php

/**
 * This is the model class for table "tbl_news".
 *
 * The followings are the available columns in table 'tbl_news':
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $preview
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $author_id
 *
 * The followings are the available model relations:
 * @property TblUser $author
 * @property TblComment[] $tblComments
 */
class News extends CActiveRecord
{
        const STATUS_DRAFT=1;
        const STATUS_PUBLISHED=2;
        const STATUS_ARCHIVED=3;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_news';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, preview, content, status', 'required'),
			array('create_time, update_time', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>128),
                        array('status', 'in', 'range'=>array(1,2,3)),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, slug, content, status, create_time, update_time, author_id', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'User', 'author_id'),
			'comments' => array(self::HAS_MANY, 'Comment', 'news_id',
                            'condition'=>'comments.status='.Comment::STATUS_APPROVED,
                            'order'=>'comments.create_time DESC'),
                         'commentCount' => array(self::STAT, 'Comment', 'news_id',
                            'condition'=>'status='.Comment::STATUS_APPROVED),
                         'category'=>array(self::MANY_MANY, 'Category',
                            'tbl_news_category(news_id, category_id)'),
		);
	}
        /**
         * Attach the behavior to create and write slug.
         * @return type
         */
        public function behaviors(){
                return array(
                    'SlugBehavior' => array(
                        'class' => 'ext.aii.behaviors.SlugBehavior',
                        'sourceAttribute' => 'title',
                        'slugAttribute' => 'slug',
                        'mode' => 'translit',
                    ),
                );
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'content' => 'Content',
			'preview' => 'preview',
			'slug' => 'Slug',
			'status' => 'Status',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'author_id' => 'Author',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('preview',$this->preview,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('author_id',$this->author_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return News the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        /**
         * Get url for news.
         * @return string
         */
        public function getUrl()
        {
                return Yii::app()->createUrl($this->slug);
        }
        
        /**
         * This method is invoked before saving a record (after validation, if any).
         * @return boolean
         */
        protected function beforeSave()
        {
                if (parent::beforeSave()) {
                    if ($this->isNewRecord) {
                            $this->create_time = $this->update_time = time();
                            $this->author_id = Yii::app()->user->id;
                        
                    } else {
                            $this->update_time = time();
                    }
                        return true;
                } else {
                        return false;
                }
        }
        
        /**
         * This method is invoked after saving a record.
         */
        protected function afterSave()
        {
                parent::afterSave();
                if (!empty($this->category)) {
                        $newsCategory = new NewsCategory;
                        $newsCategory->deleteAll('news_id=:id', array(':id'=>$this->id));
                        $newsCategory->news_id = $this->id;
                        $newsCategory->category_id = $this->category;
                        $newsCategory->save();
                }
        }
        
        public function addComment($comment)
        {
                if (Yii::app()->params['commentNeedApproval']) {
                        $comment->status=Comment::STATUS_PENDING;
                } else {
                        $comment->status=Comment::STATUS_APPROVED;
                }
                $comment->news_id=$this->id;
                return $comment->save();
        }
}
